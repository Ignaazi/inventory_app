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
    /**
     * Helper internal untuk mendeteksi lokasi Model ListLineProduction otomatis
     */
    private function getLineModel()
    {
        $modelUtama = 'App\\Models\\ListLineProduction';
        $modelSubFolder = 'App\\Models\\Production\\ListLineProduction';
        return class_exists($modelUtama) ? $modelUtama : $modelSubFolder;
    }

    /**
     * 1. Route: stock.prod.index
     * Menampilkan data stok lantai produksi dan memuat relasi line & sparepart
     */
    public function index()
    {
        $lineModel = $this->getLineModel();
        $lines = $lineModel::all();

        // Memuat relasi 'line' dan 'sparepart' terpusat
        $stocks = stock_prod::with(['line', 'sparepart'])->orderBy('created_at', 'desc')->paginate(25);
        $ListSparepartEng = ListSparepartEng::orderBy('sparepart_id', 'asc')->get(); 
        
        // Mengarah tepat ke views/stock_prod/stock_prod.blade.php
        return view('stock_prod.stock_prod', compact('stocks', 'lines', 'ListSparepartEng'));
    }

    /**
     * 2. Route: stock.prod.nozzle.store (ADD NOZZLE IN / ALOKASI BARU)
     * Menyimpan sparepart baru ke line produksi tertentu dengan validasi duplikasi
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
        
            // VALIDASI DUPLIKASI ALOKASI LINE
            $isDuplicate = stock_prod::where('line_id', $request->line_id)
                ->where('sparepart_id', $request->sparepart_id)
                ->exists();

            if ($isDuplicate) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['sparepart_id' => 'Gagal! Sparepart ini sudah dialokasikan di Line tersebut. Silakan pilih Line lain atau update kuantitas yang ada.']);
            }

            $stock = new stock_prod();
            $stock->line_id = $request->line_id;
            $stock->sparepart_id = $request->sparepart_id; 
            $stock->qty = $request->qty;
            $stock->min_stock = $request->min_stock;
            $stock->save();
        
            return redirect()->back()->with('success', 'Data Sparepart Line Berhasil Disimpan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error("Gagal simpan stock prod: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * 3. Route: stock.prod.update
     * Mengubah kuantitas atau threshold data stok lini produksi
     */
    public function update(Request $request, $id)
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

            $stock = stock_prod::findOrFail($id);
            
            $lineId = $request->line_id;
            $sparepartId = $request->sparepart_id;

            // VALIDASI DUPLIKASI SAAT UPDATE LINE & SPAREPART
            $isDuplicate = stock_prod::where('id', '!=', $id)
                ->where('line_id', $lineId)
                ->where('sparepart_id', $sparepartId)
                ->exists();

            if ($isDuplicate) {
                return redirect()->back()->with('error', 'Gagal Update! Kombinasi Sparepart dan Line tersebut sudah terdaftar di baris data lain.');
            }

            $stock->line_id      = $lineId;
            $stock->sparepart_id = $sparepartId;
            $stock->qty          = $request->qty;
            $stock->min_stock    = $request->min_stock;
            $stock->save();

            return redirect()->back()->with('success', 'Data Stok Produksi Berhasil Diubah!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error("Gagal update stock prod: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * 4. Route: stock.prod.destroy
     * Menghapus baris data alokasi stok di lantai produksi
     */
    public function destroy($id)
    {
        $stock = stock_prod::findOrFail($id);
        $stock->delete();
        return redirect()->back()->with('success', 'Data Berhasil Dihapus');
    }

    /**
     * 5. Route: stock.prod.line.store (ADD LINE)
     * Menambahkan data lini produksi baru
     */
    public function lineStore(Request $request)
    {
        $lineModel = $this->getLineModel();
        $tableLine = (new $lineModel)->getTable();

        $request->validate([
            'line_id'      => 'required|unique:' . $tableLine . ',line_id',
            'no_line'      => 'required',
            'name_machine' => 'required',
        ]);

        // FIXED: Menggunakan instansiasi manual objek untuk mencegah MassAssignmentException jika $fillable belum diatur di model
        $line = new $lineModel();
        $line->user_id      = Auth::user()->nik ?? $request->user_id ?? '-';
        $line->line_id      = strtoupper($request->line_id);
        $line->no_line      = $request->no_line;
        $line->name_machine = strtoupper($request->name_machine);
        $line->save();

        return redirect()->back()->with('success', 'Line Produksi Baru Berhasil Ditambahkan');
    }

    /**
     * 6. Route: stock.prod.line.destroy (DELETE LINE)
     * Menghapus data lini produksi tertentu
     */
    public function lineDestroy($id)
    {
        $lineModel = $this->getLineModel();
        $line = $lineModel::findOrFail($id);
        
        // Opsional: Cek jika ada stock yang masih terikat ke line ini sebelum dihapus
        $hasStocks = stock_prod::where('line_id', $id)->exists();
        if ($hasStocks) {
            return redirect()->back()->with('error', 'Gagal menghapus! Masih ada data nozzle aktif yang terdaftar di Line ini.');
        }

        $line->delete();
        return redirect()->back()->with('success', 'Line Produksi Berhasil Dihapus!');
    }

    /**
     * 7. Route: stock.prod.export.csv
     * Mengunduh rekap inventory lantai produksi menjadi file CSV
     */
    public function exportCSV()
    {
        $fileName = 'production_floor_inventory_' . date('Ymd_His') . '.csv';
        $tasks = stock_prod::with(['line', 'sparepart'])->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('No Line', 'Machine Name', 'Sparepart ID', 'Part No', 'Sap Code', 'Qty', 'Min Stock');

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                fputcsv($file, array(
                    $task->line->no_line ?? '-',
                    $task->line->name_machine ?? '-',
                    $task->sparepart->sparepart_id ?? '-',         
                    $task->sparepart->part_number ?? ($task->sparepart->part_no ?? '-'),  
                    $task->sparepart->sap_code ?? '-',     
                    $task->qty,
                    $task->min_stock
                ));
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * =========================================================================
     * TRANSAKSI REQUEST & RECEIVE PLACEHOLDERS
     * Menjaga tombol aksi view tetap berjalan normal tanpa eror
     * =========================================================================
     */

    /**
     * Route: stock.prod.request.store
     */
    public function storeRequest(Request $request)
    {
        // Logika penarikan/permintaan barang dari Gudang Engineering ke Produksi
        return redirect()->back()->with('success', 'Permintaan unit sparepart berhasil dikirim ke Engineering!');
    }

    /**
     * Route: stock.prod.receive
     */
    public function receiveItem(Request $request)
    {
        // Logika penerimaan serah terima barang untuk menambah fisik stok lini produksi
        return redirect()->back()->with('success', 'Sparepart berhasil diterima dan menambah stok lini!');
    }

    /**
     * Route: stock.prod.request.history
     */
    public function requestHistory()
    {
        // Mengarahkan ke history log request material
        return redirect()->back()->with('info', 'Halaman History Request sedang dalam pengembangan.');
    }
}