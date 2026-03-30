<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CostingOverviewController extends Controller
{
    public function index()
    {
        // Data Dummy untuk Dashboard Costing
        $stats = [
            'total_pr' => 12,
            'total_expenses' => '45.2M',
            'out_of_stock' => 8,
            'po_issued' => 24
        ];

        return view('cost_overview.index', compact('stats'));
    }
}