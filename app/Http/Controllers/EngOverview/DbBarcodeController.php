<?php

namespace App\Http\Controllers\EngOverview;

use App\Http\Controllers\Controller;
use App\Models\DbBarcode; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DbBarcodeController extends Controller
{
    /**
     * Menampilkan list data final barcode dari database
     */
    public function index(Request $request)
    {
        $filter = strtolower((string) $request->input('filter', 'all'));
        $query = DbBarcode::query();

        if ($filter === 'in') {
            $query->where('barcode_id', 'LIKE', 'TXENGINRAK%');
        } elseif ($filter === 'out') {
            $query->where('barcode_id', 'LIKE', 'TXENGRAK%');
        } else {
            $filter = 'all';
        }

        $barcodes = $query->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('eng_overview.db_barcode', compact('barcodes', 'filter'));
    }

    /**
     * Menyimpan data barcode baru via AJAX FETCH (Format JSON)
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari request JSON
        $validator = Validator::make($request->all(), [
            'production_request_id' => 'required', 
            'barcode_type'          => 'required|string',
            'barcode_size'          => 'required|string',
            'final_content'         => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all())
            ], 422);
        }

        // Ambil NIM/NIK dari user login, jika kosong gunakan fallback 123456
        $currentCreatorNim = Auth::user()->nim ?? Auth::user()->nik ?? '123456';

        DB::beginTransaction();
        try {
            $finalContent = $request->final_content;

            // 🌟 DETEKSI TRANSAKSI: INBOUND vs OUT 🌟
            // Jika final_content diawali 'TXENGIN' (Inbound Format Terbaru), gunakan string tersebut sebagai barcode_id
            if (str_starts_with(strtoupper($finalContent), 'TXENGIN')) {
                $barcodeId = $request->barcode_id ?? $finalContent;
                $lifecycle = $request->current_lifecycle ?? 'AVAILABLE';
            } else {
                // --- LOGIKA BARCODE OUT (TIDAK DIUBAH SAMA SEKALI / 100% UTUH) ---
                $latestBarcode = DB::table('db_barcodes')->orderBy('id', 'desc')->first();
                
                if (!$latestBarcode) {
                    $barcodeId = 'SIIXENG001';
                } else {
                    $number = (int) substr($latestBarcode->barcode_id, 7);
                    $barcodeId = 'SIIXENG' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
                }
                $lifecycle = 'USED_IN';
            }

            // 2. INSERT KE TABEL db_barcodes
            $insertedId = DB::table('db_barcodes')->insertGetId([
                'barcode_id'        => $barcodeId, 
                'barcode_type'      => $request->barcode_type,
                'barcode_size'      => $request->barcode_size,
                'final_content'     => $finalContent,
                'nik'               => $currentCreatorNim,
                'current_lifecycle' => $lifecycle,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            // 3. INSERT KE TABEL barcode_parsings
            DB::table('barcode_parsings')->insert([
                'barcode_db_id'         => $insertedId, 
                'production_request_id' => $request->production_request_id, 
                'nik'                   => $currentCreatorNim,
                'qty_parsed'            => 1,
                'description'           => 'Barcode generated and locked for production request ID: ' . $request->production_request_id,
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Barcode ' . $barcodeId . ' berhasil disimpan dan dikunci untuk PR!',
                'barcode_id' => $barcodeId
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal simpan ke DB: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus data barcode
     */
    public function destroy($id)
    {
        DB::table('barcode_parsings')->where('barcode_db_id', $id)->delete();
        DB::table('db_barcodes')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'Data barcode berhasil dihapus dari database!');
    }
}
