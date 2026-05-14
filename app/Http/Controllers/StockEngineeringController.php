<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockEng;
use App\Models\Rak; 
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk cek error di log

class StockEngineeringController extends Controller
{
    public function index()
    {
        $raks = Rak::all();
        $stocks = StockEng::with('rak')->orderBy('created_at', 'desc')->paginate(25);

        return view('stock_eng.index', compact('stocks', 'raks'));
    }

    /**
     * Menampilkan halaman Transaction IN
     * Menampilkan data stock untuk pilihan manual & history update terbaru
     */
    public function indexIn()
    {
        // Mengambil semua data untuk kebutuhan dropdown manual & pencarian scan
        $stocks = StockEng::all();

        // Mengambil 5 data yang baru saja diupdate qty-nya untuk history di samping
        $recent_logs = StockEng::orderBy('updated_at', 'desc')->take(5)->get();

        return view('stock_eng.transaction.in', compact('stocks', 'recent_logs'));
    }

    /**
     * Memproses penambahan stok (QTY IN)
     */
    public function updateStockIn(Request $request)
    {
        try {
            $request->validate([
                'stock_id' => 'required|exists:stock_engs,id',
                'qty_in'   => 'required|numeric|min:1'
            ]);

            $stock = StockEng::findOrFail($request->stock_id);
            
            $oldQty = $stock->qty;
            // Menambah qty yang sudah ada dengan input qty baru
            $stock->qty = $oldQty + $request->qty_in;
            $stock->save();

            // Flash session untuk memunculkan angka penambahan di history (opsional)
            session()->flash('last_in_' . $stock->id, $request->qty_in);

            return redirect()->back()->with('success', "Stok {$stock->no_nozzle} berhasil ditambah! ({$oldQty} -> {$stock->qty})");

        } catch (\Exception $e) {
            Log::error("Gagal update stok IN: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'rak_id'    => 'required|exists:raks,id',
                'no_nozzle' => 'required',
                'qty'       => 'required|numeric',
                'min_stock' => 'required|numeric',
                'part_no'   => 'nullable',
                'sap_code'  => 'nullable',
                'category'  => 'nullable',
            ]);
        
            // Cara simpan manual (lebih aman untuk debugging)
            $stock = new StockEng();
            $stock->rak_id = $request->rak_id;
            $stock->no_nozzle = $request->no_nozzle;
            $stock->part_no = $request->part_no;
            $stock->sap_code = $request->sap_code;
            $stock->category = $request->category;
            $stock->qty = $request->qty;
            $stock->min_stock = $request->min_stock;
            $stock->save();
        
            return redirect()->back()->with('success', 'Data Nozzle Berhasil Disimpan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Kalau "geter" doang, berarti error validasi nyangkut di sini
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            // Log error ke storage/logs/laravel.log jika ada masalah database
            Log::error("Gagal simpan nozzle: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function storeRak(Request $request)
    {
        $request->validate([
            'nama_rak' => 'required|unique:raks,nama_rak',
        ]);

        // Berdasarkan screenshot lu, kolomnya adalah 'lokasi'
        Rak::create([
            'nama_rak' => $request->nama_rak,
            'lokasi'   => $request->lokasi ?? '-' 
        ]);

        return redirect()->back()->with('success', 'Rak Baru Berhasil Ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $stock = StockEng::findOrFail($id);
        // Pastikan model StockEng punya $fillable untuk update()
        $stock->update($request->all());
        return redirect()->back()->with('success', 'Data Berhasil Diupdate');
    }

    public function destroy($id)
    {
        $stock = StockEng::findOrFail($id);
        $stock->delete();
        return redirect()->back()->with('success', 'Data Berhasil Dapus');
    }

    public function export()
    {
        $fileName = 'inventory_nozzle_' . date('Ymd_His') . '.csv';
        $tasks = StockEng::with('rak')->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('No Rak', 'No Nozzle', 'Part No', 'Sap Code', 'Category', 'Qty', 'Min Stock');

        $callback = function() use($tasks, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                fputcsv($file, array(
                    $task->rak->nama_rak ?? '-',
                    $task->no_nozzle,
                    $task->part_no,
                    $task->sap_code,
                    $task->category,
                    $task->qty,
                    $task->min_stock
                ));
            }
            fclose($file);
        };

        
        return response()->stream($callback, 200, $headers);
    }
}