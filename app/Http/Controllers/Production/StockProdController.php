<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller; 
use App\Models\production\stock_prod; // Tetap menggunakan huruf kecil sesuai file kamu
use Illuminate\Http\Request;

class StockProdController extends Controller
{
    public function index()
    {
        // 1. Ambil data stock menggunakan nama class huruf kecil beserta relasi line-nya
        $stocks = stock_prod::with('line')->paginate(10); 

        // 2. Ambil data master line untuk filter tab atas
        $modelUtama = 'App\\Models\\LineLineProduction';
        $modelSubFolder = 'App\\Models\\Production\\LineLineProduction';

        if (class_exists($modelUtama)) {
            $lines = $modelUtama::all();
        } elseif (class_exists($modelSubFolder)) {
            $lines = $modelSubFolder::all();
        } else {
            $lines = collect([]); 
        }

        // 3. Ambil jenis nozzle unik
        $allNozzles = stock_prod::select('no_nozzle', 'sap_code')->distinct()->get();

        // 4. Kirim ke view
        return view('stock_prod.stock_prod', compact('stocks', 'lines', 'allNozzles'));
    }

    /**
     * 🚀 ACTION: UPDATE / EDIT DATA STOCK PRODUCTION
     */
    public function update(Request $request, $id)
    {
        // Validasi input data dari form edit
        $request->validate([
            'line_id'    => 'required',
            'no_nozzle'  => 'required|string',
            'qty'        => 'required|integer|min:0',
            'min_stock'  => 'required|integer|min:0',
        ]);

        // Cari data stock berdasarkan ID
        $stock = stock_prod::findOrFail($id);

        // Update data ke database
        $stock->update([
            'line_id'    => $request->line_id,
            'no_nozzle'  => $request->no_nozzle,
            'request_no' => $request->request_no, // Mengikuti request sparepart ID jika diubah
            'part_no'    => $request->part_no,
            'sap_code'   => $request->sap_code,
            'qty'        => $request->qty,
            'min_stock'  => $request->min_stock,
        ]);

        return redirect()->back()->with('success', 'Data Stock Production berhasil diperbarui, Coy!');
    }

    /**
     * 🚀 ACTION: DELETE / HAPUS DATA STOCK PRODUCTION
     */
    public function destroy($id)
    {
        // Cari data stock berdasarkan ID lalu hapus
        $stock = stock_prod::findOrFail($id);
        $stock->delete();

        return redirect()->back()->with('success', 'Data Stock Production berhasil dihapus dari sistem!');
    }

    /**
     * Placeholder Method agar tidak eror saat dipanggil rute bawaan view sebelumnya
     */
    public function storeRequest(Request $request)
    {
        return redirect()->back()->with('success', 'Request stock berhasil dikirim ke Engineering!');
    }

    public function receiveItem(Request $request)
    {
        return redirect()->back()->with('success', 'Item berhasil diterima di lantai produksi!');
    }

    public function requestHistory()
    {
        return "Halaman Request History Production - Comming Soon, Bro!";
    }

    public function exportCSV()
    {
        return "Proses Export CSV - Comming Soon, Bro!";
    }
}