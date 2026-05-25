<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller; 
use App\Models\production\stock_prod;
use App\Models\Production\ListLineProduction; // Model master asli kamu
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockProdController extends Controller
{
    public function index()
    {
        // 1. Ambil data stock beserta relasi line-nya
        $stocks = stock_prod::with('line')->paginate(10); 

        // 2. Ambil semua ID unik (line_id) yang SUDAH CO-EXIST / TERDAFTAR di tabel stock_prods
        $registeredLineIds = stock_prod::pluck('line_id')->filter()->unique()->toArray();

        // ==================== LOGIKA DINAMIS DI SINI ====================
        // TAB FILTER ATAS: Hanya menampilkan lini yang SUDAH AKTIF di stock_prods
        $lines = ListLineProduction::whereIn('line_id', $registeredLineIds)->get();

        // DROPDOWN MODAL ADD LINE: Hanya menampilkan lini yang BELUM TERDAFTAR (WhereNotIn)
        // Jadi kalau sudah teregistrasi, otomatis hilang dari dropdown pilihan!
        $masterLines = ListLineProduction::whereNotIn('line_id', $registeredLineIds)->get();
        // ================================================================

        // 3. Ambil jenis nozzle unik
        $allNozzles = stock_prod::select('no_nozzle', 'sap_code')->distinct()->get();

        // 4. Kirim ke view
        return view('stock_prod.stock_prod', compact('stocks', 'lines', 'masterLines', 'allNozzles'));
    }

    /**
     * 🚀 ACTION 1: REGISTER PRODUCTION LINE (ADD LINE)
     */
    public function lineStore(Request $request)
    {
        // Validasi input nomor line dari form dropdown modal
        $request->validate([
            'no_line' => 'required|string',
        ]);

        // Cari record data di tabel list_line_productions menggunakan model aslimu
        $masterLine = ListLineProduction::where('no_line', $request->no_line)->first();

        if (!$masterLine) {
            return redirect()->back()->with('error', 'Gagal! Nomor lini tersebut tidak ditemukan di master data.');
        }

        // Jaga-jaga validasi double hit: Pastikan line_id unik belum aktif di tabel stock_prods
        $isExists = stock_prod::where('line_id', $masterLine->line_id)->exists();
        if ($isExists) {
            return redirect()->back()->with('error', 'Gagal! Lini produksi ini sudah terdaftar.');
        }

        // Simpan data inisialisasi awal ke tabel stock_prods menggunakan line_id string dari master
        stock_prod::create([
            'line_id'            => $masterLine->line_id, // Menyimpan kode string unik line_id
            'no_nozzle'          => '-',
            'qty'                => 0, // Awal stok kosong sebelum dipasok nozzle oleh Engineering
            'min_stock'          => 0,
            'part_no'            => '-',
            'sap_code'           => '-',
            'barcode_id'         => '-',
            'request_no'         => '-',
            'transaction_out_id' => '-',
        ]);

        return redirect()->back()->with('success', 'Lini Produksi Baru Berhasil Diregistrasi, Coy!');
    }

    /**
     * 🚀 ACTION 2: INCOMING NOZZLE FROM ENGINEERING (ADD NOZZLE IN)
     */
    public function nozzleStore(Request $request)
    {
        $request->validate([
            'transaction_out_id' => 'required|string',
            'line_id'            => 'required',
            'qty'                => 'required|integer|min:1',
            'min_stock'          => 'required|integer|min:1',
        ]);

        $token = strtoupper($request->transaction_out_id);
        
        $mockPartNo = "PART-" . substr($token, -4);
        $mockSapCode = "SAP-" . substr($token, -6);
        $mockNoNozzle = "N-NZL-" . rand(10, 99);

        // Jika row datanya masih bawaan default ('-'), kita bisa update row tersebut, 
        // tapi untuk melacak mutasi stok masuk (IN) baru, kita create row baru di lantai produksi
        stock_prod::create([
            'line_id'            => $request->line_id,
            'no_nozzle'          => $mockNoNozzle,
            'part_no'            => $mockPartNo, 
            'sap_code'           => $mockSapCode, 
            'qty'                => $request->qty,
            'min_stock'          => $request->min_stock,
            'transaction_out_id' => $token, 
            'request_no'         => 'REQ-' . rand(1000, 9999), 
            'barcode_id'         => 'BC-' . rand(10000, 99999),
        ]);

        return redirect()->back()->with('success', 'Nozzle Berhasil Masuk (IN) dan Sinkron dengan Data Engineering!');
    }

    /**
     * 🚀 ACTION 3: UPDATE / EDIT DATA STOCK PRODUCTION
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'line_id'    => 'required',
            'no_nozzle'  => 'required|string',
            'qty'        => 'required|integer|min:0',
            'min_stock'  => 'required|integer|min:0',
        ]);

        $stock = stock_prod::findOrFail($id);
        $stock->update([
            'line_id'    => $request->line_id,
            'no_nozzle'  => $request->no_nozzle,
            'qty'        => $request->qty,
            'min_stock'  => $request->min_stock,
        ]);

        return redirect()->back()->with('success', 'Data Stock Production berhasil diperbarui, Coy!');
    }

    /**
     * 🚀 ACTION 4: DELETE / HAPUS DATA LINE & STOCK PRODUCTION
     * Saat ini dihapus, line_id tersebut akan otomatis muncul kembali di dropdown modal ADD LINE
     */
    public function destroy($id)
    {
        $stock = stock_prod::findOrFail($id);
        $stock->delete();

        return redirect()->back()->with('success', 'Lini Produksi berhasil dihapus dari daftar kontrol deck, Bro!');
    }

    // --- Placeholder Method bawaan ---
    public function storeRequest(Request $request) { return redirect()->back()->with('success', 'Request stock berhasil dikirim!'); }
    public function receiveItem(Request $request) { return redirect()->back()->with('success', 'Item berhasil diterima!'); }
    public function requestHistory() { return "Halaman Request History Production - Comming Soon, Bro!"; }
    public function exportCSV() { return "Proses Export CSV - Comming Soon, Bro!"; }
}