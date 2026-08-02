<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Menampilkan History Stock Out (Scan & Manual)
     */
    public function indexOut(Request $request)
    {
        $query = DB::table('stock_eng_transactions')
            ->leftJoin('users', 'stock_eng_transactions.users_id', '=', 'users.id')
            ->leftJoin('stock_engs', 'stock_eng_transactions.stock_engs_id', '=', 'stock_engs.id')
            ->leftJoin('db_barcodes', 'stock_eng_transactions.db_barcodes_id', '=', 'db_barcodes.id')
            ->leftJoin('production_requests', 'stock_eng_transactions.production_request_id', '=', 'production_requests.id')
            ->select([
                'stock_eng_transactions.*',
                'users.name as operator_name',
                'users.nik as operator_nik',
                'production_requests.request_no as production_req_no', 
                'db_barcodes.barcode_id', 
                'stock_engs.rak_id',
                'stock_engs.sparepart_id'
            ])
            ->where('stock_eng_transactions.tx_type', 'out');

        // Filter Tab Aktif
        if ($request->has('filter') && $request->filter != 'all') {
            $filter = strtolower($request->filter);
            if (in_array($filter, ['success', 'pending', 'failed'])) {
                $query->where('stock_eng_transactions.status', $filter);
            } elseif (in_array($filter, ['manual', 'scan'])) {
                $query->where('stock_eng_transactions.process_type', $filter);
            }
        }

        // Pencarian Global
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('stock_eng_transactions.tx_id', 'LIKE', "%$search%")
                  ->orWhere('stock_eng_transactions.remark', 'LIKE', "%$search%");
            });
        }

        $history = $query->orderBy('stock_eng_transactions.created_at', 'desc')->paginate(10);
        return view('stock_eng.transaction.out', compact('history'));
    }

    /**
     * Menampilkan History Stock In
     */
    public function indexIn(Request $request)
    {
        $query = DB::table('stock_eng_transactions')
            ->leftJoin('users', 'stock_eng_transactions.users_id', '=', 'users.id')
            ->leftJoin('stock_engs', 'stock_eng_transactions.stock_engs_id', '=', 'stock_engs.id')
            ->select([
                'stock_eng_transactions.*',
                'users.name as operator_name',
                'users.nik as operator_nik',
                'stock_engs.rak_id',
                'stock_engs.sparepart_id'
            ])
            ->where('stock_eng_transactions.tx_type', 'in');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('stock_eng_transactions.tx_id', 'LIKE', "%$search%")
                  ->orWhere('stock_eng_transactions.remark', 'LIKE', "%$search%");
            });
        }

        $history = $query->orderBy('stock_eng_transactions.created_at', 'desc')->paginate(10);
        return view('stock_eng.transaction.in', compact('history'));
    }
}