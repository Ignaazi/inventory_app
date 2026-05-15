<?php

namespace App\Http\Controllers\EngOverview;

use App\Http\Controllers\Controller;
use App\Models\DbBarcode; // Panggil model yang kita buat tadi
use Illuminate\Http\Request;

class DbBarcodeController extends Controller
{
    /**
     * Menampilkan list data final barcode dari database
     */
    public function index()
    {
        // Mengambil semua data dari tabel db_barcodes, diurutkan dari ID terbesar (terbaru)
        $barcodes = DbBarcode::orderBy('id', 'desc')->get();

        // Lempar data ke halaman view dengan nama variabel 'barcodes'
        return view('eng_overview.db_barcode', compact('barcodes'));
    }

    /**
     * Fitur Opsional: Menghapus data barcode jika diperlukan
     */
    public function destroy($id)
    {
        $barcode = DbBarcode::findOrFail($id);
        $barcode->delete();

        return redirect()->back()->with('success', 'Data barcode berhasil dihapus dari database!');
    }
}