<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Engineering\DisposalEng; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DisposalEngineeringController extends Controller
{
    /**
     * 1. Halaman Utama: Menampilkan Tabel Riwayat History Disposal
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $query = DisposalEng::disposal()->with(['user', 'stockEng.sparepart', 'barcode']);

        if ($request->has('search') && !empty($request->search)) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                $q->where('tx_id', 'LIKE', "%{$search}%")
                  ->orWhere('remark', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('nik', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('barcode', function($b) use ($search) {
                      $b->where('barcode_id', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('stockEng.sparepart', function($sq) use ($search) {
                      $sq->where('sparepart_id', 'LIKE', "%{$search}%")
                        ->orWhere('part_number', 'LIKE', "%{$search}%")
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

        $history = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
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
     * 3. CORE ENGINE: Scan Disposal (JALUR AJAX JSON)
     */
    public function processScan(Request $request)
    {
        $request->validate([
            'barcode_raw'   => 'required|string',
            'process_type'  => 'nullable|in:scan,manual',
            'nik_karyawan'  => 'nullable|string|max:50',
            'remark'        => 'nullable|string',
        ]);

        // Bersihkan spasi gaib / enter bawaan laser gun
        $cleanBarcode = trim(preg_replace('/[\n\r\t]/', '', $request->input('barcode_raw')));

        if (empty($cleanBarcode)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal Eksekusi! Kode barcode kosong atau tidak terbaca.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $reject = static function (string $message, int $status = 422) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], $status);
            };

            // A. Cari data barcode
            $barcodeData = DB::table('db_barcodes')
                ->where(function ($query) use ($cleanBarcode) {
                    $query->where('barcode_id', $cleanBarcode)
                          ->orWhere('final_content', $cleanBarcode);
                })
                ->lockForUpdate()
                ->first();
            
            if (!$barcodeData) {
                return $reject("Kode barcode '{$cleanBarcode}' tidak terdaftar di sistem inventaris.", 404);
            }

            // 🛑 B. PROTOKOL LOCKDOWN: Jika sudah DISPOSAL, tolak via JSON!
            if ($barcodeData->current_lifecycle === 'DISPOSAL') {
                return $reject("Transaksi Ditolak! Barcode '{$cleanBarcode}' sudah berstatus DISPOSAL sebelumnya.");
            }

            if ($barcodeData->current_lifecycle !== 'USED_OUT') {
                return $reject(
                    "Transaksi Ditolak! Barcode '{$barcodeData->barcode_id}' belum berstatus USED_OUT. "
                    . 'Disposal hanya menerima hasil Production OUT.'
                );
            }

            // Disposal wajib memiliki jejak Production OUT, tanpa mengubah saldo stok.
            $productionOut = DB::table('stock_prod_transactions')
                ->where('db_barcodes_id', $barcodeData->id)
                ->where('tx_type', 'out')
                ->where('status', 'success')
                ->latest('id')
                ->first();

            if (!$productionOut) {
                return $reject('Transaksi Ditolak! Riwayat Production OUT untuk barcode ini tidak ditemukan.');
            }

            $stockEngId = $barcodeData->stock_eng_id;
            if (!$stockEngId && $productionOut->stock_eng_tx_id) {
                $stockEngId = DB::table('stock_eng_transactions')
                    ->where('id', $productionOut->stock_eng_tx_id)
                    ->value('stock_engs_id');
            }

            if (!$stockEngId) {
                return $reject('Transaksi Ditolak! Master stok Engineering untuk barcode ini tidak ditemukan.');
            }

            // C. Generate Unique Transaction ID (Format: TXENGDIS + DDMMYY + 001, 002, dst.)
            // Karena menggunakan prefix tanggal ($datePrefix), counter otomatis reset ke 001 jika berganti hari.
            $txUuid = $this->generateDisposalTxId();

            // D. Susun remark audit tanpa menghapus barcode dari master.
            $inputProcess = $request->input('process_type', 'scan');
            $employeeNik = trim((string) ($productionOut->nik_karyawan ?? ''));
            if ($employeeNik === '') {
                $employeeNik = trim((string) $request->input('nik_karyawan', ''));
            }

            $remarkParts = [
                'SOURCE PRODUCTION OUT: ' . $productionOut->tx_id,
            ];
            if ($productionOut->out_category) {
                $remarkParts[] = 'KATEGORI: ' . strtoupper($productionOut->out_category);
            }
            $sourceHasEmployeeNik = stripos((string) $productionOut->remark, 'NIK KARYAWAN YANG MENGHILANGKAN:') !== false;
            if ($employeeNik !== '' && !$sourceHasEmployeeNik) {
                $remarkParts[] = 'NIK KARYAWAN YANG MENGHILANGKAN: ' . $employeeNik;
            }
            if ($productionOut->remark) {
                $remarkParts[] = 'OUT REMARK: ' . $productionOut->remark;
            }
            if ($request->filled('remark')) {
                $remarkParts[] = 'DISPOSAL REMARK: ' . trim($request->input('remark'));
            }

            // E. Insert Log Riwayat Disposal
            DisposalEng::create([
                'tx_id'                 => $txUuid,
                'users_id'              => Auth::id() ?? 1, 
                'stock_engs_id'         => $stockEngId,
                'db_barcodes_id'        => $barcodeData->id,
                'production_request_id' => $productionOut->production_request_id,
                'tx_type'               => 'disposal', 
                'qty_transaction'       => 1,
                'process_type'          => $inputProcess,
                'photo_path'            => null,
                'status'                => 'success',
                'remark'                => implode(' | ', $remarkParts),
            ]);

            // 🔒 F. Kunci status di master barcode menjadi DISPOSAL tanpa menghapus barcode.
            DB::table('db_barcodes')->where('id', $barcodeData->id)->update([
                'current_lifecycle' => 'DISPOSAL',
                'updated_at'        => now()
            ]);
            
            DB::commit();

            // 🌟 RESPONSE SUKSES JSON: Mengirim sinyal aman ke Javascript tanpa reload halaman
            return response()->json([
                'success' => true,
                'message' => "Sukses! Barcode '{$barcodeData->barcode_id}' masuk Disposal. "
                    . 'Tidak ada stok yang dikembalikan dan barcode tidak dapat digunakan lagi.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Kegagalan database internal: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateDisposalTxId(): string
    {
        $datePrefix = 'TXENGDIS' . date('dmy');
        $lastTrx = DisposalEng::where('tx_id', 'LIKE', $datePrefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastTrx
            ? ((int) substr($lastTrx->tx_id, -3)) + 1
            : 1;

        return $datePrefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

}
