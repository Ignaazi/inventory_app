<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RequestProdController extends Controller
{
    public function index()
    {
        // Mengarah ke resources/views/stock_prod/requestProd.blade.php
        return view('stock_prod.requestProd');
    }
}