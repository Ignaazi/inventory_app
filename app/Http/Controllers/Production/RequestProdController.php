<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\RequestProd;
use Illuminate\Http\Request;

class RequestProdController extends Controller
{
    public function index()
    {
        return view('stock_prod.process_req.requestProd');
    }

    public function store(Request $request)
    {
        // 1. Validasi input dari form Production + WAJIBKAN TTD
        $request->validate([
            'requestor'      => 'required|string|max:255',
            'line_machine'   => 'required|string|max:100',
            'sparepart_name' => 'required|string|max:255',
            'sap_code'       => 'required|string|max:100',
            'qty_req'        => 'required|integer|min:1',
            'signature_data' => 'required|string', // Kunci biar ga lolos kalau TTD kosong
        ], [
            'signature_data.required' => '🚨 Tanda tangan Operator / Staff Production wajib diisi!',
        ]);

        // 2. Generate Nomor Request Otomatis (Format PT. SIIX Resmi)
        $requestNo = 'REQ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        // 3. Tangkap data TTD (signature_data) dari form view
        $productionSignature = $request->input('signature_data'); 
        $productionStamp     = $request->input('stamp_data'); // Jika stempel opsional, ga masalah null

        // 4. Simpan ke database menggunakan model RequestProd
        RequestProd::create([
            'request_no'           => $requestNo,
            'sparepart_name'       => $request->sparepart_name,
            'sap_code'             => $request->sap_code,
            'qty_req'              => $request->qty_req,
            'line_machine'         => $request->line_machine,
            'requestor'            => $request->requestor,
            'production_signature' => $productionSignature, // Menyimpan TTD format string Base64 utuh
            'production_stamp'     => $productionStamp,     
            'status'               => 'Pending'             
        ]);

        return redirect()->route('prod.request.index')
            ->with('success', 'Form Request Nozzle berhasil diajukan dengan Nomor Dokumen: ' . $requestNo);
    }
}