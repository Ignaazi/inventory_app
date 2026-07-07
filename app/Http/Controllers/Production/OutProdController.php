<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OutProdController extends Controller
{
    /**
     * 1. Menampilkan Halaman Utama History Stock OUT Production (Tabel Utama)
     */
    public function stockOut()
    {
        // Menarik data logs keluar dengan join ke nama line produksi
        $history = DB::table('outProd_logs')
            ->leftJoin('list_line_productions', 'outProd_logs.line_id', '=', 'list_line_productions.id')
            ->select(
                'outProd_logs.*', 
                'list_line_productions.no_line', 
                'list_line_productions.name_machine'
            )
            ->orderBy('outProd_logs.created_at', 'desc')
            ->paginate(10);

        // ✅ Sesuai VS Code lu: folder 'transactionProd' dan file 'outProd'
        return view('stock_prod.transactionProd.outProd', compact('history'));
    }

    /**
     * 2. Menampilkan Form Pembuatan Manual OUT Production
     */
    public function manualOut()
    {
        // Ambil data dari history IN murni (inProd_logs) yang dikawinkan dengan live stock saat ini (stock_prods)
        // Kita hanya munculkan barang yang live stock-nya di lantai produksi masih di atas 0 (> 0)
        $availableIns = DB::table('inProd_logs')
            ->join('stock_prods', 'inProd_logs.stock_prod_id', '=', 'stock_prods.id')
            ->leftJoin('list_line_productions', 'inProd_logs.line_id', '=', 'list_line_productions.id')
            ->where('stock_prods.qty', '>', 0)
            ->select(
                'inProd_logs.inproduction_id',
                'inProd_logs.no_nozzle',
                'inProd_logs.stock_prod_id',
                'inProd_logs.line_id as numeric_line_id',
                'stock_prods.qty as current_stock_qty',
                'list_line_productions.line_id as string_line_code',
                'list_line_productions.no_line',
                'list_line_productions.name_machine'
            )
            ->orderBy('inProd_logs.created_at', 'desc')
            ->get();

        // ✅ Sesuai VS Code lu: folder 'transactionProd' dan file 'manual_out'
        return view('stock_prod.transactionProd.manual_out', compact('availableIns'));
    }

    /**
     * 3. API Pendukung AJAX (Fetch) untuk ditarik secara live oleh Form Blade
     */
    public function getInProductionDetail($id)
    {
        try {
            // Tarik detail data IN berdasarkan inproduction_id pilihan user
            $inData = DB::table('inProd_logs')
                ->join('stock_prods', 'inProd_logs.stock_prod_id', '=', 'stock_prods.id')
                ->leftJoin('list_line_productions', 'inProd_logs.line_id', '=', 'list_line_productions.id')
                ->where('inProd_logs.inproduction_id', $id)
                ->select(
                    'inProd_logs.*',
                    'stock_prods.qty as current_live_qty',
                    'list_line_productions.line_id as string_line_code',
                    'list_line_productions.no_line',
                    'list_line_productions.name_machine'
                )
                ->first();

            if (!$inData) {
                return response()->json(['success' => false, 'message' => 'Data IN tidak ditemukan!'], 404);
            }

            return response()->json([
                'success'           => true,
                'no_nozzle'         => $inData->no_nozzle,
                'string_line_code'  => $inData->string_line_code,
                'line_display'      => 'Line ' . $inData->no_line . ' (' . $inData->name_machine . ')',
                'barcode_id'        => $inData->barcode_id,
                'request_no'        => $inData->request_no ?? 'N/A',
                'stock_prod_id'     => $inData->stock_prod_id,
                'max_available'     => $inData->current_live_qty // Batasi input out maksimal senilai sisa stock murni
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * 4. Memproses Penyimpanan Form Manual OUT (Potong Stok & Ikat inproduction_id)
     */
    public function storeManualOut(Request $request)
    {
        // Validasi input
        $request->validate([
            'inproduction_id' => 'required|numeric',
            'qty_out'         => 'required|numeric|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Ambil data referensi logs IN murni
            $inLog = DB::table('inProd_logs')->where('inproduction_id', $request->inproduction_id)->first();
            if (!$inLog) {
                return redirect()->back()->with('error', 'Referensi data IN Production tidak valid!')->withInput();
            }

            // Cek ketersediaan sisa stock live di tabel stock_prods
            $liveStock = DB::table('stock_prods')->where('id', $inLog->stock_prod_id)->first();
            if (!$liveStock || $liveStock->qty < $request->qty_out) {
                return redirect()->back()->with('error', 'Gagal! Jumlah pengeluaran melebihi sisa live stock yang tersedia di lantai produksi (Sisa saat ini: ' . ($liveStock->qty ?? 0) . ').')->withInput();
            }

            // Generate Otomatis TRANSACTION OUT ID Khusus Produksi (ex: TRXP-OUT-20260707-0001)
            $datePrefix = 'TRXP-OUT-' . date('Ymd');
            $lastTrx = DB::table('outProd_logs')->where('transaction_out_id', 'LIKE', $datePrefix . '%')->orderBy('outproduction_id', 'desc')->first();
            
            if ($lastTrx) {
                $lastNum = (int) substr($lastTrx->transaction_out_id, -4);
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            $transactionOutId = $datePrefix . '-' . $nextNum;

            // INSERT DATA BARU KE outProd_logs (inproduction_id tercatat aman!)
            DB::table('outProd_logs')->insert([
                'inproduction_id'    => $request->inproduction_id, // Bukti fisik pelacakan buat dosen
                'nik'                => Auth::user()->nik ?? Auth::user()->id ?? '123456',
                'line_id'            => $inLog->line_id,
                'no_nozzle'          => $inLog->no_nozzle,
                'transaction_out_id' => $transactionOutId,
                'request_no'         => $inLog->request_no,
                'barcode_id'         => $inLog->barcode_id,
                'stock_prod_id'      => $inLog->stock_prod_id,
                'qty_out'            => $request->qty_out,
                'status'             => 'success',
                'remark'             => 'Manual Out',
                'comment'            => $request->comment ?? 'Barang dikeluarkan dari lini produksi secara manual',
                'created_at'         => now(),
                'updated_at'         => now()
            ]);

            // DECREMENT LIVE STOK: Kurangi jumlah kuantitas unit di tabel stock_prods
            DB::table('stock_prods')
                ->where('id', $inLog->stock_prod_id)
                ->decrement('qty', $request->qty_out);

            DB::commit();
            return redirect()->route('prod.transaction.out')->with('success', 'Transaksi Manual OUT Berhasil! Jejak data IN terikat sempurna dan stok lantai produksi berkurang.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Gagal memproses transaksi out: ' . $e->getMessage())->withInput();
        }
    }
}