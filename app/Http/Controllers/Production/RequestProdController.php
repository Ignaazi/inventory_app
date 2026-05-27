<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\RequestProd;
use Illuminate\Http\Request;

class RequestProdController extends Controller
{
    public function index() {
        $requests = RequestProd::orderBy('created_at', 'desc')->paginate(25); 
        
        return view('stock_prod.process_req.listRequestProd', compact('requests'));
    }
    public function listRequest()
    {
        $requests = RequestProd::orderBy('created_at', 'desc')->paginate(25);
        
        return view('stock_prod.process_req.listRequestProd', compact('requests'));
    }
    public function fetchUpdates()
    {
        $requests = RequestProd::orderBy('updated_at', 'desc')->take(15)->get();

        return response()->json([
            'success' => true,
            'data'    => $requests
        ]);
    }
    public function create()
    {
        return view('stock_prod.process_req.requestProd');
    }
    public function store(Request $request)
    {
        $actionType = $request->input('action_type', 'submit');

        $rules = [
            'requestor'      => 'required|string|max:255',
            'line_machine'   => 'required|string|max:100',
            'sparepart_name' => 'required|string|max:255',
            'remark'         => 'required|string|max:255', 
            'qty_req'        => 'required|integer|min:1',
        ];

        if ($actionType === 'submit') {
            $rules['signature_data'] = 'required|string';
        }

        $request->validate($rules, [
            'signature_data.required' => ' Tanda tangan Operator / Staff Production wajib diisi untuk mengajukan request!',
        ]);

        $lastRequest = RequestProd::where('request_no', 'LIKE', 'REQ-PRD-SIIX-%')
                                    ->orderBy('id', 'desc')
                                    ->first();

        if ($lastRequest) {
            $lastNumber = (int) substr($lastRequest->request_no, 13);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $length = $nextNumber > 999 ? strlen((string)$nextNumber) : 3;
        $requestNo = 'REQ-PRD-SIIX-' . str_pad($nextNumber, $length, '0', STR_PAD_LEFT);   
        $statusAkhir = ($actionType === 'draft') ? 'Draft' : 'Pending';
        $prodSignature = $request->input('signature_data');
        $prodStamp     = $request->input('stamp_data');

        if ($prodSignature === 'null' || empty($prodSignature)) $prodSignature = null;
        if ($prodStamp === 'null' || empty($prodStamp)) $prodStamp = null;

        RequestProd::create([
            'request_no'           => $requestNo,
            'sparepart_name'       => $request->sparepart_name,
            'sap_code'             => null,             
            'remark'               => $request->remark, 
            'qty_req'              => $request->qty_req,
            'line_machine'         => $request->line_machine,
            'requestor'            => $request->requestor,
            'production_signature' => $prodSignature, 
            'production_stamp'     => $prodStamp,     
            'status'               => $statusAkhir,
            
            'staff_name'           => null,
            'staff_signature'      => null,
            'spv_name'             => null,
            'spv_signature'        => null,
        ]);

        $pesanSukses = ($statusAkhir === 'Draft') 
            ? 'Form Request berhasil disimpan sebagai Draft dengan Nomor Dokumen: ' . $requestNo
            : 'Form Request Nozzle berhasil diajukan dengan Nomor Dokumen: ' . $requestNo;

        return redirect()->route('prod.request.list')->with('success', $pesanSukses);
    }
    public function editDraft($id)
    {
        $requestData = RequestProd::findOrFail($id);
        
        if ($requestData->status !== 'Draft') {
            return redirect()->route('prod.request.list')->with('error', 'Hanya data dengan status Draft yang bisa diedit kembali!');
        }

        return view('stock_prod.process_req.draftRequestProd', compact('requestData'));
    }
    public function updateDraft(Request $request, $id)
    {
        $requestProd = RequestProd::findOrFail($id);
        $actionType = $request->input('action_type', 'submit');

        $rules = [
            'requestor'      => 'required|string|max:255',
            'line_machine'   => 'required|string|max:100',
            'sparepart_name' => 'required|string|max:255',
            'remark'         => 'required|string|max:255', 
            'qty_req'        => 'required|integer|min:1',
        ];
        if ($actionType === 'submit' && empty($requestProd->production_signature)) {
            $rules['signature_data'] = 'required|string';
        }

        $request->validate($rules, [
            'signature_data.required' => ' Tanda tangan Operator / Staff Production wajib diisi untuk mengajukan request!',
        ]);
        $statusAkhir = ($actionType === 'draft') ? 'Draft' : 'Pending';
        $newSignature = $request->input('signature_data');
        $newStamp     = $request->input('stamp_data');
        $finalSignature = (!empty($newSignature) && $newSignature !== 'null') ? $newSignature : $requestProd->production_signature;
        $finalStamp     = (!empty($newStamp) && $newStamp !== 'null') ? $newStamp : $requestProd->production_stamp;
        $requestProd->update([
            'sparepart_name'       => $request->sparepart_name,
            'remark'               => $request->remark, 
            'qty_req'              => $request->qty_req,
            'line_machine'         => $request->line_machine,
            'requestor'            => $request->requestor,
            'production_signature' => $finalSignature,
            'production_stamp'     => $finalStamp,     
            'status'               => $statusAkhir,
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
    public function preview($id)
    {
        $requestData = RequestProd::findOrFail($id);
        return view('stock_prod.process_req.previewRequestProd', compact('requestData'));
    }
    public function destroy($id)
    {
        $requestProd = RequestProd::findOrFail($id);
        $requestNo = $requestProd->request_no;
        $requestProd->delete();
        return redirect()->route('prod.request.list')->with('success', 'Request ' . $requestNo . ' berhasil dihapus dari sistem, coy!');
    }
}