<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnProdController extends Controller
{
    /**
     * Menampilkan halaman utama tabel/log riwayat Transaksi Return
     */
    public function stockReturn(Request $request)
    {
        // Jika Model ReturnProd / sejenisnya sudah dibuat, aktifkan query di bawah:
        /*
        $query = \App\Models\Production\ReturnProd::with(['sparepart', 'lineProduction']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('return_no', 'like', "%{$search}%")
                  ->orWhereHas('sparepart', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate(25);
        */

        // Fallback variabel agar view returnProd.blade.php tidak error saat render
        $returns = collect(); 

        return view('stock_prod.transactionProd.returnProd', compact('returns'));
    }

    /**
     * Menampilkan halaman form input Manual Return Production
     */
    public function manualReturn()
    {
        return view('stock_prod.transactionProd.manualReturn');
    }

    /**
     * Eksekusi simpan submit data Manual Return ke Database
     */
    public function storeManualReturn(Request $request)
    {
        $request->validate([
            'sparepart_id' => 'required',
            'qty'          => 'required|integer|min:1',
            'remark'       => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // TODO: Tambahkan logika penambahan/pengembalian stok & pencatatan log transaksi Return di sini

            DB::commit();

            return redirect()->route('prod.transaction.return')
                ->with('success', 'Transaksi Return Production berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memproses Return: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman Terminal Live Scan Return
     */
    public function scanReturn()
    {
        return view('stock_prod.transactionProd.scanReturn');
    }

    /**
     * Eksekusi submit data hasil Scan Return (Handling Form Submit & AJAX Request)
     */
    public function storeScanReturn(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'qty'     => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // TODO: Tambahkan logika pencarian barcode, update stok, & pencatatan transaksi Return di sini

            DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Scan Return berhasil diproses!'
                ]);
            }

            return redirect()->route('prod.transaction.return')
                ->with('success', 'Scan Return berhasil diproses!');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses Scan Return: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal memproses Scan Return: ' . $e->getMessage());
        }
    }
}