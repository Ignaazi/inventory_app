<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InProdController extends Controller
{
    /**
     * Menampilkan Halaman Utama History Stock IN Production
     */
    public function stockIn()
    {
        // Menggunakan ->paginate(10) agar variabel $history, ->currentPage(), dan ->links() di View terbaca sempurna
        $history = DB::table('inProd_logs')
            ->leftJoin('list_line_productions', 'inProd_logs.line_id', '=', 'list_line_productions.id')
            ->select(
                'inProd_logs.*', 
                'list_line_productions.no_line',             // Mengambil kode line (misal: SMT01)
                'list_line_productions.name_machine'          // Mengambil nama mesin
            )
            ->orderBy('inProd_logs.created_at', 'desc')
            ->paginate(10); 

        return view('stock_prod.transactionProd.inProd', compact('history'));
    }

    /**
     * Menampilkan View Form Pembuatan Manual IN Production
     */
    public function manualIn()
    {
        // 1. Ambil list 'transaction_out_id' yang SUDAH PERNAH digunakan di lantai produksi
        $usedTrxIds = DB::table('inProd_logs')
            ->whereNotNull('transaction_out_id')
            ->pluck('transaction_out_id')
            ->toArray();

        // 2. Ambil data pengeluaran engineering (SUCCESS) yang BELUM PERNAH diklaim oleh produksi sama sekali (Sekali Pakai)
        $engineeringOuts = DB::table('stock_out_logs')
            ->where('status', 'SUCCESS')
            ->whereNotIn('transaction_out_id', $usedTrxIds) // 🔥 KUNCI UTAMA: Menyaring ID yang sudah terpakai
            ->orderBy('created_at', 'desc')
            ->get();

        // 3. Ambil daftar line produksi untuk dropdown target alokasi
        $lines = DB::table('list_line_productions')->orderBy('no_line', 'asc')->get();

        return view('stock_prod.transactionProd.manual_inProd', compact('engineeringOuts', 'lines'));
    }

    /**
     * API Pendukung AJAX (Fetch) untuk menarik detail aspek data Engineering secara live
     */
    public function getEngineeringDetail($id)
    {
        try {
            $engData = DB::table('stock_out_logs')
                ->where('transaction_out_id', $id)
                ->first();

            if (!$engData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detail data transaksi out tidak ditemukan!'
                ], 404);
            }

            return response()->json([
                'success'              => true,
                'barcode_id'           => $engData->barcode_id,
                'no_nozzle'            => $engData->no_nozzle,
                'request_sparepart_id' => $engData->request_sparepart_id ?? 'N/A',
                'qty_out'              => $engData->qty_out
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Memproses Penyimpanan Form Manual IN (Proteksi Double input & Sinkronisasi Data)
     */
    public function storeManualIn(Request $request)
    {
        // Validasi input kiriman form
        $request->validate([
            'transaction_out_id' => 'required|string',
            'line_id'            => 'required|string', 
            'barcode_id'         => 'required',
            'no_nozzle'          => 'required|string',
            'qty_in'             => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 🔥 BACKEND PROTECTION: Cek apakah transaction_out_id ini mendadak sudah di-insert orang lain
            $isAlreadyInvoiced = DB::table('inProd_logs')
                ->where('transaction_out_id', $request->transaction_out_id)
                ->exists();

            if ($isAlreadyInvoiced) {
                return redirect()->back()->with('error', 'Transaksi Gagal! ID Pengeluaran Engineering ini sudah pernah diproses sebelumnya.')->withInput();
            }

            // 1. Konversi Kode String Line (ex: 'SIIXSMTLINE001') ke ID Int Murni untuk tabel inProd_logs
            $lineProduction = DB::table('list_line_productions')
                ->where('line_id', $request->line_id)
                ->first();

            if (!$lineProduction) {
                return redirect()->back()->with('error', 'Target Line Production tidak terdaftar di sistem!')->withInput();
            }

            $lineIdNumeric = $lineProduction->id; 

            // 2. Validasi data pembanding ke log asli gudang engineering
            $engData = DB::table('stock_out_logs')->where('transaction_out_id', $request->transaction_out_id)->first();
            if (!$engData) {
                return redirect()->back()->with('error', 'Transaksi pengeluaran tidak valid atau tidak ditemukan!')->withInput();
            }

            $noNozzle  = $request->no_nozzle;
            $barcodeId = $request->barcode_id;
            $requestNo = $request->request_no ?? $engData->request_sparepart_id;

            // 3. Sinkronisasi Live Stock Produksi di tabel stock_prods
            $matchedStockProd = DB::table('stock_prods')
                ->where('line_id', $request->line_id) 
                ->where('no_nozzle', $noNozzle)
                ->first();

            if (!$matchedStockProd) {
                // Buat baris baru di stock_prods jika kombinasi line & nozzle belum pernah ada
                $masterEng = DB::table('stock_engs')->where('no_nozzle', $noNozzle)->first();

                $stockProdId = DB::table('stock_prods')->insertGetId([
                    'line_id'    => $request->line_id, 
                    'no_nozzle'  => $noNozzle,
                    'part_no'    => $masterEng->part_no ?? 'N/A',
                    'sap_code'   => $masterEng->sap_code ?? 'N/A',
                    'category'   => $masterEng->category ?? 'N/A',
                    'qty'        => 0, 
                    'min_stock'  => 5,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $stockProdId = $matchedStockProd->id;
            }

            // 4. INSERT LOG BARU: Catat ke tabel inProd_logs
            DB::table('inProd_logs')->insert([
                'nik'                => Auth::user()->nik ?? Auth::user()->id ?? '123456',
                'line_id'            => $lineIdNumeric, // Menyimpan Angka murni (ex: 1, 2, 3) aman dari crash SQL
                'no_nozzle'          => $noNozzle,
                'transaction_out_id' => $request->transaction_out_id,
                'request_no'         => $requestNo,
                'barcode_id'         => $barcodeId,
                'stock_prod_id'      => $stockProdId,
                'qty_in'             => $request->qty_in,
                'status'             => 'success',
                'remark'             => 'Manual In',
                'comment'            => $request->comment ?? 'Diterima via form manual otomatis',
                'created_at'         => now(),
                'updated_at'         => now()
            ]);

            // 5. INCREMENT STOK: Tambah kuantitas unit pada live stock produksi
            DB::table('stock_prods')
                ->where('id', $stockProdId)
                ->increment('qty', $request->qty_in);

            DB::commit();
            return redirect()->route('prod.transaction.in')->with('success', 'Transaksi Manual In Berhasil Diproses! Stok lantai produksi telah ditambahkan dan ID transaksi ini telah dikunci.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memproses transaksi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan Halaman History Stock OUT Production (Optional)
     */
    public function stockOut()
    {
        return view('stock_prod.transactionProd.outProd');
    }

    /**
     * Fitur Simpan General Log (Optional)
     */
    public function store(Request $request)
    {
        return redirect()->route('prod.transaction.in');
    }
}