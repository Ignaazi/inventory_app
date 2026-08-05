<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\StockEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInEngineeringController extends Controller
{
    /**
     * 📊 METHOD INDEX: Menampilkan log mutasi MASUK (IN) dari tabel transaksi utama
     */
    public function index(Request $request)
    {
        // Query riwayat transaksi IN dengan relasi User, Barcode, Material Received, Master Stok, & Rak
        $query = DB::table('stock_eng_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->leftJoin('barcode_parsings as bp', 'b.id', '=', 'bp.barcode_in_id')
            ->leftJoin('material_received as mr', 'bp.material_received_id', '=', 'mr.id')
            ->leftJoin('stock_engs as s', 't.stock_engs_id', '=', 's.id')
            ->leftJoin('raks as r', 's.rak_id', '=', 'r.id')
            ->leftJoin('spareparts as sp', 's.sparepart_id', '=', 'sp.id')
            ->select([
                't.*',
                'u.name as operator_name',
                'b.barcode_id as barcode_code',
                'b.final_content as barcode_final',
                'mr.no_mr as material_req_no',
                's.id as stock_eng_id_raw',
                'r.nama_rak as rak_name',
                'sp.sparepart_id as part_name',
                'sp.part_number'
            ])
            ->where('t.tx_type', 'in');

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
                  ->orWhere('mr.no_mr', 'LIKE', "%{$search}%")
                  ->orWhere('sp.sparepart_id', 'LIKE', "%{$search}%");
            });
        }

        $history = $query->latest('t.created_at')->paginate(10)->withQueryString();

        return view('stock_eng.transaction.in', compact('history'));
    }

    /**
     * 🔍 METHOD KONTROLER: TAMPILAN TERMINAL SCANNER IN
     */
    public function scan()
    {
        // Ambil ID barcode yang sudah pernah dipakai untuk transaksi IN
        $usedBarcodeIds = DB::table('stock_eng_transactions')
            ->where('tx_type', 'in')
            ->whereNotNull('db_barcodes_id')
            ->pluck('db_barcodes_id')
            ->toArray();

        // Tarik data master stok engineering beserta relasi sparepart & rak
        $stocks = StockEng::with(['sparepart', 'rak'])
            ->get()
            ->sortBy(function ($stock) {
                return $stock->sparepart->sparepart_id ?? $stock->sparepart->part_number ?? '';
            })
            ->values();

        // Tarik master barcode bertipe IN (Prefix TXENGIN)
        $barcodes = \App\Models\DbBarcode::where('barcode_id', 'LIKE', 'TXENGINRAK%')
            ->get()
            ->map(function($item) use ($usedBarcodeIds) {
                return [
                    'id'            => $item->id,
                    'barcode_id'    => trim($item->barcode_id),    
                    'final_content' => trim($item->final_content), 
                    'is_used'       => in_array($item->id, $usedBarcodeIds) || $item->current_lifecycle !== 'AVAILABLE',
                    'stock_eng_id'  => $item->stock_eng_id
                ];
            });

        $raks = \App\Models\Rak::orderBy('nama_rak', 'asc')->get();

        return view('stock_eng.transaction.in_scan', compact('stocks', 'barcodes', 'raks'));
    }

    /**
     * 📝 METHOD KONTROLER: FORM MANUAL IN
     */
    public function manual()
    {
        $stocks = StockEng::with(['sparepart', 'rak'])->get();
        $raks = \App\Models\Rak::orderBy('nama_rak', 'asc')->get();

        return view('stock_eng.transaction.in_manual', compact('stocks', 'raks'));
    }

    /**
     * 🚀 METHOD KONTROLER UNTUK: EKSEKUSI PROSES STOK MASUK (SCAN & MANUAL IN)
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'barcode_scan' => 'nullable|string',
            'stock_eng_id' => 'nullable|integer',
            'qty_in'       => 'nullable|integer|min:1',
            'process_type' => 'nullable|in:scan,manual', 
        ]);

        $processType = $request->input('process_type', 'scan');
        $scannedInput = trim($request->input('barcode_scan', ''));
        $scannedInput = str_replace(["\r", "\n", "\t"], '', $scannedInput);

        DB::beginTransaction();
        try {
            $barcodeDb = null;
            $stock = null;
            $qtyMasuk = (int) $request->input('qty_in', 1);

            // ----------------------------------------------------
            // MODE 1: PROSES SCANNER (Mencari via Barcode Scanned)
            // ----------------------------------------------------
            if (!empty($scannedInput)) {
                $barcodeDb = \App\Models\DbBarcode::where(function ($query) use ($scannedInput) {
                    $query->where('barcode_id', $scannedInput)
                          ->orWhere('final_content', $scannedInput);
                })
                    ->lockForUpdate()
                    ->first();

                if (!$barcodeDb) {
                    DB::rollBack();
                    return $this->buildResponse($request, false, 'Gagal! Kode Barcode "' . $scannedInput . '" tidak terdaftar di database master.', 404);
                }

                // Barcode OUT tidak boleh diproses melalui terminal Stock IN.
                if (!str_starts_with(strtoupper(trim($barcodeDb->barcode_id)), 'TXENGINRAK')) {
                    DB::rollBack();
                    return $this->buildResponse($request, false, 'Gagal! Barcode ini bukan Barcode IN Engineering dan tidak dapat diproses di terminal Stock IN.', 422);
                }

                // Validasi Status Lifecycle Barcode
                if ($barcodeDb->current_lifecycle !== 'AVAILABLE') {
                    DB::rollBack();
                    return $this->buildResponse($request, false, 'Gagal! Barcode ' . $barcodeDb->barcode_id . ' sudah pernah di-scan / tidak aktif (Status: ' . $barcodeDb->current_lifecycle . ').', 422);
                }

                // Cek pemetaan ke Master Stock
                if (!$barcodeDb->stock_eng_id) {
                    DB::rollBack();
                    return $this->buildResponse($request, false, 'Gagal! Barcode ini belum dipetakan ke item master gudang.', 422);
                }

                $stock = StockEng::with(['sparepart', 'rak'])
                    ->lockForUpdate()
                    ->find($barcodeDb->stock_eng_id);
            } 
            // ----------------------------------------------------
            // MODE 2: PROSES MANUAL (Mencari via Input Form Direct)
            // ----------------------------------------------------
            else if ($request->filled('stock_eng_id')) {
                $stock = StockEng::with(['sparepart', 'rak'])
                    ->lockForUpdate()
                    ->find($request->stock_eng_id);
            } else {
                DB::rollBack();
                return $this->buildResponse($request, false, 'Gagal! Barcode scan atau pilih item barang wajib diisi.', 400);
            }

            if (!$stock) {
                DB::rollBack();
                return $this->buildResponse($request, false, 'Gagal! Master stok barang tidak ditemukan atau telah dihapus.', 404);
            }

            // Validasi Penempatan Rak
            if (empty($stock->rak_id)) {
                DB::rollBack();
                return $this->buildResponse($request, false, 'Gagal! Posisi Rak untuk item ini belum ditentukan pada master data.', 422);
            }

            // 2. Handling Dokumentasi / Foto
            $fotoPath = null;
            if ($request->hasFile('test_photo')) {
                $fotoPath = $request->file('test_photo')->store('stock_ins', 'public');
            }

            // 3. 📈 EKSEKUSI UTAMA: TAMBAH STOK FISIK
            $stock->increment('qty', $qtyMasuk);

            // 4. Cari relasi Dokumen Material Received dari `barcode_parsings` jika berasal dari Scan Barcode
            $materialReceivedId = null;
            if ($barcodeDb) {
                $parsingData = DB::table('barcode_parsings')
                    ->where('barcode_in_id', $barcodeDb->id)
                    ->first();
                $materialReceivedId = $parsingData ? $parsingData->material_received_id : null;
            }

            // 5. GENERATE TX_ID UNIK BERURUTAN (Format: TXENGIN + DDMMYY + COUNTER 3 DIGIT)
            $datePrefix = 'TXENGIN' . date('dmy'); // Contoh: TXENGIN040826
            
            $latestTxLog = DB::table('stock_eng_transactions')
                ->where('tx_id', 'LIKE', $datePrefix . '%')
                ->orderBy('tx_id', 'desc')
                ->first();

            if (!$latestTxLog) {
                $txUuid = $datePrefix . '001';
            } else {
                $lastNumber = (int) substr($latestTxLog->tx_id, -3);
                $nextNumber = $lastNumber + 1;
                $txUuid = $datePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            }

            // Insert data transaksi ke database
            DB::table('stock_eng_transactions')->insert([
                'tx_id'                 => $txUuid,
                'users_id'              => Auth::id() ?? 1,
                'stock_engs_id'         => $stock->id,
                'db_barcodes_id'        => $barcodeDb ? $barcodeDb->id : null,
                'production_request_id' => null,
                'tx_type'               => 'in',
                'qty_transaction'       => $qtyMasuk,
                'process_type'          => $processType,
                'photo_path'            => $fotoPath,
                'status'                => 'success',
                'remark'                => $request->input('comment', 'AUTOMATIC IN'),
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // 7. 🔒 UPDATE LIFECYCLE BARCODE (Ubah status menjadi USED_IN)
            if ($barcodeDb) {
                DB::table('db_barcodes')->where('id', $barcodeDb->id)->update([
                    'current_lifecycle' => 'USED_IN',
                    'updated_at'        => now()
                ]);
            }

            DB::commit();

            $sparepartName = $stock->sparepart->sparepart_id ?? $stock->sparepart->part_number ?? 'Item';
            $successMsg = 'Berhasil Masuk! Item [' . $sparepartName . '] di Rak [' . ($stock->rak->nama_rak ?? '-') . '] bertambah ' . $qtyMasuk . ' Pcs. Total stok rak sekarang: ' . $stock->qty . ' Pcs.';

            return $this->buildResponse($request, true, $successMsg, 200, [
                'part_name'  => $sparepartName,
                'barcode_id' => $barcodeDb ? $barcodeDb->barcode_id : '-',
                'total_qty'  => $stock->qty
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->buildResponse($request, false, 'Terjadi kegagalan sistem database internal: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Helper privat untuk membedakan output AJAX JSON vs Form Redirect Blade
     */
    private function buildResponse(Request $request, bool $isSuccess, string $message, int $statusCode = 200, array $data = [])
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => $isSuccess,
                'message' => $message,
                'data'    => $data
            ], $statusCode);
        }

        if ($isSuccess) {
            return redirect()->route('eng.in.index')->with('success', $message);
        }

        return redirect()->back()->withInput()->with('error', $message);
    }
}
