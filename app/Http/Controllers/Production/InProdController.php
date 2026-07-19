<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Production\inProd;

class InProdController extends Controller
{
    /**
     * Tampilan Utama Halaman Monitoring / Log Transaksi Masuk
     */
    public function stockIn()
    {
        $history = inProd::with(['line', 'barcode'])->latest()->paginate(10);
        return view('stock_prod.transactionProd.inProd', compact('history'));
    }

    /**
     * Halaman Form Input Manual (Dropdown Mode)
     */
    public function manualIn()
    {
        // 1. Ambil data list line produksi untuk drop down line
        $lines = DB::table('list_line_productions')->orderBy('no_line', 'asc')->get();

        // 2. Ambil data barcode yang belum di-scan masuk ke lini produksi ('USED_IN')
        $barcodes = DB::table('db_barcodes')
            ->where('current_lifecycle', '!=', 'USED_IN')
            ->orderBy('barcode_id', 'asc')
            ->get();

        return view('stock_prod.transactionProd.manual_inProd', compact('lines', 'barcodes'));
    }

    /**
     * Eksekusi Simpan Form Manual via Dropdown dengan Fitur Pengecekan Ketat (DIE-TRACER)
     */
    public function storeManualIn(Request $request)
    {
        $scannedInput = trim($request->input('barcode_scan'));
        $targetLineIdString = $request->input('line_id');

        if (!$scannedInput || !$targetLineIdString) {
            return redirect()->back()->withInput()->with('error', 'DIE-TRACER: Gagal! Nilai Barcode Scan atau Target Line kosong saat dikirim.');
        }

        DB::beginTransaction();
        try {
            // 1. Validasi Keberadaan Barcode di DB Master Barcode
            $barcodeDb = DB::table('db_barcodes')
                ->where('final_content', $scannedInput)
                ->orWhere('barcode_id', $scannedInput)
                ->first();

            if (!$barcodeDb) {
                return redirect()->back()->withInput()->with('error', 'DIE-TRACER 1: Barcode [' . $scannedInput . '] tidak terdaftar di sistem tabel db_barcodes.');
            }

            // 2. Validasi Silang: Apakah Barcode sudah di-OUT oleh Engineering gudang?
            $outLog = DB::table('stock_out_logs')->where('barcode_id', $barcodeDb->id)->first();
            if (!$outLog) {
                // Percobaan cadangan: Mencocokkan via string barcode_id
                $outLog = DB::table('stock_out_logs')->where('barcode_id', $barcodeDb->barcode_id)->first();
                
                if (!$outLog) {
                    return redirect()->back()->withInput()->with('error', 'DIE-TRACER 2: Barcode ID [' . $barcodeDb->barcode_id . '] (Internal ID: ' . $barcodeDb->id . ') terdaftar di master barcode, tapi data transaksi OUT di gudang engineering (stock_out_logs) belum pernah dibuat.');
                }
            }

            // 3. Proteksi Duplikasi Transaksi Produksi
            $isAlreadyIn = DB::table('inProd_logs')->where('barcode_id', $barcodeDb->id)->exists();
            if ($isAlreadyIn) {
                return redirect()->back()->withInput()->with('error', 'DIE-TRACER 3: Barcode ini ditolak karena sudah tercatat masuk di log produksi (inProd_logs) sebelumnya.');
            }

            // 4. Validasi Keberadaan Master Lini Produksi
            $masterLine = DB::table('list_line_productions')
                ->where('line_id', $targetLineIdString)
                ->orWhere('id', $targetLineIdString)
                ->first();
                
            if (!$masterLine) {
                return redirect()->back()->withInput()->with('error', 'DIE-TRACER 4: Identitas Lini [' . $targetLineIdString . '] tidak ditemukan di tabel list_line_productions.');
            }

            // 5. Pencarian Stok Produksi untuk update/insert Qty
            $stockProd = DB::table('stock_prods')
                ->where('stock_eng_id', $barcodeDb->stock_eng_id)
                ->where(function($query) use ($masterLine, $targetLineIdString) {
                    $query->where('line_id', $targetLineIdString)
                          ->orWhere('line_id', $masterLine->line_id)
                          ->orWhere('line_id', $masterLine->id);
                })
                ->first();

            // Mengambil spesifikasi sparepart dari database Engineering
            $engItem = DB::table('stock_engs')
                ->leftJoin('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id')
                ->where('stock_engs.id', $barcodeDb->stock_eng_id)
                ->select('spareparts.part_number', 'spareparts.sap_code', 'spareparts.category')
                ->first();

            // Konversi Qty masuk, default ke angka 1 jika field kosong
            $qtyMasuk = isset($outLog->qty_out) ? intval($outLog->qty_out) : 1;

            if ($stockProd) {
                // Skenario A: Tambah Stok yang sudah eksis di lini tersebut
                $stockProdId = $stockProd->id;
                DB::table('stock_prods')->where('id', $stockProdId)->update([
                    'qty'        => $stockProd->qty + $qtyMasuk,
                    'updated_at' => now()
                ]);
            } else {
                // Skenario B: Insert data stok baru untuk lini tersebut
                $stockProdId = DB::table('stock_prods')->insertGetId([
                    'line_id'      => $masterLine->line_id ?? $targetLineIdString,
                    'stock_eng_id' => $barcodeDb->stock_eng_id,
                    'no_nozzle'    => $outLog->no_nozzle ?? 'NZ-NEW',
                    'part_no'      => $engItem->part_number ?? '-',
                    'sap_code'     => $engItem->sap_code ?? '-',
                    'category'     => $engItem->category ?? 'NOZZLE',
                    'qty'          => $qtyMasuk,
                    'min_stock'    => 0,
                    'created_at'   => now(),
                    'updated_at'   => now()
                ]);
            }

            // 6. Catat Log Transaksi Masuk Produksi ke tabel inProd_logs
            DB::table('inProd_logs')->insert([
                'nik'                => Auth::user()->nik ?? '9999',
                'line_id'            => $masterLine->id,
                'no_nozzle'          => $outLog->no_nozzle ?? '-',
                'transaction_out_id' => $outLog->transaction_out_id ?? 'TRX-MANUAL',
                'request_no'         => $outLog->request_sparepart_id ?? '-', 
                'barcode_id'         => $barcodeDb->id,
                'stock_prod_id'      => $stockProdId,
                'qty_in'             => $qtyMasuk,
                'status'             => 'success',
                'remark'             => 'MANUAL IN VIA DROPDOWN SELECT',
                'comment'            => 'Menerima pasokan stok manual dari engineering',
                'created_at'         => now(),
                'updated_at'         => now()
            ]);

            // 7. Update status siklus barcode agar tidak bisa disalahgunakan lagi
            DB::table('db_barcodes')->where('id', $barcodeDb->id)->update([
                'current_lifecycle' => 'USED_IN',
                'updated_at'        => now()
            ]);

            DB::commit();

            return redirect()->route('prod.transaction.in')->with('success', 'Berhasil! Item sukses didaftarkan ke Lini ' . ($masterLine->no_line ?? $targetLineIdString) . '. Stok bertambah +' . $qtyMasuk . ' Pcs.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'DIE-EXCEPTION: Terjadi eror kegagalan database sistem: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        return $this->storeManualIn($request);
    }

    public function getEngineeringDetail($id)
    {
        $detail = DB::table('stock_engs')->where('id', $id)->first();
        return response()->json($detail);
    }
}