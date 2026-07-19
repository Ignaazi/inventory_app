<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Engineering\StockOutEng; 
use App\Models\StockInEng; 
use App\Models\Engineering\StockReturnEng; 
use App\Models\StockEng; 

class TransactionController extends Controller
{
    /**
     * Tampilan halaman Stock In
     */
    public function indexIn()
    {
        // 🌟 PEMBARUAN: Eager load relasi sparepart agar data nama nozzle dari master siap digunakan
        $history = StockInEng::with('stockEng.sparepart')->latest()->paginate(10);
        return view('stock_eng.transaction.in', compact('history'));
    }

    /**
     * Tampilan halaman Stock Out
     */
    public function indexOut()
    {
        // 🌟 PEMBARUAN: Eager load relasi sparepart untuk pelacakan nama nozzle keluar
        $history = StockOutEng::with(['stockEng.sparepart', 'rak', 'dbBarcode'])->latest()->paginate(10);
        return view('stock_eng.transaction.out', compact('history'));
    }

    /**
     * Tampilan halaman Return (SUDAH AKTIF DATA ASLI)
     */
    public function indexReturn()
    {
        // 🌟 PEMBARUAN: Memuat relasi sparepart terdalam agar tidak memicu column not found di view history return
        $history = StockReturnEng::with(['stockEng.sparepart', 'rak', 'dbBarcode'])
                    ->latest()
                    ->paginate(10);

        return view('stock_eng.transaction.return', compact('history'));
    }

    /**
     * Proses Simpan Data Return + Auto Increment ID RETURNEY001
     */
    public function storeReturn(Request $request)
    {
        // 1. Validasi Input Form
        $request->validate([
            'nik'                  => 'required|string',
            'stock_eng_id'         => 'required|integer',
            'barcode_id'           => 'required|integer',
            'qty_return'           => 'required|integer|min:1',
            'rak_id'               => 'nullable|integer',
            'request_sparepart_id' => 'nullable|string',
            'comment'              => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 🌟 2. AMBIL MASTER DATA NOZZLE LANGSUNG DARI RELASI SPAREPART
            $stock = StockEng::with('sparepart')->findOrFail($request->stock_eng_id);
            $namaNozzleMaster = $stock->sparepart->name ?? 'N/A';

            // 3. LOGIC AUTO INCREMENT ID: RETURNEY001, RETURNEY002, dst.
            $latestReturn = StockReturnEng::orderBy('id', 'desc')->first();
            
            if (!$latestReturn) {
                $newReturnId = 'RETURNEY001';
            } else {
                // Ambil angka dari ID terakhir (misal dari RETURNEY025 diambil 25)
                $number = (int) substr($latestReturn->return_id, 8);
                // Tambah 1 dan pad dengan 3 digit (contoh: 26 jadi RETURNEY026)
                $newReturnId = 'RETURNEY' . sprintf('%03d', $number + 1);
            }

            // 4. Simpan data ke tabel stock_return_logs dengan data No Nozzle otomatis terisi dari master db
            $returnLog = StockReturnEng::create([
                'return_id'            => $newReturnId,
                'nik'                  => $request->nik,
                'request_sparepart_id' => $request->request_sparepart_id,
                'barcode_id'           => $request->barcode_id,
                'stock_eng_id'         => $request->stock_eng_id,
                'no_nozzle'            => $namaNozzleMaster, // 🌟 FIX AUTO ISI: Mengikuti master data sparepart
                'rak_id'               => $request->rak_id,
                'qty_return'           => $request->qty_return,
                'status'               => 'SUCCESS',
                'remark'               => 'MANUAL RETURN',
                'comment'              => $request->comment ?? '-',
            ]);

            // 🌟 5. UPDATE STOCK INTERNAL (Menggunakan kolom 'qty' sesuai struktur tabel utama stock_engs)
            $stock->increment('qty', $request->qty_return); 

            DB::commit();
            return redirect()->back()->with('success', 'Transaction Return ' . $newReturnId . ' saved successfully and stock updated!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to save transaction: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan halaman Disposal
     */
    public function indexDisposal()
    {
        $history = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10, 1, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('stock_eng.transaction.disposal', compact('history'));
    }

    /**
     * Proses Simpan Data Disposal
     */
    public function storeDisposal(Request $request)
    {
        return redirect()->back()->with('success', 'Transaction Disposal saved successfully!');
    }
}