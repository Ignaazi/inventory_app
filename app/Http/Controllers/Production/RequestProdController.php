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
    public function listRequest()
    {
        // Mengambil semua data dari tabel production_requests, urut dari yang terbaru
        $requests = RequestProd::orderBy('created_at', 'desc')->get();
        
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

        // Validasi input dasar dari form Production
        $rules = [
            'requestor'      => 'required|string|max:255',
            'line_machine'   => 'required|string|max:100',
            'sparepart_name' => 'required|string|max:255',
            'sap_code'       => 'required|string|max:100',
            'qty_req'        => 'required|integer|min:1',
        ];

        // WAJIBKAN TTD HANYA jika aksinya adalah REAL SUBMIT (Bukan Draft)
        if ($actionType === 'submit') {
            $rules['signature_data'] = 'required|string';
        }

        $request->validate($rules, [
            'signature_data.required' => '🚨 Tanda tangan Operator / Staff Production wajib diisi untuk mengajukan request!',
        ]);

        // Generate nomor request baru karena ini data benar-benar baru
        $requestNo = 'REQ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        
        // Tentukan Status Akhir berdasarkan tombol yang diklik
        $statusAkhir = ($actionType === 'draft') ? 'Draft' : 'Pending';

        // Simpan data baru ke database
        RequestProd::create([
            'request_no'           => $requestNo,
            'sparepart_name'       => $request->sparepart_name,
            'sap_code'             => $request->sap_code,
            'qty_req'              => $request->qty_req,
            'line_machine'         => $request->line_machine,
            'requestor'            => $request->requestor,
            'production_signature' => $request->input('signature_data'), 
            'production_stamp'     => $request->input('stamp_data'),     
            'status'               => $statusAkhir
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

        // Validasi input
        $rules = [
            'requestor'      => 'required|string|max:255',
            'line_machine'   => 'required|string|max:100',
            'sparepart_name' => 'required|string|max:255',
            'sap_code'       => 'required|string|max:100',
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
            'sap_code'             => $request->sap_code,
            'qty_req'              => $request->qty_req,
            'line_machine'         => $request->line_machine,
            'requestor'            => $request->requestor,
            'production_signature' => $request->input('signature_data'),
            'production_stamp'     => $request->input('stamp_data'),
            'status'               => $statusAkhir
        ]);

        $pesanSukses = ($statusAkhir === 'Draft') 
            ? 'Draft Request No: ' . $requestProd->request_no . ' berhasil diperbarui!'
            : 'Draft Request No: ' . $requestProd->request_no . ' resmi dikirim ke Engineering!';

        return redirect()->route('prod.request.list')->with('success', $pesanSukses);
    }
}