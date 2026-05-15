<?php

namespace App\Http\Controllers\EngOverview;

use App\Http\Controllers\Controller;
use App\Models\TypeBarcode;
use Illuminate\Http\Request;

class TypeBarcodeController extends Controller
{
    /**
     * Menampilkan halaman list segment struktur barcode
     */
    public function index()
    {
        // Ambil semua log segment komponen dari MySQL, urutkan dari yang terbaru
        $types = TypeBarcode::orderBy('id', 'desc')->get();

        return view('eng_overview.type_barcode', compact('types'));
    }

    /**
     * Menghapus konfigurasi template tipe jika sudah tidak dipakai
     */
    public function destroy($id)
    {
        $type = TypeBarcode::findOrFail($id);
        $type->delete();

        return redirect()->back()->with('success', 'Konfigurasi komponen sukses dihapus dari database!');
    }
}