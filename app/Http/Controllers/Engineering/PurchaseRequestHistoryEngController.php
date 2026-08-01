<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;
use Illuminate\Http\Request;

class PurchaseRequestHistoryEngController extends Controller
{
    /**
     * Menampilkan semua data dengan fitur Pencarian Ringkas & Pagination
     * Mengikuti standar penanganan relasi dari listPurchaseRequest
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10); // Sinkron dengan dropdown show entries

        // Eager loading relasi user & sparepart + Global Multi-Column Search
        $historyPr = PurchaseRequestEng::with(['user', 'sparepart'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('no_pr', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%")
                      ->orWhere('priority', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($qUser) use ($search) {
                          $qUser->where('name', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%")
                                ->orWhere('nim', 'like', "%{$search}%");
                      })
                      ->orWhereHas('sparepart', function ($qPart) use ($search) {
                          $qPart->where('sparepart_id', 'like', "%{$search}%")
                                ->orWhere('part_number', 'like', "%{$search}%")
                                ->orWhere('sap_code', 'like', "%{$search}%")
                                ->orWhere('category', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
        
        return view('stock_eng.purchase_request.historyPr', compact('historyPr'));
    }

    /**
     * Mengambil data tunggal secara instan untuk disuntikkan ke Modal Preview & Edit via AJAX
     */
    public function preview($id)
    {
        // Memuat data lengkap dengan relasi untuk kebutuhan modal preview
        $pr = PurchaseRequestEng::with(['user', 'sparepart'])->findOrFail($id);
        return response()->json($pr);
    }

    /**
     * Method Proses Update Data dari Modal Pop-up Edit
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type_product' => 'required|string|max:255',
            'product'      => 'required|string|max:255',
            'qty'          => 'required|integer|min:1',
            'priority'     => 'required|in:normal,urgent',
            'status'       => 'required|in:draft,waiting approval,approved,rejected,done', 
        ]);

        $pr = PurchaseRequestEng::findOrFail($id);
        
        $statusBaru = $request->status;
        if ($statusBaru === 'waiting') {
            $statusBaru = 'waiting approval';
        }

        $pr->update([
            'type_product' => $request->type_product,
            'product'      => $request->product,
            'qty'          => $request->qty,
            'priority'     => $request->priority,
            'status'       => $statusBaru,
        ]);

        // FIXED: Mengubah $pr->pr_code menjadi $pr->no_pr agar tidak memicu error Property Not Found
        return redirect()->back()->with('success', 'Data Purchase Request ' . $pr->no_pr . ' berhasil diperbarui, Bro!');
    }

    /**
     * Menghapus Permanent Data PR dari List Database History
     */
    public function destroy($id)
    {
        $pr = PurchaseRequestEng::findOrFail($id);
        $pr->delete();

        return redirect()->back()->with('success', 'Data Purchase Request berhasil dihapus permanent!');
    }
}