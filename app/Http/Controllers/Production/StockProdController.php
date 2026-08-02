<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Production\stock_prod; 
use App\Models\ListSparepartEng; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StockProdController extends Controller
{
    private function getLineModel()
    {
        $modelUtama = 'App\\Models\\ListLineProduction';
        $modelSubFolder = 'App\\Models\\Production\\ListLineProduction';
        return class_exists($modelUtama) ? $modelUtama : $modelSubFolder;
    }

    /**
     * Menampilkan data utama & memisahkan Line Active vs Available
     */
    public function index()
    {
        $lineModel = $this->getLineModel();
        
        // 1. Ambil semua ID line yang sudah terdaftar/terpakai di tabel stock_prods
        $usedLineIds = stock_prod::pluck('line_id')->unique()->toArray();

        // 2. Line yang SUDAH AKTIF (Untuk nampilin TAB di samping "All Lines")
        $activeLines = $lineModel::whereIn('id', $usedLineIds)->orderBy('no_line', 'asc')->get();

        // 3. Line Master yang BELUM AKTIF (Untuk isi dropdown di dalam modal Add New Line)
        $availableLines = $lineModel::whereNotIn('id', $usedLineIds)->orderBy('no_line', 'asc')->get();

        // 4. Semua Line (Untuk modal Add Nozzle biasa)
        $allLines = $lineModel::orderBy('no_line', 'asc')->get();

        $stocks = stock_prod::with(['line', 'sparepart'])->orderBy('created_at', 'desc')->paginate(25);
        $ListSparepartEng = ListSparepartEng::orderBy('sparepart_id', 'asc')->get(); 
        
        return view('stock_prod.stock_prod', compact('stocks', 'activeLines', 'availableLines', 'allLines', 'ListSparepartEng'));
    }

    /**
     * Tombol "Add New Line" - Mendaftarkan Line Baru ke tabel stock_prods (Hanya 1 Input Dropdown)
     */
    public function lineStore(Request $request)
    {
        try {
            $request->validate([
                'line_id' => 'required', // ID dari tabel list_line_productions
            ]);

            // Ambil sparepart pertama di database buat pemicu awal (karena DB lu NOT NULL)
            $firstSparepart = ListSparepartEng::first();
            if (!$firstSparepart) {
                return redirect()->back()->with('error', 'Gagal! Isi master sparepart dulu di sistem baru bisa daftarin line.');
            }

            // Daftarkan langsung ke tabel stock_prods biar permanen
            $stock = new stock_prod();
            $stock->line_id = $request->line_id;
            $stock->sparepart_id = $firstSparepart->id;
            $stock->qty = 0;
            $stock->min_stock = 0;
            $stock->save();

            return redirect()->back()->with('success', 'Line Berhasil Didaftarkan ke Stock Prod & Tab Aktif!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendaftarkan line: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan data Nozzle Baru (Modal Add Nozzle biasa)
     */
    public function nozzleStore(Request $request)
    {
        try {
            $tableSparepart = (new ListSparepartEng)->getTable();
            $lineModel = $this->getLineModel();
            $tableLine = (new $lineModel)->getTable();

            $request->validate([
                'line_id'      => 'required|exists:' . $tableLine . ',id',
                'sparepart_id' => 'required|exists:' . $tableSparepart . ',id', 
                'qty'          => 'required|numeric|min:0',
                'min_stock'    => 'required|numeric|min:0',
            ]);
        
            // Jika line diinisialisasi dengan qty=0 dan sparepart yang sama, kita update aja biar gak duplikat
            $existingEmptyStock = stock_prod::where('line_id', $request->line_id)
                ->where('sparepart_id', $request->sparepart_id)
                ->where('qty', 0)
                ->first();

            if ($existingEmptyStock) {
                $existingEmptyStock->qty = $request->qty;
                $existingEmptyStock->min_stock = $request->min_stock;
                $existingEmptyStock->save();
                return redirect()->back()->with('success', 'Data Nozzle Berhasil Ditambahkan ke Line!');
            }

            // Validasi normal jika kombinasi sudah ada
            $isDuplicate = stock_prod::where('line_id', $request->line_id)
                ->where('sparepart_id', $request->sparepart_id)
                ->exists();

            if ($isDuplicate) {
                return redirect()->back()->withErrors(['sparepart_id' => 'Nozzle ini sudah terdaftar di Line tersebut.'])->withInput();
            }

            $stock = new stock_prod();
            $stock->line_id = $request->line_id;
            $stock->sparepart_id = $request->sparepart_id; 
            $stock->qty = $request->qty;
            $stock->min_stock = $request->min_stock;
            $stock->save();
        
            return redirect()->back()->with('success', 'Data Nozzle Berhasil Didaftarkan!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $stock = stock_prod::findOrFail($id);
            $stock->line_id      = $request->line_id;
            $stock->sparepart_id = $request->sparepart_id;
            $stock->qty          = $request->qty;
            $stock->min_stock    = $request->min_stock;
            $stock->save();

            return redirect()->back()->with('success', 'Data Stok Berhasil Diubah!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $stock = stock_prod::findOrFail($id);
        $stock->delete();
        return redirect()->back()->with('success', 'Data Berhasil Dihapus');
    }

    public function lineDestroy($id) { return redirect()->back(); }
    public function exportCSV() { }
    public function storeRequest(Request $request) { return redirect()->back(); }
    public function receiveItem(Request $request) { return redirect()->back(); }
    public function requestHistory() { return redirect()->back(); }
}