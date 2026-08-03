<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Engineering\TransactionDisposal; // Memakai Model resmi milik lu!
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionDisposalController extends Controller
{
    /**
     * 1. Halaman Utama: Menampilkan Tabel Riwayat History Disposal
     * FIX: Menggunakan eager loading & sorting terbaru agar data langsung muncul!
     */
    public function index()
    {
        // Menarik data dengan relasi 'user' dan 'stockEng' sesuai kebutuhan di Blade lu
        $history = TransactionDisposal::with(['user', 'stockEng'])
            ->where('tx_type', 'disposal')
            ->orderBy('id', 'desc') // PENTING: Biar data baru hasil scan langsung nongol di baris paling atas!
            ->paginate(15);

        // Mengirimkan variabel $history ke view Blade lu
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
     * 3. Eksekutor Scan: Menyimpan Data & Mematikan Barcode Lifecyle
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'barcode_raw' => 'required|string'
        ]);

        DB::beginTransaction();
        try {
            // A. Cari barcode di sistem
            $barcodeData = DB::table('db_barcodes')
                ->where('barcode_id', trim($request->barcode_raw))
                ->first();
            
            if (!$barcodeData) {
                return redirect()->back()
                    ->with('error', 'Gagal Eksekusi! Kode barcode tidak terdaftar di sistem inventaris.')
                    ->withInput();
            }

            // 🛑 B. PROTEKSI: Tolak kalau statusnya emang udah DISPOSAL
            if ($barcodeData->current_lifecycle === 'DISPOSAL') {
                return redirect()->back()
                    ->with('error', "🚨 Transaksi Ditolak! Barcode '{$request->barcode_raw}' sudah berstatus DISPOSAL dan tidak valid untuk diproses ulang.")
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

            // D. Simpan Log menggunakan Model 'TransactionDisposal' kesayangan lu
            TransactionDisposal::create([
                'tx_id'                 => $txUuid,
                'users_id'              => Auth::id() ?? 1, 
                'stock_engs_id'         => $barcodeData->stock_eng_id, 
                'db_barcodes_id'        => $barcodeData->id,
                'production_request_id' => null, 
                'tx_type'               => 'disposal', 
                'qty_transaction'       => 1,          
                'process_type'          => 'scan',     
                'photo_path'            => null,
                'status'                => 'success',
                'remark'                => 'Instant Scrap via Terminal Scanner (Pemusnahan Permanen Barcode)'
            ]);

            // 🔒 E. Kunci status barcode di table master barcode menjadi DISPOSAL
            DB::table('db_barcodes')
                ->where('id', $barcodeData->id)
                ->update([
                    'current_lifecycle' => 'DISPOSAL',
                    'updated_at'        => now()
                ]);

            DB::commit();

            // Redirect balik ke route index halaman utama lu
            return redirect()->route('stock_eng.transaction.disposal')
                ->with('success', "🔥 Sukses! Barcode '{$request->barcode_raw}' berhasil dimatikan secara permanen.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Kegagalan database internal: ' . $e->getMessage())
                ->withInput();
        }
    }
}