<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\HistoryApproval;
use Illuminate\Http\Request;

class HistoryApprovalController extends Controller
{
    public function index(Request $request)
    {
        // Fitur pencarian berdasarkan nomor request atau line sesuai komponen UI lo
        $search = $request->input('search');

        $history = HistoryApproval::query()
            ->when($search, function($query, $search) {
                return $query->where('request_no', 'LIKE', "%{$search}%")
                             ->orWhere('line_machine', 'LIKE', "%{$search}%")
                             ->orWhere('sparepart_name', 'LIKE', "%{$search}%");
            })
            ->orderBy('processed_at', 'desc')
            ->paginate(10); // Mengikuti sistem pagination 10 records per page dari gambar lo

        return view('stock_eng.process_req.historyApproval', compact('history', 'search'));
    }
}