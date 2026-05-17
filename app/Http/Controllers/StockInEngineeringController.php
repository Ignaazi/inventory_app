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
                    ->paginate(10); 

        return view('stock_eng.transaction.in_manual', compact('stocks', 'history'));
    }

    /**
     * Tampilan untuk Scan Barcode
     */
    public function scan()
    {
        $stocks = StockEng::orderBy('no_nozzle', 'asc')->get();

        $history = StockInEng::with('stockEng')
                    ->latest()
                    ->paginate(10);

        return view('stock_eng.transaction.in_scan', compact('stocks', 'history'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input (Ditambahkan aturan untuk 'comment')
        $request->validate([
            'stock_eng_id' => 'required|exists:stock_engs,id',
            'qty_in' => 'required|integer|min:1',
            'remark' => 'nullable|string',
            'comment' => 'nullable|string' // FIX 1: Validasi kolom comment baru
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
        // Kolom 'remark' otomatis dari sistem, 'comment' berisi catatan dari user
        StockInEng::create([
            'stock_eng_id' => $stock->id,
            'nik' => Auth::user()->nim, 
            'qty_added' => $request->qty_in,
            'status' => 'Success',
            'remark' => $finalRemark,
            'comment' => $request->comment, // FIX 2: Simpan data komentar ke DB
        ]);

        return redirect()->route('eng.in')->with('success', 'Stock In Berhasil dicatat!');
    }   
}