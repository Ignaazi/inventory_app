<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ProductionOverviewController extends Controller
{
    public function index()
    {
        // 1. STATISTIK UTAMA (KPI)
        $totalRequestsCount     = DB::table('production_requests')->count();
        $pendingRequestsCount   = DB::table('production_requests')
            ->whereIn('status', ['Pending', 'Draft Submit', 'Draft'])
            ->count();
        $approvedRequestsCount  = DB::table('production_requests')->where('status', 'Approved')->count();
        $completedRequestsCount = DB::table('production_requests')->where('status', 'Completed')->count();
        $rejectedRequestsCount  = DB::table('production_requests')->where('status', 'Rejected')->count();

        // 2. STATISTIK STOK & PERGERAKAN (IN, OUT, RETURN)
        $totalTxCount       = DB::table('stock_prod_transactions')->count();
        $txInCount          = DB::table('stock_prod_transactions')->where('tx_type', 'in')->count();
        $txOutCount         = DB::table('stock_prod_transactions')->where('tx_type', 'out')->count();
        $txReturnCount      = DB::table('stock_prod_transactions')->where('tx_type', 'return')->count();
        
        $totalCurrentStock  = DB::table('stock_prods')->sum('qty');
        $totalLinesCount    = DB::table('list_line_productions')->count();

        $totalQtyRequested  = DB::table('production_requests')->sum('qty_req');
        $totalQtyFulfilled  = DB::table('stock_prod_transactions')->where('tx_type', 'in')->sum('qty_transaction');

        // 3. CHART 1: DAILY REQUEST FREQUENCY (7 HARI TERAKHIR)
        $chartDates    = [];
        $chartTotalReq = [];
        $chartTotalQty = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $chartDates[] = $date->format('d M');
            
            $reqCount = DB::table('production_requests')
                ->whereDate('created_at', $dateStr)
                ->count();
                
            $qtySum = DB::table('production_requests')
                ->whereDate('created_at', $dateStr)
                ->sum('qty_req');

            $chartTotalReq[] = $reqCount;
            $chartTotalQty[] = (int) $qtySum;
        }

        // 4. CHART 2: MOVEMENT DYNAMICS (IN, OUT, RETURN)
        $chartTxIn     = [];
        $chartTxOut    = [];
        $chartTxReturn = [];

        for ($i = 6; $i >= 0; $i--) {
            $dateStr = Carbon::now()->subDays($i)->format('Y-m-d');
            
            $chartTxIn[]     = DB::table('stock_prod_transactions')->where('tx_type', 'in')->whereDate('created_at', $dateStr)->count();
            $chartTxOut[]    = DB::table('stock_prod_transactions')->where('tx_type', 'out')->whereDate('created_at', $dateStr)->count();
            $chartTxReturn[] = DB::table('stock_prod_transactions')->where('tx_type', 'return')->whereDate('created_at', $dateStr)->count();
        }

        // 5. DETEKSI NAMA NAMA TABEL & KOLOM SPAREPART SECARA DINAMIS (BEBAS ERROR 1054)
        $sparepartTable = Schema::hasTable('spareparts') ? 'spareparts' : 'list_sparepart_engs';

        // Deteksi kolom nama sparepart yang ada di DB
        $nameColumn = 'id';
        if (Schema::hasColumn($sparepartTable, 'nama_sparepart')) {
            $nameColumn = 'nama_sparepart';
        } elseif (Schema::hasColumn($sparepartTable, 'name_sparepart')) {
            $nameColumn = 'name_sparepart';
        } elseif (Schema::hasColumn($sparepartTable, 'sparepart_name')) {
            $nameColumn = 'sparepart_name';
        } elseif (Schema::hasColumn($sparepartTable, 'description')) {
            $nameColumn = 'description';
        } elseif (Schema::hasColumn($sparepartTable, 'name')) {
            $nameColumn = 'name';
        }

        // Deteksi kolom kode sparepart (misal: sparepart_id)
        $codeColumn = Schema::hasColumn($sparepartTable, 'sparepart_id') ? 'sparepart_id' : 'id';

        // 6. QUERY DATA RECENT REQUESTS
        $recentRequests = DB::table('production_requests as pr')
            ->leftJoin('list_line_productions as lp', 'pr.list_line_production_id', '=', 'lp.id')
            ->leftJoin($sparepartTable . ' as s', 'pr.sparepart_id', '=', 's.id')
            ->select([
                'pr.id',
                'pr.request_no',
                'pr.qty_req',
                'pr.status',
                'pr.remark',
                'pr.created_at',
                'lp.no_line',
                'lp.name_machine',
                DB::raw("COALESCE(s.{$nameColumn}, s.{$codeColumn}, 'Sparepart Item') as sparepart_name"),
                DB::raw("COALESCE(s.{$codeColumn}, '-') as sparepart_code")
            ])
            ->orderBy('pr.created_at', 'desc')
            ->limit(10)
            ->get();

        // 7. RETURN VIEW
        return view('prod_overview.index', compact(
            'totalRequestsCount',
            'pendingRequestsCount',
            'approvedRequestsCount',
            'completedRequestsCount',
            'rejectedRequestsCount',
            'totalTxCount',
            'txInCount',
            'txOutCount',
            'txReturnCount',
            'totalCurrentStock',
            'totalLinesCount',
            'totalQtyRequested',
            'totalQtyFulfilled',
            'chartDates',
            'chartTotalReq',
            'chartTotalQty',
            'chartTxIn',
            'chartTxOut',
            'chartTxReturn',
            'recentRequests'
        ));
    }
}