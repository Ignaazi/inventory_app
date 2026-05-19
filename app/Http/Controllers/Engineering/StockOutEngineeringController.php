<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\StockOutEng;
use App\Models\StockEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        // 1. Ambil semua data master stock engineering
        $stocks = StockEng::orderBy('no_nozzle', 'asc')->get();
        
        // 2. Ambil semua data master dari table db_barcodes
        $barcodes = \App\Models\DbBarcode::orderBy('barcode_id', 'asc')->get();

        // 3. Ambil semua data master Rak
        $raks = \App\Models\Rak::orderBy('nama_rak', 'asc')->get();

        // Kirim ketiga data master tersebut ke view manual out
        return view('stock_eng.transaction.out_manual', compact('stocks', 'barcodes', 'raks'));
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
        // FIX: Menambahkan validasi 'barcode_id' berbasis 'id' tabel target agar sinkron dengan kiriman value form
        $request->validate([
            'stock_eng_id'         => 'required|exists:stock_engs,id',
            'rak_id'               => 'required|exists:raks,id', 
            'barcode_id'           => 'nullable|exists:db_barcodes,id', 
            'request_sparepart_id' => 'nullable|string',
            'qty_out'              => 'required|integer|min:1',
            'remark'       => 'nullable|string',
            'comment'      => 'nullable|string',
            'source'       => 'required|in:manual,scan'
        ]);

        DB::beginTransaction();
        try {
            $stock = StockEng::findOrFail($request->stock_eng_id);

            if ($stock->qty < $request->qty_out) {
                return redirect()->back()->with('error', 'Gagal! Stok tidak mencukupi.');
            }

            $latestTx = StockOutEng::latest()->first();
            if (!$latestTx) {
                $nextId = 'ENGOUT001';
            } else {
                $number = (int) substr($latestTx->transaction_out_id, 6);
                $nextId = 'ENGOUT' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
            }

            $finalRemark = $request->remark ?: ($request->source === 'manual' ? 'MANUAL OUT' : 'SCAN OUT');

            // Insert data ke tabel stock_out_logs
            StockOutEng::create([
                'transaction_out_id'   => $nextId,
                'nik'                  => Auth::user()->nim ?? Auth::user()->nik ?? '9999',
                'request_sparepart_id' => $request->request_sparepart_id ?? null,
                'barcode_id'           => $request->barcode_id ?? null,
                'stock_eng_id'         => $stock->id,
                'rak_id'               => $request->rak_id, // UPDATE: Mengunci penyimpanan data rak_id langsung ke log transaksi
                'qty_out'              => $request->qty_out,
                'status'               => 'SUCCESS',
                'remark'               => strtoupper($finalRemark),
                'comment'              => $request->comment ?? '-',
            ]);

            $stock->decrement('qty', $request->qty_out);

            DB::commit();
            return redirect()->route('eng.out')->with('success', 'Transaksi ' . $nextId . ' Berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error Sistem: ' . $e->getMessage());
        }
    }
}