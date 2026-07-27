<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StockInEng; 
use App\Models\StockEng;
use App\Models\Engineering\EngMaterialReceiving; 

class StockInEngineeringController extends Controller
{
    /**
     * Tampilan Utama / History Stock In dengan Filter Backend
     */
    public function index(Request $request)
    {
        // Inisialisasi query dengan eager loading relasi terkait
        $query = StockInEng::with(['stockEng.sparepart', 'stockEng.rak', 'engMaterialReceiving'])
            ->latest();

        // 1. FILTER LIVE SEARCH (Mencari berdasarkan NIK, No Nozzle, Part Number, SAP Code, atau RAK)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('no_nozzle', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('remark', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%")
                  ->orWhereHas('stockEng.sparepart', function($sq) use ($search) {
                      $sq->where('part_number', 'like', "%{$search}%")
                        ->orWhere('sap_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('stockEng.rak', function($rq) use ($search) {
                      $rq->where('nama_rak', 'like', "%{$search}%");
                  });
            });
        }

        // 2. FILTER DENGAN KATEGORI TOMBOL (All, Success, Pending, Manual In, Scan In)
        if ($request->has('filter') && !empty($request->filter)) {
            $filter = strtolower($request->filter);
            if (in_array($filter, ['success', 'pending'])) {
                $query->where('status', $filter);
            } elseif ($filter === 'manual in') {
                $query->where('remark', 'like', '%manual%');
            } elseif ($filter === 'scan in') {
                $query->where('remark', 'like', '%scan%');
            }
        }

        // Simpan jumlah per-halaman secara dinamis (Default: 10)
        $history = $query->paginate(10)->withQueryString();

        return view('stock_eng.transaction.in', compact('history'));
    }

    /**
     * 🌟 TAMBAHAN: Tampilan untuk Scan Barcode/QR IN
     * Menangani route name: eng.in.scan
     */
    public function scan()
    {
        // Ganti string view di bawah ini sesuai dengan path file blade scan milikmu
        // Contoh: return view('stock_eng.transaction.in_scan');
        return view('stock_eng.transaction.in_scan');
    }

    /**
     * Tampilan untuk Input Manual
     */
    public function manual()
    {
        $stocks = StockEng::with(['sparepart', 'rak'])->get();

        // Ambil daftar nama RAK unik untuk filter dropdown bertingkat di view
        $listRak = $stocks->map(function ($item) {
            return $item->rak->nama_rak ?? null;
        })->filter()->unique()->sort()->values();

        // Ambil data PR/Receiving yang belum pernah digunakan di log masuk manapun
        $usedPrIds = StockInEng::whereNotNull('request_no')->pluck('request_no')->toArray();
        $costingReceivings = EngMaterialReceiving::whereNotIn('id', $usedPrIds)->latest()->get();

        return view('stock_eng.transaction.in_manual', compact('stocks', 'listRak', 'costingReceivings'));
    }

    /**
     * Proses Simpan Data (Aman & Sinkron dengan Tabel inProd_logs)
     */
    public function store(Request $request)
    {
        $request->validate([
            'stock_eng_id'              => 'required|exists:stock_engs,id',
            'qty_in'                    => 'required|integer|min:1',
            'eng_material_receiving_id' => 'nullable|exists:eng_material_receivings,id', 
            'remark'                    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Ambil data stock utama beserta master sparepart dan raks
            $stock = StockEng::with(['sparepart', 'rak'])->findOrFail($request->stock_eng_id);
            
            $namaNozzle = $stock->sparepart->sparepart_id ?? $stock->sparepart->name ?? 'N/A';
            $sapCode    = $stock->sparepart->sap_code ?? '-';
            $partNumber = $stock->sparepart->part_number ?? '-';
            $lokasiRak  = $stock->rak->nama_rak ?? 'N/A';

            // Proteksi double PR menggunakan kolom 'request_no'
            if ($request->eng_material_receiving_id) {
                $isPrUsed = StockInEng::where('request_no', $request->eng_material_receiving_id)->exists();
                if ($isPrUsed) {
                    return redirect()->back()->withInput()->with('error', 'Gagal: Dokumen PR/Receiving ini sudah terpakai!');
                }
            }

            // Generate nomor transaksi dinamis secara aman untuk kolom `transaction_out_id`
            $latestTx = StockInEng::orderBy('inproduction_id', 'desc')->first();
            $num = 1;
            if ($latestTx && $latestTx->transaction_out_id) {
                $onlyNumbers = (int) filter_var($latestTx->transaction_out_id, FILTER_SANITIZE_NUMBER_INT);
                if ($onlyNumbers > 0) {
                    $num = $onlyNumbers + 1;
                }
            }
            $nextTxId = 'ENGIN' . str_pad($num, 3, '0', STR_PAD_LEFT);

            // Petakan data persis seperti struktur tabel `inProd_logs`
            $logData = [
                'nik'                => Auth::user()->nik ?? 'SYSTEM',
                'line_id'            => $stock->line_id ?? 1, 
                'no_nozzle'          => $namaNozzle, 
                'transaction_out_id' => $nextTxId, 
                'request_no'         => $request->eng_material_receiving_id ?? null, 
                'barcode_id'         => $stock->barcode_id ?? 1, 
                'stock_prod_id'      => $stock->id, 
                'qty_in'             => $request->qty_in, 
                'status'             => 'SUCCESS',
                'remark'             => $request->remark ?? 'MANUAL IN',
                'comment'            => "RAK: {$lokasiRak} | SAP: {$sapCode} | PN: {$partNumber}"
            ];

            // Masukkan data transaksi ke tabel inProd_logs
            StockInEng::create($logData);

            // Update/tambahkan stok fisik utama di tabel stock_engs
            $stock->increment('qty', $request->qty_in);

            DB::commit();
            return redirect()->route('eng.in')->with('success', "Stok sukses ditambahkan ke RAK [{$lokasiRak}] untuk sparepart [{$namaNozzle}]!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memproses stock in: ' . $e->getMessage());
        }
    }
}