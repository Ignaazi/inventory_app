<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockEng; 
use App\Models\Rak; 
use App\Models\ListSparepartEng; 
use Illuminate\Support\Facades\Log;

class StockEngineeringController extends Controller
{
    public function index()
    {
        $raks = Rak::all();
        $stocks = StockEng::with('rak')->orderBy('created_at', 'desc')->paginate(25);
        $ListSparepartEng = ListSparepartEng::orderBy('name', 'asc')->get(); 
        return view('stock_eng.index', compact('stocks', 'raks', 'ListSparepartEng'));
    }
    public function indexIn()
    {
        $recent_logs = StockEng::orderBy('updated_at', 'desc')->take(10)->get();
        return view('stock_eng.transaction.in', compact('recent_logs'));
    }
    public function inScan()
    {
        $stocks = StockEng::all(); 
        return view('stock_eng.transaction.in_scan', compact('stocks'));
    }

    public function inManual()
    {
        $stocks = StockEng::all(); 
        return view('stock_eng.transaction.in_manual', compact('stocks'));
    }

    public function updateStockIn(Request $request)
    {
        try {
            $request->validate([
                'stock_id' => 'required|exists:stock_engs,id',
                'qty_in'   => 'required|numeric|min:1'
            ]);

            $stock = StockEng::findOrFail($request->stock_id);
            
            $oldQty = $stock->qty;
            $stock->qty = $oldQty + $request->qty_in;
            $stock->save();

            session()->flash('last_in_' . $stock->id, $request->qty_in);

            return redirect()->route('eng.in')->with('success', "Stok {$stock->no_nozzle} berhasil ditambah! ({$oldQty} -> {$stock->qty})");

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
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error("Gagal simpan nozzle: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function storeRak(Request $request)
    {
        $request->validate([
            'nama_rak' => 'required|unique:raks,nama_rak',
        ]);

        Rak::create([
            'nama_rak' => $request->nama_rak,
            'lokasi'   => $request->lokasi ?? '-' 
        ]);

        return redirect()->back()->with('success', 'Rak Baru Berhasil Ditambahkan');
    }
    public function destroyRak($id)
    {
        try {
            $rak = Rak::findOrFail($id);
            $checkUsage = StockEng::where('rak_id', $id)->exists();
            if ($checkUsage) {
                return redirect()->back()->with('error', 'Rak gagal dihapus karena masih ada nozzle di dalamnya!');
            }
            $rak->delete();
            return redirect()->back()->with('success', 'Rak Berhasil Dihapus!');
        } catch (\Exception $e) {
            Log::error("Gagal hapus rak: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal hapus rak: ' . $e->getMessage());
        }
    }
    public function update(Request $request, $id)
    {
        $stock = StockEng::findOrFail($id);
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