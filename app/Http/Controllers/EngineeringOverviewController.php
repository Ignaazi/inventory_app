<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EngineeringOverviewController extends Controller
{
    public function index(Request $request)
    {
        // 1. QUERY MASTER STOCK. Gunakan LEFT JOIN agar baris stock_engs tetap
        // terlihat walaupun relasi sparepart/rak belum lengkap, sama seperti
        // halaman Stock Engineering.
        $rawParts = DB::table('stock_engs')
            ->leftJoin('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id')
            ->leftJoin('raks', 'stock_engs.rak_id', '=', 'raks.id')
            ->select(
                'stock_engs.id as stock_id',
                'stock_engs.qty as current_stock',
                'stock_engs.min_stock as min_stock_threshold',
                'stock_engs.updated_at',
                'spareparts.id as sparepart_id',
                'spareparts.sparepart_id as part_name',
                'spareparts.sap_code',
                'spareparts.part_number',
                'spareparts.category',
                'raks.nama_rak as rack_position'
            )
            ->orderByDesc('stock_engs.updated_at')
            ->get();

        // Mapping status stok sesuai threshold yang dipakai halaman Stock Engineering.
        $parts = $rawParts->map(function ($item) {
            $currentStock = (int) $item->current_stock;
            $minStock     = (int) $item->min_stock_threshold;

            if ($currentStock <= 0) {
                $status = 'Critical';
            } elseif ($currentStock <= $minStock) {
                $status = 'Warning';
            } else {
                $status = 'Safe';
            }

            $item->status = $status;
            return $item;
        });

        // Data Sparepart Critical untuk Card Urgent PR
        $criticalParts = $parts->where('status', 'Critical')->take(5);

        // 2. AGREGASI STATISTIK KPI DARI 5 TABEL DATABASE
        $today = Carbon::today()->toDateString();

        $stats = [
            'total_part'   => $parts->count(),
            'critical'     => $parts->where('status', 'Critical')->count(),
            'warning'      => $parts->where('status', 'Warning')->count(),
            'safe'         => $parts->where('status', 'Safe')->count(),

            // Harus mengikuti status yang dipakai ApprovalEngController.
            'approval_total'       => DB::table('production_requests')
                                        ->whereIn('status', ['Pending', 'Checked by Staff'])
                                        ->count(),
            'approval_staff'       => DB::table('production_requests')
                                        ->where('status', 'Pending')
                                        ->count(),
            'approval_supervisor'  => DB::table('production_requests')
                                        ->where('status', 'Checked by Staff')
                                        ->count(),

            // Halaman Engineering hanya memproses MR pending dan checked.
            'material_receiving'   => DB::table('material_received')
                                        ->whereIn('status', ['pending', 'checked'])
                                        ->count(),
            'material_pending'     => DB::table('material_received')
                                        ->where('status', 'pending')
                                        ->count(),
            'material_checked'     => DB::table('material_received')
                                        ->where('status', 'checked')
                                        ->count(),

            'lost_report'  => DB::table('stock_eng_transactions')
                                ->where('tx_type', 'disposal')
                                ->sum('qty_transaction'),

            'tx_today'     => DB::table('stock_eng_transactions')
                                ->whereIn('tx_type', ['in', 'out'])
                                ->whereDate('created_at', $today)
                                ->count(),

            'tx_in_total'  => DB::table('stock_eng_transactions')->where('tx_type', 'in')->count(),
            'tx_out_total' => DB::table('stock_eng_transactions')->where('tx_type', 'out')->count(),
            'tx_return_total' => DB::table('stock_eng_transactions')->where('tx_type', 'return')->count(),
            'tx_disposal_total' => DB::table('stock_eng_transactions')->where('tx_type', 'disposal')->count(),
        ];

        // 3. DYNAMIC DATASET UNTUK APEXCHARTS (7 HARI TERAKHIR)
        $chartDates    = [];
        $chartStockIn  = [];
        $chartStockOut = [];
        $chartStockReturn = [];
        $chartStockDisposal = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $chartDates[] = $date->format('d M');

            $chartStockIn[]  = DB::table('stock_eng_transactions')
                ->where('tx_type', 'in')
                ->whereDate('created_at', $dateStr)
                ->count();

            $chartStockOut[] = DB::table('stock_eng_transactions')
                ->where('tx_type', 'out')
                ->whereDate('created_at', $dateStr)
                ->count();

            $chartStockReturn[] = DB::table('stock_eng_transactions')
                ->where('tx_type', 'return')
                ->whereDate('created_at', $dateStr)
                ->count();

            $chartStockDisposal[] = DB::table('stock_eng_transactions')
                ->where('tx_type', 'disposal')
                ->whereDate('created_at', $dateStr)
                ->count();
        }

        $chartMonthlyDates = [];
        $chartMonthlyIn = [];
        $chartMonthlyOut = [];
        $chartMonthlyReturn = [];
        $chartMonthlyDisposal = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->startOfMonth()->subMonths($i);
            $chartMonthlyDates[] = $month->format('M Y');
            $chartMonthlyIn[] = DB::table('stock_eng_transactions')->where('tx_type', 'in')->whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
            $chartMonthlyOut[] = DB::table('stock_eng_transactions')->where('tx_type', 'out')->whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
            $chartMonthlyReturn[] = DB::table('stock_eng_transactions')->where('tx_type', 'return')->whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
            $chartMonthlyDisposal[] = DB::table('stock_eng_transactions')->where('tx_type', 'disposal')->whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count();
        }

        $chartYearlyDates = [];
        $chartYearlyIn = [];
        $chartYearlyOut = [];
        $chartYearlyReturn = [];
        $chartYearlyDisposal = [];

        for ($i = 2; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $chartYearlyDates[] = (string) $year;
            $chartYearlyIn[] = DB::table('stock_eng_transactions')->where('tx_type', 'in')->whereYear('created_at', $year)->count();
            $chartYearlyOut[] = DB::table('stock_eng_transactions')->where('tx_type', 'out')->whereYear('created_at', $year)->count();
            $chartYearlyReturn[] = DB::table('stock_eng_transactions')->where('tx_type', 'return')->whereYear('created_at', $year)->count();
            $chartYearlyDisposal[] = DB::table('stock_eng_transactions')->where('tx_type', 'disposal')->whereYear('created_at', $year)->count();
        }

        $transactionFilter = strtolower((string) $request->input('tx_filter', 'all'));
        $allowedTransactionFilters = ['all', 'in', 'out', 'return', 'disposal'];
        if (!in_array($transactionFilter, $allowedTransactionFilters, true)) {
            $transactionFilter = 'all';
        }

        $transactionQuery = DB::table('stock_eng_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('stock_engs as se', 't.stock_engs_id', '=', 'se.id')
            ->leftJoin('spareparts as sp', 'se.sparepart_id', '=', 'sp.id')
            ->leftJoin('raks as r', 'se.rak_id', '=', 'r.id')
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->select([
                't.tx_id',
                't.tx_type',
                't.qty_transaction',
                't.status',
                't.process_type',
                't.remark',
                't.created_at',
                'u.nik',
                'u.name as operator_name',
                'sp.sparepart_id as sparepart_code',
                'r.nama_rak',
                'b.barcode_id',
                'b.current_lifecycle as barcode_lifecycle',
            ])
            ->when($transactionFilter !== 'all', function ($query) use ($transactionFilter) {
                $query->where('t.tx_type', $transactionFilter);
            })
            ->orderByDesc('t.created_at');

        $transactions = $transactionQuery
            ->paginate(10, ['*'], 'tx_page')
            ->withQueryString();

        return view('eng_overview.index', compact(
            'stats',
            'parts',
            'criticalParts',
            'chartDates',
            'chartStockIn',
            'chartStockOut',
            'chartStockReturn',
            'chartStockDisposal',
            'chartMonthlyDates',
            'chartMonthlyIn',
            'chartMonthlyOut',
            'chartMonthlyReturn',
            'chartMonthlyDisposal',
            'chartYearlyDates',
            'chartYearlyIn',
            'chartYearlyOut',
            'chartYearlyReturn',
            'chartYearlyDisposal',
            'transactions',
            'transactionFilter'
        ));
    }
}
