<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListSparepartEngController extends Controller
{
    public function index()
    {
        // Pastikan file view ini ada di resources/views/stock_eng/list_sparepart.blade.php
        return view('stock_eng.list_sparepart');
    }
}