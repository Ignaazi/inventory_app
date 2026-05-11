<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockEng;
use App\Models\Rak; 
use Illuminate\Support\Facades\Response;

class StockEngineeringController extends Controller
{
    public function index()
    {
        // Ambil data rak dulu
    $raks = \App\Models\Rak::all();
    
    // Ambil data stock dengan relasi rak
    $stocks = \App\Models\StockEng::with('rak')->orderBy('created_at', 'desc')->paginate(25);

        return view('stock_eng.index', compact('stocks', 'raks'));
    }

    public function store(Request $request)
    {
        // Cek dulu data nyampe apa nggak (Buka tab Network di Inspect Element buat liat ini)
        // dd($request->all()); 
    
        $request->validate([
            'rak_id'    => 'required|exists:raks,id',
            'no_nozzle' => 'required',
            'qty'       => 'required|numeric',
            'min_stock' => 'required|numeric',
            // Kolom di bawah ini JANGAN dikasih 'required' karena di view lu bisa kosong
            'part_no'   => 'nullable',
            'sap_code'  => 'nullable',
            'category'  => 'nullable',
        ]);
    
        // Proses Simpan
        $stock = new \App\Models\StockEng();
        $stock->rak_id = $request->rak_id;
        $stock->no_nozzle = $request->no_nozzle;
        $stock->part_no = $request->part_no;
        $stock->sap_code = $request->sap_code;
        $stock->category = $request->category;
        $stock->qty = $request->qty;
        $stock->min_stock = $request->min_stock;
        $stock->save();
    
        return redirect()->back()->with('success', 'Data Berhasil Disimpan!');
    }

    public function storeRak(Request $request)
{
    $request->validate([
        'nama_rak' => 'required|unique:raks,nama_rak',
    ]);

    \App\Models\Rak::create([
        'nama_rak' => $request->nama_rak,
        // Kalau di tabel lu adanya 'lokasi', ganti ke 'lokasi'
        // Kalau nggak ada kolom tambahan sama sekali, hapus baris ini
        'lokasi' => $request->keterangan ?? '-' 
    ]);

    return redirect()->back()->with('success', 'Rak Baru Berhasil Ditambahkan');
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
        return redirect()->back()->with('success', 'Data Berhasil Dihapus');
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
                    $task->rak->nama_rak ?? '-', // Ambil nama rak dari relasi
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