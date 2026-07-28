<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\RequestProd;
use App\Models\Production\ListLineProduction; // Model master line
use App\Models\ListSparepartEng; // Model master sparepart
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $user = Auth::user();

        // 1. Cari data line otomatis berdasarkan NIK karyawan yang sedang login[cite: 2, 4]
        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();

        // 2. Ambil semua list sparepart untuk opsi pilihan item di dropdown form[cite: 5]
        $spareparts = ListSparepartEng::all();

        return view('stock_prod.process_req.requestProd', compact('activeLine', 'spareparts'));
    }

    public function store(Request $request)
    {
        $actionType = $request->input('action_type', 'submit');
        $user = Auth::user(); 

        // Aturan validasi input teks dasar + validasi foreign key
        $request->validate([
            'sparepart_id' => 'required|exists:list_sparepart_engs,id',
            'remark'       => 'required|string|max:255', 
            'qty_req'      => 'required|integer|min:1',
        ]);

        // Mengambil detail sparepart berdasarkan sparepart_id pilihan operator[cite: 5]
        $sparepartItem = ListSparepartEng::findOrFail($request->sparepart_id);

        // Mengambil line otomatis berdasarkan user nik[cite: 2, 4]
        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();
        $lineMachineText = $activeLine ? "LINE " . $activeLine->no_line . " - " . $activeLine->name_machine : "N/A";

        // Generate Request Number otomatis
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

        // Logika Otomatisasi Tanda Tangan Database dari table users
        $prodSignature = ($statusAkhir === 'Pending') ? $user->signature_path : null;

        RequestProd::create([
            'list_line_production_id' => $activeLine ? $activeLine->id : null,
            'sparepart_id'            => $request->sparepart_id,
            'request_no'              => $requestNo,
            'sparepart_name'          => $sparepartItem->category . ' (' . $sparepartItem->part_number . ')',
            'sap_code'                => $sparepartItem->sap_code ?? '-',             
            'remark'                  => $request->remark, 
            'qty_req'                 => $request->qty_req,
            'line_machine'            => $lineMachineText,
            'requestor'               => $user->name, 
            'production_signature'    => $prodSignature, 
            'production_stamp'        => null,     
            'status'                  => $statusAkhir,
            
            'staff_name'              => null,
            'staff_signature'         => null,
            'spv_name'                => null,
            'spv_signature'           => null,
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

        $user = Auth::user();
        
        // Ambil data pendukung master agar dropdown & info line terisi saat halaman edit dibuka[cite: 4, 5]
        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();
        $spareparts = ListSparepartEng::all();

        return view('stock_prod.process_req.requestProd', compact('requestData', 'activeLine', 'spareparts'));
    }

    public function updateDraft(Request $request, $id)
    {
        $requestProd = RequestProd::findOrFail($id);
        $actionType = $request->input('action_type', 'submit');
        $user = Auth::user();

        $request->validate([
            'sparepart_id' => 'required|exists:list_sparepart_engs,id',
            'remark'       => 'required|string|max:255', 
            'qty_req'      => 'required|integer|min:1',
        ]);

        // Mengambil data item & data line terbaru dari DB
        $sparepartItem = ListSparepartEng::findOrFail($request->sparepart_id);
        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();
        $lineMachineText = $activeLine ? "LINE " . $activeLine->no_line . " - " . $activeLine->name_machine : "N/A";

        $statusAkhir = ($actionType === 'draft') ? 'Draft' : 'Pending';
        
        // Mengisi tanda tangan otomatis dari user login saat beralih status ke Pending
        $finalSignature = ($statusAkhir === 'Pending') ? $user->signature_path : $requestProd->production_signature;

        $requestProd->update([
            'list_line_production_id' => $activeLine ? $activeLine->id : null,
            'sparepart_id'            => $request->sparepart_id,
            'sparepart_name'          => $sparepartItem->category . ' (' . $sparepartItem->part_number . ')',
            'sap_code'                => $sparepartItem->sap_code ?? '-',             
            'remark'                  => $request->remark, 
            'qty_req'                 => $request->qty_req,
            'line_machine'            => $lineMachineText,
            'requestor'               => $user->name, 
            'production_signature'    => $finalSignature,
            'production_stamp'        => null,     
            'status'                  => $statusAkhir,
            'staff_name'              => null,
            'staff_signature'         => null,
            'spv_name'                => null,
            'spv_signature'           => null,
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
            'remark'  => 'required|string|max:255',
            'qty_req' => 'required|integer|min:1',
            'status'  => 'nullable|string'
        ]);

        $requestProd->update([
            'remark'  => $request->remark,
            'qty_req' => $request->qty_req,
            'status'  => $request->input('status', $requestProd->status)
        ]);

        return redirect()->route('prod.request.list')->with('success', 'Request ' . $requestProd->request_no . ' berhasil diupdate!');
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
        return redirect()->route('prod.request.list')->with('success', 'Request ' . $requestNo . ' berhasil dihapus dari sistem!');
    }
}