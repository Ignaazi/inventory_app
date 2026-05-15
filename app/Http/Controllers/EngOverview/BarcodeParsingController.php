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

            // 1. Simpan ke Halaman/Tabel DB Barcode
            DB::table('db_barcodes')->insert([
                'barcode_type' => $request->barcode_type,
                'barcode_size' => $request->barcode_size,
                'final_content' => $request->final_content,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Simpan setiap komponen di list struktur ke Halaman/Tabel Type Barcode
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

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
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