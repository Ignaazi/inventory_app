<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Engineering\StockEngTransaction; // Sesuaikan dengan nama model transaksi kamu

class TransactionInController extends Controller
{
    /**
     * Menampilkan tabel riwayat transaksi Stock IN
     */
    public function index(Request $request)
    {
        $query = StockEngTransaction::with(['stockEng.sparepart', 'stockEng.rak'])
            ->where('tx_type', 'in') // Hanya ambil transaksi tipe IN
            ->latest();

        // Fitur Search (NIK, Sparepart ID, Part No, SAP Code, Rak)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'LIKE', "%{$search}%")
                  ->orWhere('remark', 'LIKE', "%{$search}%")
                  ->orWhere('process_type', 'LIKE', "%{$search}%")
                  ->orWhereHas('stockEng.sparepart', function($sp) use ($search) {
                      $sp->where('sparepart_id', 'LIKE', "%{$search}%")
                         ->orWhere('part_number', 'LIKE', "%{$search}%")
                         ->orWhere('sap_code', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('stockEng.rak', function($rk) use ($search) {
                      $rk->where('nama_rak', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Fitur Filter Status / Process Type
        if ($request->has('filter') && $request->filter != '') {
            $filter = strtolower($request->filter);
            if (in_array($filter, ['success', 'pending', 'failed'])) {
                $query->where('status', $filter);
            } elseif ($filter === 'manual in') {
                $query->where('process_type', 'manual');
            } elseif ($filter === 'scan in') {
                $query->where('process_type', 'scan');
            }
        }

        $history = $query->paginate(10)->withQueryString();

        // Mengarah ke file blade history kamu
        return view('stock_eng.transactionEng.in', compact('history'));
    }
}