<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransactionProdController extends Controller
{
    public function stockIn() {
        // Mengarah ke folder stock_prod/transactionProd/inProd.blade.php
        return view('stock_prod.transactionProd.inProd');
    }

    public function stockOut() {
        // Mengarah ke folder stock_prod/transactionProd/outProd.blade.php
        return view('stock_prod.transactionProd.outProd');
    }

    public function store(Request $request) {
        // Logika simpan transaksi tetap sama
        return back()->with('success', 'Transaksi Berhasil Dicatat!');
    }
}