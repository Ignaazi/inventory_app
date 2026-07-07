<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller; 
use App\Models\Production\stock_prod;
use App\Models\Production\ListLineProduction;
use App\Models\StockEng; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockProdController extends Controller
{
    /**
     * Halaman Utama: Monitoring Stok Nozzle per Line Produksi
     */
    public function index()
    {
        // Panggil seluruh master line beserta relasi stoknya
        $lines = ListLineProduction::with(['stocks'])->orderBy('line_id', 'asc')->get();

        // Ambil data master stock dari engineering untuk modal ADD NOZZLE
        $stockEngs = StockEng::orderBy('no_nozzle', 'asc')->get();
    
        // Kirim $lines (ListLineProduction) dan $stockEngs ke view
        return view('stock_prod.stock_prod', compact('lines', 'stockEngs'));
    }

    /**
     * Memproses Request dari Modal (ADD LINE & ADD NOZZLE terpisah)
     */
    public function nozzleStore(Request $request)
    {
        if ($request->input('action_type') === 'line') {
            // ==========================================
            // JALUR 1: ADD LINE
            // ==========================================
            $request->validate([
                'register_line_id' => 'required'
            ]);

            // Cek apakah line_id ini sudah terdaftar tanpa nozzle di tabel stock_prods
            $isExist = stock_prod::where('line_id', $request->register_line_id)
                                 ->whereNull('no_nozzle')
                                 ->exists();
            if ($isExist) {
                return redirect()->back()->with('error', 'Lini Produksi tersebut sudah terdaftar!');
            }

            stock_prod::create([
                'line_id'    => $request->register_line_id,
                'no_nozzle'  => null,
                'part_no'    => null,
                'sap_code'   => null,
                'category'   => null,
                'qty'        => 0,
                'min_stock'  => 0,
            ]);
            return redirect()->back()->with('success', 'Lini Produksi Baru Berhasil Didaftarkan ke Sistem!');
        }

        // ==========================================
        // JALUR 2: ADD NOZZLE (Alokasi Master Komponen ke Line)
        // ==========================================
        $request->validate([
            'line_id'      => 'required', 
            'stock_eng_id' => 'required',
            'qty'          => 'required|integer|min:1',
            'min_stock'    => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $engItem = StockEng::findOrFail($request->stock_eng_id);
            
            // 🔥 PERBAIKAN: Cari baris berdasarkan Kombinasi Line ID DAN No Nozzle agar data tidak teroverwrite!
            $stockProd = stock_prod::where('line_id', $request->line_id)
                                    ->where('no_nozzle', $engItem->no_nozzle)
                                    ->first();

            if ($stockProd) {
                // Jika jenis nozzle tersebut sudah ada di line ini, kita akumulasikan qty-nya
                $stockProd->increment('qty', $request->qty);
                $stockProd->update(['min_stock' => $request->min_stock]);
            } else {
                // Cek apakah ada record dummy (line baru yang no_nozzle-nya masih null) untuk dibersihkan/dipakai
                $dummyLine = stock_prod::where('line_id', $request->line_id)
                                       ->whereNull('no_nozzle')
                                       ->first();
                
                if ($dummyLine) {
                    // Pakai record dummy yang sudah ada
                    $dummyLine->update([
                        'no_nozzle' => $engItem->no_nozzle,
                        'part_no'   => $engItem->part_no ?? 'N/A',
                        'sap_code'  => $engItem->sap_code ?? 'N/A',
                        'category'  => $engItem->category ?? 'N/A',
                        'qty'       => $request->qty,
                        'min_stock' => $request->min_stock,
                    ]);
                } else {
                    // Buat record pasang baru secara mandiri
                    stock_prod::create([
                        'line_id'    => $request->line_id,
                        'no_nozzle'  => $engItem->no_nozzle,
                        'part_no'    => $engItem->part_no ?? 'N/A',
                        'sap_code'   => $engItem->sap_code ?? 'N/A',
                        'category'   => $engItem->category ?? 'N/A',
                        'qty'        => $request->qty,
                        'min_stock'  => $request->min_stock,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Komponen Nozzle Berhasil Dialokasikan ke Lini Produksi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengalokasikan nozzle: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'qty'        => 'required|integer|min:0',
            'min_stock'  => 'required|integer|min:0',
        ]);

        $stock = stock_prod::findOrFail($id);
        $stock->update([
            'qty'        => $request->qty,
            'min_stock'  => $request->min_stock,
        ]);

        return redirect()->back()->with('success', 'Kapasitas Stok Produksi Berhasil Disesuaikan!');
    }

    public function destroy($id)
    {
        $stock = stock_prod::findOrFail($id);
        $stock->delete(); 

        return redirect()->back()->with('success', 'Stok Nozzle pada Lini Berhasil Dikosongkan!');
    }

    public function exportCSV() { return redirect()->back()->with('error', 'Fitur Ekspor dalam pengembangan.'); }
}