<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;
use Illuminate\Http\Request;

class PurchaseRequestHistoryEngController extends Controller
{
    /**
     * Menampilkan semua data dengan fitur Pagination
     * Sinkron dengan views hitoryPr agar links() aktif sempurna
     */
    public function index()
    {
        // Menggunakan paginate(10) menggantikan get() agar method firstItem() & links() di views aktif sempurna!
        $historyPr = PurchaseRequestEng::orderBy('id', 'desc')->paginate(10);
        
        return view('stock_eng.purchase_request.historyPr', compact('historyPr'));
    }

    /**
     * Mengambil data tunggal secara instan untuk disuntikkan ke Modal Preview & Edit via AJAX
     */
    public function preview($id)
    {
        $pr = PurchaseRequestEng::findOrFail($id);
        return response()->json($pr);
    }

    /**
     * Method Proses Update Data dari Modal Pop-up Edit
     */
    public function update(Request $request, $id)
    {
        // 🛠️ UPDATE VALIDASI: Status dikunci hanya boleh menerima opsi enum yang sah di database
        $request->validate([
            'type_product' => 'required|string|max:255',
            'product'      => 'required|string|max:255',
            'priority'     => 'required|in:normal,urgent',
            'status'       => 'required|in:draft,waiting approval,approved,rejected,done', 
        ]);

        $pr = PurchaseRequestEng::findOrFail($id);
        
        // 🛠️ SAFEGUARD: Jika status bawaannya masih 'waiting approval', 
        // jangan biarkan form edit modal merubah statusnya secara tidak sengaja kecuali diubah manual
        $statusBaru = $request->status;
        if ($statusBaru === 'waiting') {
            $statusBaru = 'waiting approval';
        }

        $pr->update([
            'type_product' => $request->type_product,
            'product'      => $request->product,
            'priority'     => $request->priority,
            'status'       => $statusBaru,
        ]);

        return redirect()->back()->with('success', 'Data Purchase Request ' . $pr->pr_code . ' berhasil diperbarui, Bro!');
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