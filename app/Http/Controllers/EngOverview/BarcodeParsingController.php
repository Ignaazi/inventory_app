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
        // 1. Ambil data request produksi dengan JOIN ke tabel spareparts
        $productionRequests = DB::table('production_requests')
                                ->leftJoin('spareparts', 'production_requests.sparepart_id', '=', 'spareparts.id')
                                ->where('production_requests.status', 'Approved')
                                ->select(
                                    'production_requests.*',
                                    'spareparts.sparepart_id as custom_sparepart_code',
                                    'spareparts.part_number',
                                    'spareparts.sap_code'
                                )
                                ->orderBy('production_requests.id', 'desc')
                                ->get()
                                ->map(function ($pr) {
                                    $pr->sparepart = (object) [
                                        'id'        => $pr->sparepart_id, 
                                        'part_no'   => $pr->custom_sparepart_code, 
                                        'part_name' => $pr->custom_sparepart_code
                                    ];
                                    return $pr;
                                });

        // 2. Ambil data material received untuk dropdown select IN
        $materialReceived = DB::table('material_received')
                                ->orderBy('id', 'desc')
                                ->get();

        // 3. Data master stok engineering
        $stockEngineering = DB::table('stock_engs')
                                ->join('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id')
                                ->join('raks', 'stock_engs.rak_id', '=', 'raks.id')
                                ->select(
                                    'stock_engs.id as stock_id',
                                    'stock_engs.sparepart_id as sparepart_id', 
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
        $request->validate([
            'mode'         => 'required|in:IN,OUT',
            'source_id'    => 'required|integer',
            'barcode_type' => 'required|string',
            'barcode_size' => 'required|string',
            'stock_eng_id' => 'required|integer', 
        ]);

        try {
            DB::beginTransaction();

            $currentUserId = Auth::id();
            $mode = $request->mode;
            $sourceId = $request->source_id;
            $stockEngId = $request->stock_eng_id;

            $stockEngRecord = DB::table('stock_engs')->where('id', $stockEngId)->first();
            if (!$stockEngRecord) {
                return response()->json(['success' => false, 'message' => 'Lokasi Rak atau Data Stok Engineering tidak ditemukan!'], 404);
            }
            
            if ($mode === 'IN') {
                // =======================================================
                // 📥 LOGIKA MODE IN: PURE PEMBUATAN BARCODE BARU (MATERIAL BARU)
                // =======================================================
                $mrDoc = DB::table('material_received')->where('id', $sourceId)->first();
                if (!$mrDoc) {
                    return response()->json(['success' => false, 'message' => 'Dokumen Material Received tidak valid!'], 404);
                }

                $qty = (int) $mrDoc->qty_received;
                $dateStr = date('dmy'); 

                $latestIn = DB::table('db_barcodes')
                                ->where('barcode_id', 'LIKE', 'TXENGIN%')
                                ->orderBy('id', 'desc')
                                ->first();
                                
                $globalCounter = 0;
                if ($latestIn) {
                    $globalCounter = (int) substr($latestIn->barcode_id, -5);
                }

                for ($i = 1; $i <= $qty; $i++) {
                    $globalCounter++;
                    $generatedBarcodeId = 'TXENGIN' . $dateStr . str_pad($globalCounter, 5, '0', STR_PAD_LEFT);

                    DB::table('db_barcodes')->insert([
                        'barcode_id'        => $generatedBarcodeId,
                        'users_id'          => $currentUserId,
                        'barcode_type'      => $request->barcode_type,
                        'barcode_size'      => $request->barcode_size,
                        'final_content'     => $generatedBarcodeId,
                        'stock_eng_id'      => $stockEngId, 
                        'current_lifecycle' => 'AVAILABLE', 
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    $txUuid = 'TX-IN-' . strtoupper(Str::random(4)) . '-' . time();
                    DB::table('stock_eng_transactions')->insert([
                        'tx_id'           => $txUuid,
                        'users_id'        => $currentUserId,
                        'stock_engs_id'   => $stockEngId,
                        'tx_type'         => 'in',
                        'qty_transaction' => 1,
                        'process_type'    => 'manual',
                        'status'          => 'success',
                        'remark'          => 'Automated Batch IN from Doc MR: ' . ($mrDoc->no_mr ?? $sourceId),
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }

                DB::table('stock_engs')->where('id', $stockEngId)->increment('qty', $qty);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Sukses memproses Batch IN! Berhasil mendaftarkan {$qty} barcode baru ke Rak pilihan."
                ]);

            } else {
                // =======================================================
                // 📤 LOGIKA MODE OUT: PURE PENERBITAN BARCODE OUT KE LINI
                // =======================================================
                $prDoc = DB::table('production_requests')->where('id', $sourceId)->first();
                if (!$prDoc) {
                    return response()->json(['success' => false, 'message' => 'Dokumen Production Request tidak valid!'], 404);
                }

                $qty = (int) $prDoc->qty_req;
                
                // Validasi kecukupan nominal angka stok di Rak master
                if ($stockEngRecord->qty < $qty) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Stok di Rak ini tidak mencukupi! Sisa stok saat ini: {$stockEngRecord->qty} pcs, sedangkan permintaan: {$qty} pcs."
                    ], 400);
                }

                $lineRaw = $prDoc->list_line_production_id ?? '01';
                $lineStr = str_pad($lineRaw, 2, '0', STR_PAD_LEFT); 
                
                // Ambil Nama Rak asli untuk format prefix barcode
                $rakRecord = DB::table('stock_engs')
                                ->join('raks', 'stock_engs.rak_id', '=', 'raks.id')
                                ->where('stock_engs.id', $stockEngId)
                                ->select('raks.nama_rak')
                                ->first();

                $rawRakName = $rakRecord ? $rakRecord->nama_rak : '00';
                $cleanRakName = trim(str_replace(['[', ']'], '', $rawRakName));
                $formattedRakCode = str_starts_with(strtoupper($cleanRakName), 'RAK') ? strtoupper($cleanRakName) : 'RAK' . str_pad($cleanRakName, 2, '0', STR_PAD_LEFT);

                // Ambil kode Sparepart ID asli (ex: 148)
                $sparepartRecord = DB::table('spareparts')->where('id', $prDoc->sparepart_id)->first();
                $partCode = '00';
                if ($sparepartRecord) {
                    $partCode = $sparepartRecord->sparepart_id ?? '00'; 
                }

                // Gabungkan susunan Prefix Barcode OUT khusus Lini
                $barcodePrefix = "TXENG{$formattedRakCode}LINE{$lineStr}{$partCode}";

                $latestOut = DB::table('db_barcodes')
                                    ->where('barcode_id', 'LIKE', "{$barcodePrefix}%")
                                    ->orderBy('id', 'desc')
                                    ->first();

                $localCounter = 0;
                if ($latestOut) {
                    $localCounter = (int) substr($latestOut->barcode_id, -4);
                }

                // Loop eksekusi: HANYA MEMBUAT DATA BARCODE OUT BARU
                for ($i = 0; $i < $qty; $i++) {
                    $localCounter++;
                    $generatedBarcodeId = $barcodePrefix . str_pad($localCounter, 4, '0', STR_PAD_LEFT);

                    // 1. Simpan Barcode OUT baru ke db_barcodes dengan status AVAILABLE (agar bisa dilacak/di-scan lini)
                    $barcodeOutId = DB::table('db_barcodes')->insertGetId([
                        'barcode_id'        => $generatedBarcodeId,
                        'users_id'          => $currentUserId,
                        'barcode_type'      => $request->barcode_type,
                        'barcode_size'      => $request->barcode_size,
                        'final_content'     => $generatedBarcodeId,
                        'stock_eng_id'      => $stockEngId, 
                        'current_lifecycle' => 'AVAILABLE', 
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    // 2. Dokumentasikan ke barcode_parsings (barcode_in_id dikosongkan/null karena pure cetak baru)
                    DB::table('barcode_parsings')->insert([
                        'users_id'              => $currentUserId,
                        'production_request_id' => $sourceId,
                        'barcode_in_id'         => null, 
                        'barcode_out_id'        => $barcodeOutId,
                        'qty_parsed'            => 1,
                        'status'                => 'success',
                        'remark'                => 'Automated pure barcode OUT generation for Line ' . $lineStr,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);

                    // 3. Catat ke Buku Koran Mutasi Stok
                    $txUuid = 'TX-OUT-' . strtoupper(Str::random(4)) . '-' . time();
                    DB::table('stock_eng_transactions')->insert([
                        'tx_id'                 => $txUuid,
                        'users_id'              => $currentUserId,
                        'stock_engs_id'         => $stockEngId,
                        'db_barcodes_id'        => $barcodeOutId,
                        'production_request_id' => $sourceId,
                        'tx_type'               => 'out',
                        'qty_transaction'       => 1,
                        'process_type'          => 'manual',
                        'status'                => 'success',
                        'remark'                => 'Automated batch OUT generation for Line: ' . $lineStr,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                }

                // Tetap kurangi saldo kuantitas aktual di master stok karena barang fisik keluar dari rak gudang
                DB::table('stock_engs')->where('id', $stockEngId)->decrement('qty', $qty);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Sukses memproses Batch OUT! Berhasil menerbitkan {$qty} barcode OUT baru untuk Lini {$lineStr}."
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