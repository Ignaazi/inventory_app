<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Engineering\StockOutEng; 
use App\Models\StockInEng; 
// 🚀 IMPORT MODEL BARU UNTUK RETURN
use App\Models\Engineering\StockReturnEng; 
use App\Models\StockEng; // Digunakan jika lu mau update stock internal saat barang di-return

class TransactionController extends Controller
{
    /**
     * Tampilan halaman Stock In
     */
    public function indexIn()
    {
        $history = StockInEng::with('stockEng')->latest()->paginate(10);
        return view('stock_eng.transaction.in', compact('history'));
    }

    /**
     * Tampilan halaman Stock Out
     */
    public function indexOut()
    {
        $history = StockOutEng::with(['stockEng', 'rak', 'dbBarcode'])->latest()->paginate(10);
        return view('stock_eng.transaction.out', compact('history'));
    }

    /**
     * Tampilan halaman Return (SUDAH AKTIF DATA ASLI)
     */
    public function indexReturn()
    {
        // 🚀 Tarik data asli dari tabel stock_return_logs beserta relasi lengkapnya
        $history = StockReturnEng::with(['stockEng', 'rak', 'dbBarcode'])
                    ->latest()
                    ->paginate(10);

        return view('stock_eng.transaction.return', compact('history'));
    }

    /**
     * Proses Simpan Data Return + Auto Increment ID RETURNEY001
     */
    public function storeReturn(Request $request)
    {
        // 1. Validasi Input Form (Sesuaikan dengan field di view lu)
        $request->validate([
            'nik'                  => 'required|string',
            'stock_eng_id'         => 'required|integer',
            'barcode_id'           => 'required|integer',
            'qty_return'           => 'required|integer|min:1',
            'rak_id'               => 'nullable|integer',
            'no_nozzle'            => 'nullable|string',
            'request_sparepart_id' => 'nullable|string',
            'comment'              => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 2. LOGIC AUTO INCREMENT ID: RETURNEY001, RETURNEY002, dst.
            $latestReturn = StockReturnEng::orderBy('id', 'desc')->first();
            
            if (!$latestReturn) {
                $newReturnId = 'RETURNEY001';
            } else {
                // Ambil angka dari ID terakhir (misal dari RETURNEY025 diambil 25)
                $number = (int) substr($latestReturn->return_id, 8);
                // Tambah 1 dan pad dengan 3 digit (contoh: 26 jadi RETURNEY026)
                $newReturnId = 'RETURNEY' . sprintf('%03d', $number + 1);
            }

            // 3. Simpan data ke tabel stock_return_logs
            $returnLog = StockReturnEng::create([
                'return_id'            => $newReturnId,
                'nik'                  => $request->nik,
                'request_sparepart_id' => $request->request_sparepart_id,
                'barcode_id'           => $request->barcode_id,
                'stock_eng_id'         => $request->stock_eng_id,
                'no_nozzle'            => $request->no_nozzle,
                'rak_id'               => $request->rak_id,
                'qty_return'           => $request->qty_return,
                'status'               => 'SUCCESS',
                'remark'               => 'MANUAL RETURN',
                'comment'              => $request->comment ?? '-',
            ]);

            // 4. OPTIONAL LOGIC (Menambah kembali stock engineering yang dikembalikan)
            $stock = StockEng::find($request->stock_eng_id);
            if ($stock) {
                $stock->increment('stock', $request->qty_return); // Auto nambah stok di master tabel
            }

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