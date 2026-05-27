<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    /**
     * Menampilkan daftar Purchase Request yang membutuhkan approval
     * Menampilkan fitur pencarian Odoo Style dan pengurutan skala prioritas
     */
    public function index(Request $request)
    {
        // Fitur pencarian Odoo Style via query string
        $search = $request->input('search');

        $query = PurchaseRequestEng::query();

        // Cari berdasarkan Kode PR, Tipe Product, SAP/Category, NIK, atau Nama Pembuat
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('pr_code', 'LIKE', "%{$search}%")
                  ->orWhere('type_product', 'LIKE', "%{$search}%")
                  ->orWhere('product', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('nik', 'LIKE', "%{$search}%");
            });
        }

        // Ambil data PR, dahulukan yang URGENT, lalu paginate 10 data
        $pendingPr = $query->orderByRaw("FIELD(priority, 'urgent', 'normal') ASC")
                           ->orderBy('created_at', 'DESC')
                           ->paginate(10);

        // Diarahkan langsung ke file view milik lu
        return view('cost_section.approval_pr', compact('pendingPr', 'search'));
    }

    /**
     * Mengubah status Purchase Request menjadi 'approved'
     */
    public function approve($id)
    {
        $pr = PurchaseRequestEng::findOrFail($id);

        try {
            // 🛠️ FIX ENUM: Langsung update menggunakan 'approved' sesuai string di migration database
            $pr->update(['status' => 'approved']);
            
            return redirect()->back()->with('success', 'PR ' . $pr->pr_code . ' Berhasil di-Approve, Stock Siap Diproses!');
        } catch (\Exception $e) {
            // Jika gagal, akan memunculkan pesan error teknis asli dari database agar mudah dilacak
            return redirect()->back()->with('error', 'Gagal memproses approval! Error: ' . $e->getMessage());
        }
    }

    /**
     * Mengubah status Purchase Request menjadi 'rejected'
     */
    public function reject($id)
    {
        $pr = PurchaseRequestEng::findOrFail($id);

        try {
            // 🛠️ FIX ENUM: Langsung update menggunakan 'rejected' sesuai string di migration database
            $pr->update(['status' => 'rejected']);
            
            // Menggunakan default session success agar alert di blade berwarna hijau/sukses (informasi berhasil ditolak)
            return redirect()->back()->with('success', 'PR ' . $pr->pr_code . ' Telah Berhasil Ditolak (Rejected).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses penolakan! Error: ' . $e->getMessage());
        }
    }
}