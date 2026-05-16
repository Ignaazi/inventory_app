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
        // 1. Validasi mengikuti field name baru dari View blade
        $request->validate([
            'requestor'      => 'required|string|max:255',
            'line_machine'   => 'required|string|max:100',
            'sparepart_name' => 'required|string|max:255',
            'sap_code'       => 'required|string|max:100',
            'qty_req'        => 'required|integer|min:1',
        ]);

        // 2. Generate No Request Otomatis & Unik (Sangat keren untuk skripsi)
        // Format: REQ-20260516-XXXX (4 digit acak di belakang)
        $requestNo = 'REQ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        // 3. Simpan data klop 100% dengan kolom Migration lo
        RequestProd::create([
            'request_no'     => $requestNo,
            'sparepart_name' => $request->sparepart_name,
            'sap_code'       => $request->sap_code,
            'qty_req'        => $request->qty_req,
            'line_machine'   => $request->line_machine,
            'requestor'      => $request->requestor,
            'status'         => 'Pending' // Default enum status sesuai migration
        ]);

        return redirect()->route('prod.request.index')->with('success', 'Sparepart request submitted successfully with ID: ' . $requestNo);
    }
}