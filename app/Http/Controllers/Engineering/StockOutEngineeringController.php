<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\StockOutEng;
use App\Models\StockEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// 🌟 Menambahkan import model RequestProd agar bisa digunakan
use App\Models\Production\RequestProd;

class StockOutEngineeringController extends Controller
{
    /**
     * Tampil Utama History List Log
     */
    public function index()
    {
        // UPDATE: Langsung panggil 'rak' secara mandiri karena sudah direlasikan di model StockOutEng
        $history = StockOutEng::with(['stockEng', 'dbBarcode', 'rak'])->latest()->paginate(10);
        return view('stock_eng.transaction.out', compact('history'));
    }

    /**
     * Tampilan untuk Input Manual OUT
     */
    public function manual()
    {
        // 🌟 LOGIKA: Ambil daftar ID/No yang sudah ada di log agar tidak muncul di dropdown
        $usedRequests = StockOutEng::whereNotNull('request_sparepart_id')->pluck('request_sparepart_id')->toArray();
        $usedBarcodes = StockOutEng::whereNotNull('barcode_id')->pluck('barcode_id')->toArray();

        // 1. Ambil semua data master stock engineering
        $stocks = StockEng::orderBy('no_nozzle', 'asc')->get();
        
        // 2. Ambil semua data master dari table db_barcodes (Filter: hanya yang belum terpakai)
        $barcodes = \App\Models\DbBarcode::whereNotIn('id', $usedBarcodes)
                                         ->orderBy('barcode_id', 'asc')
                                         ->get();

        // 3. Ambil semua data master Rak
        $raks = \App\Models\Rak::orderBy('nama_rak', 'asc')->get();

        // 🌟 FIX: Menambahkan data productionRequests (Filter: hanya yang belum terpakai)
        $productionRequests = RequestProd::where('status', '!=', 'Draft')
                                         ->whereNotIn('request_no', $usedRequests)
                                         ->orderBy('request_no', 'asc')
                                         ->get();

        // Kirim semua data termasuk productionRequests ke view manual out
        return view('stock_eng.transaction.out_manual', compact('stocks', 'barcodes', 'raks', 'productionRequests'));
    }

    /**
     * Tampilan untuk Scan Barcode OUT
     */
    public function scan()
    {
        $stocks = StockEng::orderBy('no_nozzle', 'asc')->get();
        return view('stock_eng.transaction.out_scan', compact('stocks'));
    }

    /**
     * Simpan Transaksi & Potong Stok Otomatis
     */
    public function store(Request $request)
    {
        // FIX 1: Menyelaraskan rule 'barcode_id' menjadi required dan validasi unique agar tidak bisa duplikat
        $request->validate([
            'stock_eng_id'         => 'required|exists:stock_engs,id',
            'rak_id'               => 'required|exists:raks,id', 
            'barcode_id'           => 'required|exists:db_barcodes,id|unique:stock_out_logs,barcode_id', 
            'request_sparepart_id' => 'nullable|string|unique:stock_out_logs,request_sparepart_id',
            'qty_out'              => 'required|integer|min:1',
            'remark'               => 'nullable|string',
            'comment'              => 'nullable|string',
            'source'               => 'required|in:manual,scan'
        ]);

        DB::beginTransaction();
        try {
            $stock = StockEng::findOrFail($request->stock_eng_id);

            if ($stock->qty < $request->qty_out) {
                return redirect()->back()->withInput()->with('error', 'Gagal! Stok tidak mencukupi untuk melakukan pengeluaran.');
            }

            // FIX 2: Perbaikan sistem generate auto-increment ID transaksi
            $latestTx = StockOutEng::where('transaction_out_id', 'LIKE', 'ENGOUT%')
                                    ->orderBy('id', 'desc')
                                    ->first();
            if (!$latestTx) {
                $nextId = 'ENGOUT001';
            } else {
                $currentNumber = (int) filter_var($latestTx->transaction_out_id, FILTER_SANITIZE_NUMBER_INT);
                $nextId = 'ENGOUT' . str_pad($currentNumber + 1, 3, '0', STR_PAD_LEFT);
            }

            $finalRemark = $request->remark ?: ($request->source === 'manual' ? 'MANUAL OUT' : 'SCAN OUT');

            // Insert data ke tabel stock_out_logs
            StockOutEng::create([
                'transaction_out_id'   => $nextId,
                'nik'                  => Auth::user()->nim ?? Auth::user()->nik ?? '9999',
                'request_sparepart_id' => $request->request_sparepart_id ?? null,
                'barcode_id'           => $request->barcode_id,
                'stock_eng_id'         => $stock->id,
                'no_nozzle'            => $stock->no_nozzle,
                'rak_id'               => $request->rak_id, 
                'qty_out'              => $request->qty_out,
                'status'               => 'SUCCESS',
                'remark'               => strtoupper($finalRemark),
                'comment'              => $request->comment ?? '-',
            ]);

            // Potong jumlah kuantitas stok
            $stock->decrement('qty', $request->qty_out);

            DB::commit();
            return redirect()->route('eng.out')->with('success', 'Transaksi ' . $nextId . ' Berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error Sistem: ' . $e->getMessage());
        }
    }
}