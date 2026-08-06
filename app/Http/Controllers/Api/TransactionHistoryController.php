<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionHistoryController extends Controller
{
    public function engineering(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:in,out,return,disposal',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $query = DB::table('stock_eng_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->leftJoin('stock_engs as s', 't.stock_engs_id', '=', 's.id')
            ->leftJoin('spareparts as sp', 's.sparepart_id', '=', 'sp.id')
            ->leftJoin('raks as r', 's.rak_id', '=', 'r.id')
            ->select([
                't.id',
                't.tx_id',
                't.tx_type',
                't.qty_transaction',
                't.process_type',
                't.status',
                't.remark',
                't.photo_path',
                't.created_at',
                'u.name as operator_name',
                'u.nik as operator_nik',
                'b.barcode_id',
                'b.current_lifecycle as barcode_lifecycle',
                'sp.sparepart_id as item_code',
                'sp.part_number',
                'r.nama_rak as rack_name',
            ])
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('t.tx_type', strtolower($request->input('type')));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                $query->where(function ($query) use ($search) {
                    $query->where('t.tx_id', 'like', "%{$search}%")
                        ->orWhere('b.barcode_id', 'like', "%{$search}%")
                        ->orWhere('sp.sparepart_id', 'like', "%{$search}%")
                        ->orWhere('u.name', 'like', "%{$search}%")
                        ->orWhere('u.nik', 'like', "%{$search}%");
                });
            })
            ->latest('t.created_at');

        return response()->json([
            'success' => true,
            'data' => $query->paginate((int) $request->input('per_page', 15)),
        ]);
    }

    public function production(Request $request)
    {
        $request->validate([
            'type' => 'nullable|in:in,out,return',
            'per_page' => 'nullable|integer|min:1|max:50',
        ]);

        $query = DB::table('stock_prod_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->leftJoin('stock_prods as s', 't.stock_prods_id', '=', 's.id')
            ->leftJoin('spareparts as sp', 's.sparepart_id', '=', 'sp.id')
            ->leftJoin('list_line_productions as l', 's.line_id', '=', 'l.id')
            ->select([
                't.id',
                't.tx_id',
                't.tx_type',
                't.out_category',
                't.nik_karyawan',
                't.qty_transaction',
                't.process_type',
                't.status',
                't.remark',
                't.photo_path',
                't.created_at',
                'u.name as operator_name',
                'u.nik as operator_nik',
                'b.barcode_id',
                'b.current_lifecycle as barcode_lifecycle',
                'sp.sparepart_id as item_code',
                'sp.part_number',
                'l.no_line as line_name',
            ])
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('t.tx_type', strtolower($request->input('type')));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                $query->where(function ($query) use ($search) {
                    $query->where('t.tx_id', 'like', "%{$search}%")
                        ->orWhere('b.barcode_id', 'like', "%{$search}%")
                        ->orWhere('sp.sparepart_id', 'like', "%{$search}%")
                        ->orWhere('u.name', 'like', "%{$search}%")
                        ->orWhere('u.nik', 'like', "%{$search}%");
                });
            })
            ->latest('t.created_at');

        return response()->json([
            'success' => true,
            'data' => $query->paginate((int) $request->input('per_page', 15)),
        ]);
    }
}
