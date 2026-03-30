<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockProdController extends Controller
{
    public function index()
    {
        // Mengarah ke resources/views/stock_prod/stockProd.blade.php
        return view('stock_prod.stock_prod');
    }
}