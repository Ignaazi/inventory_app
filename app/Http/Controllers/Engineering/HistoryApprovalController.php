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
            // 💡 OPTIMASI: Eager loading relasi ke tabel produksi 
            // Agar pas panggil $log->productionRequest tidak kena error atau lambat
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
}