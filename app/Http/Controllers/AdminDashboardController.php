<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $engineeringStock = DB::table('stock_engs')->get(['qty', 'min_stock']);

        $engineering = [
            'stock_qty' => (int) $engineeringStock->sum('qty'),
            'stock_safe' => $engineeringStock->filter(fn ($stock) => $stock->qty > $stock->min_stock)->count(),
            'stock_warning' => $engineeringStock->filter(fn ($stock) => $stock->qty > 0 && $stock->qty <= $stock->min_stock)->count(),
            'stock_critical' => $engineeringStock->where('qty', '<=', 0)->count(),
            'pending_approval' => DB::table('production_requests')
                ->whereIn('status', ['Pending', 'Checked by Staff'])
                ->count(),
            'pending_receiving' => DB::table('material_received')
                ->whereIn('status', ['pending', 'checked'])
                ->count(),
            'transactions' => DB::table('stock_eng_transactions')->count(),
        ];

        $production = [
            'stock_qty' => (int) DB::table('stock_prods')->sum('qty'),
            'requests' => DB::table('production_requests')->count(),
            'pending_requests' => DB::table('production_requests')
                ->whereIn('status', ['Pending', 'Draft Submit', 'Draft'])
                ->count(),
            'transactions' => DB::table('stock_prod_transactions')->count(),
        ];

        $costing = [
            'purchase_requests' => DB::table('purchase_requests')->count(),
            'pending_approval' => DB::table('purchase_requests')
                ->where('status', 'checked')
                ->count(),
            'urgent' => DB::table('purchase_requests')
                ->where('priority', 'urgent')
                ->count(),
            'material_received' => DB::table('material_received')->count(),
            'material_open' => DB::table('material_received')
                ->where('qty_status', 'open')
                ->count(),
        ];

        $alerts = [
            'critical_stock' => $engineering['stock_critical'],
            'engineering_approval' => $engineering['pending_approval'],
            'engineering_receiving' => $engineering['pending_receiving'],
            'production_requests' => $production['pending_requests'],
            'costing_approval' => $costing['pending_approval'],
        ];

        $chartDates = [];
        $engineeringActivity = [];
        $productionActivity = [];
        $costingPurchaseRequests = [];
        $costingMaterialReceived = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateString = $date->toDateString();
            $chartDates[] = $date->format('d M');

            $engineeringActivity[] = DB::table('stock_eng_transactions')
                ->whereDate('created_at', $dateString)
                ->count();
            $productionActivity[] = DB::table('stock_prod_transactions')
                ->whereDate('created_at', $dateString)
                ->count();
            $costingPurchaseRequests[] = DB::table('purchase_requests')
                ->whereDate('created_at', $dateString)
                ->count();
            $costingMaterialReceived[] = DB::table('material_received')
                ->whereDate('created_at', $dateString)
                ->count();
        }

        $queueSummary = [
            'Engineering Approval' => $engineering['pending_approval'],
            'Engineering Receiving' => $engineering['pending_receiving'],
            'Production Request' => $production['pending_requests'],
            'Costing Approval' => $costing['pending_approval'],
            'Critical Stock' => $engineering['stock_critical'],
        ];

        $stockHealth = [
            'safe' => $engineering['stock_safe'],
            'warning' => $engineering['stock_warning'],
            'critical' => $engineering['stock_critical'],
        ];

        return view('admin', compact(
            'engineering',
            'production',
            'costing',
            'alerts',
            'chartDates',
            'engineeringActivity',
            'productionActivity',
            'costingPurchaseRequests',
            'costingMaterialReceived',
            'queueSummary',
            'stockHealth'
        ));
    }
}
