<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ListSparepartEng;

class OutProdController extends Controller
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
     * 1. Halaman Utama Riwayat Stock OUT Production (Lengkap Join User, Barcode, & Line)
     */
    public function stockOut()
    {
        $tableSparepart = class_exists(ListSparepartEng::class) 
            ? (new ListSparepartEng)->getTable() 
            : 'list_sparepart_engs';
            
        $tableLine = $this->getLineTableName();

        $history = DB::table('stock_prod_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('stock_prods as sp', 't.stock_prods_id', '=', 'sp.id')
            ->leftJoin($tableLine . ' as lp', 'sp.line_id', '=', 'lp.id') 
            ->leftJoin($tableSparepart . ' as se', 'sp.sparepart_id', '=', 'se.id') 
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->select([
                't.*',
                'u.nik as nik',
                'u.name as operator_name',
                DB::raw("REPLACE(UPPER(lp.no_line), 'LINI', 'LINE') as line_name"), // Mengubah LINI menjadi LINE01/LINE XX
                'se.sparepart_id as item_code',
                'b.barcode_id as barcode_code'
            ])
            ->where('t.tx_type', 'out')
            ->orderBy('t.created_at', 'desc')
            ->paginate(10);

        return view('stock_prod.transactionProd.outProd', compact('history'));
    }

    /**
     * 2. Menampilkan Halaman Terminal Live Scan QR-Code / Barcode OUT
     */
    public function scanOut()
    {
        return view('stock_prod.transactionProd.scan_out');
    }

    /**
     * 3. CORE ENGINE: Memproses Eksekusi Scan OUT via AJAX Gun Scanner
     */
    public function storeScanOut(Request $request)
    {
        // Validasi parameter request scanner
        $request->validate([
            'barcode_scan' => 'required|string',
            'process_type' => 'nullable|string|in:scan,manual',
            'out_category' => 'nullable|string|in:broken,lost,other', 
            'remark'       => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            // Bersihkan string spasi atau baris baru hasil ketikan gun scanner
            $targetCode = trim($request->barcode_scan);
            $targetCode = str_replace(["\r", "\n", "\t"], '', $targetCode);

            // 🔍 SINKRONISASI PENCATATAN BALIK: Melacak history IN berdasarkan barcode_id atau final_content
            $historyIn = DB::table('stock_prod_transactions')
                ->leftJoin('db_barcodes', 'stock_prod_transactions.db_barcodes_id', '=', 'db_barcodes.id')
                ->leftJoin('stock_eng_transactions', 'stock_prod_transactions.stock_eng_tx_id', '=', 'stock_eng_transactions.id')
                ->where('stock_prod_transactions.tx_type', 'in')
                ->where(function($query) use ($targetCode) {
                    $query->where('db_barcodes.barcode_id', $targetCode)
                          ->orWhere('db_barcodes.final_content', $targetCode)
                          ->orWhere('stock_eng_transactions.tx_id', $targetCode); // Fallback tracking
                })
                ->select([
                    'stock_prod_transactions.stock_prods_id',
                    'stock_prod_transactions.db_barcodes_id',
                    'stock_prod_transactions.production_request_id',
                    'stock_prod_transactions.stock_eng_tx_id',
                    'db_barcodes.current_lifecycle as barcode_lifecycle'
                ])
                ->orderBy('stock_prod_transactions.id', 'desc')
                ->lockForUpdate()
                ->first();

            // Proteksi 1: Jika barcode tidak terdaftar di riwayat transaksi masuk lantai produksi
            if (!$historyIn) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal Scan! Kode QR/Barcode (' . $targetCode . ') tidak ditemukan atau belum pernah di-Stock IN ke produksi.'
                ], 422);
            }

            // Production OUT hanya boleh dilakukan setelah Production IN dan sebelum barcode di-return.
            if ($historyIn->db_barcodes_id && $historyIn->barcode_lifecycle !== 'USED_IN') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal Scan! Barcode tidak berstatus USED_IN (status saat ini: '
                        . ($historyIn->barcode_lifecycle ?? '-') . '). Barcode sudah dipakai atau sudah di-return.'
                ], 422);
            }

            // Proteksi 2: Cek sisa stock fisik barang tersebut di tabel live stock produksi (stock_prods)
            $liveStock = DB::table('stock_prods')
                ->where('id', $historyIn->stock_prods_id)
                ->where('qty', '>', 0)
                ->first();

            if (!$liveStock) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal Scan! Item terdeteksi, namun sisa live stock untuk komponen ini di lini terkait sudah habis (0).'
                ], 422);
            }

            // 🌟 Penomoran Otomatis Kode Transaksi Baru (Format: TXPRODOUT + DDMMYY + 0001 s/d 9999)
            $datePrefix = 'TXPRODOUT' . date('dmy');
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

            // Jembatan Penyelamat ENUM: Jika dropdown memilih 'other', amankan menjadi NULL
            $outCatInput = $request->input('out_category');
            $finalCategory = in_array($outCatInput, ['broken', 'lost']) ? $outCatInput : null;
            $processType   = $request->input('process_type', 'scan');

            // 1. TULIS LOG TRANSAKSI BARU (stock_prod_transactions)
            DB::table('stock_prod_transactions')->insert([
                'tx_id'                 => $txId,
                'users_id'              => Auth::id() ?? 1,
                'stock_prods_id'        => $liveStock->id,
                'stock_eng_tx_id'       => $historyIn->stock_eng_tx_id,
                'db_barcodes_id'        => $historyIn->db_barcodes_id,
                'production_request_id' => $historyIn->production_request_id,
                'nik_karyawan'          => Auth::user()->nik ?? null,
                'tx_type'               => 'out',
                'out_category'          => $finalCategory,
                'qty_transaction'       => 1, // Pengurangan 1 unit per scan
                'process_type'          => $processType,
                'status'                => 'success',
                'remark'                => $request->remark ?? 'AUTOMATED OUT',
                'created_at'            => now(),
                'updated_at'            => now()
            ]);

            // 2. POTONG LIVE STOK UTAMA DI LANTAI PRODUKSI (stock_prods)
            DB::table('stock_prods')
                ->where('id', $liveStock->id)
                ->decrement('qty', 1);

            // 3. UPDATE LOG SIKLUS HIDUP BARCODE
            if ($historyIn->db_barcodes_id) {
                DB::table('db_barcodes')
                    ->where('id', $historyIn->db_barcodes_id)
                    ->update([
                        'current_lifecycle' => 'USED_OUT',
                        'updated_at'        => now()
                    ]);
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sukses! Barcode ' . $targetCode . ' berhasil di-Scan OUT dan memotong stok lini produksi.'
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
