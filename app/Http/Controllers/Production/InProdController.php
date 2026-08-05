<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ListSparepartEng;
use App\Models\Production\stock_prod;

class InProdController extends Controller
{
    /**
     * Helper internal untuk mendeteksi Model Line secara dinamis
     */
    private function getLineModel()
    {
        $modelUtama = 'App\\Models\\ListLineProduction';
        $modelSubFolder = 'App\\Models\\Production\\ListLineProduction';
        return class_exists($modelUtama) ? $modelUtama : $modelSubFolder;
    }

    /**
     * Helper internal untuk mendapatkan nama tabel Line secara dinamis
     */
    private function getLineTableName()
    {
        $lineModel = $this->getLineModel();
        return (new $lineModel)->getTable();
    }

    /**
     * Tampilan Utama Halaman Monitoring / Log Transaksi Masuk
     */
    public function stockIn()
    {
        $tableSparepart = (new ListSparepartEng)->getTable();
        $tableLine = $this->getLineTableName();

        $history = DB::table('stock_prod_transactions as t')
            ->leftJoin('users as u', 't.users_id', '=', 'u.id')
            ->leftJoin('stock_prods as sp', 't.stock_prods_id', '=', 'sp.id')
            ->leftJoin($tableLine . ' as lp', 'sp.line_id', '=', 'lp.id') 
            ->leftJoin($tableSparepart . ' as se', 'sp.sparepart_id', '=', 'se.id') 
            ->leftJoin('db_barcodes as b', 't.db_barcodes_id', '=', 'b.id')
            ->select([
                't.*',
                'u.nik as nik', // <- Ditambahkan agar NIK Operator muncul di tabel
                'u.name as operator_name',
                'lp.no_line as line_name',
                'se.sparepart_id as item_code',
                'b.barcode_id as barcode_code'
            ])
            ->where('t.tx_type', 'in')
            ->orderBy('t.created_at', 'desc')
            ->paginate(10);
            
        return view('stock_prod.transactionProd.inProd', compact('history'));
    }

    /**
     * Halaman Khusus Fitur Scan QR / Barcode IN (Terminal Hijau)
     */
    public function scanIn()
    {
        return view('stock_prod.transactionProd.inProd_scan');
    }

    /**
     * Halaman Form Input Manual (Dropdown Mode)
     */
    public function stockInManual()
    {
        $tableLine = $this->getLineTableName();
        $lines = DB::table($tableLine)->orderBy('no_line', 'asc')->get();

        $barcodes = DB::table('db_barcodes')
            ->where('current_lifecycle', 'USED_OUT')
            ->orderBy('barcode_id', 'asc')
            ->get();

        return view('stock_prod.transactionProd.manual_inProd', compact('lines', 'barcodes'));
    }

    /**
     * JEMBATAN ALIAS: Menangani request dari form route manual storeManualIn
     */
    public function storeManualIn(Request $request)
    {
        return $this->store($request);
    }

    /**
     * CORE ENGINE: Eksekusi Scan IN & Manual IN (ANTI-CRASH EDITION)
     */
    public function store(Request $request)
    {
        $request->validate([
            'barcode_scan' => 'required|string',
            'process_type' => 'nullable|in:scan,manual',
            'line_id'      => 'nullable' 
        ]);

        $scannedInput = trim($request->barcode_scan);
        $scannedInput = str_replace(["\r", "\n", "\t"], '', $scannedInput);
        $processType  = $request->input('process_type', 'scan');
        $isAjax       = $request->expectsJson(); 
        
        $qtyMasuk     = 1; 

        DB::beginTransaction();
        try {
            // 1. Ambil Master Data Barcode
            $barcodeDb = DB::table('db_barcodes')
                ->where('barcode_id', $scannedInput)
                ->orWhere('final_content', $scannedInput)
                ->first();

            if (!$barcodeDb) {
                return $this->buildResponse($isAjax, false, 'Gagal! Kode "' . $scannedInput . '" tidak terdaftar di database master.', 404);
            }

            // 2. Lifecycle Guard
            if ($barcodeDb->current_lifecycle === 'AVAILABLE') {
                return $this->buildResponse($isAjax, false, 'Ditolak! Item masih berstatus AVAILABLE di Engineering (Belum scan OUT).', 422);
            }
            if (in_array($barcodeDb->current_lifecycle, ['USED_IN', 'ON_PRODUCTION'])) {
                return $this->buildResponse($isAjax, false, 'Double Scan! Barcode ' . $barcodeDb->barcode_id . ' ini sudah masuk di Lini Produksi.', 422);
            }
            if ($barcodeDb->current_lifecycle !== 'USED_OUT') {
                return $this->buildResponse($isAjax, false, 'Gagal! Siklus hidup barcode tidak valid (Status: ' . $barcodeDb->current_lifecycle . ').', 422);
            }

            // 3. Hubungkan ke Transaksi Hulu Engineering
            $engTx = DB::table('stock_eng_transactions')
                ->where('db_barcodes_id', $barcodeDb->id)
                ->where('tx_type', 'out')
                ->first();

            if (!$engTx) {
                return $this->buildResponse($isAjax, false, 'Gagal! Riwayat pengeluaran dari Gudang Engineering tidak ditemukan.', 404);
            }

            // 4. FIXED ENGINE: 2-DIGIT BOUNDED LINE RESOLVER
            $targetLineId = $request->input('line_id'); 
            $tableLine = $this->getLineTableName();
            $allLines = DB::table($tableLine)->get();

            if (!$targetLineId) {
                if (preg_match('/LINE(\d{2})/i', $scannedInput, $matches)) {
                    $lineNoClean = (int)$matches[1]; 
                    
                    foreach ($allLines as $lineItem) {
                        preg_match('/\d+/', $lineItem->no_line, $dbLineMatches);
                        if (isset($dbLineMatches[0]) && (int)$dbLineMatches[0] === $lineNoClean) {
                            $targetLineId = $lineItem->id;
                            break;
                        }
                        $paddedTarget = str_pad($lineNoClean, 3, '0', STR_PAD_LEFT); 
                        if (str_contains($lineItem->line_id, $paddedTarget)) {
                            $targetLineId = $lineItem->id;
                            break;
                        }
                    }
                }
            }

            if (!$targetLineId && $engTx->production_request_id) {
                $productionRequest = DB::table('production_requests')->where('id', $engTx->production_request_id)->first();
                if ($productionRequest) {
                    foreach ((array)$productionRequest as $columnName => $columnValue) {
                        if (in_array(strtolower($columnName), ['line', 'line_id', 'no_line']) && $columnValue) {
                            foreach ($allLines as $lineItem) {
                                if ($lineItem->id == $columnValue || 
                                    strcasecmp($lineItem->line_id, $columnValue) === 0 || 
                                    strcasecmp($lineItem->no_line, $columnValue) === 0) {
                                    $targetLineId = $lineItem->id;
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }

            if (!$targetLineId) {
                return $this->buildResponse(
                    $isAjax, 
                    false, 
                    'Gagal mendeteksi target Lini untuk Barcode: "' . $scannedInput . '". Silakan daftarkan item via menu Input Manual Dropdown.', 
                    422
                );
            }

            // 5. Ambil Data Master Komponen Induk dari Engineering
            $stockEng = DB::table('stock_engs')->where('id', $barcodeDb->stock_eng_id)->first();
            if (!$stockEng) {
                return $this->buildResponse($isAjax, false, 'Gagal! Master item asal dari Engineering tidak ditemukan.', 404);
            }
            $sparepartId = $stockEng->sparepart_id;

            // 6. 🛡️ SINKRONISASI & PROTEKSI MISMATCH ALOKASI
            $stockProd = stock_prod::where('line_id', $targetLineId)
                ->where('sparepart_id', $sparepartId)
                ->first();

            // 🛑 JIKA ALOKASI TIDAK ADA DI MASTER PRODUKSI, CEGAH CRASH & TOLAK RAMAH
            if (!$stockProd) {
                $tableSparepart = (new ListSparepartEng)->getTable();
                $itemName = DB::table($tableSparepart)->where('id', $sparepartId)->value('sparepart_id') ?? 'Sparepart';
                
                $lineName = DB::table($tableLine)->where('id', $targetLineId)->value('no_line') ?? 'Lini Terkait';

                return $this->buildResponse(
                    $isAjax, 
                    false, 
                    "🚨 Mismatch Lini! Sparepart [{$itemName}] tidak terdaftar/dialokasikan untuk {$lineName}. (Lini ini hanya mendukung alokasi komponen tipe tertentu).", 
                    422
                );
            }

            // Jika relasi valid dan aman, jalankan penambahan stok
            $stockProd->increment('qty', $qtyMasuk);

            // 7. PENCATATAN LOG TRANSAKSI BARU (stock_prod_transactions)
            $datePrefix = 'TXPRODIN' . date('dmy');
            $latestTxLog = DB::table('stock_prod_transactions')
                ->where('tx_id', 'LIKE', $datePrefix . '%')
                ->orderBy('tx_id', 'desc')
                ->first();

            $txUuid = !$latestTxLog 
                ? $datePrefix . '001' 
                : $datePrefix . str_pad(((int) substr($latestTxLog->tx_id, -3)) + 1, 3, '0', STR_PAD_LEFT);

            // Remark disetting konsisten menjadi "AUTOMATED IN"
            $remarkText = $request->input('remark') ?? $request->input('comment') ?? 'AUTOMATED IN';

            DB::table('stock_prod_transactions')->insert([
                'tx_id'                 => $txUuid,
                'users_id'              => Auth::id() ?? 1,
                'stock_prods_id'        => $stockProd->id,
                'stock_eng_tx_id'       => $engTx->id,
                'db_barcodes_id'        => $barcodeDb->id,
                'production_request_id' => $engTx->production_request_id,
                'tx_type'               => 'in',
                'qty_transaction'       => $qtyMasuk,
                'process_type'          => $processType,
                'status'                => 'success',
                'remark'                => $remarkText,
                'created_at'            => now(),
                'updated_at'            => now()
            ]);

            // 8. KUNCI STATUS SIKLUS HIDUP BARCODE
            DB::table('db_barcodes')->where('id', $barcodeDb->id)->update([
                'current_lifecycle' => 'USED_IN',
                'updated_at'        => now()
            ]);

            DB::commit();

            $tableSparepart = (new ListSparepartEng)->getTable();
            $itemName = DB::table($tableSparepart)->where('id', $sparepartId)->value('sparepart_id') ?? 'Sparepart';
            $msgSuccess = 'Sukses! Item [' . $itemName . '] berhasil ditambahkan ke Lini Produksi. Qty Saat Ini: ' . $stockProd->qty . ' Pcs.';

            if ($isAjax) {
                return response()->json(['success' => true, 'message' => $msgSuccess]);
            }
            return redirect()->route('prod.transaction.in')->with('success', $msgSuccess);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->buildResponse($isAjax, false, 'Terjadi kegagalan sistem database: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Helper internal pemisah format respon
     */
    private function buildResponse($isAjax, $success, $message, $statusCode = 200)
    {
        if ($isAjax) {
            return response()->json(['success' => $success, 'message' => $message], $statusCode);
        }
        return redirect()->back()->withInput()->with('error', $message);
    }

    public function getEngineeringDetail($id)
    {
        $detail = DB::table('stock_engs')->where('id', $id)->first();
        return response()->json($detail);
    }
}