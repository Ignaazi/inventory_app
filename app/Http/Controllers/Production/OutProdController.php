<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ListSparepartEng;
use App\Models\Production\stock_prod;

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
                'u.nik as operator_nik',
                't.nik_karyawan as employee_nik',
                'u.name as operator_name',
                DB::raw("REPLACE(UPPER(lp.no_line), 'LINI', 'LINE') as line_name"), // Mengubah LINI menjadi LINE01/LINE XX
                'se.sparepart_id as item_code',
                'b.barcode_id as barcode_code',
                'b.current_lifecycle as barcode_lifecycle'
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
     * Form manual OUT untuk barang hilang tanpa barcode.
     */
    public function manualOut()
    {
        $activeStocks = stock_prod::with(['line', 'sparepart'])
            ->where('qty', '>', 0)
            ->whereIn('sparepart_id', DB::table('stock_engs')->select('sparepart_id'))
            ->orderBy('line_id')
            ->get()
            ->map(function ($stock) {
                $stock->line_name = $stock->line->no_line ?? $stock->line_id;
                $stock->item_code = $stock->sparepart->sparepart_id ?? $stock->sparepart_id;
                $stock->part_number = $stock->sparepart->part_number ?? '-';
                $stock->sap_code = $stock->sparepart->sap_code ?? '-';

                return $stock;
            });

        return view('stock_prod.transactionProd.outProdManual', compact('activeStocks'));
    }

    /**
     * Manual OUT LOST: tidak membutuhkan barcode dan selalu mengurangi satu unit.
     */
    public function storeManualOut(Request $request)
    {
        $request->validate([
            'stock_prods_id' => 'required|integer|exists:stock_prods,id',
            'nik_karyawan'   => 'required|string|max:50',
            'remark'         => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $stock = stock_prod::with(['line', 'sparepart'])
                ->lockForUpdate()
                ->find($request->stock_prods_id);

            if (!$stock) {
                throw new \RuntimeException('Stock produksi tidak ditemukan.');
            }

            if ($stock->qty < 1) {
                throw new \RuntimeException('Stok produksi untuk item tersebut sudah habis.');
            }

            // Disposal memakai master Engineering sebagai referensi item, tetapi
            // tidak mengurangi saldo Engineering karena barang hilang di produksi.
            $engineeringStock = DB::table('stock_engs')
                ->where('sparepart_id', $stock->sparepart_id)
                ->orderBy('id')
                ->first();

            if (!$engineeringStock) {
                throw new \RuntimeException('Master stok Engineering untuk sparepart ini tidak ditemukan.');
            }

            $txId = $this->generateProductionOutTxId();
            $disposalTxId = $this->generateDisposalTxId();
            $nikKaryawan = trim($request->input('nik_karyawan'));
            $itemCode = $stock->sparepart->sparepart_id ?? $stock->sparepart_id;
            $lineName = $stock->line->no_line ?? $stock->line_id;
            $remark = 'AUTOMATED LOST | NIK KARYAWAN YANG MENGHILANGKAN: ' . $nikKaryawan;

            if ($request->filled('remark')) {
                $remark .= ' | ' . trim($request->input('remark'));
            }

            $stock->decrement('qty', 1);

            DB::table('stock_prod_transactions')->insert([
                'tx_id'                 => $txId,
                'users_id'              => Auth::id() ?? 1,
                'stock_prods_id'        => $stock->id,
                'stock_eng_tx_id'       => null,
                'db_barcodes_id'        => null,
                'production_request_id' => null,
                'nik_karyawan'          => $nikKaryawan,
                'tx_type'               => 'out',
                'out_category'          => 'lost',
                'qty_transaction'       => 1,
                'process_type'          => 'manual',
                'photo_path'            => null,
                'status'                => 'success',
                'remark'                => $remark,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            DB::table('stock_eng_transactions')->insert([
                'tx_id'                 => $disposalTxId,
                'users_id'              => Auth::id() ?? 1,
                'stock_engs_id'         => $engineeringStock->id,
                'db_barcodes_id'        => null,
                'tx_type'               => 'disposal',
                'qty_transaction'       => 1,
                'process_type'          => 'manual',
                'photo_path'            => null,
                'status'                => 'success',
                'remark'                => $remark
                    . ' | SOURCE PRODUCTION MANUAL OUT: ' . $txId
                    . ' | LINE: ' . $lineName
                    . ' | SPAREPART: ' . $itemCode,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            DB::commit();

            return redirect()->route('prod.transaction.out')
                ->with('success', 'Manual LOST berhasil dicatat. Stok ' . $itemCode . ' di Line ' . $lineName . ' berkurang 1.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan Manual LOST: ' . $e->getMessage());
        }
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
            'remark'       => 'nullable|string',
            'nik_karyawan' => 'nullable|string|max:50',
            'photo_path'   => 'nullable|image|max:5120',
        ]);

        DB::beginTransaction();
        try {
            $reject = static function (string $message, int $status = 422) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $status);
            };

            // Bersihkan string spasi atau baris baru hasil ketikan gun scanner
            $targetCode = trim($request->barcode_scan);
            $targetCode = str_replace(["\r", "\n", "\t"], '', $targetCode);

            // 🔍 SINKRONISASI PENCATATAN BALIK: Melacak history IN berdasarkan barcode_id atau final_content
            $historyIn = DB::table('stock_prod_transactions')
                ->leftJoin('db_barcodes', 'stock_prod_transactions.db_barcodes_id', '=', 'db_barcodes.id')
                ->leftJoin('stock_eng_transactions', 'stock_prod_transactions.stock_eng_tx_id', '=', 'stock_eng_transactions.id')
                ->where('stock_prod_transactions.tx_type', 'in')
                ->where('stock_prod_transactions.status', 'success')
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
                return $reject('Gagal Scan! Kode QR/Barcode (' . $targetCode . ') tidak ditemukan atau belum pernah di-Stock IN ke produksi.');
            }

            // Kunci master barcode juga, supaya dua scanner tidak memproses barcode yang sama.
            $barcodeData = $historyIn->db_barcodes_id
                ? DB::table('db_barcodes')->where('id', $historyIn->db_barcodes_id)->lockForUpdate()->first()
                : null;

            // Production OUT hanya boleh dilakukan setelah Production IN dan sebelum barcode di-return.
            if ($barcodeData && $barcodeData->current_lifecycle !== 'USED_IN') {
                return $reject('Gagal Scan! Barcode tidak berstatus USED_IN (status saat ini: '
                    . ($barcodeData->current_lifecycle ?? '-') . '). Barcode sudah dipakai atau sudah di-return.');
            }

            // Pastikan IN adalah transaksi produksi terakhir untuk barcode ini.
            $latestProductionTx = $historyIn->db_barcodes_id
                ? DB::table('stock_prod_transactions')
                    ->where('db_barcodes_id', $historyIn->db_barcodes_id)
                    ->where('status', 'success')
                    ->latest('id')
                    ->first()
                : null;

            if (!$latestProductionTx || $latestProductionTx->tx_type !== 'in') {
                return $reject('Gagal Scan! Barcode belum berada pada siklus Production IN aktif.');
            }

            // Proteksi 2: Cek sisa stock fisik barang tersebut di tabel live stock produksi (stock_prods)
            $liveStock = DB::table('stock_prods')
                ->where('id', $historyIn->stock_prods_id)
                ->where('qty', '>', 0)
                ->lockForUpdate()
                ->first();

            if (!$liveStock) {
                return $reject('Gagal Scan! Item terdeteksi, namun sisa live stock untuk komponen ini di lini terkait sudah habis (0).');
            }

            $outCatInput = $request->input('out_category');
            $finalCategory = in_array($outCatInput, ['broken', 'lost'], true) ? $outCatInput : null;
            $nikKaryawan = trim((string) $request->input('nik_karyawan', ''));

            if ($finalCategory === 'lost' && $nikKaryawan === '') {
                return $reject('NIK karyawan wajib diisi untuk kategori LOST.');
            }

            $fotoPath = null;
            if ($request->hasFile('photo_path')) {
                $fotoPath = $request->file('photo_path')->store('production_outs', 'public');
            } elseif ($request->hasFile('test_photo')) {
                $fotoPath = $request->file('test_photo')->store('production_outs', 'public');
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

            $processType   = $request->input('process_type', 'scan');
            $remark = trim((string) ($request->input('remark') ?? 'AUTOMATED OUT'));
            if ($finalCategory === 'lost') {
                $remark .= ' | NIK KARYAWAN YANG MENGHILANGKAN: ' . $nikKaryawan;
            }

            // 1. TULIS LOG TRANSAKSI BARU (stock_prod_transactions)
            DB::table('stock_prod_transactions')->insert([
                'tx_id'                 => $txId,
                'users_id'              => Auth::id() ?? 1,
                'stock_prods_id'        => $liveStock->id,
                'stock_eng_tx_id'       => $historyIn->stock_eng_tx_id,
                'db_barcodes_id'        => $historyIn->db_barcodes_id,
                'production_request_id' => $historyIn->production_request_id,
                'nik_karyawan'          => $nikKaryawan !== '' ? $nikKaryawan : null,
                'tx_type'               => 'out',
                'out_category'          => $finalCategory,
                'qty_transaction'       => 1, // Pengurangan 1 unit per scan
                'process_type'          => $processType,
                'photo_path'            => $fotoPath,
                'status'                => 'success',
                'remark'                => $remark,
                'created_at'            => now(),
                'updated_at'            => now()
            ]);

            // 2. POTONG LIVE STOK UTAMA DI LANTAI PRODUKSI (stock_prods)
            $affectedRows = DB::table('stock_prods')
                ->where('id', $liveStock->id)
                ->where('qty', '>', 0)
                ->decrement('qty', 1);

            if ($affectedRows !== 1) {
                return $reject('Gagal Scan! Stok line sudah habis saat transaksi diproses.');
            }

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
                'message' => 'Sukses! Barcode ' . $targetCode . ' berhasil di-Scan OUT. Stok line berkurang 1 dan '
                    . 'barcode siap diproses Engineering Disposal.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem internal: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateProductionOutTxId(): string
    {
        $datePrefix = 'TXPRODOUT' . date('dmy');
        $lastTrx = DB::table('stock_prod_transactions')
            ->where('tx_id', 'LIKE', $datePrefix . '%')
            ->orderBy('tx_id', 'desc')
            ->first();

        $nextNumber = $lastTrx ? ((int) substr($lastTrx->tx_id, -4)) + 1 : 1;

        return $datePrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    private function generateDisposalTxId(): string
    {
        $datePrefix = 'TXENGDIS' . date('dmy');
        $lastTrx = DB::table('stock_eng_transactions')
            ->where('tx_id', 'LIKE', $datePrefix . '%')
            ->orderBy('tx_id', 'desc')
            ->first();

        $nextNumber = $lastTrx ? ((int) substr($lastTrx->tx_id, -3)) + 1 : 1;

        return $datePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
