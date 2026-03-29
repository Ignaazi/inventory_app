<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\StockTransaction; // Nanti diaktifkan jika model sudah siap

class TransactionController extends Controller
{
    /**
     * Tampilan halaman Stock In
     */
    public function indexIn()
    {
        // Sementara kita return view kosong dulu agar tidak error
        return view('stock_eng.transaction.in');
    }

    /**
     * Tampilan halaman Stock Out
     */
    public function indexOut()
    {
        return view('stock_eng.transaction.out');
    }

    /**
     * Tampilan halaman Disposal
     */
    public function indexDisposal()
    {
        return view('stock_eng.transaction.disposal');
    }
}