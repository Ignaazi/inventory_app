<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Production\inProd;       
use App\Models\Production\stock_prod;   
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InProdController extends Controller
{
    public function stockIn() 
    {
        $history = inProd::latest('inproduction_id')->paginate(10); 
     return view('stock_prod.transactionProd.inProd', compact('history'));
    }
    public function stockOut() 
    {
     return view('stock_prod.transactionProd.outProd');
    }
    public function store(Request $request) 
    {
        $request->validate([
            'line_id'            => 'required',
            'no_nozzle'          => 'required|string',
            'transaction_out_id' => 'required',
            'barcode_id'         => 'required',
            'stock_prod_id'      => 'required', 
            'qty_in'             => 'required|numeric|min:1',
        ]);
        DB::beginTransaction();
        try {
            inProd::create([
                'nik'                => Auth::user()->nik ?? Auth::user()->nim ?? 'UNKNOWN',
                'line_id'            => $request->line_id,
                'no_nozzle'          => $request->no_nozzle,
                'transaction_out_id' => $request->transaction_out_id,
                'request_no'         => $request->request_no,
                'barcode_id'         => $request->barcode_id,
                'stock_prod_id'      => $request->stock_prod_id, 
                'qty_in'             => $request->qty_in,
                'status'             => 'success',
                'remark'             => $request->remark ?? 'scan in',
                'comment'            => $request->comment
            ]);
            $stockProd = stock_prod::findOrFail($request->stock_prod_id);
            $stockProd->increment('qty', $request->qty_in); 

            DB::commit();
            return back()->with('success', 'Transaksi Production In Berhasil Dicatat & Stock Qty Line Otomatis Bertambah!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage());
        }
    }
}