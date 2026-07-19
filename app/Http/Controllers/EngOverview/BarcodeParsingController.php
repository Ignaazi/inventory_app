<?php

namespace App\Http\Controllers\EngOverview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BarcodeParsingController extends Controller
{
    // Menampilkan halaman utama barcode builder
    public function index()
    {
        // 1. Ambil data request produksi untuk dropdown select di view
        $productionRequests = DB::table('production_requests')
                                ->orderBy('id', 'desc')
                                ->get();

        // 2. Ambil data stok engineering (Kolom no_nozzle sudah DIBUANG agar tidak error)
        $stockEngineering = DB::table('stock_engs')
                                ->join('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id')
                                ->join('raks', 'stock_engs.rak_id', '=', 'raks.id')
                                ->select(
                                    'stock_engs.id as stock_id',
                                    'spareparts.name as part_name',
                                    'raks.nama_rak as rak_name'
                                )
                                ->orderBy('spareparts.name', 'asc')
                                ->get();

        // Pastikan variabel stockEngineering ikut dilempar ke compact
        return view('eng_overview.barcode_parsing', compact('productionRequests', 'stockEngineering'));
    }

    // Menyimpan data terintegrasi ke db_barcodes, barcode_parsings, dan type_barcodes
    public function store(Request $request)
    {
        // Validasi input dasar dari request
        if (!$request->final_content || empty($request->final_content)) {
            return response()->json([
                'success' => false,
                'message' => 'Konten barcode tidak boleh kosong!'
            ], 400);
        }

        // 🚀 PROTEKSI ANTI-DUPLIKAT: Cek apakah string composite ini sudah pernah terdaftar sebelumnya
        $isDuplicate = DB::table('db_barcodes')
                         ->where('final_content', trim($request->final_content))
                         ->exists();

        if ($isDuplicate) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Barcode dengan kombinasi tulisan/konten "' . $request->final_content . '" sudah terdaftar di sistem dan tidak boleh double data!'
            ], 422); // HTTP Status 422 Unprocessable Entity
        }

        try {
            DB::beginTransaction();

            // 1. LOGIKA OTOMATIS GENERATE KODE UNIK (SIIXENG001, SIIXENG002, dst.)
            $latestBarcode = DB::table('db_barcodes')->orderBy('id', 'desc')->first();
            
            if (!$latestBarcode) {
                $nextBarcodeId = 'SIIXENG001';
            } else {
                $number = (int) substr($latestBarcode->barcode_id, 7);
                $nextBarcodeId = 'SIIXENG' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
            }

            // 2. AMBIL NIK/NIM BAWAAN DARI AKUN LOGIN SEKARANG
            $currentCreatorNim = Auth::user()->nik ?? Auth::user()->nim ?? '123456';

            // 3. Simpan ke Tabel db_barcodes 
            $barcodeDbId = DB::table('db_barcodes')->insertGetId([
                'barcode_id'        => $nextBarcodeId, 
                'barcode_type'      => $request->barcode_type,
                'barcode_size'      => $request->barcode_size,
                'final_content'     => trim($request->final_content),
                'stock_eng_id'      => $request->stock_eng_id, 
                'creator_nik'       => $currentCreatorNim, 
                'current_lifecycle' => 'USED_IN', 
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // 4. Hubungkan langsung ke tabel Barcode Parsing
            DB::table('barcode_parsings')->insert([
                'barcode_db_id'         => $barcodeDbId,
                'production_request_id' => $request->production_request_id, 
                'nik'                   => $currentCreatorNim, 
                'qty_parsed'            => 1,
                'description'           => 'Barcode generated and locked for production request ID: ' . $request->production_request_id,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // 5. Simpan setiap komponen di list struktur ke Halaman/Tabel Type Barcode
            if ($request->has('components') && is_array($request->components)) {
                foreach ($request->components as $component) {
                    DB::table('type_barcodes')->insert([
                        'char_type'       => $component['type'],
                        'char_length'     => $component['length'],
                        'char_value'      => $component['value'],
                        'components_json' => json_encode($request->components), 
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }

            DB::commit();
            
            return response()->json([
                'success'    => true,
                'barcode_id' => $nextBarcodeId, 
                'message'    => 'Barcode ' . $nextBarcodeId . ' berhasil disimpan dan dikunci untuk Request Produksi!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Gagal simpan ke DB: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mengambil riwayat konfigurasi struktur untuk fitur Import
    public function getConfigs()
    {
        $configs = DB::table('type_barcodes')
                     ->orderBy('id', 'desc')
                     ->get();

        return response()->json($configs);
    }
}