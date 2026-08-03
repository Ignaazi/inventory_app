<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StockEng; // Sesuaikan model stok
use App\Models\StockEngTransaction; // Sesuaikan model transaksi

class StockInEngineeringController extends Controller
{
    /**
     * Tampilan Halaman Scanner Kamera / Barcode Gun
     */
    public function scanIn()
    {
        return view('stock_eng.transactionEng.in_scan');
    }

    /**
     * Tampilan Halaman Input Manual
     */
    public function manualIn()
    {
        // Ambil data pendukung seperti list RAK atau Sparepart jika dibutuhkan untuk dropdown
        return view('stock_eng.transactionEng.in_manual');
    }

    /**
     * Proses Eksekusi Scan IN (AJAX / Form)
     */
    public function storeScan(Request $request)
    {
        $request->validate([
            'barcode_scan' => 'required|string',
        ]);

        $scannedCode = trim($request->barcode_scan);

        DB::beginTransaction();
        try {
            // Contoh Logika Tambah Stok Engineering
            // 1. Cari Barcode / Item di Database
            // 2. Sync Stock Increment
            // 3. Simpan Log Transaksi
            
            /* 
            StockEngTransaction::create([
                'nik'           => Auth::user()->nik ?? 'SYSTEM',
                'stock_eng_id' => $stockEngId,
                'qty_added'     => 1,
                'status'        => 'success',
                'remark'        => 'SCAN IN AUTOMATED',
                'process_type'  => 'scan',
                'tx_type'       => 'in',
            ]);
            */

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Stok berhasil ditambahkan via Scan IN!'
                ]);
            }

            return redirect()->route('eng.in.index')->with('success', 'Barang berhasil discan & stok bertambah!');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal memproses Scan: ' . $e->getMessage());
        }
    }

    /**
     * Proses Eksekusi Manual IN
     */
    public function storeManual(Request $request)
    {
        // Masukkan validasi & logika simpan manual IN sesuai kebutuhan sistem kamu
        return redirect()->route('eng.in.index')->with('success', 'Stok manual berhasil ditambahkan!');
    }
}