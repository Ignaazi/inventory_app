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
    public function index()
    {
        $lines = ListLineProduction::with(['stocks'])->orderBy('line_id', 'asc')->get();

        $stockEngs = StockEng::with('sparepart')
            ->get()
            ->sortBy(function ($stock) {
                return $stock->sparepart->name ?? '';
            })
            ->values();
    
        return view('stock_prod.stock_prod', compact('lines', 'stockEngs'));
    }

    public function nozzleStore(Request $request)
    {
        if ($request->input('action_type') === 'line') {
            $request->validate(['register_line_id' => 'required']);

            if (stock_prod::where('line_id', $request->register_line_id)->exists()) {
                return redirect()->back()->with('error', 'Lini Produksi tersebut sudah terdaftar!');
            }

            stock_prod::create([
                'line_id'       => $request->register_line_id,
                'stock_eng_id'  => null,
                'no_nozzle'     => null,
                'part_no'       => null,
                'sap_code'      => null,
                'category'      => null,
                'qty'           => 0,
                'min_stock'     => 0,
            ]);
            return redirect()->back()->with('success', 'Lini Produksi Baru Berhasil Didaftarkan!');
        }

        $request->validate([
            'line_id'      => 'required', 
            'stock_eng_id' => 'required',
            'qty'          => 'required|integer|min:0',
            'min_stock'    => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $engItem = StockEng::with('sparepart')->find($request->stock_eng_id);

            if (!$engItem) {
                return redirect()->back()->with('error', 'Master komponen Engineering tidak ditemukan!');
            }

            $noNozzle = $engItem->sparepart->name ?? $engItem->no_nozzle ?? 'UNKNOWN';
            $partNo   = $engItem->sparepart->part_number ?? $engItem->sparepart->part_no ?? $engItem->part_no ?? '-';
            $sapCode  = $engItem->sparepart->sap_code ?? $engItem->sap_code ?? '-';
            $category = $engItem->sparepart->category ?? $engItem->category ?? 'NOZZLE';

            // Cari row line yang kosong untuk ditimpa (jika ada sisa baris kosong bawaan)
            $dummyLine = stock_prod::where('line_id', $request->line_id)
                ->where(function($query) {
                    $query->whereNull('no_nozzle')
                          ->orWhere('no_nozzle', '')
                          ->orWhere('no_nozzle', '-')
                          ->orWhere('no_nozzle', 'N/A');
                })
                ->first();

            if ($dummyLine) {
                $dummyLine->update([
                    'stock_eng_id' => $request->stock_eng_id, 
                    'no_nozzle'    => $noNozzle,
                    'part_no'      => $partNo,
                    'sap_code'     => $sapCode,
                    'category'     => $category,
                    'qty'          => $request->qty,
                    'min_stock'    => $request->min_stock,
                ]);
            } else {
                // Skenario: cek apakah nozzle yang sama sudah ada di line tersebut
                $matchedStockProd = stock_prod::where('line_id', $request->line_id)
                    ->where('part_no', $partNo)
                    ->first();

                if ($matchedStockProd) {
                    $matchedStockProd->update([
                        'qty'       => $matchedStockProd->qty + intval($request->qty),
                        'min_stock' => $request->min_stock, 
                    ]);
                } else {
                    stock_prod::create([
                        'line_id'      => $request->line_id,
                        'stock_eng_id' => $request->stock_eng_id, 
                        'no_nozzle'    => $noNozzle,
                        'part_no'      => $partNo,
                        'sap_code'     => $sapCode,
                        'category'     => $category,
                        'qty'          => $request->qty,
                        'min_stock'    => $request->min_stock,
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Komponen Nozzle Berhasil Disimpan ke Lini Produksi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
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

    /**
     * MURNI MENGHAPUS / DELETE BARIS DARI DATABASE
     */
    public function destroy($id)
    {
        $stock = stock_prod::findOrFail($id);
        $stock->delete(); // <--- Ini diubah jadi delete murni bor!

        return redirect()->back()->with('success', 'Data Alokasi Nozzle Berhasil Dihapus dari Sistem!');
    }
}