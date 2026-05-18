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
        $history = StockOutEng::with('stockEng')->latest()->paginate(10);
        return view('stock_eng.transaction.out', compact('history'));
    }

    /**
     * Simpan Transaksi & Potong Stok Otomatis
     */
    public function store(Request $request)
    {
        $request->validate([
            'stock_eng_id' => 'required|exists:stock_engs,id',
            'qty_out' => 'required|integer|min:1',
            'status' => 'required|in:SUCCESS,PENDING',
            'remark' => 'required|in:SCAN OUT,MANUAL OUT',
        ]);

        DB::beginTransaction();
        try {
            $stock = StockEng::findOrFail($request->stock_eng_id);

            if ($request->status === 'SUCCESS' && $stock->stock < $request->qty_out) {
                return redirect()->back()->with('error', 'Gagal! Stok di Engineering tidak mencukupi.');
            }

            // AUTO INCREMENT STRING GENERATOR (ENGOUT001, ENGOUT002, dst.)
            $latestTx = StockOutEng::latest()->first();
            if (!$latestTx) {
                $nextId = 'ENGOUT001';
            } else {
                $number = (int) substr($latestTx->transaction_out_id, 6);
                $nextId = 'ENGOUT' . str_pad($number + 1, 3, '0', STR_PAD_LEFT);
            }

            // Insert data ke tabel stock_out_logs via Eloquent
            StockOutEng::create([
                'transaction_out_id'   => $nextId,
                'nik'                  => Auth::user()->nim ?? Auth::user()->nik ?? '9999',
                'request_sparepart_id' => $request->request_sparepart_id ?? null,
                'barcode_id'           => $request->barcode_id ?? null,
                'stock_eng_id'         => $stock->id,
                'qty_out'              => $request->qty_out,
                'status'               => $request->status,
                'remark'               => $request->remark,
                'comment'              => $request->comment ?? '-',
            ]);

            // Potong stok utama di table stock_engs jika status SUCCESS
            if ($request->status === 'SUCCESS') {
                $stock->decrement('stock', $request->qty_out);
            }

            DB::commit();
            return redirect()->route('eng.out.index')->with('success', 'Transaksi ' . $nextId . ' Berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error Sistem: ' . $e->getMessage());
        }
    }
}