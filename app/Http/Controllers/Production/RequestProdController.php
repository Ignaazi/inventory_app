<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\RequestProd;
use Illuminate\Http\Request;

class RequestProdController extends Controller
{
    /**
     * Menampilkan semua daftar request (Form List)
     * UPDATE: Sekarang mengarah ke file listRequestProd.blade.php
     */
    public function index() {
        // Ganti ->get() jadi ->paginate(25)
        $requests = RequestProd::orderBy('created_at', 'desc')->paginate(25); 
        
        return view('stock_prod.process_req.listRequestProd', compact('requests'));
    }

    public function listRequest()
    {
        // Mengambil semua data dari tabel production_requests, urut dari yang terbaru
        // KODE BARU (Ganti get() menjadi paginate(25))
        $requests = RequestProd::orderBy('created_at', 'desc')->paginate(25);
        
        // Mengarah ke file listRequestProd di dalam folder view lu
        return view('stock_prod.process_req.listRequestProd', compact('requests'));
    }

    /**
     * Menampilkan Form Create Utama (Pertama kali bikin request)
     */
    public function create()
    {
        return view('stock_prod.process_req.requestProd');
    }

    /**
     * Menyimpan data BARU pertama kali dari form requestProd
     */
    public function store(Request $request)
    {
        // Tangkap jenis aksi tombol (apakah 'draft' atau 'submit')
        $actionType = $request->input('action_type', 'submit');

        // Validasi input dasar dari form Production (sap_code diganti remark)
        $rules = [
            'requestor'      => 'required|string|max:255',
            'line_machine'   => 'required|string|max:100',
            'sparepart_name' => 'required|string|max:255',
            'remark'         => 'required|string|max:255', // UPDATE: sap_code -> remark
            'qty_req'        => 'required|integer|min:1',
        ];

        // WAJIBKAN TTD HANYA jika aksinya adalah REAL SUBMIT (Bukan Draft)
        if ($actionType === 'submit') {
            $rules['signature_data'] = 'required|string';
        }

        $request->validate($rules, [
            'signature_data.required' => '🚨 Tanda tangan Operator / Staff Production wajib diisi untuk mengajukan request!',
        ]);

        // =========================================================================
        // LOGIKA BARU: GENERATE NOMOR URUT DINAMIS (REQ-PRD-SIIX-001)
        // =========================================================================
        $lastRequest = RequestProd::where('request_no', 'LIKE', 'REQ-PRD-SIIX-%')
                                    ->orderBy('id', 'desc')
                                    ->first();

        if ($lastRequest) {
            // Ambil angka setelah string 'REQ-PRD-SIIX-', misal dari REQ-PRD-SIIX-001 diambil 1
            $lastNumber = (int) substr($lastRequest->request_no, 13);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        // Jika angka di bawah 1000 tetap pakai 3 digit (001), jika di atas 1000 panjangnya menyesuaikan otomatis (dinamis tanpa batas)
        $length = $nextNumber > 999 ? strlen((string)$nextNumber) : 3;
        $requestNo = 'REQ-PRD-SIIX-' . str_pad($nextNumber, $length, '0', STR_PAD_LEFT);
        // =========================================================================
        
        // Tentukan Status Akhir berdasarkan tombol yang diklik
        $statusAkhir = ($actionType === 'draft') ? 'Draft' : 'Pending';

        // Simpan data baru ke database
        RequestProd::create([
            'request_no'           => $requestNo,
            'sparepart_name'       => $request->sparepart_name,
            'sap_code'             => null,             // 💡 FIX: Set null biar aman & tetep ada di DB
            'remark'               => $request->remark, // 💡 FIX: Simpan ke kolom remark yang baru
            'qty_req'              => $request->qty_req,
            'line_machine'         => $request->line_machine,
            'requestor'            => $request->requestor,
            'production_signature' => $request->input('signature_data'), 
            'production_stamp'     => $request->input('stamp_data'),     
            'status'               => $statusAkhir,
            
            // 🎯 SINKRONISASI DATABASE: Set null awal karena Engineering belum proses ttd bertahap
            'staff_name'           => null,
            'staff_signature'      => null,
            'spv_name'             => null,
            'spv_signature'        => null,
        ]);

        $pesanSukses = ($statusAkhir === 'Draft') 
            ? 'Form Request berhasil disimpan sebagai Draft dengan Nomor Dokumen: ' . $requestNo
            : 'Form Request Nozzle berhasil diajukan dengan Nomor Dokumen: ' . $requestNo;

        // Redirect balik ke halaman list request baru lu
        return redirect()->route('prod.request.list')->with('success', $pesanSukses);
    }

    /**
     * Mengambil data draft lama untuk dilempar ke Form Edit Khusus Draft
     * UPDATE: Sekarang mengarah ke draftRequestProd.blade.php
     */
    public function editDraft($id)
    {
        // Cari data berdasarkan ID draft tersebut
        $requestData = RequestProd::findOrFail($id);
        
        // Validasi keamanan: Pastikan hanya data berstatus 'Draft' yang bisa dibuka kembali untuk di-edit
        if ($requestData->status !== 'Draft') {
            return redirect()->route('prod.request.list')->with('error', 'Hanya data dengan status Draft yang bisa diedit kembali!');
        }

        // Buka halaman edit khusus draf, bawa variabel $requestData berisi data lama
        return view('stock_prod.process_req.draftRequestProd', compact('requestData'));
    }

    /**
     * Memproses Update Data Draft dari Form draftRequestProd (Method: PUT)
     * BARU: Method ini memisahkan logika update data draf agar tidak bentrok di store()
     */
    public function updateDraft(Request $request, $id)
    {
        $requestProd = RequestProd::findOrFail($id);
        $actionType = $request->input('action_type', 'submit');

        // Validasi input (sap_code diganti remark)
        $rules = [
            'requestor'      => 'required|string|max:255',
            'line_machine'   => 'required|string|max:100',
            'sparepart_name' => 'required|string|max:255',
            'remark'         => 'required|string|max:255', // UPDATE: sap_code -> remark
            'qty_req'        => 'required|integer|min:1',
        ];

        if ($actionType === 'submit') {
            $rules['signature_data'] = 'required|string';
        }

        $request->validate($rules, [
            'signature_data.required' => ' Tanda tangan Operator / Staff Production wajib diisi untuk mengajukan request!',
        ]);

        // Status berubah jadi 'Pending' jika di-submit resmi, atau tetap 'Draft' jika di-update draf lagi
        $statusAkhir = ($actionType === 'draft') ? 'Draft' : 'Pending';

        // Update records dengan Eloquent
        $requestProd->update([
            'sparepart_name'       => $request->sparepart_name,
            'remark'               => $request->remark, // 💡 FIX: Update data remark baru lu
            'qty_req'              => $request->qty_req,
            'line_machine'         => $request->line_machine,
            'requestor'            => $request->requestor,
            'production_signature' => $request->input('signature_data'),
            'production_stamp'     => $request->input('stamp_data'),
            'status'               => $statusAkhir,
            
            // 🎯 SINKRONISASI DATABASE: Pastikan kolom otorisasi Eng tetap clear saat draf diperbarui
            'staff_name'           => null,
            'staff_signature'      => null,
            'spv_name'             => null,
            'spv_signature'        => null,
        ]);

        $pesanSukses = ($statusAkhir === 'Draft') 
            ? 'Draft Request No: ' . $requestProd->request_no . ' berhasil diperbarui!'
            : 'Draft Request No: ' . $requestProd->request_no . ' resmi dikirim ke Engineering!';

        return redirect()->route('prod.request.list')->with('success', $pesanSukses);
    }

    // =========================================================================
    // 🔥 TAMBAHAN LOGIKA BARU UNTUK ACTION UPDATE, DESTROY & PREVIEW (TANPA MENGHAPUS YANG ADA)
    // =========================================================================

    /**
     * 1. Method Update Utama (PUT)
     * Berfungsi menangani request edit status global atau data jika diperlukan di luar alur draft
     */
    public function update(Request $request, $id)
    {
        $requestProd = RequestProd::findOrFail($id);

        $request->validate([
            'requestor'      => 'required|string|max:255',
            'line_machine'   => 'required|string|max:100',
            'sparepart_name' => 'required|string|max:255',
            'remark'         => 'required|string|max:255',
            'qty_req'        => 'required|integer|min:1',
            'status'         => 'nullable|string'
        ]);

        $requestProd->update([
            'sparepart_name' => $request->sparepart_name,
            'remark'         => $request->remark,
            'qty_req'        => $request->qty_req,
            'line_machine'   => $request->line_machine,
            'requestor'      => $request->requestor,
            'status'         => $request->input('status', $requestProd->status)
        ]);

        return redirect()->route('prod.request.list')->with('success', 'Request ' . $requestProd->request_no . ' berhasil diupdate, coy!');
    }

    /**
     * 2. Method Preview Form / Cetak Dokumen (GET)
     * Mengarahkan ke halaman cetak/formulir resmi preview sparepart nozzle
     */
    public function preview($id)
    {
        $requestData = RequestProd::findOrFail($id);

        // Arahkan ke file preview.blade.php di dalam folder view production kamu
        return view('stock_prod.process_req.previewRequestProd', compact('requestData'));
    }

    /**
     * 3. Method Destroy / Hapus Data Permanen (DELETE)
     * Menghapus request data production dari database secara aman
     */
    public function destroy($id)
    {
        $requestProd = RequestProd::findOrFail($id);
        $requestNo = $requestProd->request_no;

        $requestProd->delete();

        return redirect()->route('prod.request.list')->with('success', 'Request ' . $requestNo . ' berhasil dihapus dari sistem, coy!');
    }
}