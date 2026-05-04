<?php

namespace App\Http\Controllers;

use App\Models\BarcodeParsing;
use Illuminate\Http\Request;

class BarcodeParsingController extends Controller
{
    public function index()
    {
        // Mengarahkan ke resources/views/eng_overview/barcode_parsing.blade.php
        return view('eng_overview.barcode_parsing');
    }
}