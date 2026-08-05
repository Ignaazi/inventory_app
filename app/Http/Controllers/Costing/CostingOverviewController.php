<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;
use App\Models\Costing\MaterialReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostingOverviewController extends Controller
{
    public function index()
    {
        // 1. Top 3 KPI Summary
        $totalPrCount   = PurchaseRequestEng::count();
        $totalMrCount   = MaterialReceived::count();
        $urgentPrCount  = PurchaseRequestEng::where('priority', 'urgent')->count();
        
        // Data untuk Alert Box Pengingat Approval (ApprovalController Integration)
        $pendingCheckedPrCount = PurchaseRequestEng::where('status', 'checked')->count();

        // Data untuk Ringkasan Data Card
        $prApprovedCount       = PurchaseRequestEng::where('status', 'approved')->count();
        $prRejectedCount       = PurchaseRequestEng::where('status', 'rejected')->count();
        $mrClosedCount         = MaterialReceived::where('qty_status', 'closed')->count();
        $mrOpenCount           = MaterialReceived::where('qty_status', 'open')->count();

        $totalQtyPr       = PurchaseRequestEng::where('status', 'approved')->sum('qty_pr');
        $totalQtyReceived = MaterialReceived::where('status', 'approved')->sum('qty_received');

        // 2. Data Grafik Kiri Top: Daily Purchase Request Process
        $dailyRequests = DB::table('purchase_requests')
            ->select(
                DB::raw("DATE(request_date) as date_key"),
                DB::raw("DATE_FORMAT(request_date, '%d %b %Y') as formatted_date"),
                DB::raw("COUNT(id) as total_pr"),
                DB::raw("SUM(qty_pr) as total_qty")
            )
            ->groupBy('date_key', 'formatted_date')
            ->orderBy('date_key', 'asc')
            ->get();

        $chartDates    = $dailyRequests->pluck('formatted_date')->toArray();
        $chartTotalPr  = $dailyRequests->pluck('total_pr')->toArray();
        $chartTotalQty = $dailyRequests->pluck('total_qty')->toArray();

        // 3. Data Grafik Kiri Bottom: Material Received Timeline
        $mrRecords = DB::table('material_received as mr')
            ->join('purchase_requests as pr', 'mr.purchase_request_id', '=', 'pr.id')
            ->select(
                'mr.no_mr',
                'pr.no_pr',
                'pr.request_date',
                'pr.expected_arrival_date',
                'mr.created_at as actual_received_at'
            )
            ->orderBy('mr.id', 'desc')
            ->limit(5)
            ->get();

        $actualTimelineData = [];
        $targetTimelineData = [];

        foreach ($mrRecords as $item) {
            $label = $item->no_mr . ' (' . substr($item->no_pr, -4) . ')';
            $prStartMs = strtotime($item->request_date) * 1000;
            $mrRecvMs  = strtotime($item->actual_received_at) * 1000;
            $targetMs  = $item->expected_arrival_date ? strtotime($item->expected_arrival_date) * 1000 : $mrRecvMs;

            $actualTimelineData[] = [
                'x' => $label,
                'y' => [$prStartMs, $mrRecvMs]
            ];

            $targetTimelineData[] = [
                'x' => $label,
                'y' => [$prStartMs, $targetMs]
            ];
        }

        $timelineSeries = [
            [
                'name' => 'Target Leadtime (PR)',
                'data' => array_reverse($targetTimelineData)
            ],
            [
                'name' => 'Actual Arrival (MR)',
                'data' => array_reverse($actualTimelineData)
            ]
        ];

        return view('cost_overview.index', compact(
            'totalPrCount',
            'totalMrCount',
            'urgentPrCount',
            'pendingCheckedPrCount',
            'prApprovedCount',
            'prRejectedCount',
            'mrClosedCount',
            'mrOpenCount',
            'totalQtyPr',
            'totalQtyReceived',
            'chartDates',
            'chartTotalPr',
            'chartTotalQty',
            'timelineSeries'
        ));
    }
}