<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ListSparepartEng;

class ReturnProdController extends Controller
{
    /**
     * Helper internal untuk mendeteksi Model Line secara dinamis
     */
    private function getLineModel()
    {
        $modelUtama = 'App\\Models\\ListLineProduction';
        $modelSubFolder = 'App\\Models\\Production\\ListLineProduction';
        return class_exists($modelUtama) ? $modelUtama : $modelSubFolder;
    }

    /**
     * Helper internal untuk mendapatkan nama tabel Line secara dinamis
     */
    private function getLineTableName()
    {
        $lineModel = $this->getLineModel();
        return (new $lineModel)->getTable();
    }

    /**
     * 1. Halaman Utama Riwayat Stock RETURN Production
     */
    public function stockReturn(Request $request)
    {
        $tableSparepart = class_exists(ListSparepartEng::class) 
            ? (new ListSparepartEng)->getTable() 
            : 'list_sparepart_engs';
            
        $tableLine = $this->getLineTableName();

        $perPage = $request->input('per_page', 10);
        $search  = $request->input('search');

        $query = DB::table('stock_prod_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('stock_prods as sp', 't.stock_prods_id', '=', 'sp.id')
            ->leftJoin($tableLine . ' as lp', 'sp.line_id', '=', 'lp.id') 
            ->leftJoin($tableSparepart . ' as se', 'sp.sparepart_id', '=', 'se.id') 
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->select([
                't.*',
                'u.nik as nik',
                'u.name as operator_name',
                DB::raw("REPLACE(UPPER(lp.no_line), 'LINI', 'LINE') as line_name"),
                'se.sparepart_id as item_code',
                'b.barcode_id as barcode_code',
                'b.current_lifecycle as barcode_lifecycle'
            ])
            ->where('t.tx_type', 'return');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('t.tx_id', 'LIKE', "%{$search}%")
                  ->orWhere('b.barcode_id', 'LIKE', "%{$search}%")
                  ->orWhere('u.nik', 'LIKE', "%{$search}%")
                  ->orWhere('u.name', 'LIKE', "%{$search}%")
                  ->orWhere('se.sparepart_id', 'LIKE', "%{$search}%");
            });
        }

        $history = $query->orderBy('t.created_at', 'desc')->paginate($perPage);

        return view('stock_prod.transactionProd.returnProd', compact('history'));
    }

    /**
     * 2. Halaman Terminal Scan Return (Mengarahkan ke returnProd_scan.blade.php + Mengirim $history)
     */
    public function scanReturn(Request $request)
    {
        $tableSparepart = class_exists(ListSparepartEng::class) 
            ? (new ListSparepartEng)->getTable() 
            : 'list_sparepart_engs';
            
        $tableLine = $this->getLineTableName();

        $perPage = $request->input('per_page', 10);
        $search  = $request->input('search');

        $query = DB::table('stock_prod_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('stock_prods as sp', 't.stock_prods_id', '=', 'sp.id')
            ->leftJoin($tableLine . ' as lp', 'sp.line_id', '=', 'lp.id') 
            ->leftJoin($tableSparepart . ' as se', 'sp.sparepart_id', '=', 'se.id') 
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->select([
                't.*',
                'u.nik as nik',
                'u.name as operator_name',
                DB::raw("REPLACE(UPPER(lp.no_line), 'LINI', 'LINE') as line_name"),
                'se.sparepart_id as item_code',
                'b.barcode_id as barcode_code',
                'b.current_lifecycle as barcode_lifecycle'
            ])
            ->where('t.tx_type', 'return');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('t.tx_id', 'LIKE', "%{$search}%")
                  ->orWhere('b.barcode_id', 'LIKE', "%{$search}%")
                  ->orWhere('u.nik', 'LIKE', "%{$search}%")
                  ->orWhere('u.name', 'LIKE', "%{$search}%")
                  ->orWhere('se.sparepart_id', 'LIKE', "%{$search}%");
            });
        }

        $history = $query->orderBy('t.created_at', 'desc')->paginate($perPage);

        return view('stock_prod.transactionProd.returnProd_scan', compact('history'));
    }

    /**
     * 3. CORE ENGINE: Memproses Eksekusi Scan RETURN via AJAX Gun Scanner/Kamera
     */
    public function storeScanReturn(Request $request)
    {
        $request->validate([
            'barcode_scan' => 'required|string',
            'process_type' => 'nullable|string|in:scan,manual',
            'remark'       => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $targetCode = trim($request->barcode_scan);
            $targetCode = str_replace(["\r", "\n", "\t"], '', $targetCode);

            // 1. Cari data barcode di database
            $barcodeDb = DB::table('db_barcodes')
                ->where(function ($query) use ($targetCode) {
                    $query->where('barcode_id', $targetCode)
                          ->orWhere('final_content', $targetCode);
                })
                ->lockForUpdate()
                ->first();

            if (!$barcodeDb) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal Return! Barcode "' . $targetCode . '" tidak terdaftar di database.'
                ], 422);
            }

            if ($barcodeDb->current_lifecycle !== 'USED_OUT') {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal Return! Barcode tidak berstatus USED_OUT '
                        . '(status saat ini: ' . $barcodeDb->current_lifecycle . ').'
                ], 422);
            }

            // 2. Ambil transaksi terakhir item ini di produksi
            $historyTx = DB::table('stock_prod_transactions')
                ->where('db_barcodes_id', $barcodeDb->id)
                ->orderBy('id', 'desc')
                ->first();

            if (!$historyTx) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal Return! Item barcode "' . $targetCode . '" belum pernah tercatat masuk/IN di lantai produksi.'
                ], 422);
            }

            // Validasi agar tidak di-return berulang kali jika transaksi terakhir sudah 'return'
            if (strtolower($historyTx->tx_type) === 'return') {
                return response()->json([
                    'success' => false,
                    'message' => 'Item barcode ini sudah berstatus RETURNED sebelumnya!'
                ], 422);
            }

            // 3. Format Kode Transaksi Counter: TXPRODRET + DDMMYY + 0001 s/d 9999
            $datePrefix = 'TXPRODRET' . date('dmy');
            $lastTrx = DB::table('stock_prod_transactions')
                ->where('tx_id', 'LIKE', $datePrefix . '%')
                ->orderBy('tx_id', 'desc')
                ->first();
            
            if ($lastTrx) {
                $lastNum = (int) substr($lastTrx->tx_id, -4);
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            $txId = $datePrefix . $nextNum;

            $processType = $request->input('process_type', 'scan');

            // 4. Catat Log Transaksi RETURN
            DB::table('stock_prod_transactions')->insert([
                'tx_id'                 => $txId,
                'users_id'              => Auth::id() ?? 1,
                'stock_prods_id'        => $historyTx->stock_prods_id,
                'stock_eng_tx_id'       => $historyTx->stock_eng_tx_id,
                'db_barcodes_id'        => $barcodeDb->id,
                'production_request_id' => $historyTx->production_request_id,
                'nik_karyawan'          => Auth::user()->nik ?? null,
                'tx_type'               => 'return',
                'qty_transaction'       => 1,
                'process_type'          => $processType,
                'status'                => 'success',
                'remark'                => $request->remark ?? ($request->comment ?? 'CORRECTION RETURN TO STOCK'),
                'created_at'            => now(),
                'updated_at'            => now()
            ]);

            // 5. KURANGI STOK PRODUKSI (Pengembalian/Koreksi akibat salah IN)
            if ($historyTx->stock_prods_id) {
                $currentStock = DB::table('stock_prods')->where('id', $historyTx->stock_prods_id)->value('qty');
                if ($currentStock > 0) {
                    DB::table('stock_prods')
                        ->where('id', $historyTx->stock_prods_id)
                        ->decrement('qty', 1);
                }
            }

            // 6. Update Lifecycle Barcode menjadi RETURNED
            DB::table('db_barcodes')
                ->where('id', $barcodeDb->id)
                ->update([
                    'current_lifecycle' => 'RETURNED',
                    'updated_at'        => now()
                ]);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sukses! Barcode ' . $targetCode . ' berhasil di-RETURN & stok produksi disesuaikan (-1).'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem internal: ' . $e->getMessage()
            ], 500);
        }
    }
}
