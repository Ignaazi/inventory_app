<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\HistoryApproval;
use Illuminate\Http\Request;

class HistoryApprovalController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Eager loading tetep dipakai biar part_number & sap_code ke-load otomatis
        // Data lama yang production_request_id-nya NULL dijamin TETEP MUNCUL aman
        $history = HistoryApproval::query()
            ->with(['productionRequest.user', 'productionRequest.sparepart', 'productionRequest.lineProduction'])
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    // Search langsung ke kolom lokal tabel history_approvals
                    $q->where('request_no', 'LIKE', "%{$search}%")
                      ->orWhere('line_machine', 'LIKE', "%{$search}%")
                      ->orWhere('sparepart_id', 'LIKE', "%{$search}%") // FIX: Pakai sparepart_id sesuai entitas baru
                      ->orWhere('nik', 'LIKE', "%{$search}%")
                      ->orWhere('approver_name', 'LIKE', "%{$search}%")
                      ->orWhere('status', 'LIKE', "%{$search}%")
                      ->orWhere('remark', 'LIKE', "%{$search}%")
                      
                      // Tambahan: Biar bisa search menembus tabel spareparts lewat relasi
                      ->orWhereHas('productionRequest.sparepart', function($spQuery) use ($search) {
                          $spQuery->where('part_number', 'LIKE', "%{$search}%")
                                  ->orWhere('sap_code', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->orderBy('processed_at', 'desc') // Konsisten urut berdasarkan processed_at dari DB lo
            ->paginate(25) 
            ->withQueryString(); 

        return view('stock_eng.process_req.historyApproval', compact('history', 'search'));
    }

    /**
     * Menampilkan halaman preview detail request dari history log
     */
    public function preview($id)
    {
        // Ambil data history log beserta seluruh relasi data production request utamanya
        $log = HistoryApproval::with([
            'productionRequest.user', 
            'productionRequest.sparepart', 
            'productionRequest.lineProduction'
        ])->findOrFail($id);

        // Diarahkan ke folder: views/stock_eng/process_req/previewApprovalReqProd.blade.php
        return view('stock_eng.process_req.previewApprovalReqProd', compact('log'));
    }

    /**
     * Menghapus record history approval
     */
    public function destroy($id)
    {
        try {
            $log = HistoryApproval::findOrFail($id);
            $log->delete();

            return redirect()->back()->with('success', 'History approval berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}