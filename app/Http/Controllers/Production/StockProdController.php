<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller; 
use App\Models\Production\stock_prod;
use App\Models\Production\ListLineProduction;
use App\Models\Engineering\StockOutEng; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockProdController extends Controller
{
    /**
     * Halaman Utama: Monitoring Stok Nozzle per Line Produksi
     */
    public function index()
    {
        // Hanya mengambil lini yang SUDAH terdaftar di table stock_prods (dipakai untuk Tab samping & list tabel)
        // Relasi 'line' digunakan untuk mengambil detail master nama line
        $lines = stock_prod::with(['line'])->get();

        // Mengambil semua template master line untuk opsi pilihan di Modal "ADD LINE"
        // Lini yang sudah terdaftar diabaikan agar tidak terjadi duplikasi pendaftaran line
        $registeredLineIds = $lines->pluck('line_id')->toArray();
        $masterLines = ListLineProduction::whereNotIn('id', $registeredLineIds)->get();

        // Mengambil seluruh log dari Engineering untuk opsi pilihan di Modal "ADD NOZZLE"
        $logs = StockOutEng::with(['stockEng'])
                           ->orderBy('created_at', 'desc')
                           ->get();
    
        return view('stock_prod.stock_prod', compact('lines', 'masterLines', 'logs'));
    }

    /**
     * Memproses Request dari Modal (ADD LINE & ADD NOZZLE terpisah lewat action_type)
     */
    public function nozzleStore(Request $request)
    {
        // Jalur 1: JIKA YANG DIKLIK ADALAH TOMBOL ADD LINE
        if ($request->input('action_type') === 'line') {
            $request->validate([
                'register_line_id' => 'required|integer'
            ]);

            // Buat record kosong baru di tabel stock_prod agar Line-nya muncul di tab & tabel
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

        // Jalur 2: JIKA YANG DIKLIK ADALAH TOMBOL ADD NOZZLE
        $request->validate([
            'line_id'          => 'required', 
            'stock_out_log_id' => 'required', 
            'qty'              => 'required|integer|min:1',
            'min_stock'        => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $log = StockOutEng::with('stockEng')->findOrFail($request->stock_out_log_id);
            
            // Cari data baris lini yang sudah diregistrasikan sebelumnya
            $stockProd = stock_prod::where('line_id', $request->line_id)->first();

            if (!$stockProd) {
                return redirect()->back()->with('error', 'Gagal! Daftarkan lini produksi terlebih dahulu menggunakan tombol ADD LINE.');
            }

            // Update baris kosongan tadi dengan data nozzle dari Engineering
            $stockProd->update([
                'no_nozzle' => $log->no_nozzle,
                'part_no'   => $log->stockEng->part_no ?? 'N/A',
                'sap_code'  => $log->stockEng->sap_code ?? 'N/A',
                'category'  => $log->stockEng->category ?? 'N/A',
                'qty'       => $request->qty, 
                'min_stock' => $request->min_stock,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Komponen Nozzle Berhasil Dialokasikan ke Lini Produksi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengalokasikan nozzle: ' . $e->getMessage());
        }
    }

    /**
     * Penyesuaian Kuantitas / Threshold di Line via Modal Edit
     */
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

    /**
     * Menghapus secara permanen baris pemantauan Lini dari tabel dashboard (Kembali Kosong)
     */
    public function destroy($id)
    {
        $stock = stock_prod::findOrFail($id);
        $stock->delete(); 

        return redirect()->back()->with('success', 'Lini Produksi Berhasil Dihapus dari Dashboard Pemantauan!');
    }

    public function exportCSV() 
    { 
        return redirect()->back()->with('error', 'Fitur Ekspor laporan sedang dikembangkan.'); 
    }
}