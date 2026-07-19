<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\StockOutEng; 
use App\Models\StockEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                return $stock->sparepart->name ?? '';
            })
            ->values();

        $barcodes = \App\Models\DbBarcode::get()->map(function($item) use ($usedBarcodeIds) {
            return [
                'id'            => $item->id,
                'barcode_id'    => trim($item->barcode_id),    
                'final_content' => trim($item->final_content), 
                'is_used'       => in_array($item->id, $usedBarcodeIds),
                'stock_eng_id'  => $item->stock_eng_id
            ];
        });

        $raks = \App\Models\Rak::orderBy('nama_rak', 'asc')->get();

        return view('stock_eng.transaction.out_scan', compact('stocks', 'barcodes', 'raks'));
    }

    /**
     * 🚀 METHOD STORE: Full Validasi Pintar Server-Side untuk Kamera & File Upload
     */
    public function store(Request $request)
    {
        if (!$request->has('barcode_scan') || empty($request->barcode_scan)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Hasil scan barcode terdeteksi kosong.'
            ], 400);
        }

        // SANITASI BACKEND: Hapus paksa space hantu, newlines, dan carriage returns dari SQL query
        $scannedInput = trim($request->barcode_scan);
        $scannedInput = str_replace(["\r", "\n", "\t"], '', $scannedInput);

        DB::beginTransaction();
        try {
            // 1. Pencarian akurat menggunakan TRIM di SQL level agar case-insensitive & space-immune
            $barcodeDb = \App\Models\DbBarcode::whereRaw('TRIM(final_content) = ?', [$scannedInput])
                ->orWhereRaw('TRIM(barcode_id) = ?', [$scannedInput])
                ->first();

            // Fallback: Jika QR Code berisi link/teks dinamis tambahan, cari yang mengandung nilai tersebut
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

            // 2. Proteksi Duplikat: Barcode yang sudah dipakai TIDAK BISA digunakan lagi
            $isBarcodeUsed = StockOutEng::where('barcode_id', $barcodeDb->id)->exists();
            if ($isBarcodeUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Barcode ' . $barcodeDb->barcode_id . ' (' . $barcodeDb->final_content . ') sudah pernah di-scan keluar sebelumnya.'
                ], 422);
            }

            // 3. Tarik relasi data mapping stock_eng_id
            if (!$barcodeDb->stock_eng_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Barcode ini belum dipetakan ke target barang logistik mana pun (Nilai stock_eng_id masih NULL di database).'
                ], 422);
            }

            $stock = StockEng::with(['sparepart', 'rak'])->find($barcodeDb->stock_eng_id);
            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Data master stok engineering untuk item ini tidak ditemukan.'
                ], 404);
            }

            // PROTEKSI RAK: Dilarang keluar jika belum pernah diletakkan di rak di master data
            if (empty($stock->rak_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Item [' . ($stock->sparepart->name ?? 'Barang') . '] ditolak karena belum diletakkan di lokasi rak mana pun pada master data.'
                ], 422);
            }

            // 4. Validasi kecukupan jumlah kuantitas stok
            $qtyKeluar = $request->input('qty_out', 1); 
            if ($stock->qty <= 0 || $stock->qty < $qtyKeluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Sisa stok untuk ' . ($stock->sparepart->name ?? 'Barang') . ' saat ini tidak mencukupi. (Sisa: ' . $stock->qty . ')'
                ], 400);
            }

            // 5. Penanganan file upload foto jika ada
            $fotoPath = null;
            if ($request->hasFile('test_photo')) {
                $file = $request->file('test_photo');
                $fotoPath = $file->store('stock_outs', 'public');
            }

            // 6. Auto generate Transaction ID
            $latestTx = StockOutEng::where('transaction_out_id', 'LIKE', 'ENGOUT%')->orderBy('id', 'desc')->first();
            $nextId = !$latestTx ? 'ENGOUT001' : 'ENGOUT' . str_pad(((int) filter_var($latestTx->transaction_out_id, FILTER_SANITIZE_NUMBER_INT)) + 1, 3, '0', STR_PAD_LEFT);

            $finalComment = $request->input('comment', 'Auto Scan Out via Camera');
            if ($fotoPath) {
                $finalComment .= ' | Photo: ' . $fotoPath;
            }

            // 7. Simpan log transaksi pengeluaran barang
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
            
            // 8. Kurangi kuantitas stok di gudang utama engineering
            $stock->decrement('qty', $qtyKeluar);

            // 9. Update lifecycle state di db_barcodes
            DB::table('db_barcodes')->where('id', $barcodeDb->id)->update([
                'current_lifecycle' => 'USED_OUT',
                'updated_at'        => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi ' . $nextId . ' Berhasil! Stok ' . ($stock->sparepart->name ?? '') . ' berkurang ' . $qtyKeluar . ' Pcs.',
                'data'    => [
                    'part_name'  => $stock->sparepart->name ?? 'Unknown Part',
                    'barcode_id' => $barcodeDb->barcode_id,
                    'remaining'  => $stock->qty 
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kegagalan system server: ' . $e->getMessage()
            ], 500);
        }
    }
}