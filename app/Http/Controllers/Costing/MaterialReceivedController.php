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
     * 1. HALAMAN LIST DATA / TRACKING (SISI COSTING)
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
     * 2. HALAMAN FORM INPUT BARU (COSTING)
     */
    public function create($pr_id = null)
    {
        $latestMr = MaterialReceived::orderBy('id', 'desc')->first();
        $nextId = $latestMr ? $latestMr->id + 1 : 1;
        $nextMrNo = 'MR' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $purchaseRequests = PurchaseRequestEng::with('sparepart')
            ->get()
            ->map(function ($pr) {
                $totalReceived = MaterialReceived::where('purchase_request_id', $pr->id)
                    ->whereIn('status', ['pending', 'checked', 'approved'])
                    ->sum('qty_received');

                $pr->qty_remaining = max(0, $pr->qty_pr - $totalReceived);
                return $pr;
            })
            ->filter(function ($pr) {
                return $pr->qty_remaining > 0;
            })
            ->values();

        if ($pr_id) {
            $selectedPr = $purchaseRequests->firstWhere('id', $pr_id);
            if (!$selectedPr) {
                return redirect()->route('costing.material.list')
                    ->with('error', 'Dokumen PR tidak ditemukan atau Qty sudah CLOSED!');
            }
        }

        return view('cost_section.material_received', compact('purchaseRequests', 'pr_id', 'nextMrNo'));
    }

    /**
     * 3. HALAMAN PREVIEW DOKUMEN VIA BLADE VIEW (COSTING)
     */
    public function show($id)
    {
        $mr = MaterialReceived::with(['user', 'purchaseRequest.sparepart'])->findOrFail($id);
        return view('cost_section.material_received_preview', compact('mr'));
    }

    /**
     * 4. PROSES SUBMIT & TTD AWAL (ROLE: COSTING)
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
        $alreadyReceived = MaterialReceived::where('purchase_request_id', $pr->id)
            ->whereIn('status', ['pending', 'checked', 'approved'])
            ->sum('qty_received');
        
        $maxAllowed = $pr->qty_pr - $alreadyReceived;

        if ($maxAllowed <= 0) {
            return redirect()->back()->withErrors(['purchase_request_id' => "Transaksi Ditolak! Dokumen PR ini sudah berstatus CLOSED."]);
        }

        if ($request->qty_received > $maxAllowed) {
            return redirect()->back()->withErrors(['qty_received' => "Jumlah QTY RECEIVED melebihi sisa batas QTY PR terbuka."]);
        }

        $latestMr = MaterialReceived::orderBy('id', 'desc')->first();
        $nextId = $latestMr ? $latestMr->id + 1 : 1;
        $finalMrNo = 'MR' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $signaturePath = $this->uploadBase64Signature($request->prepared_signature, 'prepared');

        MaterialReceived::create([
            'no_mr'               => $finalMrNo,
            'purchase_request_id' => $request->purchase_request_id,
            'user_id'             => Auth::id(),
            'qty_received'        => $request->qty_received,
            'qty_status'          => 'open', 
            'remark'              => $request->remark,
            'status'              => 'pending',
            'prepared_signature'  => $signaturePath,
        ]);

        return redirect()->route('costing.material.list')->with('success', "Form MR berhasil diajukan ke tim Engineering!");
    }

    /* =========================================================================
     * ⚙️ LOGIC INTEGRASI PINDAHAN SISI ENGINEERING
     * ========================================================================= */

    /**
     * 5. HALAMAN UTAMA LIST MONITORING MATERIAL (SISI ENGINEERING)
     */
    public function engIndex(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10); 
        
        $receivings = MaterialReceived::with(['user', 'purchaseRequest.sparepart'])
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

        return view('stock_eng.material_received.eng_list_material_received', compact('receivings', 'search'));
    }

    /**
     * 6. HALAMAN FORM CHECKED / VERIFIKASI (SISI ENGINEERING STAFF)
     */
    public function engConfirm($id)
    {
        $receiving = MaterialReceived::with(['user', 'purchaseRequest.sparepart'])->findOrFail($id);
        
        return view('stock_eng.material_received.eng_checked_material_received', compact('receiving'));
    }

    /**
     * 7. HALAMAN FORM APPROVAL AKHIR (SISI ENGINEERING SUPERVISOR)
     */
    public function engApprove($id)
    {
        $receiving = MaterialReceived::with(['user', 'purchaseRequest.sparepart'])->findOrFail($id);

        if (strtolower($receiving->status) !== 'checked') {
            return redirect()->route('eng.material.receiving.index')
                ->with('error', 'Dokumen harus berstatus Checked oleh Staff terlebih dahulu sebelum di-approve!');
        }

        return view('stock_eng.material_received.eng_approve_material_received', compact('receiving'));
    }

    /**
     * 8. PROSES SIGNATURE STAFF ENGINEERING (STATUS: PENDING -> CHECKED)
     */
    public function signEngineeringStaff($id, Request $request)
    {
        $receiving = MaterialReceived::findOrFail($id);

        if (strtolower($receiving->status) !== 'pending') {
            return redirect()->back()->with('error', 'Dokumen sudah diproses atau di-checked oleh orang lain!');
        }

        $pr = $receiving->purchaseRequest;
        $alreadyReceived = MaterialReceived::where('purchase_request_id', $receiving->purchase_request_id)
            ->where('id', '!=', $id)
            ->whereIn('status', ['pending', 'checked', 'approved'])
            ->sum('qty_received');
            
        $maxAllowed = $pr->qty_pr - $alreadyReceived;

        $request->validate([
            'checked_signature' => 'required|string',
            'qty_received'      => "required|integer|min:0|max:{$maxAllowed}",
            'remark'            => 'required|string'
        ]);

        $signaturePath = $this->uploadBase64Signature($request->checked_signature, 'checked');

        $receiving->update([
            'status'            => 'checked',
            'qty_received'      => $request->qty_received,
            'checked_signature' => $signaturePath,
            'remark'            => $request->remark
        ]);

        return redirect()->route('eng.material.receiving.index')->with('success', 'Material Received berhasil diperiksa (Checked) oleh Staff Engineering!');
    }

    /**
     * 9. PROSES APPROVAL AKHIR SUPERVISOR / ADMIN (STATUS: CHECKED -> APPROVED)
     * FIX: Murni hanya mengubah entitas Material Received saja tanpa menyentuh tabel PR
     */
    public function approveEngineeringSpv($id, Request $request)
    {
        $request->validate([
            'approved_signature' => 'required|string',
            'notes'              => 'nullable|string'
        ]);

        $mr = MaterialReceived::findOrFail($id);

        if (strtolower($mr->status) !== 'checked') {
            return redirect()->back()->with('error', 'Dokumen harus melalui status Checked Staff terlebih dahulu!');
        }

        $signaturePath = $this->uploadBase64Signature($request->approved_signature, 'approved');
        $updatedRemark = $mr->remark . ($request->notes ? "\n[SPV Eng Notes]: " . $request->notes : "");

        // Update record MR sesuai konfigurasi ENUM dari struktur database yang dikirim
        $mr->update([
            'status'             => 'approved', // Menjadi Approved sesuai enum data
            'qty_status'         => 'closed',   // Menjadi Closed sesuai enum data
            'approved_signature' => $signaturePath,
            'remark'             => $updatedRemark
        ]);

        // KODE PENGUBAH STATUS DI TABEL PURCHASE REQUEST SEKARANG TELAH DIHAPUS TOTAL

        return redirect()->route('eng.material.receiving.index')->with('success', 'Dokumen Material Received dinyatakan FULLY APPROVED!');
    }

    /**
     * 10. PROSES HAPUS / REJECT DATA
     */
    public function destroy($id)
    {
        $mr = MaterialReceived::findOrFail($id);
        
        if ($mr->prepared_signature) Storage::disk('public')->delete(str_replace('storage/', '', $mr->prepared_signature));
        if ($mr->checked_signature) Storage::disk('public')->delete(str_replace('storage/', '', $mr->checked_signature));
        if ($mr->approved_signature) Storage::disk('public')->delete(str_replace('storage/', '', $mr->approved_signature));

        $mr->delete();

        return redirect()->back()->with('success', 'Data Berkas Material Received berhasil dihapus / direject dari sistem!');
    }

    /**
     * HELPER UPLOAD BASE64 TTD DIGITAL
     */
    private function uploadBase64Signature($base64String, $prefix)
    {
        if (!str_contains($base64String, 'data:image')) {
            return $base64String;
        }

        $image_parts = explode(";base64,", $base64String);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        
        $fileName = 'signatures/mr/' . $prefix . '_' . uniqid() . '.' . $image_type;
        Storage::disk('public')->put($fileName, $image_base64);
        
        return 'storage/' . $fileName;
    }
}