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
     * Tampilan Log History Stok Masuk
     */
    public function index(Request $request)
    {
        $query = StockInEng::with(['stockEng.sparepart', 'stockEng.rak', 'engMaterialReceiving'])
            ->orderBy('id', 'desc');

        // Handler Live Search (Mengubah filter comment ke process_type)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('remark', 'like', "%{$search}%")
                  ->orWhere('process_type', 'like', "%{$search}%") // FIXED: Mengganti comment ke process_type
                  ->orWhereHas('stockEng.sparepart', function($sq) use ($search) {
                      $sq->where('sparepart_id', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%")
                        ->orWhere('part_number', 'like', "%{$search}%")
                        ->orWhere('sap_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('stockEng.rak', function($rq) use ($search) {
                      $rq->where('nama_rak', 'like', "%{$search}%");
                  });
            });
        }

        // Handler Filter Kategori Log (Menggunakan kolom process_type secara presisi)
        if ($request->has('filter') && !empty($request->filter)) {
            $filter = strtolower($request->filter);
            if (in_array($filter, ['success', 'pending'])) {
                $query->where('status', $filter);
            } elseif ($filter === 'manual in') {
                $query->where('process_type', 'Manual'); // FIXED: Langsung cek ke kolom process_type
            } elseif ($filter === 'scan in') {
                $query->where('process_type', 'Scan');   // FIXED: Langsung cek ke kolom process_type
            }
        }

        $history = $query->paginate(10)->withQueryString();

        return view('stock_eng.transaction.in', compact('history'));
    }

    /**
     * Tampilan untuk Barcode Scan In
     */
    public function scan()
    {
        return view('stock_eng.transaction.in_scan');
    }

    /**
     * Tampilan Form Input Manual IN
     */
    public function manual()
    {
        $stocks = StockEng::with(['sparepart', 'rak'])->get();

        // Mengambil daftar nama RAK unik untuk filter bertingkat di view
        $listRak = $stocks->map(function ($item) {
            return $item->rak->nama_rak ?? null;
        })->filter()->unique()->sort()->values();

        // Kunci dokumen PR yang sudah pernah di-klaim sebelumnya di table stock_in_logs
        $usedRequestNos = StockInEng::whereNotNull('eng_material_receiving_id')
            ->pluck('eng_material_receiving_id')
            ->toArray();

        // Mengambil dokumen PR receiving yang belum pernah terpakai
        $costingReceivings = EngMaterialReceiving::whereNotIn('id', $usedRequestNos)->latest()->get();

        return view('stock_eng.transaction.in_manual', compact('stocks', 'listRak', 'costingReceivings'));
    }

    /**
     * Menyimpan transaksi stok masuk secara aman ke table stock_in_logs
     */
    public function store(Request $request)
    {
        // Validasi input: process_type wajib dikirim dan isinya harus Manual atau Scan
        $request->validate([
            'stock_eng_id'              => 'required|exists:stock_engs,id',
            'qty_in'                    => 'required|integer|min:1',
            'eng_material_receiving_id' => 'nullable|exists:eng_material_receivings,id', 
            'remark'                    => 'nullable|string',
            'process_type'              => 'required|in:Manual,Scan', // Wajib diisi (Manual / Scan)
        ]);

        DB::beginTransaction();
        try {
            $stock = StockEng::with(['sparepart', 'rak'])->findOrFail($request->stock_eng_id);
            $sparepartId = $stock->sparepart->sparepart_id ?? 'N/A';
            $lokasiRak   = $stock->rak->nama_rak ?? 'N/A';

            // Kunci proteksi ganda agar dokumen PR/Receiving tidak disubmit berulang kali
            if ($request->eng_material_receiving_id) {
                $isPrUsed = StockInEng::where('eng_material_receiving_id', $request->eng_material_receiving_id)->exists();
                if ($isPrUsed) {
                    return redirect()->back()->withInput()->with('error', 'Gagal: Dokumen PR ini sudah pernah digunakan!');
                }
            }

            // Simpan log transaksi dengan kolom process_type (Menghapus total comment jadul)
            StockInEng::create([
                'stock_eng_id'              => $stock->id,
                'eng_material_receiving_id' => $request->eng_material_receiving_id ?? null,
                'nik'                       => Auth::user()->nik ?? 'SYSTEM',
                'qty_added'                 => $request->qty_in, 
                'status'                    => 'Success',
                'remark'                    => $request->remark ?? ($request->process_type . ' IN'),
                'process_type'              => $request->process_type // FIXED: Menyimpan 'Manual' atau 'Scan'
            ]);

            // Tambahkan kuantitas stok fisik pada master gudang (tabel stock_engs)
            $stock->increment('qty', $request->qty_in);

            DB::commit();
            return redirect()->route('eng.in')->with('success', "Stok berhasil ditambahkan via {$request->process_type} ke RAK [{$lokasiRak}] untuk sparepart ID {$sparepartId}!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
        }
    }
}