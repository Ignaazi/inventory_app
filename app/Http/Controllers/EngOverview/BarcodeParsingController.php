<?php

namespace App\Http\Controllers\EngOverview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BarcodeParsingController extends Controller
{
    /**
     * Menampilkan halaman utama barcode builder / parsing (Automated Version)
     */
    public function index()
    {
        // 1. Ambil data request produksi untuk dropdown select OUT (Hanya yang sudah Approved)
        $productionRequests = DB::table('production_requests')
                                ->where('status', 'Approved')
                                ->orderBy('id', 'desc')
                                ->get();

        // 2. Ambil data material received untuk dropdown select IN
        $materialReceived = DB::table('material_received')
                                ->orderBy('id', 'desc')
                                ->get();

        // 3. Data master stok engineering (Disinkronkan agar mempermudah filtering di JavaScript Frontend)
        $stockEngineering = DB::table('stock_engs')
                                ->join('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id')
                                ->join('raks', 'stock_engs.rak_id', '=', 'raks.id')
                                ->select(
                                    'stock_engs.id as stock_id',
                                    'stock_engs.sparepart_id as sparepart_id', // Helper ID untuk relasi filter UI
                                    'spareparts.sparepart_id as part_name', 
                                    'spareparts.part_number',              
                                    'spareparts.sap_code',                 
                                    'raks.nama_rak as rak_name'
                                )
                                ->orderBy('spareparts.sparepart_id', 'asc')
                                ->get();

        return view('eng_overview.barcode_parsing', compact('productionRequests', 'stockEngineering', 'materialReceived'));
    }

    /**
     * Menyimpan data secara Otomatis & Massal (Batching Engine) berdasarkan Dokumen Asal & Lokasi Rak
     */
    public function store(Request $request)
    {
        // Validasi payload terupdate: stock_eng_id sekarang WAJIB dikirim dari UI
        $request->validate([
            'mode'         => 'required|in:IN,OUT',
            'source_id'    => 'required|integer',
            'barcode_type' => 'required|string',
            'barcode_size' => 'required|string',
            'stock_eng_id' => 'required|integer', // Mengunci kepastian lokasi Rak asal/tujuan
        ]);

        try {
            DB::beginTransaction();

            $currentUserId = Auth::id();
            $mode = $request->mode;
            $sourceId = $request->source_id;
            $stockEngId = $request->stock_eng_id;

            // Validasi keandalan data kombinasi Rak & Sparepart di database
            $stockEngRecord = DB::table('stock_engs')->where('id', $stockEngId)->first();
            if (!$stockEngRecord) {
                return response()->json(['success' => false, 'message' => 'Lokasi Rak atau Data Stok Engineering tidak ditemukan!'], 404);
            }
            
            if ($mode === 'IN') {
                // ==========================================
                // LOGIKA OTOMASI MODE IN (Barang Masuk Gudang)
                // ==========================================
                
                // Ambil data dokumen Material Received
                $mrDoc = DB::table('material_received')->where('id', $sourceId)->first();
                if (!$mrDoc) {
                    return response()->json(['success' => false, 'message' => 'Dokumen Material Received tidak valid!'], 404);
                }

                $qty = (int) $mrDoc->qty_received;
                $dateStr = date('dmy'); // 6 Digit format tanggal ddmmyy

                // Ambil Counter Global Terakhir untuk prefix 'TXENGIN'
                $latestIn = DB::table('db_barcodes')
                                ->where('barcode_id', 'LIKE', 'TXENGIN%')
                                ->orderBy('id', 'desc')
                                ->first();
                                
                $globalCounter = 0;
                if ($latestIn) {
                    $globalCounter = (int) substr($latestIn->barcode_id, -5);
                }

                // Lakukan looping massal pembuatan nomor seri
                for ($i = 1; $i <= $qty; $i++) {
                    $globalCounter++;
                    $generatedBarcodeId = 'TXENGIN' . $dateStr . str_pad($globalCounter, 5, '0', STR_PAD_LEFT);

                    // 1. Masukkan ke database master barcode (Terkunci ke Rak Pilihan)
                    DB::table('db_barcodes')->insert([
                        'barcode_id'        => $generatedBarcodeId,
                        'users_id'          => $currentUserId,
                        'barcode_type'      => $request->barcode_type,
                        'barcode_size'      => $request->barcode_size,
                        'final_content'     => $generatedBarcodeId,
                        'stock_eng_id'      => $stockEngId, // Rak yang dipilih operator di UI
                        'current_lifecycle' => 'AVAILABLE', 
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    // 2. Buku Koran Mutasi Stok (Pemasukan)
                    $txUuid = 'TX-IN-' . strtoupper(Str::random(4)) . '-' . time();
                    DB::table('stock_eng_transactions')->insert([
                        'tx_id'           => $txUuid,
                        'users_id'        => $currentUserId,
                        'stock_engs_id'   => $stockEngId,
                        'tx_type'         => 'in',
                        'qty_transaction' => 1,
                        'process_type'    => 'system_batch',
                        'status'          => 'success',
                        'remark'          => 'Automated Batch IN from Doc MR: ' . ($mrDoc->no_mr ?? $sourceId),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }

                // 3. Update nominal saldo quantity aktual di tabel master stock_engs
                DB::table('stock_engs')->where('id', $stockEngId)->increment('qty', $qty);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Sukses memproses Batch IN! Berhasil mendaftarkan {$qty} barcode baru ke Rak pilihan Anda."
                ]);

            } else {
                // ==========================================
                // LOGIKA OTOMASI MODE OUT (Distribusi ke Lini)
                // ==========================================
                
                // Ambil data dokumen Production Request
                $prDoc = DB::table('production_requests')->where('id', $sourceId)->first();
                if (!$prDoc) {
                    return response()->json(['success' => false, 'message' => 'Dokumen Production Request tidak valid!'], 404);
                }

                // Validasi tambahan: Pastikan stok di rak tersebut cukup sebelum dikurangi
                $qty = (int) $prDoc->qty_req;
                if ($stockEngRecord->qty < $qty) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Stok di Rak ini tidak mencukupi! Sisa stok saat ini: {$stockEngRecord->qty} pcs, sedangkan permintaan: {$qty} pcs."
                    ], 400);
                }

                $lineRaw = $prDoc->no_line ?? '01';
                $lineStr = str_pad($lineRaw, 2, '0', STR_PAD_LEFT); 
                
                // Anti-Crash Properti: Cari kecocokan penamaan sparepart ID pada tabel production_requests
                $partId = $prDoc->sparepart_id ?? $prDoc->part_id ?? $prDoc->spareparts_id ?? $prDoc->id_sparepart ?? '00';

                // Cari historical counter terakhir berdasarkan prefix Line Lini Produksi
                $latestOutLine = DB::table('db_barcodes')
                                    ->where('barcode_id', 'LIKE', "TXENGLINE{$lineStr}%")
                                    ->orderBy('id', 'desc')
                                    ->first();

                $localCounter = 0;
                if ($latestOutLine) {
                    $localCounter = (int) substr($latestOutLine->barcode_id, -5);
                }

                // Eksekusi pembuatan batch barcode lini produksi secara sekuensial
                for ($i = 1; $i <= $qty; $i++) {
                    $localCounter++;
                    // Pola Barcode OUT: TXENGLINE + Lini (2 digit) + Code Sparepart + 5 Digit Counter Lokal
                    $generatedBarcodeId = "TXENGLINE{$lineStr}{$partId}" . str_pad($localCounter, 5, '0', STR_PAD_LEFT);

                    // 1. Registrasi ke master barcode
                    $barcodeOutId = DB::table('db_barcodes')->insertGetId([
                        'barcode_id'        => $generatedBarcodeId,
                        'users_id'          => $currentUserId,
                        'barcode_type'      => $request->barcode_type,
                        'barcode_size'      => $request->barcode_size,
                        'final_content'     => $generatedBarcodeId,
                        'stock_eng_id'      => $stockEngId, // Sumber rak pengambilan barang
                        'current_lifecycle' => 'USED_IN', 
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    // 2. Dokumentasikan relasi distribusi di tabel barcode_parsings
                    DB::table('barcode_parsings')->insert([
                        'users_id'              => $currentUserId,
                        'production_request_id' => $sourceId,
                        'barcode_in_id'         => null,
                        'barcode_out_id'        => $barcodeOutId,
                        'qty_parsed'            => 1,
                        'status'                => 'success',
                        'remark'                => 'Automated parsed output for Line ' . $lineStr,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);

                    // 3. Buku Koran Mutasi Stok (Pengeluaran)
                    $txUuid = 'TX-OUT-' . strtoupper(Str::random(4)) . '-' . time();
                    DB::table('stock_eng_transactions')->insert([
                        'tx_id'                 => $txUuid,
                        'users_id'              => $currentUserId,
                        'stock_engs_id'         => $stockEngId,
                        'db_barcodes_id'        => $barcodeOutId,
                        'production_request_id' => $sourceId,
                        'tx_type'               => 'out',
                        'qty_transaction'       => 1,
                        'process_type'          => 'system_batch',
                        'status'                => 'success',
                        'remark'                => 'Automated batch parsing deduction for Line: ' . $lineStr,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                }

                // 4. Kurangi nominal saldo quantity aktual di tabel master stock_engs
                DB::table('stock_engs')->where('id', $stockEngId)->decrement('qty', $qty);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Sukses memproses Batch OUT! Berhasil mendistribusikan {$qty} barcode khusus dari Rak asal ke Lini {$lineStr}."
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kegagalan sistem database internal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil riwayat konfigurasi struktur
     */
    public function getConfigs()
    {
        $configs = DB::table('type_barcodes')
                     ->orderBy('id', 'desc')
                     ->get();

        return response()->json($configs);
    }
}