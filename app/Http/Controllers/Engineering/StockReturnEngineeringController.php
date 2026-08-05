<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\StockEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockReturnEngineeringController extends Controller
{
    /**
     * 📊 METHOD INDEX: Menampilkan log mutasi KEMBALI (RETURN) dari tabel transaksi utama
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        // Query riwayat transaksi RETURN dengan Join lengkap
        $query = DB::table('stock_eng_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->leftJoin('production_requests as p', 't.production_request_id', '=', 'p.id')
            ->leftJoin('stock_engs as s', 't.stock_engs_id', '=', 's.id')
            ->leftJoin('raks as r', 's.rak_id', '=', 'r.id')
            ->leftJoin('spareparts as sp', 's.sparepart_id', '=', 'sp.id')
            ->select([
                't.*',
                'u.nik',
                'u.name',
                'b.barcode_id',
                'p.request_no',
                'sp.sparepart_id',
                'r.nama_rak'
            ])
            ->where('t.tx_type', 'return');

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
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('t.tx_id', 'LIKE', "%{$search}%")
                  ->orWhere('t.remark', 'LIKE', "%{$search}%")
                  ->orWhere('u.nik', 'LIKE', "%{$search}%")
                  ->orWhere('u.name', 'LIKE', "%{$search}%")
                  ->orWhere('b.barcode_id', 'LIKE', "%{$search}%")
                  ->orWhere('p.request_no', 'LIKE', "%{$search}%")
                  ->orWhere('sp.sparepart_id', 'LIKE', "%{$search}%")
                  ->orWhere('r.nama_rak', 'LIKE', "%{$search}%");
            });
        }

        $history = $query->orderBy('t.created_at', 'desc')
                        ->paginate($perPage)
                        ->appends($request->all());

        return view('stock_eng.transaction.return', compact('history'));
    }

    /**
     * 🔍 METHOD KONTROLER: TAMPILAN TERMINAL SCANNER RETURN
     */
    public function scan()
    {
        // Ambil ID barcode yang sedang dalam status USED_OUT (Yang bisa di-return)
        $outBarcodeIds = DB::table('stock_eng_transactions')
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

        // Ambil barcode yang layak di-return (current_lifecycle = 'USED_OUT')
        $barcodes = \App\Models\DbBarcode::get()->map(function($item) use ($outBarcodeIds) {
            return [
                'id'            => $item->id,
                'barcode_id'    => trim($item->barcode_id),    
                'final_content' => trim($item->final_content), 
                'can_return'    => $item->current_lifecycle === 'USED_OUT',
                'stock_eng_id'  => $item->stock_eng_id
            ];
        });

        $raks = \App\Models\Rak::orderBy('nama_rak', 'asc')->get();

        return view('stock_eng.transaction.return_scan', compact('stocks', 'barcodes', 'raks'));
    }

    /**
     * 🚀 METHOD KONTROLER: EKSEKUSI PROSES RETURN STOK
     */
    public function store(Request $request)
    {
        $request->validate([
            'barcode_scan' => 'required|string',
            'process_type' => 'nullable|in:scan,manual',
        ]);

        if (empty($request->barcode_scan)) {
            return response()->json(['success' => false, 'message' => 'Gagal! Barcode tidak terbaca.'], 400);
        }

        $scannedInput = trim($request->barcode_scan);
        $scannedInput = str_replace(["\r", "\n", "\t"], '', $scannedInput);
        $processType = $request->input('process_type', 'scan');
        $qtyReturn = 1;

        DB::beginTransaction();
        try {
            $reject = static function (string $message, int $status = 422) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $status);
            };

            // Kunci barcode selama proses agar dua scanner tidak mengembalikan item yang sama.
            $barcodeDb = \App\Models\DbBarcode::where(function ($query) use ($scannedInput) {
                    $query->where('barcode_id', $scannedInput)
                          ->orWhere('final_content', $scannedInput);
                })
                ->lockForUpdate()
                ->first();

            if (!$barcodeDb) {
                return $reject('Gagal! Kode Barcode "' . $scannedInput . '" tidak terdaftar di database master.', 404);
            }

            if ($barcodeDb->current_lifecycle !== 'USED_OUT') {
                return $reject(
                    'Gagal! Barcode ' . $barcodeDb->barcode_id . ' tidak berada pada status yang dapat di-return '
                    . '(status saat ini: ' . $barcodeDb->current_lifecycle . ').'
                );
            }

            if (!$barcodeDb->stock_eng_id) {
                return $reject('Gagal! Barcode ini tidak terhubung dengan master stok barang.');
            }

            // Return hanya boleh berasal dari Engineering OUT yang sukses.
            $engineeringOut = DB::table('stock_eng_transactions')
                ->where('db_barcodes_id', $barcodeDb->id)
                ->where('tx_type', 'out')
                ->where('status', 'success')
                ->latest('id')
                ->first();

            if (!$engineeringOut) {
                return $reject('Gagal! Riwayat Engineering OUT untuk barcode ini tidak ditemukan.');
            }

            // Dua alur yang valid:
            // 1) Engineering OUT lalu langsung return sebelum Production IN.
            // 2) Engineering OUT, Production IN, Production OUT, lalu return.
            $productionIn = DB::table('stock_prod_transactions')
                ->where('db_barcodes_id', $barcodeDb->id)
                ->where('tx_type', 'in')
                ->where('status', 'success')
                ->latest('id')
                ->first();
            $productionOut = DB::table('stock_prod_transactions')
                ->where('db_barcodes_id', $barcodeDb->id)
                ->where('tx_type', 'out')
                ->where('status', 'success')
                ->latest('id')
                ->first();

            if ($productionIn && !$productionOut) {
                return $reject(
                    'Return ditolak! Barcode sudah masuk ke Production, tetapi belum di-OUT. '
                    . 'Lakukan Production OUT terlebih dahulu.'
                );
            }

            if ($productionOut && !$productionIn) {
                return $reject('Gagal! Riwayat Production OUT tidak memiliki pasangan Production IN.');
            }

            $returnScenario = $productionIn
                ? 'setelah Production IN dan OUT'
                : 'sebelum Production IN';

            $stock = StockEng::with(['sparepart', 'rak'])
                ->lockForUpdate()
                ->find($barcodeDb->stock_eng_id);
            if (!$stock) {
                return $reject('Gagal! Data master stok untuk item ini tidak ditemukan.', 404);
            }

            $fotoPath = null;
            if ($request->hasFile('test_photo')) {
                $fotoPath = $request->file('test_photo')->store('stock_returns', 'public');
            }

            $stock->increment('qty', $qtyReturn);

            // Ambil referensi request dari transaksi OUT terlebih dahulu, lalu fallback ke parsing lama.
            $productionRequestId = $engineeringOut->production_request_id;
            if (!$productionRequestId) {
                $parsingData = DB::table('barcode_parsings')
                    ->where(function ($query) use ($barcodeDb) {
                        $query->where('barcode_out_id', $barcodeDb->id)
                              ->orWhere('barcode_in_id', $barcodeDb->id);
                    })
                    ->first();
                $productionRequestId = $parsingData?->production_request_id;
            }

            $datePrefix = 'TXENGRET' . date('dmy');
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

            DB::table('stock_eng_transactions')->insert([
                'tx_id'                 => $txUuid,
                'users_id'              => Auth::id() ?? 1,
                'stock_engs_id'         => $stock->id,
                'db_barcodes_id'        => $barcodeDb->id,
                'production_request_id' => $productionRequestId,
                'tx_type'               => 'return',
                'qty_transaction'       => $qtyReturn,
                'process_type'          => $processType,
                'photo_path'            => $fotoPath,
                'status'                => 'success',
                'remark'                => $request->input('comment', 'AUTOMATIC RETURN'),
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            // Barcode return adalah barcode sekali pakai: stok kembali, barcode tidak diaktifkan lagi.
            DB::table('db_barcodes')->where('id', $barcodeDb->id)->update([
                'current_lifecycle' => 'RETURNED',
                'updated_at'        => now()
            ]);

            DB::commit();

            $sparepartName = $stock->sparepart->sparepart_id ?? $stock->sparepart->part_number ?? 'Item';
            return response()->json([
                'success' => true,
                'message' => 'Berhasil Return (' . $returnScenario . ')! Item [' . $sparepartName . '] telah '
                    . 'dikembalikan ke Rak [' . ($stock->rak->nama_rak ?? '-') . ']. Stok rak sekarang: '
                    . $stock->qty . ' Pcs. Barcode ditutup dan tidak dapat dipakai lagi.',
                'data'    => [
                    'part_name'  => $sparepartName,
                    'barcode_id' => $barcodeDb->barcode_id,
                    'total_qty'  => $stock->qty,
                    'scenario'   => $returnScenario,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem internal: ' . $e->getMessage()
            ], 500);
        }
    }
}
