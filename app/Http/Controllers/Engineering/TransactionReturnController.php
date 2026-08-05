<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionReturnController extends Controller
{
    /**
     * Menampilkan log/tabel riwayat Return Engineering
     */
    public function index(Request $request)
    {
        // Fallback data (Silakan ganti dengan model/query asli jika tabel DB sudah ada)
        $returns = collect();

        return view('stock_eng.transaction.return', compact('returns'));
    }

    /**
     * Menyimpan data Return Engineering
     */
    public function store(Request $request)
    {
        $request->validate([
            'sparepart_id' => 'required',
            'qty'          => 'required|integer|min:1',
            'remark'       => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // TODO: Tambahkan logika penambahan stok & pencatatan log transaksi Return Engineering di sini

            DB::commit();

            return redirect()->route('stock_eng.transaction.return')
                ->with('success', 'Transaksi Return Engineering berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memproses Return: ' . $e->getMessage());
        }
    }
}