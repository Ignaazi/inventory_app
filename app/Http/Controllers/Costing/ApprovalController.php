<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    /**
     * Menampilkan daftar Purchase Request yang berstatus 'checked'
     * (Menunggu persetujuan akhir dari pihak Costing)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Costing hanya memproses data yang statusnya sudah 'checked' oleh Engineering
        $query = PurchaseRequestEng::with(['user', 'sparepart'])
                                   ->where('status', 'checked');

        // Fitur pencarian tembus ke tabel relasi (Sudah di-update untuk kolom sparepart yang baru)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_pr', 'LIKE', "%{$search}%")
                  ->orWhere('destination', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('nik', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('sparepart', function($partQuery) use ($search) {
                      $partQuery->where('sparepart_id', 'LIKE', "%{$search}%")
                                ->orWhere('part_number', 'LIKE', "%{$search}%")
                                ->orWhere('sap_code', 'LIKE', "%{$search}%")
                                ->orWhere('category', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan skala prioritas URGENT dulu, lalu tanggal dibuat terbaru
        $pendingPr = $query->orderByRaw("FIELD(priority, 'urgent', 'normal') ASC")
                           ->orderBy('created_at', 'DESC')
                           ->paginate($perPage);

        return view('cost_section.approval_pr', compact('pendingPr', 'search'));
    }

    /**
     * Menampilkan Form Halaman Baru untuk Approval Costing
     * (Membuka views/cost_section/approve_PrForm.blade.php)
     */
    public function approveForm($id)
    {
        // Eager load user dan sparepart agar data di form baru lengkap
        $pr = PurchaseRequestEng::with(['user', 'sparepart'])->findOrFail($id);

        return view('cost_section.approve_PrForm', compact('pr'));
    }

    /**
     * Mengubah status Purchase Request menjadi 'approved'
     * Menyimpan tanda tangan digital Costing ke dalam kolom 'approved_signature'
     * (Dipanggil dari form action di dalam approve_PrForm.blade.php)
     */
    public function approve(Request $request, $id)
    {
        $pr = PurchaseRequestEng::findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'Sesi login Anda telah habis.');
        }

        try {
            // Ambil path tanda tangan milik user Costing yang sedang login
            $approverSignature = $user->signature_path ?? $user->signature ?? null;

            $updateData = [
                'status'             => 'approved',
                'approved_signature' => $approverSignature
            ];

            // Jika ada penyesuaian parameter QTY atau field tambahan dari form baru
            if ($request->has('qty_pr')) {
                $request->validate([
                    'qty_pr' => 'required|integer|min:1'
                ]);
                $updateData['qty_pr'] = $request->qty_pr;
            }
            
            // Tambahkan request penyesuaian custom lain di sini jika form baru memuat data input tambahan

            // Eksekusi update ke database
            $pr->update($updateData);
            
            // Redirect dikembalikan ke halaman INDEX antrean utama agar user tidak stuck di halaman form
            return redirect()->route('costing.pr.index')->with('success', 'PR ' . $pr->no_pr . ' telah berhasil disetujui sepenuhnya (Approved) & ditandatangani.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses approval akhir! Error: ' . $e->getMessage());
        }
    }

    /**
     * Mengubah status Purchase Request menjadi 'rejected'
     * (Tetap dipanggil secara Onsite dari halaman depan via SweetAlert2)
     */
    public function reject($id)
    {
        $pr = PurchaseRequestEng::findOrFail($id);

        try {
            $pr->update(['status' => 'rejected']);
            
            return redirect()->back()->with('success', 'PR ' . $pr->no_pr . ' telah berhasil ditolak (Rejected).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses penolakan! Error: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat Purchase Request yang sudah selesai diproses oleh Costing
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Menampilkan data yang sudah final (approved / rejected)
        $query = PurchaseRequestEng::with(['user', 'sparepart'])
                                   ->whereIn('status', ['approved', 'rejected']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_pr', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan riwayat terbaru saat dokumen diperbarui
        $historyPr = $query->orderBy('updated_at', 'DESC')->paginate($perPage);

        return view('cost_section.history_pr', compact('historyPr', 'search'));
    }

    /**
     * Menampilkan Detail Arsip Purchase Request untuk Preview Log di Halaman History
     * (Membuka views/cost_section/preview_approval_pr.blade.php)
     */
    public function show($id)
    {
        // Eager load untuk mengambil data PR beserta relasi user dan sparepart-nya
        $pr = PurchaseRequestEng::with(['user', 'sparepart'])->findOrFail($id);

        // Diarahkan ke file blade baru yang akan Lu buat nanti
        return view('cost_section.preview_approval_pr', compact('pr'));
    }
}