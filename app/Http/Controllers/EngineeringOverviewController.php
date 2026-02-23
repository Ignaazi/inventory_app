<?php

namespace App\Http\Controllers;

use App\Models\EngineeringOverview;
use Illuminate\Http\Request;

class EngineeringOverviewController extends Controller
{
    public function index()
    {
        // Ambil data part yang stoknya kritis dari model EngineeringOverview
        $parts = EngineeringOverview::all();
        
        $stats = [
            'total_part' => EngineeringOverview::count(),
            'critical' => EngineeringOverview::where('status', 'Critical')->count(),
            'warning' => EngineeringOverview::where('status', 'Warning')->count(),
        ];
    return view('eng_overview.index', compact('parts', 'stats'));
    }
}