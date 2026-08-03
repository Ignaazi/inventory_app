<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Engineering\DisposalEng; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DisposalEngineeringController extends Controller
{
    public function index(Request $request)
    {
        $query = DisposalEng::disposal()->with(['user', 'stockEng']);

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('tx_id', 'LIKE', "%{$search}%")
                  ->orWhere('remark', 'LIKE', "%{$search}%")
                  ->orWhereHas('stockEng', function($sq) use ($search) {
                      $sq->where('part_no', 'LIKE', "%{$search}%")
                        ->orWhere('sap_code', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->has('filter') && !empty($request->filter)) {
            $filter = strtolower(trim($request->filter));
            if (in_array($filter, ['scan', 'manual'])) {
                $query->where('process_type', $filter);
            }
        }

        $history = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        return view('stock_eng.transaction.disposal', compact('history'));
    }

    public function scanView()
    {
        return view('stock_eng.transaction.disposal_scan');
    }

    /**
     * CORE ENGINE: Instant Barcode Burning (JALUR AJAX JSON)
     */
    public function processScan(Request $request)
    {
        // Bersihkan spasi gaib / enter bawaan laser gun
        $cleanBarcode = trim(preg_replace('/[\n\r\t]/', '', $request->barcode_raw));

        if (empty($cleanBarcode)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Eksekusi! Kode barcode kosong atau tidak terbaca.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // A. Cari data barcode
            $barcodeData = DB::table('db_barcodes')->where('barcode_id', $cleanBarcode)->first();
            
            if (!$barcodeData) {
                return response()->json([
                    'success' => false,
                    'message' => "Kode barcode '{$cleanBarcode}' tidak terdaftar di sistem inventaris."
                ], 404);
            }

            // 🛑 B. PROTOKOL LOCKDOWN: Jika sudah DISPOSAL, tolak via JSON!
            if ($barcodeData->current_lifecycle === 'DISPOSAL') {
                return response()->json([
                    'success' => false,
                    'message' => "🚨 Transaksi Ditolak! Barcode '{$cleanBarcode}' sudah berstatus DISPOSAL (Mati/Scrap) sebelumnya."
                ], 422);
            }

            // C. Generate Unique Transaction ID
            $datePrefix = 'TX-DISP-' . date('dmy'); 
            $lastTrx = DisposalEng::where('tx_id', 'LIKE', $datePrefix . '%')->orderBy('id', 'desc')->first();

            if ($lastTrx) {
                $lastNum = (int) substr($lastTrx->tx_id, -4);
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            $txUuid = $datePrefix . $nextNum;

            // D. Tentukan Process Type
            $inputProcess = $request->input('process_type') ?? ((strlen($cleanBarcode) > 20) ? 'scan' : 'manual');

            // E. Insert Log Riwayat Disposal
            DisposalEng::create([
                'tx_id'                 => $txUuid,
                'users_id'              => Auth::id() ?? 1, 
                'stock_engs_id'         => $barcodeData->stock_eng_id, 
                'db_barcodes_id'        => $barcodeData->id,
                'production_request_id' => null, 
                'tx_type'               => 'disposal', 
                'qty_transaction'       => 1,
                'process_type'          => $inputProcess,
                'photo_path'            => null,
                'status'                => 'success',
                'remark'                => 'Instant Scrap via Terminal Scanner (Pemusnahan Permanen Barcode)'
            ]);

            // 🔒 F. BURN IT: Kunci status di master barcode menjadi DISPOSAL
            DB::table('db_barcodes')->where('id', $barcodeData->id)->update([
                'current_lifecycle' => 'DISPOSAL',
                'updated_at'        => now()
            ]);
            
            DB::commit();

            // 🌟 RESPONSE SUKSES JSON: Mengirim sinyal aman ke Javascript tanpa reload halaman
            return response()->json([
                'success' => true,
                'message' => "🔥 Sukses! Barcode '{$cleanBarcode}' berhasil dihancurkan secara permanen dari sistem."
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kegagalan database internal: ' . $e->getMessage()
            ], 500);
        }
    }
}