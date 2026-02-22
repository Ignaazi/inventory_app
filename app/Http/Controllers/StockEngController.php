<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockEng; // Pastikan nama model sesuai

class StockEngController extends Controller
{
    public function index() {
        $stocks = StockEng::all()->map(function($item) {
            if ($item->qty <= 0) {
                $item->status_label = 'ZERO STOCK';
                $item->status_class = 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400';
                $item->text_color = 'text-rose-500';
            } elseif ($item->qty <= $item->min_stock) {
                $item->status_label = 'LOW STOCK';
                $item->status_class = 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400';
                $item->text_color = 'text-amber-500';
            } else {
                $item->status_label = 'SAFE STOCK';
                $item->status_class = 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400';
                $item->text_color = 'text-slate-800 dark:text-white';
            }
            return $item;
        });
    
        return view('stock_eng.index', compact('stocks'));
    }

    // TAMBAHKAN FUNGSI STORE INI
    public function store(Request $request)
    {
        $request->validate([
            'id_rak'    => 'required|unique:stock_engs,id_rak',
            'item_name' => 'required|string|max:255',
            'qty'       => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        StockEng::create([
            'id_rak'    => $request->id_rak,
            'item_name' => $request->item_name,
            'qty'       => $request->qty,
            'min_stock' => $request->min_stock,
        ]);

        return redirect()->back()->with('success', 'Data Rak berhasil ditambahkan!');
    }

    // TAMBAHKAN FUNGSI UPDATE UNTUK FITUR EDIT
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_rak'    => 'required|unique:stock_engs,id_rak,'.$id,
            'item_name' => 'required|string|max:255',
            'qty'       => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
        ]);

        $stock = StockEng::findOrFail($id);
        $stock->update($request->all());

        return redirect()->back()->with('success', 'Data Rak berhasil diperbarui!');
    }

    // TAMBAHKAN FUNGSI DESTROY UNTUK FITUR HAPUS
    public function destroy($id)
    {
        $stock = StockEng::findOrFail($id);
        $stock->delete();

        return redirect()->back()->with('success', 'Data Rak berhasil dihapus!');
    }
}