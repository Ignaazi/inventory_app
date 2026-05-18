<?php

namespace App\Http\Controllers\EngOverview;

use App\Http\Controllers\Controller;
use App\Models\DbBarcode; // Pastikan model ini mengarah ke model db_barcodes lu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DbBarcodeController extends Controller
{
    /**
     * Menampilkan list data final barcode dari database
     */
    public function index()
    {
        $barcodes = DbBarcode::orderBy('id', 'desc')->paginate(10);
        return view('eng_overview.db_barcode', compact('barcodes'));
    }

    /**
     * Menyimpan data barcode baru via AJAX FETCH (Format JSON)
     */
    public function store(Request $request)
    {
        // Gunakan Validator manual agar jika gagal bisa dikembalikan dalam bentuk JSON error ke Javascript
        $validator = Validator::make($request->all(), [
            'barcode_type'  => 'required|string',
            'barcode_size'  => 'required|string',
            'final_content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all())
            ], 422);
        }

        DB::beginTransaction();
        try {
            // LOGIKA OTOMATIS GENERATE KODE UNIK (SIIXENG001, SIIXENG002, dst.)
            $latestBarcode = DbBarcode::latest('id')->first();
            
            if (!$latestBarcode) {
                $nextBarcodeId = 'SIIXENG001';
            } else {
                // Mengambil angka di belakang string 'SIIXENG' (Mulai indeks ke-7)
                $number = (int) substr($latestBarcode->barcode_id, 7);
                // Menambahkan +1 dan memformatnya kembali jadi 3 digit (002, 003, dst.)
                $nextBarcodeId = 'SIIXENG' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
            }

            // Simpan data baru ke tabel db_barcodes
            $newBarcode = DbBarcode::create([
                'barcode_id'        => $nextBarcodeId, // Isinya SIIXENG001
                'barcode_type'      => $request->barcode_type,
                'barcode_size'      => $request->barcode_size,
                'final_content'     => $request->final_content,
                'current_lifecycle' => 'AVAILABLE', // Default siap pakai
            ]);

            DB::commit();

            // Kembalikan response sukses berbentuk JSON agar ditangkap dengan benar oleh JavaScript halaman customizer
            return response()->json([
                'success' => true,
                'message' => 'Barcode ' . $nextBarcodeId . ' berhasil disimpan!',
                'data'    => $newBarcode
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal simpan ke database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus data barcode jika diperlukan
     */
    public function destroy($id)
    {
        $barcode = DbBarcode::findOrFail($id);
        $barcode->delete();

        return redirect()->back()->with('success', 'Data barcode berhasil dihapus dari database!');
    }
}