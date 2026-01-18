<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockEngController extends Controller
{
    public function index() {
        $stocks = \App\Models\StockEng::all()->map(function($item) {
            // Logika penentuan status dan warna
            if ($item->qty <= 0) {
                $item->status_label = 'ZERO STOCK';
                $item->color = 'red';
            } elseif ($item->qty < $item->min_stock) {
                $item->status_label = 'LOW STOCK';
                $item->color = 'amber'; // Kuning
            } else {
                $item->status_label = 'SAFE STOCK';
                $item->color = 'emerald'; // Hijau
            }
            return $item;
        });
    
        return view('stock_eng.index', compact('stocks'));
    }
}
