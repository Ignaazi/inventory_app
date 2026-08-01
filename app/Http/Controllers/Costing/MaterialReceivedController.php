<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use App\Models\Costing\MaterialReceived;
use App\Models\Engineering\PurchaseRequestEng; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialReceivedController extends Controller
{
    /**
     * 1. HALAMAN LIST DATA / TRACKING
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);

        $materialReceived = MaterialReceived::with(['user', 'purchaseRequest.sparepart'])
            ->when($search, function ($query) use ($search) {
                $query->where('no_mr', 'LIKE', "%{$search}%")
                      ->orWhereHas('purchaseRequest', function ($q) use ($search) {
                          $q->where('no_pr', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('user', function ($q) use ($search) {
                          $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('nik', 'LIKE', "%{$search}%");
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('cost_section.material_received_list', compact('materialReceived', 'search'));
    }

    /**
     * 2. HALAMAN FORM INPUT BARU
     */
    public function create($pr_id = null)
    {
        // Generasi Nomor Urut MR Baru Secara Presisi (Contoh: MR000001)
        $latestMr = MaterialReceived::orderBy('id', 'desc')->first();
        $nextId = $latestMr ? $latestMr->id + 1 : 1;
        $nextMrNo = 'MR' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        // Ambil semua PR aktif, lalu kalkulasi kuota yang tersisa (terbuka)
        $purchaseRequests = PurchaseRequestEng::with('sparepart')
            ->get()
            ->map(function ($pr) {
                $totalReceived = MaterialReceived::where('purchase_request_id', $pr->id)
                    ->whereIn('status', ['pending', 'checked', 'approved'])
                    ->sum('qty_received');

                // Menyuntikkan saldo kuota yang saat ini dibutuhkan ke 'qty_remaining'
                $pr->qty_remaining = max(0, $pr->qty_pr - $totalReceived);
                return $pr;
            })
            ->filter(function ($pr) {
                // Hanya loloskan PR yang kuotanya masih ada (> 0).
                return $pr->qty_remaining > 0;
            })
            ->values();

        // Proteksi jika user mencoba akses direct link PR lewat URL
        if ($pr_id) {
            $selectedPr = $purchaseRequests->firstWhere('id', $pr_id);
            if (!$selectedPr) {
                return redirect()->route('costing.material.list')
                    ->with('error', 'Dokumen PR tidak ditemukan atau status item Qty sudah selesai dipenuhi (CLOSED)!');
            }
        }

        return view('cost_section.material_received', compact('purchaseRequests', 'pr_id', 'nextMrNo'));
    }

    /**
     * 3. HALAMAN PREVIEW DOKUMEN VIA BLADE VIEW (Klik Mata)
     * FIX SINKRONISASI: Variabel distandarkan menggunakan $mr sesuai target Blade
     */
    public function show($id)
    {
        // Ambil data MR beserta relasi PR, sparepart, dan user pembuatnya
        $mr = MaterialReceived::with(['user', 'purchaseRequest.sparepart', 'purchaseRequest.user'])->findOrFail($id);
        
        // Mengarahkan ke file material_received_preview.blade.php di folder cost_section
        return view('cost_section.material_received_preview', compact('mr'));
    }

    /**
     * 4. PROSES SUBMIT & TTD BERJENJANG (ROLE: COSTING)
     */
    public function storeCostingSignature(Request $request)
    {
        $prTable = (new PurchaseRequestEng())->getTable();

        $request->validate([
            'purchase_request_id' => "required|exists:{$prTable},id",
            'qty_received'        => 'required|integer|min:1',
            'lot_no'              => 'nullable|string|max:100',
            'remark'              => 'required|string',
            'prepared_signature'  => 'required|string',
        ]);

        $pr = PurchaseRequestEng::findOrFail($request->purchase_request_id);

        // Hitung ulang saldo terbuka di database server
        $alreadyReceived = MaterialReceived::where('purchase_request_id', $pr->id)
            ->whereIn('status', ['pending', 'checked', 'approved'])
            ->sum('qty_received');
        
        $maxAllowed = $pr->qty_pr - $alreadyReceived;

        // Proteksi jika sisa saldo PR 0 atau minus, block langsung!
        if ($maxAllowed <= 0) {
            return redirect()->back()->withErrors([
                'purchase_request_id' => "Transaksi Ditolak! Dokumen PR ini sudah berstatus CLOSED dan seluruh kuantitasnya telah terpenuhi."
            ]);
        }

        // Proteksi server-side: Input Qty Received tidak boleh melampaui sisa balance yang tersedia
        if ($request->qty_received > $maxAllowed) {
            return redirect()->back()->withErrors([
                'qty_received' => "Jumlah QTY RECEIVED ({$request->qty_received} Pcs) melebihi sisa batas QTY PR terbuka (Maksimal sisa: {$maxAllowed} Pcs)."
            ]);
        }

        // MENENTUKAN STATUS QTY LANGSUNG DENGAN MINUSNYA KE DATABASE
        $qtyGap = max(0, $maxAllowed - $request->qty_received);

        if ($qtyGap === 0) {
            $qtyStatus = 'CLOSE';
        } else {
            // Database akan langsung menyimpan string lengkap, contoh: "OPEN (-5 Pcs)"
            $qtyStatus = 'OPEN (-' . number_format($qtyGap) . ' Pcs)';
        }

        // Generate nomor MR final
        $latestMr = MaterialReceived::orderBy('id', 'desc')->first();
        $nextId = $latestMr ? $latestMr->id + 1 : 1;
        $finalMrNo = 'MR' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        // Proses penyimpanan path TTD
        $signaturePath = null;
        if (str_contains($request->prepared_signature, 'data:image')) {
            $signaturePath = $this->uploadBase64Signature($request->prepared_signature, 'prepared');
        } else {
            $signaturePath = $request->prepared_signature;
        }

        // Insert ke database
        MaterialReceived::create([
            'no_mr'               => $finalMrNo,
            'purchase_request_id' => $request->purchase_request_id,
            'user_id'             => Auth::id(),
            'qty_received'        => $request->qty_received,
            'qty_status'          => $qtyStatus, 
            'lot_no'              => $request->lot_no,
            'remark'              => $request->remark,
            'status'              => 'pending',
            'prepared_signature'  => $signaturePath,
        ]);

        return redirect()->route('costing.material.list')->with('success', "Form MR berhasil diajukan dengan status Qty: {$qtyStatus}!");
    }

    /**
     * 5. PROSES SIGNATURE STAFF ENGINEERING (CHECKED)
     */
    public function signEngineeringStaff($id, Request $request)
    {
        $request->validate([
            'checked_signature' => 'required|string',
        ]);

        $mr = MaterialReceived::findOrFail($id);

        if ($mr->status !== 'pending') {
            return redirect()->back()->with('error', 'Status dokumen bukan pending!');
        }

        $signaturePath = $this->uploadBase64Signature($request->checked_signature, 'checked');

        $mr->update([
            'status'            => 'checked',
            'checked_signature' => $signaturePath
        ]);

        return redirect()->route('costing.material.list')->with('success', 'Dokumen berhasil dicheck oleh Staff Engineering!');
    }

    /**
     * 6. PROSES APPROVAL SUPERVISOR / ADMIN (APPROVED)
     */
    public function approveEngineeringSpv($id, Request $request)
    {
        $request->validate([
            'approved_signature' => 'required|string',
        ]);

        $mr = MaterialReceived::findOrFail($id);

        if ($mr->status !== 'checked') {
            return redirect()->back()->with('error', 'Dokumen harus dicheck oleh staff terlebih dahulu!');
        }

        $signaturePath = $this->uploadBase64Signature($request->approved_signature, 'approved');

        $mr->update([
            'status'             => 'approved',
            'approved_signature' => $signaturePath
        ]);

        return redirect()->route('costing.material.list')->with('success', 'Dokumen MR Approved oleh Admin!');
    }

    /**
     * 7. PROSES HAPUS DATA
     */
    public function destroy($id)
    {
        $mr = MaterialReceived::findOrFail($id);
        
        if ($mr->prepared_signature) Storage::disk('public')->delete($mr->prepared_signature);
        if ($mr->checked_signature) Storage::disk('public')->delete($mr->checked_signature);
        if ($mr->approved_signature) Storage::disk('public')->delete($mr->approved_signature);

        $mr->delete();

        return redirect()->route('costing.material.list')->with('success', 'Data MR berhasil dihapus!');
    }

    /**
     * HELPER UPLOAD BASE64 TTD DIGITAL
     */
    private function uploadBase64Signature($base64String, $prefix)
    {
        $image_parts = explode(";base64,", $base64String);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        
        $fileName = 'signatures/mr/' . $prefix . '_' . uniqid() . '.' . $image_type;
        Storage::disk('public')->put($fileName, $image_base64);
        
        return $fileName;
    }
}