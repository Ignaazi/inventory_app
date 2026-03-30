<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalEngController extends Controller
{
    public function index()
    {
        // Mengarah ke resources/views/stock_eng/approval.blade.php
        return view('stock_eng.approval');
    }
}