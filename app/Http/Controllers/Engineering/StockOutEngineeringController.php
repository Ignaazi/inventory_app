<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\StockOutEng; 
use App\Models\StockEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockOutEngineeringController extends Controller
{
    /**
     * 📊 METHOD INDEX: Menampilkan log mutasi keluar dari tabel utama
     */
    public function index(Request $request)
    {
        // Inisialisasi query transaksi OUT dengan koreksi relasi Rak via stock_engs (s.rak_id)
        $query = DB::table('stock_eng_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->leftJoin('production_requests as p', 't.production_request_id', '=', 'p.id')
            ->leftJoin('stock_engs as s', 't.stock_engs_id', '=', 's.id')
            ->leftJoin('raks as r', 's.rak_id', '=', 'r.id') // 🛠️ FIX: Rak terikat di master stok eng, bukan di barcode langsung
            ->select([
                't.*',
                'u.name as operator_name',
                'b.barcode_id as barcode_code',
                'b.final_content as barcode_final',
                'p.request_no as production_req_no',
                'p.no_nozzle as production_nozzle',
                's.id as stock_eng_id_raw',
                'r.nama_rak as rak_name'
            ])
            ->where('t.tx_type', 'out');

        // Handler Filter Status / Tipe Proses
        if ($request->has('filter') && !empty($request->filter)) {
            $filter = strtolower($request->filter);
            if (in_array($filter, ['success', 'pending', 'failed'])) {
                $query->where('t.status', $filter);
            } elseif (in_array($filter, ['manual', 'scan'])) {
                $query->where('t.process_type', $filter);
            }
        }

        // Handler Live Search Bar
        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('t.tx_id', 'LIKE', "%{$search}%")
                  ->orWhere('t.remark', 'LIKE', "%{$search}%")
                  ->orWhere('b.barcode_id', 'LIKE', "%{$search}%")
                  ->orWhere('p.request_no', 'LIKE', "%{$search}%");
            });
        }

        $history = $query->latest('t.created_at')->paginate(10)->withQueryString();

        return view('stock_eng.transaction.out', compact('history'));
    }

    /**
     * 🔍 METHOD KONTROLER UNTUK: HALAMAN TAMPILAN SCAN OUT
     * Menampilkan halaman terminal scanner / input manual UI
     */
    public function scan()
    {
        $usedBarcodeIds = DB::table('stock_eng_transactions')
            ->where('tx_type', 'out')
            ->whereNotNull('db_barcodes_id')
            ->pluck('db_barcodes_id')
            ->toArray();

        $stocks = StockEng::with(['sparepart', 'rak'])
            ->get()
            ->sortBy(function ($stock) {
                return $stock->sparepart->sparepart_id ?? $stock->sparepart->part_number ?? '';
            })
            ->values();

        $barcodes = \App\Models\DbBarcode::get()->map(function($item) use ($usedBarcodeIds) {
            return [
                'id'            => $item->id,
                'barcode_id'    => trim($item->barcode_id),    
                'final_content' => trim($item->final_content), 
                'is_used'       => in_array($item->id, $usedBarcodeIds) || $item->current_lifecycle !== 'AVAILABLE',
                'stock_eng_id'  => $item->stock_eng_id
            ];
        });

        $raks = \App\Models\Rak::orderBy('nama_rak', 'asc')->get();

        return view('stock_eng.transaction.out_scan', compact('stocks', 'barcodes', 'raks'));
    }

    /**
     * 🚀 METHOD KONTROLER UNTUK: EKSEKUSI PROSES (SCAN OUT & MANUAL OUT)
     * Keduanya bermuara di sini, dibedakan berdasarkan payload 'process_type'.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Awal
        $request->validate([
            'barcode_scan' => 'required|string',
            'process_type' => 'nullable|in:scan,manual', 
        ]);

        if (empty($request->barcode_scan)) {
            return response()->json(['success' => false, 'message' => 'Gagal! Barcode tidak terbaca.'], 400);
        }

        // Pembersihan spasi / karakter aneh kiriman hardware scanner
        $scannedInput = trim($request->barcode_scan);
        $scannedInput = str_replace(["\r", "\n", "\t"], '', $scannedInput);
        $processType  = $request->input('process_type', 'scan'); 

        // 🎯 KUNCI UTAMA: 1 Barcode mutlak mengurangi 1 stok fisik
        $qtyKeluar = 1; 

        DB::beginTransaction();
        try {
            // 2. Pencarian data barcode di DB Master berdasarkan barcode_id atau kontennya
            $barcodeDb = \App\Models\DbBarcode::where('barcode_id', $scannedInput)
                ->orWhere('final_content', $scannedInput)
                ->first();

            if (!$barcodeDb) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Kode "' . $scannedInput . '" tidak terdaftar di database master.'
                ], 404);
            }

            // 3. Validasi Siklus Hidup Barcode (Mencegah double scan data yang sama)
            if ($barcodeDb->current_lifecycle !== 'AVAILABLE') {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Barcode ' . $barcodeDb->barcode_id . ' sudah tidak aktif (Status: ' . $barcodeDb->current_lifecycle . ')'
                ], 422);
            }

            // 4. Tarik data Master Stock berdasarkan stock_eng_id yang melekat pada barcode
            if (!$barcodeDb->stock_eng_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Barcode ini belum dipetakan ke master item gudang manapun.'
                ], 422);
            }

            $stock = StockEng::with(['sparepart', 'rak'])->find($barcodeDb->stock_eng_id);
            if (!$stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Lokasi master stok untuk item ini tidak valid atau telah dihapus.'
                ], 404);
            }

            // Validasi keberadaan fisik rak
            if (empty($stock->rak_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Posisi Rak untuk item ini belum ditentukan pada master data.'
                ], 422);
            }

            // 5. Validasi Ketersediaan Saldo Stok di Rak Tersebut
            if ($stock->qty < $qtyKeluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Stok pada Rak ' . ($stock->rak->nama_rak ?? '-') . ' kosong atau tidak mencukupi.'
                ], 400);
            }

            // 6. Penanganan File Upload (Jika ada dokumentasi foto)
            $fotoPath = null;
            if ($request->hasFile('test_photo')) {
                $fotoPath = $request->file('test_photo')->store('stock_outs', 'public');
            }

            // 7. Generate Sequence ID Unik Log Internal
            $latestTx = StockOutEng::where('transaction_out_id', 'LIKE', 'ENGOUT%')->orderBy('id', 'desc')->first();
            $nextId = !$latestTx ? 'ENGOUT001' : 'ENGOUT' . str_pad(((int) filter_var($latestTx->transaction_out_id, FILTER_SANITIZE_NUMBER_INT)) + 1, 3, '0', STR_PAD_LEFT);

            $finalComment = $request->input('comment', 'Out transaction executed via ' . strtoupper($processType) . ' mode');

            // 8. INSERT LOG LOGISTIK LAMA (Fallback Compatibility)
            StockOutEng::create([
                'transaction_out_id'   => $nextId,
                'nik'                  => Auth::user()->nik ?? '9999',
                'barcode_id'           => $barcodeDb->id, 
                'stock_eng_id'         => $stock->id,
                'no_nozzle'            => $stock->no_nozzle ?? '-', 
                'rak_id'               => $stock->rak_id,      
                'qty_out'              => $qtyKeluar, 
                'status'               => 'SUCCESS',
                'remark'               => strtoupper($processType) . ' AUTOMATED TRANSACTIONS',
                'comment'              => $finalComment,
            ]);
            
            // 9. 📉 EKSEKUSI UTAMA: Kurangi 1 Stok Langsung Pada Baris Rak yang Sesuai
            $stock->decrement('qty', $qtyKeluar);

            // 🛠️ FIX LOGIC: Cari production_request_id dari jembatan data tabel barcode_parsings
            $parsingData = DB::table('barcode_parsings')
                ->where('barcode_out_id', $barcodeDb->id)
                ->first();

            // Jika ditemukan relasi parsingnya, ambil production_request_id asli milik barcode ini
            $productionRequestId = $parsingData ? $parsingData->production_request_id : null;

            // 10. 📝 GENERATE LOG UTAMA DENGAN FORMAT KODE BERURUTAN GLOBAL (TXENGOUT + DDMMYY + COUNTER 3 DIGIT)
            $datePrefix = 'TXENGOUT' . date('dmy'); // Format dinamis harian (Contoh hari ini: TXENGOUT020826)
            
            // Mengambil nomor terakhir hari ini dari gabungan data manual & scan di tabel stock_eng_transactions
            $latestTxLog = DB::table('stock_eng_transactions')
                ->where('tx_id', 'LIKE', $datePrefix . '%')
                ->orderBy('tx_id', 'desc')
                ->first();

            if (!$latestTxLog) {
                // Jika hari ini belum ada transaksi manual / scan sama sekali, start dari 001
                $txUuid = $datePrefix . '001';
            } else {
                // Jika sudah ada data transaksi sebelumnya, ambil 3 digit terakhir, ubah jadi integer, lalu increment +1
                $lastNumber = (int) substr($latestTxLog->tx_id, -3);
                $nextNumber = $lastNumber + 1;
                $txUuid = $datePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            // INSERT BUKU KORAN UTAMA (`stock_eng_transactions`)
            DB::table('stock_eng_transactions')->insert([
                'tx_id'                 => $txUuid, // 🔥 KODE KINI KONSISTEN BERURUTAN GLOBAL (Contoh: TXENGOUT020826001)
                'users_id'              => Auth::id() ?? 1,
                'stock_engs_id'         => $stock->id,
                'db_barcodes_id'        => $barcodeDb->id,
                'production_request_id' => $productionRequestId, 
                'tx_type'               => 'out',
                'qty_transaction'       => $qtyKeluar, 
                'process_type'          => $processType, 
                'photo_path'            => $fotoPath,    
                'status'                => 'success',
                'remark'                => 'Automated Stock OUT via ' . $processType . '. Rak: ' . ($stock->rak->nama_rak ?? '-'),
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // 11. 🔒 KUNCI BARCODE: Ubah status lifecycle menjadi USED_OUT agar tidak bisa ditembak ulang
            DB::table('db_barcodes')->where('id', $barcodeDb->id)->update([
                'current_lifecycle' => 'USED_OUT',
                'updated_at'        => now()
            ]);

            DB::commit();

            $sparepartName = $stock->sparepart->sparepart_id ?? $stock->sparepart->part_number ?? 'Item';
            return response()->json([
                'success' => true,
                'message' => 'Berhasil Keluar! Item [' . $sparepartName . '] di Rak [' . ($stock->rak->nama_rak ?? '-') . '] berkurang 1 Pcs. Sisa stok rak: ' . $stock->qty . ' Pcs.',
                'data'    => [
                    'part_name'  => $sparepartName,
                    'barcode_id' => $barcodeDb->barcode_id,
                    'remaining'  => $stock->qty 
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kegagalan sistem database: ' . $e->getMessage()
            ], 500);
        }
    }
}