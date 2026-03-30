<?php

namespace App\Http\Controllers\Production; // Namespace harus sesuai folder

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductionOverviewController extends Controller
{
    public function index()
    {
        // Data dummy untuk mengetes tampilan agar tidak error
        $stats = [
            'total_request' => 45,
            'pending_approval' => 8,
            'received_today' => 12,
        ];
        
        // Mengarah ke resources/views/prod_overview/index.blade.php
        return view('prod_overview.index', compact('stats'));
    }
}