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
        if ($response = $this->guardSignatureRole(['costing', 'admin'], 'Costing atau Admin')) {
            return $response;
        }

        $user = Auth::user();
        $prTable = (new PurchaseRequestEng())->getTable();

        $request->validate([
            'purchase_request_id' => "required|exists:{$prTable},id",
            'qty_received'        => 'required|integer|min:1',
            'lot_no'              => 'nullable|string|max:100',
            'remark'              => 'required|string',
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

        // Never trust a signature path sent by the browser. Use the signed-in
        // Costing user's signature registered in User Management.
        $signaturePath = $user->signature_path;
        $remainingQty = $pr->qty_pr - ($alreadyReceived + $request->qty_received);

        MaterialReceived::create([
            'no_mr'               => $finalMrNo,
            'purchase_request_id' => $request->purchase_request_id,
            'user_id'             => Auth::id(),
            'qty_received'        => $request->qty_received,
            'qty_status'          => $remainingQty > 0 ? 'open' : 'closed',
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
     * FIX: Hanya menampilkan status 'pending' dan 'checked' agar list bersih saat selesai
     */
    public function engIndex(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10); 
        $liveVersion = $this->materialReceivedLiveVersion(['pending', 'checked']);

        if ($request->boolean('live')) {
            return response()->json(['version' => $liveVersion]);
        }
        
        $receivings = MaterialReceived::with(['user', 'purchaseRequest.sparepart'])
            ->whereIn('status', ['pending', 'checked']) // 💡 Data 'approved' otomatis sembunyi dari list utama
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_mr', 'LIKE', "%{$search}%")
                      ->orWhereHas('purchaseRequest', function ($purchaseRequestQuery) use ($search) {
                          $purchaseRequestQuery->where('no_pr', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('nik', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->orderByDesc('id')
            ->paginate($perPage);

        return view('stock_eng.material_received.eng_list_material_received', compact('receivings', 'search', 'liveVersion'));
    }

    /**
     * 🌟 BARU: 5B. HALAMAN HISTORY MATERIAL RECEIVED (SISI ENGINEERING)
     * Menampilkan rekaman data yang sudah FULLY APPROVED
     */
    public function engHistory(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10); 
        $liveVersion = $this->materialReceivedLiveVersion(['approved']);

        if ($request->boolean('live')) {
            return response()->json(['version' => $liveVersion]);
        }
        
        $receivings = MaterialReceived::with(['user', 'purchaseRequest.sparepart'])
            ->where('status', 'approved') // 💡 Hanya menarik data yang sudah disetujui
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_mr', 'LIKE', "%{$search}%")
                      ->orWhereHas('purchaseRequest', function ($purchaseRequestQuery) use ($search) {
                          $purchaseRequestQuery->where('no_pr', 'LIKE', "%{$search}%");
                      })
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'LIKE', "%{$search}%")
                                    ->orWhere('nik', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->orderByDesc('updated_at') // Diurutkan berdasarkan approval paling baru
            ->orderByDesc('id')
            ->paginate($perPage);

        return view('stock_eng.material_received.history_mr', compact('receivings', 'search', 'liveVersion'));
    }

    /**
     * 6. HALAMAN FORM CHECKED / VERIFIKASI (SISI ENGINEERING STAFF)
     */
    public function engConfirm($id)
    {
        $receiving = MaterialReceived::with(['user', 'purchaseRequest.sparepart'])->findOrFail($id);

        if (strtolower($receiving->status) !== 'pending') {
            return redirect()->route('eng.material.receiving.index')
                ->with('error', 'Dokumen sudah diperiksa atau diproses oleh user lain.');
        }
        
        return view('stock_eng.material_received.eng_checked_material_received', compact('receiving'));
    }

    /**
     * 7. HALAMAN FORM APPROVAL AKHIR (SISI ADMIN)
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
        if ($response = $this->guardSignatureRole(['engineering', 'admin'], 'Engineering atau Admin')) {
            return $response;
        }

        $user = Auth::user();
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
            'qty_received'      => "required|integer|min:0|max:{$maxAllowed}",
            'remark'            => 'required|string'
        ]);

        $signaturePath = $user->signature_path;

        $remainingQty = $pr->qty_pr - ($alreadyReceived + $request->qty_received);

        $receiving->update([
            'status'            => 'checked',
            'qty_received'      => $request->qty_received,
            'qty_status'        => $remainingQty > 0 ? 'open' : 'closed',
            'checked_signature' => $signaturePath,
            'remark'            => $request->remark
        ]);

        return redirect()->route('eng.material.receiving.approve', $receiving->id)
            ->with('success', 'Material Received berhasil diperiksa (Checked) oleh Staff Engineering dan siap di-approve!');
    }

    /**
     * 9. PROSES APPROVAL AKHIR ADMIN (STATUS: CHECKED -> APPROVED)
     * FIX: Murni hanya mengubah entitas Material Received saja tanpa menyentuh tabel PR
     */
    public function approveEngineeringSpv($id, Request $request)
    {
        if ($response = $this->guardSignatureRole('admin', 'Admin')) {
            return $response;
        }

        $user = Auth::user();
        $request->validate([
            'notes'              => 'nullable|string'
        ]);

        $mr = MaterialReceived::findOrFail($id);

        if (strtolower($mr->status) !== 'checked') {
            return redirect()->back()->with('error', 'Dokumen harus melalui status Checked Staff terlebih dahulu!');
        }

        $signaturePath = $user->signature_path;
        $updatedRemark = $mr->remark . ($request->notes ? "\n[Admin Notes]: " . $request->notes : "");
        $totalReceived = MaterialReceived::where('purchase_request_id', $mr->purchase_request_id)
            ->whereIn('status', ['pending', 'checked', 'approved'])
            ->sum('qty_received');
        $remainingQty = $mr->purchaseRequest->qty_pr - $totalReceived;

        // Update record MR sesuai konfigurasi ENUM dari struktur database yang dikirim
        $mr->update([
            'status'             => 'approved', // Menjadi Approved sesuai enum data[cite: 2]
            'qty_status'         => $remainingQty > 0 ? 'open' : 'closed',
            'approved_signature' => $signaturePath,
            'remark'             => $updatedRemark
        ]);

        return redirect()->route('eng.material.receiving.history')
            ->with('success', 'Dokumen Material Received dinyatakan FULLY APPROVED dan sudah masuk ke History!');
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
     * Ensure the current department owns the workflow step and has a
     * signature configured in User Management.
     */
    private function guardSignatureRole(array|string $requiredRoles, string $roleLabel)
    {
        $user = Auth::user();
        $requiredRoles = (array) $requiredRoles;

        if (!$user || !in_array(strtolower((string) $user->role), $requiredRoles, true)) {
            return redirect()->back()->with(
                'error',
                "Tahap ini hanya dapat ditandatangani oleh user role {$roleLabel}."
            );
        }

        if (blank($user->signature_path)) {
            return redirect()->back()->with(
                'error',
                "Signature user {$roleLabel} belum diatur di User Management."
            );
        }

        return null;
    }

    private function materialReceivedLiveVersion(array $statuses): string
    {
        $latest = MaterialReceived::whereIn('status', $statuses)
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first(['id', 'updated_at']);

        return $latest
            ? $latest->id . ':' . ($latest->updated_at?->getTimestamp() ?? 0)
            : 'empty';
    }
}
