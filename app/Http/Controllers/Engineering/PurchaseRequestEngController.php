<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseRequestEngController extends Controller
{
    public function index()
    {
        // Mengarah ke resources/views/stock_eng/purchase_request.blade.php
        return view('stock_eng.purchase_request');
    }
}