<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller; 
use App\Models\production\stock_prod;
use App\Models\Production\ListLineProduction;
use App\Models\Engineering\StockOutEng; // 🌟 IMPORT MODEL LOG ENGINEERING
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockProdController extends Controller
{
    public function index()
    {
        $stocks = stock_prod::with('line')->paginate(10); 
        $registeredLineIds = stock_prod::pluck('line_id')->filter()->unique()->toArray();
        $lines = ListLineProduction::whereIn('line_id', $registeredLineIds)->get();
        $masterLines = ListLineProduction::whereNotIn('line_id', $registeredLineIds)->get();
        $allNozzles = stock_prod::select('no_nozzle', 'sap_code')->distinct()->get();
    
        // 🌟 FILTER: Ambil ID log yang SUDAH terpakai di stock_prods (berdasarkan transaction_out_id)
        $usedTransactionIds = stock_prod::pluck('transaction_out_id')->filter()->toArray();
    
        // 🌟 Hanya ambil log yang BELUM ada di stock_prods
        $logs = StockOutEng::with(['stockEng', 'dbBarcode'])
                           ->whereNotIn('transaction_out_id', $usedTransactionIds)
                           ->orderBy('created_at', 'desc')
                           ->get();
    
        return view('stock_prod.stock_prod', compact('stocks', 'lines', 'masterLines', 'allNozzles', 'logs'));
    }

    /**
     * 🚀 ACTION 1: REGISTER PRODUCTION LINE (ADD LINE)
     */
    public function lineStore(Request $request)
    {
        $request->validate(['no_line' => 'required|string']);

        $masterLine = ListLineProduction::where('no_line', $request->no_line)->first();
        if (!$masterLine) {
            return redirect()->back()->with('error', 'Gagal! Nomor lini tersebut tidak ditemukan di master data.');
        }

        $isExists = stock_prod::where('line_id', $masterLine->line_id)->exists();
        if ($isExists) {
            return redirect()->back()->with('error', 'Gagal! Lini produksi ini sudah terdaftar.');
        }

        stock_prod::create([
            'line_id'            => $masterLine->line_id,
            'no_nozzle'          => '-',
            'qty'                => 0,
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
            'stock_out_log_id'   => 'required|exists:stock_out_logs,id', // 🌟 VALIDASI ID DARI LOG
            'line_id'            => 'required',
            'qty'                => 'required|integer|min:1',
            'min_stock'          => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 🌟 MENGAMBIL DATA AKTUAL DARI LOG ENGINEERING
            $log = StockOutEng::findOrFail($request->stock_out_log_id);

            stock_prod::create([
                'line_id'            => $request->line_id,
                'no_nozzle'          => $log->no_nozzle,
                'part_no'            => $log->stockEng->part_no ?? 'N/A',
                'sap_code'           => $log->stockEng->sap_code ?? 'N/A',
                'qty'                => $request->qty,
                'min_stock'          => $request->min_stock,
                'transaction_out_id' => $log->transaction_out_id, 
                'request_no'         => $log->request_sparepart_id ?? '-', 
                'barcode_id'         => $log->dbBarcode->barcode_id ?? '-',
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Nozzle Berhasil Masuk (IN) dan Sinkron dengan Data Engineering!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses data: ' . $e->getMessage());
        }
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
     */
    public function destroy($id)
    {
        $stock = stock_prod::findOrFail($id);
        $stock->delete();

        return redirect()->back()->with('success', 'Lini Produksi berhasil dihapus dari daftar kontrol deck, Bro!');
    }

    public function storeRequest(Request $request) { return redirect()->back()->with('success', 'Request stock berhasil dikirim!'); }
    public function receiveItem(Request $request) { return redirect()->back()->with('success', 'Item berhasil diterima!'); }
    public function requestHistory() { return "Halaman Request History Production - Comming Soon, Bro!"; }
    public function exportCSV() { return "Proses Export CSV - Comming Soon, Bro!"; }
}