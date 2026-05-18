<?php

namespace App\Http\Controllers\EngOverview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarcodeParsingController extends Controller
{
    // Menampilkan halaman utama barcode builder
    public function index()
    {
        return view('eng_overview.barcode_parsing');
    }

    // Menyimpan data terintegrasi ke db_barcodes dan type_barcodes
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // 🚀 1. LOGIKA OTOMATIS GENERATE KODE UNIK (SIIXENG001, SIIXENG002, dst.)
            // Mengambil data terakhir dari tabel db_barcodes
            $latestBarcode = DB::table('db_barcodes')->orderBy('id', 'desc')->first();
            
            if (!$latestBarcode) {
                $nextBarcodeId = 'SIIXENG001';
            } else {
                // Mengambil angka di belakang string 'SIIXENG' (Mulai indeks ke-7)
                $number = (int) substr($latestBarcode->barcode_id, 7);
                // Menambahkan +1 dan memformatnya kembali jadi 3 digit (002, 003, dst.)
                $nextBarcodeId = 'SIIXENG' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
            }

            // 🚀 2. Simpan ke Halaman/Tabel DB Barcode (Sekarang sudah bawa barcode_id & current_lifecycle)
            DB::table('db_barcodes')->insert([
                'barcode_id' => $nextBarcodeId, // Isinya: SIIXENG001
                'barcode_type' => $request->barcode_type,
                'barcode_size' => $request->barcode_size,
                'final_content' => $request->final_content,
                'current_lifecycle' => 'AVAILABLE', // Status default awal siap discan out
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Simpan setiap komponen di list struktur ke Halaman/Tabel Type Barcode
            if ($request->has('components') && is_array($request->components)) {
                foreach ($request->components as $component) {
                    DB::table('type_barcodes')->insert([
                        'char_type' => $component['type'],
                        'char_length' => $component['length'],
                        'char_value' => $component['value'],
                        'components_json' => json_encode($request->components), // simpan full format untuk di-import ulang nanti
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            // Kembalikan status sukses true murni ke JavaScript beserta nama kodenya
            return response()->json([
                'success' => true,
                'message' => 'Barcode ' . $nextBarcodeId . ' berhasil disimpan!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            // Kirim pesan error asli jika ada kegagalan internal
            return response()->json([
                'success' => false, 
                'message' => 'Gagal simpan ke DB: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mengambil riwayat konfigurasi struktur untuk fitur Import
    public function getConfigs()
    {
        // Mengambil master konfigurasi unik agar tidak double saat di-import
        $configs = DB::table('type_barcodes')
                     ->orderBy('id', 'desc')
                     ->get();

        return response()->json($configs);
    }
}