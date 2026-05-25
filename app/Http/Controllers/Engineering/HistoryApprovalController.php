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
                return $query->where('request_no', 'LIKE', "%{$search}%")
                             ->orWhere('line_machine', 'LIKE', "%{$search}%")
                             ->orWhere('sparepart_name', 'LIKE', "%{$search}%");
            })
            ->orderBy('processed_at', 'desc')
            ->paginate(10);

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

            return redirect()->route('approval.history')
                             ->with('success', 'History approval berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('approval.history')
                             ->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}