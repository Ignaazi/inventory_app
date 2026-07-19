<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\StockInEng; // Pastikan model ini mengarah ke table: inProd_logs & primaryKey: inproduction_id
use App\Models\StockEng;
use App\Models\Engineering\EngMaterialReceiving; 

class StockInEngineeringController extends Controller
{
    /**
     * Tampilan Utama / History Stock In
     */
    public function index()
    {
        // Load history dari inProd_logs lengkap beserta relasi stock_prod_id ke stockEng, dan nested relasi sparepart-nya
        $history = StockInEng::with(['stockEng.sparepart', 'stockEng.rak', 'engMaterialReceiving'])->latest()->paginate(10);
        return view('stock_eng.transaction.in', compact('history'));
    }

    /**
     * Tampilan untuk Input Manual
     */
    public function manual()
    {
        $stocks = StockEng::with(['sparepart', 'rak'])->get();

        // Ambil daftar nama RAK unik untuk filter dropdown bertingkat
        $listRak = $stocks->map(function ($item) {
            return $item->rak->nama_rak ?? null;
        })->filter()->unique()->sort()->values();

        // 🌟 PERBAIKAN: Gunakan kolom 'request_no' sesuai yang ada di tabel inProd_logs kamu!
        $usedPrIds = StockInEng::whereNotNull('request_no')->pluck('request_no')->toArray();
        $costingReceivings = EngMaterialReceiving::whereNotIn('id', $usedPrIds)->latest()->get();

        return view('stock_eng.transaction.in_manual', compact('stocks', 'listRak', 'costingReceivings'));
    }

    /**
     * Proses Simpan Data (Aman & Sinkron dengan Tabel inProd_logs)
     */
    public function store(Request $request)
    {
        $request->validate([
            'stock_eng_id'              => 'required|exists:stock_engs,id',
            'qty_in'                    => 'required|integer|min:1',
            'eng_material_receiving_id' => 'nullable|exists:eng_material_receivings,id', 
            'remark'                    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Ambil data stock utama beserta master sparepart dan raks
            $stock = StockEng::with(['sparepart', 'rak'])->findOrFail($request->stock_eng_id);
            
            $namaNozzle = $stock->sparepart->name ?? 'N/A';
            $sapCode    = $stock->sparepart->sap_code ?? '-';
            $partNumber = $stock->sparepart->part_number ?? '-';
            $lokasiRak  = $stock->rak->nama_rak ?? 'N/A';

            // 🌟 PERBAIKAN: Cek double PR menggunakan kolom 'request_no'
            if ($request->eng_material_receiving_id) {
                $isPrUsed = StockInEng::where('request_no', $request->eng_material_receiving_id)->exists();
                if ($isPrUsed) {
                    return redirect()->back()->withInput()->with('error', 'Gagal: Dokumen PR/Receiving ini sudah terpakai!');
                }
            }

            // Generate nomor transaksi dinamis untuk kolom `transaction_out_id`
            $latestTx = StockInEng::orderBy('inproduction_id', 'desc')->first();
            $nextTxId = !$latestTx ? 'ENGIN001' : 'ENGIN' . str_pad(((int) filter_var($latestTx->transaction_out_id, FILTER_SANITIZE_NUMBER_INT)) + 1, 3, '0', STR_PAD_LEFT);

            // 🌟 Petakan data persis seperti struktur tabel `inProd_logs` di migration kamu
            $logData = [
                'nik'                => \Illuminate\Support\Facades\Auth::user()->nik ?? 'SYSTEM',
                'line_id'            => $stock->line_id ?? 1,             // Diambil dari stock_eng atau default 1
                'no_nozzle'          => $namaNozzle,                      // Menjawab "Unknown column 'no_nozzle'"
                'transaction_out_id' => $nextTxId,                        // ID Transaksi unik
                'request_no'         => $request->eng_material_receiving_id ?? null, // Mengisi kolom request_no di DB kamu
                'barcode_id'         => $stock->barcode_id ?? 1,          // ID Barcode dari relasi stock utama
                'stock_prod_id'      => $stock->id,                       // ID Stock utama (FK ke tabel stock_engs / stock_prods)
                'qty_in'             => $request->qty_in,                 // Sesuai field migration
                'status'             => 'SUCCESS',
                'remark'             => $request->remark ?? 'MANUAL IN',
                'comment'            => "RAK: {$lokasiRak} | SAP: {$sapCode} | PN: {$partNumber}"
            ];

            // Masukkan data transaksi ke tabel inProd_logs
            StockInEng::create($logData);

            // Update/tambahkan stok fisik utama di tabel stock_engs
            $stock->increment('qty', $request->qty_in);

            DB::commit();
            return redirect()->route('eng.in')->with('success', "Stok sukses ditambahkan ke RAK [{$lokasiRak}] untuk sparepart [{$namaNozzle}]!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memproses stock in: ' . $e->getMessage());
        }
    }
}