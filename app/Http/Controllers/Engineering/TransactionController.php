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
        $perPage = $request->input('per_page', 10);

        $query = DB::table('stock_eng_transactions')
            ->leftJoin('users', 'stock_eng_transactions.users_id', '=', 'users.id')
            ->leftJoin('stock_engs', 'stock_eng_transactions.stock_engs_id', '=', 'stock_engs.id')
            ->leftJoin('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id') // Join ke tabel spareparts riil
            ->leftJoin('raks', 'stock_engs.rak_id', '=', 'raks.id')                     // Join ke tabel raks
            ->leftJoin('db_barcodes', 'stock_eng_transactions.db_barcodes_id', '=', 'db_barcodes.id')
            ->leftJoin('production_requests', 'stock_eng_transactions.production_request_id', '=', 'production_requests.id')
            ->select([
                'stock_eng_transactions.*',
                'users.nik',                  // Langsung ambil NIK user
                'users.name',                 // Langsung ambil nama user
                'production_requests.request_no', // Langsung nomor request produksi
                'db_barcodes.barcode_id',     // Barcode ID riil
                'spareparts.sparepart_id',    // Sparepart ID riil (misal: 120, 148, 130, 766)
                'raks.nama_rak'               // Nama/Lokasi Rak riil
            ])
            ->where('stock_eng_transactions.tx_type', 'out');

        // Filter Status / Process Type
        if ($request->has('filter') && $request->filter != 'all') {
            $filter = strtolower($request->filter);
            if (in_array($filter, ['success', 'pending', 'failed'])) {
                $query->where('stock_eng_transactions.status', $filter);
            } elseif (in_array($filter, ['manual', 'scan'])) {
                $query->where('stock_eng_transactions.process_type', $filter);
            }
        }

        // Pencarian Global
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('stock_eng_transactions.tx_id', 'LIKE', "%{$search}%")
                  ->orWhere('stock_eng_transactions.remark', 'LIKE', "%{$search}%")
                  ->orWhere('users.nik', 'LIKE', "%{$search}%")
                  ->orWhere('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('db_barcodes.barcode_id', 'LIKE', "%{$search}%")
                  ->orWhere('production_requests.request_no', 'LIKE', "%{$search}%")
                  ->orWhere('spareparts.sparepart_id', 'LIKE', "%{$search}%")
                  ->orWhere('raks.nama_rak', 'LIKE', "%{$search}%");
            });
        }

        $history = $query->orderBy('stock_eng_transactions.created_at', 'desc')
                        ->paginate($perPage)
                        ->appends($request->all());

        return view('stock_eng.transaction.out', compact('history'));
    }

    /**
     * Menampilkan History Stock In
     */
    public function indexIn(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = DB::table('stock_eng_transactions')
            ->leftJoin('users', 'stock_eng_transactions.users_id', '=', 'users.id')
            ->leftJoin('stock_engs', 'stock_eng_transactions.stock_engs_id', '=', 'stock_engs.id')
            ->leftJoin('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id')
            ->leftJoin('raks', 'stock_engs.rak_id', '=', 'raks.id')
            ->leftJoin('db_barcodes', 'stock_eng_transactions.db_barcodes_id', '=', 'db_barcodes.id')
            ->select([
                'stock_eng_transactions.*',
                'users.nik',
                'users.name',
                'db_barcodes.barcode_id',
                'spareparts.sparepart_id',
                'raks.nama_rak'
            ])
            ->where('stock_eng_transactions.tx_type', 'in');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('stock_eng_transactions.tx_id', 'LIKE', "%{$search}%")
                  ->orWhere('stock_eng_transactions.remark', 'LIKE', "%{$search}%")
                  ->orWhere('users.nik', 'LIKE', "%{$search}%")
                  ->orWhere('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('db_barcodes.barcode_id', 'LIKE', "%{$search}%")
                  ->orWhere('spareparts.sparepart_id', 'LIKE', "%{$search}%")
                  ->orWhere('raks.nama_rak', 'LIKE', "%{$search}%");
            });
        }

        $history = $query->orderBy('stock_eng_transactions.created_at', 'desc')
                        ->paginate($perPage)
                        ->appends($request->all());

        return view('stock_eng.transaction.in', compact('history'));
    }
}