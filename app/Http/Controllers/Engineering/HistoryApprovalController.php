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

        $history = HistoryApproval::query()
            ->with('productionRequest') 
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('request_no', 'LIKE', "%{$search}%")
                      ->orWhere('line_machine', 'LIKE', "%{$search}%")
                      ->orWhere('sparepart_name', 'LIKE', "%{$search}%")
                      ->orWhere('nik', 'LIKE', "%{$search}%")
                      ->orWhere('approver_name', 'LIKE', "%{$search}%");
                });
            })
            ->orderBy('processed_at', 'desc')
            ->paginate(15) 
            ->withQueryString(); 

        return view('stock_eng.process_req.historyApproval', compact('history', 'search'));
    }

    /**
     * Menghapus record history approval
     */
    public function destroy($id)
    {
        try {
            $log = HistoryApproval::findOrFail($id);
            $log->delete();

            // Disesuaikan dengan nama route 'eng.approval.history.destroy' di blade lo
            return redirect()->back()
                             ->with('success', 'History approval berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                             ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}