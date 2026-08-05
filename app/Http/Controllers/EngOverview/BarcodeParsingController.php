<?php

namespace App\Http\Controllers\EngOverview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;

class BarcodeParsingController extends Controller
{
    /**
     * Menampilkan halaman utama barcode builder / parsing (Automated Version)
     */
    public function index()
    {
        // Ambil list ID dokumen yang sudah pernah diproses di Header
        $parsedPrIds = DB::table('barcode_parsing_headers')
                        ->whereNotNull('production_request_id')
                        ->pluck('production_request_id');

        $parsedMrIds = DB::table('barcode_parsing_headers')
                        ->whereNotNull('material_received_id')
                        ->pluck('material_received_id');

        // 1. Ambil data request produksi dengan JOIN ke tabel spareparts (Hanya yang belum diproses)
        $productionRequests = DB::table('production_requests')
                                ->leftJoin('spareparts', 'production_requests.sparepart_id', '=', 'spareparts.id')
                                ->where('production_requests.status', 'Approved')
                                ->whereNotIn('production_requests.id', $parsedPrIds)
                                ->select(
                                    'production_requests.*',
                                    'spareparts.sparepart_id as custom_sparepart_code',
                                    'spareparts.part_number',
                                    'spareparts.sap_code'
                                )
                                ->orderBy('production_requests.id', 'desc')
                                ->get()
                                ->map(function ($pr) {
                                    $pr->sparepart = (object) [
                                        'id'          => $pr->sparepart_id, 
                                        'part_no'     => $pr->custom_sparepart_code, 
                                        'part_name'   => $pr->custom_sparepart_code,
                                        'part_number' => $pr->part_number,
                                        'sap_code'    => $pr->sap_code
                                    ];
                                    return $pr;
                                });

        // 2. Ambil data material received DENGAN JOIN via purchase_requests ke spareparts (Hanya yang belum diproses)
        $materialReceived = DB::table('material_received')
                                ->leftJoin('purchase_requests', 'material_received.purchase_request_id', '=', 'purchase_requests.id')
                                ->leftJoin('spareparts', 'purchase_requests.sparepart_id', '=', 'spareparts.id')
                                ->whereNotIn('material_received.id', $parsedMrIds)
                                ->select(
                                    'material_received.*',
                                    'purchase_requests.sparepart_id',
                                    'spareparts.sparepart_id as custom_sparepart_code',
                                    'spareparts.part_number',
                                    'spareparts.sap_code'
                                )
                                ->orderBy('material_received.id', 'desc')
                                ->get()
                                ->map(function ($mr) {
                                    $mr->sparepart = (object) [
                                        'id'          => $mr->sparepart_id, 
                                        'part_no'     => $mr->custom_sparepart_code, 
                                        'part_name'   => $mr->custom_sparepart_code,
                                        'part_number' => $mr->part_number,
                                        'sap_code'    => $mr->sap_code
                                    ];
                                    return $mr;
                                });

        // 3. Data master stok engineering
        $stockEngineering = DB::table('stock_engs')
                                ->join('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id')
                                ->join('raks', 'stock_engs.rak_id', '=', 'raks.id')
                                ->select(
                                    'stock_engs.id as stock_id',
                                    'stock_engs.sparepart_id as sparepart_id', 
                                    'spareparts.sparepart_id as part_name', 
                                    'spareparts.part_number',              
                                    'spareparts.sap_code',                 
                                    'raks.nama_rak as rak_name'
                                )
                                ->orderBy('spareparts.sparepart_id', 'asc')
                                ->get();

        return view('eng_overview.barcode_parsing', compact('productionRequests', 'stockEngineering', 'materialReceived'));
    }

    /**
     * Menyimpan data secara Otomatis & Massal (Batching Engine) berdasarkan Dokumen Asal & Lokasi Rak
     */
    public function store(Request $request)
    {
        $request->validate([
            'mode'               => 'required|in:IN,OUT',
            'source_id'          => 'required|integer',
            'barcode_type'       => 'required|string',
            'barcode_size'       => 'nullable|string',
            'stock_eng_id'       => 'required|integer'
        ]);

        try {
            DB::beginTransaction();

            $currentUserId = Auth::id();
            $mode          = $request->mode;
            $sourceId      = $request->source_id;
            $stockEngId    = $request->stock_eng_id;
            $barcodeType   = $request->barcode_type;
            $barcodeSize   = $mode === 'IN' ? '10' : ($request->barcode_size ?: '10');

            $stockEngRecord = DB::table('stock_engs')->where('id', $stockEngId)->first();
            if (!$stockEngRecord) {
                return response()->json(['success' => false, 'message' => 'Lokasi Rak atau Data Stok Engineering tidak ditemukan!'], 404);
            }

            // -------------------------------------------------------------
            // STEP 1: SIMPAN HEADER (Kunci duplikasi via UNIQUE CONSTRAINT Database)
            // -------------------------------------------------------------
            try {
                $headerId = DB::table('barcode_parsing_headers')->insertGetId([
                    'users_id'              => $currentUserId,
                    'mode'                  => $mode,
                    'material_received_id'  => $mode === 'IN' ? $sourceId : null,
                    'production_request_id' => $mode === 'OUT' ? $sourceId : null,
                    'total_qty'             => 0, // Akan di-update setelah loop selesai
                    'status'                => 'completed',
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);
            } catch (QueryException $e) {
                DB::rollBack();
                if ($e->getCode() == 23000 || str_contains($e->getMessage(), '1062')) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Gagal! Dokumen ini sudah PERNAH diproses sebelumnya (Mencegah Duplikasi).'
                    ], 400);
                }
                throw $e;
            }

            // -------------------------------------------------------------
            // STEP 2: PROSES GENERATE BARCODE
            // -------------------------------------------------------------
            if ($mode === 'IN') {
                // =======================================================
                // 📥 LOGIKA MODE IN: PURE PEMBUATAN BARCODE BARU (MATERIAL BARU)
                // =======================================================
                $mrDoc = DB::table('material_received')->where('id', $sourceId)->first();
                if (!$mrDoc) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Dokumen Material Received tidak valid!'], 404);
                }

                if ((int) $stockEngRecord->sparepart_id !== (int) DB::table('purchase_requests')
                    ->where('id', $mrDoc->purchase_request_id)
                    ->value('sparepart_id')) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Rak tujuan tidak sesuai dengan sparepart pada Material Received.'
                    ], 400);
                }

                $qty = (int) $mrDoc->qty_received;

                // Ambil info Rak & Sparepart untuk fallback pembuatan string di PHP
                $stockDetails = DB::table('stock_engs')
                                    ->join('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id')
                                    ->join('raks', 'stock_engs.rak_id', '=', 'raks.id')
                                    ->where('stock_engs.id', $stockEngId)
                                    ->select('raks.nama_rak', 'spareparts.sparepart_id as sp_code')
                                    ->first();

                $rawRak   = $stockDetails->nama_rak ?? '1';
                $cleanRak = preg_replace('/[^0-9]/', '', $rawRak);
                $cleanRak = $cleanRak !== '' ? str_pad($cleanRak, 2, '0', STR_PAD_LEFT) : '01';

                $rawSp   = $stockDetails->sp_code ?? 'SP';
                $cleanSp = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $rawSp));

                $dateKey = now()->format('dmy');
                $prefix  = "TXENGINRAK{$cleanRak}{$cleanSp}";

                // Counter IN global untuk semua rak dan sparepart pada hari yang sama.
                DB::table('barcode_daily_counters')->insertOrIgnore([
                    'date_key'     => $dateKey,
                    'last_counter' => 0,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                $dailyCounter = DB::table('barcode_daily_counters')
                                  ->where('date_key', $dateKey)
                                  ->lockForUpdate()
                                  ->first();

                $localCounter = max(
                    (int) ($dailyCounter->last_counter ?? 0),
                    $this->getExistingInboundCounterForToday()
                );

                if ($localCounter + $qty > 9999) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Counter barcode IN hari ini sudah mencapai batas 9999.'
                    ], 400);
                }

                for ($i = 1; $i <= $qty; $i++) {
                    $localCounter++;
                    $generatedBarcodeId = $prefix . $dateKey . str_pad($localCounter, 4, '0', STR_PAD_LEFT);

                    $barcodeInId = DB::table('db_barcodes')->insertGetId([
                        'barcode_id'        => $generatedBarcodeId,
                        'users_id'          => $currentUserId,
                        'barcode_type'      => $barcodeType,
                        'barcode_size'      => $barcodeSize,
                        'final_content'     => $generatedBarcodeId,
                        'stock_eng_id'      => $stockEngId, 
                        'current_lifecycle' => 'AVAILABLE', 
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    DB::table('barcode_parsings')->insert([
                        'parsing_header_id'     => $headerId,
                        'users_id'              => $currentUserId,
                        'production_request_id' => null,               
                        'material_received_id'  => $sourceId,           
                        'barcode_in_id'         => $barcodeInId,       
                        'barcode_out_id'        => null,               
                        'qty_parsed'            => 1,
                        'status'                => 'success',
                        'remark'                => 'Automated Batch IN from Doc MR: ' . ($mrDoc->no_mr ?? $sourceId),
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                }

                DB::table('barcode_daily_counters')
                  ->where('date_key', $dateKey)
                  ->update(['last_counter' => $localCounter, 'updated_at' => now()]);

                // Update Total Qty di Header & Update Status MR
                DB::table('barcode_parsing_headers')->where('id', $headerId)->update(['total_qty' => $qty]);
                DB::table('material_received')->where('id', $sourceId)->update(['qty_status' => 'closed', 'updated_at' => now()]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Sukses memproses Batch IN! Berhasil mendaftarkan {$qty} data barcode baru ke sistem."
                ]);

            } else {
                // =======================================================
                // 📤 LOGIKA MODE OUT: PURE PENERBITAN BARCODE OUT KE LINI
                // =======================================================
                $prDoc = DB::table('production_requests')->where('id', $sourceId)->first();
                if (!$prDoc) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Dokumen Production Request tidak valid!'], 404);
                }

                $qty = (int) $prDoc->qty_req;
                
                if ($stockEngRecord->qty < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false, 
                        'message' => "Stok di Rak ini tidak mencukupi! Sisa stok saat ini: {$stockEngRecord->qty} pcs, sedangkan permintaan: {$qty} pcs."
                    ], 400);
                }

                $lineRaw = $prDoc->list_line_production_id ?? '01';
                $lineStr = str_pad($lineRaw, 2, '0', STR_PAD_LEFT); 
                
                $rakRecord = DB::table('stock_engs')
                                ->join('raks', 'stock_engs.rak_id', '=', 'raks.id')
                                ->where('stock_engs.id', $stockEngId)
                                ->select('raks.nama_rak')
                                ->first();

                $rawRakName       = $rakRecord ? $rakRecord->nama_rak : '00';
                $cleanRakName     = trim(str_replace(['[', ']'], '', $rawRakName));
                $formattedRakCode = str_starts_with(strtoupper($cleanRakName), 'RAK') ? strtoupper($cleanRakName) : 'RAK' . str_pad($cleanRakName, 2, '0', STR_PAD_LEFT);

                $sparepartRecord = DB::table('spareparts')->where('id', $prDoc->sparepart_id)->first();
                $partCode        = '00';
                if ($sparepartRecord) {
                    $partCode = $sparepartRecord->sparepart_id ?? '00'; 
                }

                $barcodePrefix = "TXENG{$formattedRakCode}LINE{$lineStr}{$partCode}";

                $dateKey = now()->format('dmy');
                DB::table('barcode_out_daily_counters')->insertOrIgnore([
                    'date_key'     => $dateKey,
                    'last_counter' => 0,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                $dailyCounter = DB::table('barcode_out_daily_counters')
                                  ->where('date_key', $dateKey)
                                  ->lockForUpdate()
                                  ->first();

                $localCounter = max(
                    (int) ($dailyCounter->last_counter ?? 0),
                    $this->getExistingOutboundCounterForToday()
                );

                if ($localCounter + $qty > 9999) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Counter barcode OUT hari ini sudah mencapai batas 9999.'
                    ], 400);
                }

                for ($i = 0; $i < $qty; $i++) {
                    $localCounter++;
                    $generatedBarcodeId = $barcodePrefix . $dateKey . str_pad($localCounter, 4, '0', STR_PAD_LEFT);

                    $barcodeOutId = DB::table('db_barcodes')->insertGetId([
                        'barcode_id'        => $generatedBarcodeId,
                        'users_id'          => $currentUserId,
                        'barcode_type'      => $barcodeType,
                        'barcode_size'      => $barcodeSize,
                        'final_content'     => $generatedBarcodeId,
                        'stock_eng_id'      => $stockEngId, 
                        'current_lifecycle' => 'AVAILABLE', 
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);

                    DB::table('barcode_parsings')->insert([
                        'parsing_header_id'     => $headerId,
                        'users_id'              => $currentUserId,
                        'production_request_id' => $sourceId,           
                        'material_received_id'  => null,                
                        'barcode_in_id'         => null,                
                        'barcode_out_id'        => $barcodeOutId,       
                        'qty_parsed'            => 1,
                        'status'                => 'success',
                        'remark'                => 'Automated pure barcode OUT generation for Line ' . $lineStr,
                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                }

                DB::table('barcode_out_daily_counters')
                  ->where('date_key', $dateKey)
                  ->update(['last_counter' => $localCounter, 'updated_at' => now()]);

                // Update Total Qty di Header & Update Status PR
                DB::table('barcode_parsing_headers')->where('id', $headerId)->update(['total_qty' => $qty]);
                DB::table('production_requests')->where('id', $sourceId)->update(['status' => 'Completed', 'updated_at' => now()]);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Sukses memproses Batch OUT! Berhasil menerbitkan {$qty} data barcode baru untuk Lini {$lineStr}."
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kegagalan sistem database internal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mengambil riwayat konfigurasi struktur
     */
    public function getConfigs()
    {
        $configs = DB::table('type_barcodes')
                     ->orderBy('id', 'desc')
                     ->get();

        return response()->json($configs);
    }

    /**
     * Mengambil nomor berikutnya untuk preview Barcode IN.
     * Nomor final tetap dialokasikan ulang di store dalam transaksi terkunci.
     */
    public function getLastCounter(Request $request)
    {
        $dateKey = now()->format('dmy');
        $isOutbound = strtoupper($request->query('mode', 'IN')) === 'OUT';
        $counterTable = $isOutbound ? 'barcode_out_daily_counters' : 'barcode_daily_counters';
        $counter = DB::table($counterTable)->where('date_key', $dateKey)->value('last_counter');

        $lastCounter = max(
            (int) $counter,
            $isOutbound ? $this->getExistingOutboundCounterForToday() : $this->getExistingInboundCounterForToday()
        );

        return response()->json([
            'success'      => true,
            'date_key'     => $dateKey,
            'last_counter' => $lastCounter,
            'next_counter' => $lastCounter + 1,
        ]);
    }

    private function getExistingInboundCounterForToday(): int
    {
        $today = now()->toDateString();

        return (int) DB::table('db_barcodes')
                       ->where('barcode_id', 'LIKE', 'TXENGINRAK%')
                       ->whereDate('created_at', $today)
                       ->get(['barcode_id'])
                       ->map(fn ($barcode) => (int) substr($barcode->barcode_id, -4))
                       ->max();
    }

    private function getExistingOutboundCounterForToday(): int
    {
        $today = now()->toDateString();

        return (int) DB::table('db_barcodes')
                       ->where('barcode_id', 'LIKE', 'TXENGRAK%')
                       ->whereDate('created_at', $today)
                       ->get(['barcode_id'])
                       ->map(fn ($barcode) => (int) substr($barcode->barcode_id, -4))
                       ->max();
    }

    /**
     * Halaman Khusus Barcode Parsing IN
     */
    public function indexIn()
    {
        // Ambil list ID dokumen yang sudah pernah diproses di Header
        $parsedPrIds = DB::table('barcode_parsing_headers')
                        ->whereNotNull('production_request_id')
                        ->pluck('production_request_id');

        $parsedMrIds = DB::table('barcode_parsing_headers')
                        ->whereNotNull('material_received_id')
                        ->pluck('material_received_id');

        // 1. Ambil data request produksi dengan JOIN ke tabel spareparts (Hanya yang belum diproses)
        $productionRequests = DB::table('production_requests')
                                ->leftJoin('spareparts', 'production_requests.sparepart_id', '=', 'spareparts.id')
                                ->where('production_requests.status', 'Approved')
                                ->whereNotIn('production_requests.id', $parsedPrIds)
                                ->select(
                                    'production_requests.*',
                                    'spareparts.sparepart_id as custom_sparepart_code',
                                    'spareparts.part_number',
                                    'spareparts.sap_code'
                                )
                                ->orderBy('production_requests.id', 'desc')
                                ->get()
                                ->map(function ($pr) {
                                    $pr->sparepart = (object) [
                                        'id'          => $pr->sparepart_id, 
                                        'part_no'     => $pr->custom_sparepart_code, 
                                        'part_name'   => $pr->custom_sparepart_code,
                                        'part_number' => $pr->part_number,
                                        'sap_code'    => $pr->sap_code
                                    ];
                                    return $pr;
                                });

        // 2. Ambil data material received DENGAN JOIN via purchase_requests ke spareparts (Hanya yang belum diproses)
        $materialReceived = DB::table('material_received')
                                ->leftJoin('purchase_requests', 'material_received.purchase_request_id', '=', 'purchase_requests.id')
                                ->leftJoin('spareparts', 'purchase_requests.sparepart_id', '=', 'spareparts.id')
                                ->whereNotIn('material_received.id', $parsedMrIds)
                                ->select(
                                    'material_received.*',
                                    'purchase_requests.sparepart_id',
                                    'spareparts.sparepart_id as custom_sparepart_code',
                                    'spareparts.part_number',
                                    'spareparts.sap_code'
                                )
                                ->orderBy('material_received.id', 'desc')
                                ->get()
                                ->map(function ($mr) {
                                    $mr->sparepart = (object) [
                                        'id'          => $mr->sparepart_id, 
                                        'part_no'     => $mr->custom_sparepart_code, 
                                        'part_name'   => $mr->custom_sparepart_code,
                                        'part_number' => $mr->part_number,
                                        'sap_code'    => $mr->sap_code
                                    ];
                                    return $mr;
                                });

        $stockEngineering = DB::table('stock_engs')
                                ->join('spareparts', 'stock_engs.sparepart_id', '=', 'spareparts.id')
                                ->join('raks', 'stock_engs.rak_id', '=', 'raks.id')
                                ->select(
                                    'stock_engs.id as stock_id',
                                    'stock_engs.sparepart_id as sparepart_id', 
                                    'spareparts.sparepart_id as part_name', 
                                    'spareparts.part_number',              
                                    'spareparts.sap_code',                 
                                    'raks.nama_rak as rak_name'
                                )
                                ->orderBy('spareparts.sparepart_id', 'asc')
                                ->get();

        return view('eng_overview.barcode_parsing_in', compact('productionRequests', 'stockEngineering', 'materialReceived'));
    }
}
