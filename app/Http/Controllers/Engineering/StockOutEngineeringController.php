<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\StockOutEng; 
use App\Models\StockEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockOutEngineeringController extends Controller
{
    public function index()
    {
        $history = StockOutEng::with(['stockEng.sparepart', 'dbBarcode', 'rak'])->latest()->paginate(10);
        return view('stock_eng.transaction.out', compact('history'));
    }

    public function scan()
    {
        $usedBarcodeIds = StockOutEng::whereNotNull('barcode_id')->pluck('barcode_id')->toArray();

        $stocks = StockEng::with(['sparepart', 'rak'])
            ->get()
            ->sortBy(function ($stock) {
                // Menyelaraskan sorting berdasarkan entitas sparepart_id asli (ex: 148)
                return $stock->sparepart->sparepart_id ?? $stock->sparepart->part_number ?? '';
            })
            ->values();

        $barcodes = \App\Models\DbBarcode::get()->map(function($item) use ($usedBarcodeIds) {
            return [
                'id'            => $item->id,
                'barcode_id'    => trim($item->barcode_id),    
                'final_content' => trim($item->final_content), 
                // Barcode dianggap tidak bisa dipakai jika sudah ada di log OUT atau lifecycle-nya sudah bukan AVAILABLE
                'is_used'       => in_array($item->id, $usedBarcodeIds) || $item->current_lifecycle !== 'AVAILABLE',
                'stock_eng_id'  => $item->stock_eng_id
            ];
        });

        $raks = \App\Models\Rak::orderBy('nama_rak', 'asc')->get();

        return view('stock_eng.transaction.out_scan', compact('stocks', 'barcodes', 'raks'));
    }

    /**
     * 🚀 METHOD STORE: Pemotongan Stok Akurat & Sinkron dengan Validasi Lifecycle Master
     */
    public function store(Request $request)
    {
        if (!$request->has('barcode_scan') || empty($request->barcode_scan)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Hasil scan barcode terdeteksi kosong.'
            ], 400);
        }

        // SANITASI BACKEND: Bersihkan space atau enter tak terlihat dari mesin scanner
        $scannedInput = trim($request->barcode_scan);
        $scannedInput = str_replace(["\r", "\n", "\t"], '', $scannedInput);

        DB::beginTransaction();
        try {
            // 1. Pencarian akurat menggunakan TRIM di SQL level
            $barcodeDb = \App\Models\DbBarcode::whereRaw('TRIM(final_content) = ?', [$scannedInput])
                ->orWhereRaw('TRIM(barcode_id) = ?', [$scannedInput])
                ->first();

            // Fallback: Pencarian parsial jika barcode mengandung link eksternal tambahan
            if (!$barcodeDb) {
                $barcodeDb = \App\Models\DbBarcode::where('final_content', 'LIKE', '%' . $scannedInput . '%')
                    ->orWhere('barcode_id', 'LIKE', '%' . $scannedInput . '%')
                    ->first();
            }

            if (!$barcodeDb) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Konten QR/Barcode "' . $scannedInput . '" tidak dikenali di database master.'
                ], 404);
            }

            // 2. Proteksi Siklus Hidup Barcode: Wajib AVAILABLE
            if ($barcodeDb->current_lifecycle !== 'AVAILABLE') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Barcode ' . $barcodeDb->barcode_id . ' tidak dapat digunakan karena statusnya sudah: ' . $barcodeDb->current_lifecycle
                ], 422);
            }

            // Proteksi Duplikat Log Transaksi
            $isBarcodeUsed = StockOutEng::where('barcode_id', $barcodeDb->id)->exists();
            if ($isBarcodeUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Barcode ' . $barcodeDb->barcode_id . ' sudah pernah tercatat keluar sebelumnya.'
                ], 422);
            }

            // 3. Tarik relasi data mapping stock_eng_id
            if (!$barcodeDb->stock_eng_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Barcode ini belum dipetakan ke target barang logistik mana pun (Nilai stock_eng_id NULL).'
                ], 422);
            }

            $stock = StockEng::with(['sparepart', 'rak'])->find($barcodeDb->stock_eng_id);
            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Data master stok engineering untuk item ini tidak ditemukan.'
                ], 404);
            }

            // Ambil kode Sparepart ID asli (Contoh: 148) untuk pesan notifikasi
            $sparepartCode = $stock->sparepart->sparepart_id ?? $stock->sparepart->part_number ?? 'Barang';

            // PROTEKSI RAK: Dilarang keluar jika lokasi belum didefinisikan
            if (empty($stock->rak_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Item [' . $sparepartCode . '] ditolak karena belum diletakkan di lokasi rak pada master data.'
                ], 422);
            }

            // 4. Validasi kuantitas kecukupan stok fisik di Rak terkait
            $qtyKeluar = (int) $request->input('qty_out', 1); 
            if ($stock->qty <= 0 || $stock->qty < $qtyKeluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Sisa stok untuk Sparepart ID ' . $sparepartCode . ' saat ini tidak mencukupi. (Sisa di Rak: ' . $stock->qty . ' Pcs)'
                ], 400);
            }

            // 5. Penanganan file upload foto jika ada
            $fotoPath = null;
            if ($request->hasFile('test_photo')) {
                $file = $request->file('test_photo');
                $fotoPath = $file->store('stock_outs', 'public');
            }

            // 6. Auto-generate Transaction ID khusus manual Scan Out
            $latestTx = StockOutEng::where('transaction_out_id', 'LIKE', 'ENGOUT%')->orderBy('id', 'desc')->first();
            $nextId = !$latestTx ? 'ENGOUT001' : 'ENGOUT' . str_pad(((int) filter_var($latestTx->transaction_out_id, FILTER_SANITIZE_NUMBER_INT)) + 1, 3, '0', STR_PAD_LEFT);

            $finalComment = $request->input('comment', 'Manual Scan Out via Camera');
            if ($fotoPath) {
                $finalComment .= ' | Photo: ' . $fotoPath;
            }

            // 7. Simpan log transaksi pengeluaran barang (Tabel Stock Out Eng)
            StockOutEng::create([
                'transaction_out_id'   => $nextId,
                'nik'                  => Auth::user()->nik ?? Auth::user()->nim ?? '9999',
                'barcode_id'           => $barcodeDb->id, 
                'stock_eng_id'         => $stock->id,
                'no_nozzle'            => $stock->no_nozzle ?? '-', 
                'rak_id'               => $stock->rak_id,      
                'qty_out'              => $qtyKeluar,
                'status'               => 'SUCCESS',
                'remark'               => 'SCAN OUT VIA CAMERA ENGINE',
                'comment'              => $finalComment,
            ]);
            
            // 8. KUNCI UTAMA: Kurangi nominal kuantitas stok di gudang utama engineering secara akurat
            $stock->decrement('qty', $qtyKeluar);

            // 9. SINKRONISASI BUKU KORAN MUTASI: Masukkan data ke tabel stock_eng_transactions
            $txUuid = 'TX-OUT-M*' . strtoupper(Str::random(4)) . '-' . time();
            DB::table('stock_eng_transactions')->insert([
                'tx_id'                 => $txUuid,
                'users_id'              => Auth::id(),
                'stock_engs_id'         => $stock->id,
                'db_barcodes_id'        => $barcodeDb->id,
                'production_request_id' => null, // Manual scan tidak terikat dokumen production request langsung
                'tx_type'               => 'out',
                'qty_transaction'       => $qtyKeluar,
                'process_type'          => 'scan', // 🚀 FIX: Diubah ke 'scan' agar sesuai dengan ENUM('scan', 'manual') database kamu
                'status'                => 'success',
                'remark'                => 'Manual scan out identification for Sparepart: ' . $sparepartCode,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // 10. Kunci status barcode di master data menjadi USED_OUT agar tidak bisa dipakai ulang
            DB::table('db_barcodes')->where('id', $barcodeDb->id)->update([
                'current_lifecycle' => 'USED_OUT',
                'updated_at'        => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi ' . $nextId . ' Berhasil! Stok untuk Sparepart ID [' . $sparepartCode . '] resmi berkurang ' . $qtyKeluar . ' Pcs.',
                'data'    => [
                    'part_name'  => $sparepartCode,
                    'barcode_id' => $barcodeDb->barcode_id,
                    'remaining'  => $stock->qty 
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kegagalan sistem server: ' . $e->getMessage()
            ], 500);
        }
    }
}