<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListSparepartProdController extends Controller
{
    public function index()
    {
        // Update di sini: stock_prod.list_sparepartProd
        return view('stock_prod.list_sparepartProd');
    }
}