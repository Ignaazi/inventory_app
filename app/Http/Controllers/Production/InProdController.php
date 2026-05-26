<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Production\inProd;       // Mengarah ke Models/Production/inProd.php
use App\Models\Production\stock_prod;   // Mengarah ke Models/Production/stock_prod.php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InProdController extends Controller
{
    /**
     * Menampilkan halaman History Stock In Production
     * MURNI TERHUBUNG KE DATABASE INPROD_LOGS TANPA DUMMY MOCK DATA
     */
    public function stockIn() 
    {
        // 💡 KODE ASLI SUDAH AKTIF: Mengambil data langsung dari tabel database inProd_logs
        // Menggunakan latest() agar data yang baru di-input berada di posisi paling atas tabel
        $history = inProd::latest('inproduction_id')->paginate(10);
        
        return view('stock_prod.transactionProd.inProd', compact('history'));
    }

    /**
     * Menampilkan halaman Stock Out Production
     */
    public function stockOut() 
    {
        return view('stock_prod.transactionProd.outProd');
    }

    /**
     * Menyimpan data transaksi stock masuk (Production In)
     * Ditambah trigger otomatis untuk menambahkan jumlah Qty pada tabel stock_prods
     */
    public function store(Request $request) 
    {
        // Validasi data input sesuai entitas target integrasi migration baru
        $request->validate([
            'line_id'            => 'required',
            'no_nozzle'          => 'required|string',
            'transaction_out_id' => 'required',
            'barcode_id'         => 'required',
            'stock_prod_id'      => 'required', 
            'qty_in'             => 'required|numeric|min:1',
        ]);

        // Menggunakan DB Transaction agar eksekusi log & penambahan qty seimbang
        DB::beginTransaction();
        try {
            // 1. Catat transaksi baru ke tabel inProd_logs (Isinya presisi sesuai struktur file migration)
            inProd::create([
                'nik'                => Auth::user()->nik ?? Auth::user()->nim ?? 'UNKNOWN', // Mengakomodasi login NIM/NIK kamu
                'line_id'            => $request->line_id,
                'no_nozzle'          => $request->no_nozzle,
                'transaction_out_id' => $request->transaction_out_id,
                'request_no'         => $request->request_no, // Mengisi kolom penghubung 3 arah jika ada
                'barcode_id'         => $request->barcode_id,
                'stock_prod_id'      => $request->stock_prod_id, 
                'qty_in'             => $request->qty_in,
                'status'             => 'success',
                'remark'             => $request->remark ?? 'scan in',
                'comment'            => $request->comment
            ]);

            // 2. TRIGGER LOGIC: Otomatis menambah stock qty pada DB stock_prods 
            $stockProd = stock_prod::findOrFail($request->stock_prod_id);
            $stockProd->increment('qty', $request->qty_in); 

            DB::commit();
            return back()->with('success', 'Transaksi Production In Berhasil Dicatat & Stock Qty Line Otomatis Bertambah!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
}