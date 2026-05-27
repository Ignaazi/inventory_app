<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestEngController extends Controller
{
    /**
     * Menampilkan halaman form pembuatan Purchase Request (PR)
     * Sekaligus meng-generate nomor PR otomatis (Odoo Style)
     */
    public function index(Request $request)
    {
        // 1. Dapatkan tanggal aktual hari ini (Format: YYYY/MM/DD)
        $today = Carbon::now();
        $datePrefix = $today->format('Y/m/d'); 

        // Pattern pencarian diganti mengikuti format baru RR/ENG/RFSP/
        $searchPattern = "RR/ENG/RFSP/%";

        $lastPr = null;
        try {
            // Mengambil data PR terakhir berdasarkan ID terbesar secara global (Supaya nomornya continue terus)
            $lastPr = PurchaseRequestEng::where('pr_code', 'like', $searchPattern)
                ->orderBy('id', 'desc')
                ->first();
        } catch (\Exception $e) {
            $lastPr = null;
        }

        if ($lastPr) {
            // Memotong 4 digit nomor urut paling belakang secara aman dari string RR/ENG/RFSP/YYYY/MM/DD/XXXX
            $lastNumber = (int) substr($lastPr->pr_code, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Jika database masih kosong/belum ada format baru, mulai dari 0001
            $nextNumber = '0001';
        }

        // Gabungkan menjadi format baru sesuai request, Bro!
        $generatedPrCode = "RR/ENG/RFSP/" . $datePrefix . "/" . $nextNumber;

        try {
            // Ambil data sparepart untuk select option di view
            $spareparts = DB::table('spareparts')->get(); 
        } catch (\Exception $e) {
            $spareparts = collect([]);
        }

        return view('stock_eng.purchase_request', compact('generatedPrCode', 'spareparts'));
    }

    /**
     * Menyimpan data Purchase Request baru dari Department Engineering
     * Status otomatis diset ke 'waiting approval' (Menunggu persetujuan Costing)
     */
    public function store(Request $request)
    {
        // Validasi data input dari Form
        $request->validate([
            'pr_code'      => 'required',
            'product'      => 'required',
            'type_product' => 'required',
            'priority'     => 'required|in:normal,urgent',
            'request_by'   => 'required',
            'request_date' => 'required',
            'destination'  => 'required',
            'notes'        => 'required',
        ]);

        // Mengambil data user yang sedang login
        $userLogin = Auth::user();
        $namaUser  = $userLogin ? $userLogin->name : 'muhammad ignazi';
        $nikUser   = $userLogin ? ($userLogin->nim ?? $userLogin->nik) : '20260001';

        try {
            // 🛠️ STRATEGI BARU: Kita simpan object-nya ke dalam variabel $pr
            $pr = PurchaseRequestEng::create([
                'pr_code'      => $request->pr_code,
                'name'         => $namaUser, 
                'nik'          => $nikUser,   
                'product'      => $request->product,
                'type_product' => $request->type_product,
                'priority'     => $request->priority,
                'request_by'   => $request->request_by,
                'request_date' => $request->request_date,
                'destination'  => $request->destination,
                'notes'        => $request->notes,
                'status'       => 'waiting approval',
            ]);

            // 🛠️ DOUBLE PROTECTION (FORCE UPDATE):
            // Jika ada event model atau default value database yang nakal mengubah statusnya menjadi approved, 
            // kita paksa timpa (override) kembali di sini ke 'waiting approval' lalu save ulang secara silent.
            if ($pr->status !== 'waiting approval') {
                $pr->status = 'waiting approval';
                $pr->saveQuietly(); // saveQuietly() digunakan agar tidak memicu Observer/Event bawaan model
            }

            // Redirect ke halaman history dengan pesan sukses
            return redirect()->route('purchase.request.history')->with('success', 'Purchase Request ' . $request->pr_code . ' berhasil diajukan dan saat ini berstatus Menunggu Persetujuan (Waiting Approval)!');

        } catch (\Exception $e) {
            // Penangkap error tak terduga (misal jika ada masalah koneksi database)
            return redirect()->back()->withInput()->with('error', 'Gagal memproses pengajuan! Error: ' . $e->getMessage());
        }
    }
}