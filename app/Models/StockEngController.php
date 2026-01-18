<?php

namespace App\Http\Controllers;

use App\Models\StockEng; // Pastikan Model sudah benar namespace-nya
use Illuminate\Http\Request;

class StockEngController extends Controller
{
    public function index()
    {
        $stocks = StockEng::all();
        return view('stock_eng.index', compact('stocks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_rak' => 'required|unique:stock_engs',
            'item_name' => 'required',
            'qty' => 'required|numeric',
            'min_stock' => 'required|numeric',
        ]);

        StockEng::create($request->all());
        return redirect()->back()->with('success', 'Data Rak Berhasil Ditambahkan');
    }

    // Tambahkan Fungsi Update agar Route PUT bekerja
    public function update(Request $request, $id)
    {
        $stock = StockEng::findOrFail($id);
        $stock->update($request->all());
        return redirect()->back()->with('success', 'Data Rak Berhasil Diupdate');
    }

    // Tambahkan Fungsi Destroy agar Route DELETE bekerja
    public function destroy($id)
    {
        $stock = StockEng::findOrFail($id);
        $stock->delete();
        return redirect()->back()->with('success', 'Data Rak Berhasil Dihapus');
    }
}