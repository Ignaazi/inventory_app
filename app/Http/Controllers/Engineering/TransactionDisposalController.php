<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Engineering\TransactionDisposal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionDisposalController extends Controller
{
    /**
     * 1. Halaman Utama: Menampilkan Tabel Riwayat History Disposal
     * Memuat Eager Loading (User, StockEng -> Sparepart, Barcode) & Fitur Live Search
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        // Load relasi user, stockEng beserta sparepart-nya, dan barcode
        $query = TransactionDisposal::with(['user', 'stockEng.sparepart', 'barcode'])
            ->where('tx_type', 'disposal');

        // Filter Pencarian (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tx_id', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('nik', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('barcode', function($b) use ($search) {
                      $b->where('barcode_id', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('stockEng.sparepart', function($sp) use ($search) {
                      $sp->where('sparepart_id', 'LIKE', "%{$search}%")
                         ->orWhere('part_number', 'LIKE', "%{$search}%")
                         ->orWhere('sap_code', 'LIKE', "%{$search}%");
                  });
            });
        }

        $history = $query->orderBy('id', 'desc')->paginate($perPage);

        return view('stock_eng.transaction.disposal', compact('history'));
    }

    /**
     * 2. Menampilkan Halaman Terminal Scanner
     */
    public function scanView()
    {
        return view('stock_eng.transaction.disposal_scan');
    }

    /**
     * 3. Eksekutor Scan: Menyimpan Data & Mematikan Barcode Lifecycle
     * Mendukung pemrosesan AJAX / Redirect Form
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'barcode_raw' => 'required|string'
        ]);

        $barcodeRaw = trim($request->barcode_raw);

        DB::beginTransaction();
        try {
            // A. Cari barcode di sistem
            $barcodeData = DB::table('db_barcodes')
                ->where('barcode_id', $barcodeRaw)
                ->first();
            
            if (!$barcodeData) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Gagal Eksekusi! Kode barcode tidak terdaftar di sistem inventaris.'
                    ], 404);
                }
                return redirect()->back()
                    ->with('error', 'Gagal Eksekusi! Kode barcode tidak terdaftar di sistem inventaris.')
                    ->withInput();
            }

            // B. PROTEKSI: Tolak kalau statusnya memang sudah DISPOSAL
            if ($barcodeData->current_lifecycle === 'DISPOSAL') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false, 
                        'message' => "🚨 Transaksi Ditolak! Barcode '{$barcodeRaw}' sudah berstatus DISPOSAL."
                    ], 422);
                }
                return redirect()->back()
                    ->with('error', "🚨 Transaksi Ditolak! Barcode '{$barcodeRaw}' sudah berstatus DISPOSAL dan tidak valid untuk diproses ulang.")
                    ->withInput();
            }

            // C. Generate Unique ID Otomatis (TX-DISP-DDMMYY-XXXX)
            $datePrefix = 'TX-DISP-' . date('dmy'); 
            $lastTrx = TransactionDisposal::where('tx_id', 'LIKE', $datePrefix . '%')
                ->orderBy('id', 'desc')
                ->first();

            if ($lastTrx) {
                $lastNum = (int) substr($lastTrx->tx_id, -4);
                $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNum = '0001';
            }
            $txUuid = $datePrefix . $nextNum;

            $processType = $request->input('process_type', 'scan');

            // D. Simpan Log
            TransactionDisposal::create([
                'tx_id'                 => $txUuid,
                'users_id'              => Auth::id() ?? 1, 
                'stock_engs_id'         => $barcodeData->stock_eng_id, 
                'db_barcodes_id'        => $barcodeData->id,
                'production_request_id' => null, 
                'tx_type'               => 'disposal', 
                'qty_transaction'       => 1,          
                'process_type'          => $processType,     
                'photo_path'            => null,
                'status'                => 'success',
                'remark'                => 'Instant Scrap via Terminal Scanner (Pemusnahan Permanen Barcode)'
            ]);

            // E. Kunci status barcode di table master barcode menjadi DISPOSAL
            DB::table('db_barcodes')
                ->where('id', $barcodeData->id)
                ->update([
                    'current_lifecycle' => 'DISPOSAL',
                    'updated_at'        => now()
                ]);

            DB::commit();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "🔥 Sukses! Barcode '{$barcodeRaw}' berhasil dimatikan secara permanen."
                ]);
            }

            return redirect()->route('stock_eng.transaction.disposal')
                ->with('success', "🔥 Sukses! Barcode '{$barcodeRaw}' berhasil dimatikan secara permanen.");

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Kegagalan database internal: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()
                ->with('error', 'Kegagalan database internal: ' . $e->getMessage())
                ->withInput();
        }
    }
}