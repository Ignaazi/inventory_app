<?php

namespace App\Http\Controllers;

use App\Models\StockEng;
use App\Models\StockInEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockInEngineeringController extends Controller
{
    public function index()
    {
        // Ambil data log, urutkan dari yang terbaru (latest)
        $history = StockInEng::with('stockEng')
                    ->latest()
                    ->paginate(25);

        return view('stock_eng.transaction.in', compact('history'));
    }

    /**
     * Tampilan untuk Input Manual
     */
    public function manual()
    {
        // Ambil data nozzle untuk dropdown di form manual
        $stocks = StockEng::orderBy('no_nozzle', 'asc')->get();

        // Ambil data history supaya tabel di bawah form manual tidak error
        $history = StockInEng::with('stockEng')
                    ->latest()
                    ->paginate(10); // Kita batasi 10 saja untuk form manual

        return view('stock_eng.transaction.in_manual', compact('stocks', 'history'));
    }

    /**
     * Tampilan untuk Scan Barcode
     */
    public function scan()
    {
        // Jika di halaman scan lo juga memanggil tabel history, ambil datanya di sini
        $history = StockInEng::with('stockEng')
                    ->latest()
                    ->paginate(10);

        return view('stock_eng.transaction.in_scan', compact('history'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'stock_eng_id' => 'required|exists:stock_engs,id',
            'qty_in' => 'required|integer|min:1',
            'remark' => 'nullable|string',
            'source' => 'nullable|string'
        ]);

        // 2. Update Stok Utama
        $stock = StockEng::findOrFail($request->stock_eng_id);
        $stock->increment('qty', $request->qty_in);

        // 3. Logika Penentuan Remark Otomatis
        $finalRemark = $request->remark;
        
        if (empty($finalRemark)) {
            if ($request->source === 'manual') {
                $finalRemark = 'Manual IN';
            } elseif ($request->source === 'scan') {
                $finalRemark = 'Scan IN';
            } else {
                $finalRemark = 'System IN'; 
            }
        }

        // 4. Catat transaksi ke tabel Log (StockInEng)
        // Menggunakan NIM sesuai spek skripsi lo
        StockInEng::create([
            'stock_eng_id' => $stock->id,
            'nik' => Auth::user()->nim, 
            'qty_added' => $request->qty_in,
            'status' => 'Success',
            'remark' => $finalRemark,
        ]);

        return redirect()->route('eng.in')->with('success', 'Stock In Berhasil dicatat!');
    }   
}