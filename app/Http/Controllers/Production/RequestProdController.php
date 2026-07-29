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

        // 1. Cari data line otomatis berdasarkan NIK karyawan yang sedang login
        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();

        // JIKA ADMIN: Ambil fallback line pertama agar UI di view blade tidak kosong melompong
        if (!$activeLine && $user->role === 'admin') {
            $activeLine = ListLineProduction::first();
        }

        // 2. Ambil semua list sparepart untuk opsi pilihan item di dropdown form
        $spareparts = ListSparepartEng::all();

        return view('stock_prod.process_req.requestProd', compact('activeLine', 'spareparts'));
    }

    public function store(Request $request)
    {
        $actionType = $request->input('action_type', 'submit');
        $user = Auth::user(); 

        // Validasi dilonggarkan dulu untuk melacak ID asli yang dikirim browser
        $request->validate([
            'sparepart_id' => 'required',
            'remark'       => 'required|string|max:255', 
            'qty_req'      => 'required|integer|min:1',
        ]);

        // FIX 404 BYPASS: Mencari sparepart dengan first() agar tidak memicu 404 otomatis dari Laravel jika tidak ketemu
        $sparepartItem = ListSparepartEng::where('id', $request->sparepart_id)->first();

        // JIKA MASIH BELUM KETEMU, COBA COCOKKAN DENGAN KOLOM 'sparepart_id' (Sesuaikan dengan skema tabel lu)
        if (!$sparepartItem) {
            $sparepartItem = ListSparepartEng::where('sparepart_id', $request->sparepart_id)->first();
        }

        // PANCING ERROR BIAR KELUAR DI LAYAR (BIAR GAK MENTAL 404)
        if (!$sparepartItem) {
            return dd([
                'STATUS' => 'ERROR FATAL',
                'PESAN' => 'Data Sparepart TIDAK DITEMUKAN di database. Ini alasan kenapa Laravel ngasih eror 404!',
                'ID_YANG_DIKIRIM_BLADE' => $request->sparepart_id,
                'SOLUSI' => 'Coba cek tabel master sparepart lu, kolom primary key-nya namanya apa? id atau sparepart_id?'
            ]);
        }

        // Mengambil line otomatis berdasarkan user nik
        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();
        
        // PERBAIKAN UNTUK ADMIN: Jika admin menginput, kasih toleransi line cadangan
        if (!$activeLine && $user->role === 'admin') {
            $activeLine = ListLineProduction::first();
        }

        $lineMachineText = $activeLine ? "LINE " . $activeLine->no_line . " - " . $activeLine->name_machine : "ADMINISTRATOR AREA";
        $lineId = $activeLine ? $activeLine->id : null;

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
        
        // Status awal jika langsung diajukan adalah 'Pending' agar masuk ke antrean staff Engineering
        $statusAkhir = ($actionType === 'draft') ? 'Draft' : 'Pending';

        // Logika Otomatisasi Tanda Tangan Database dari table users untuk Produksi
        $prodSignature = ($statusAkhir === 'Pending') ? $user->signature_path : null;

        // Ambil primary key asli dari record sparepart yang berhasil ditemukan
        $primarySparepartId = isset($sparepartItem->id) ? $sparepartItem->id : $sparepartItem->sparepart_id;

        RequestProd::create([
            'list_line_production_id' => $lineId,
            'sparepart_id'            => $primarySparepartId,
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
            
            // Mengosongkan data approval Engineering di awal request
            'staff_name'              => null,
            'staff_signature'         => null,
            'staff_stamp'             => null,
            'spv_name'                => null,
            'spv_signature'           => null,
            'spv_stamp'               => null,
        ]);

        $pesanSukses = ($statusAkhir === 'Draft') 
            ? 'Form Request berhasil disimpan sebagai Draft dengan Nomor Dokumen: ' . $requestNo
            : 'Form Request Nozzle berhasil diajukan ke Engineering dengan Nomor Dokumen: ' . $requestNo;

        return redirect()->route('prod.request.list')->with('success', $pesanSukses);
    }

    public function editDraft($id)
    {
        $requestData = RequestProd::findOrFail($id);
        
        if ($requestData->status !== 'Draft') {
            return redirect()->route('prod.request.list')->with('error', 'Hanya data dengan status Draft yang bisa diedit kembali!');
        }

        $user = Auth::user();
        
        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();
        
        if (!$activeLine && $user->role === 'admin') {
            $activeLine = ListLineProduction::first();
        }

        $spareparts = ListSparepartEng::all();

        return view('stock_prod.process_req.requestProd', compact('requestData', 'activeLine', 'spareparts'));
    }

    public function updateDraft(Request $request, $id)
    {
        $requestProd = RequestProd::findOrFail($id);
        $actionType = $request->input('action_type', 'submit');
        $user = Auth::user();

        $request->validate([
            'sparepart_id' => 'required',
            'remark'       => 'required|string|max:255', 
            'qty_req'      => 'required|integer|min:1',
        ]);

        $sparepartItem = ListSparepartEng::where('id', $request->sparepart_id)->first();
        if (!$sparepartItem) {
            $sparepartItem = ListSparepartEng::where('sparepart_id', $request->sparepart_id)->first();
        }

        if (!$sparepartItem) {
            return dd("Draft Update Gagal: Data sparepart tidak ditemukan di DB.");
        }

        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();
        
        if (!$activeLine && $user->role === 'admin') {
            $activeLine = ListLineProduction::first();
        }

        $lineMachineText = $activeLine ? "LINE " . $activeLine->no_line . " - " . $activeLine->name_machine : "ADMINISTRATOR AREA";
        $lineId = $activeLine ? $activeLine->id : null;

        $statusAkhir = ($actionType === 'draft') ? 'Draft' : 'Pending';
        $finalSignature = ($statusAkhir === 'Pending') ? $user->signature_path : $requestProd->production_signature;
        $primarySparepartId = isset($sparepartItem->id) ? $sparepartItem->id : $sparepartItem->sparepart_id;

        $requestProd->update([
            'list_line_production_id' => $lineId,
            'sparepart_id'            => $primarySparepartId,
            'sparepart_name'          => $sparepartItem->category . ' (' . $sparepartItem->part_number . ')',
            'sap_code'                => $sparepartItem->sap_code ?? '-',             
            'remark'                  => $request->remark, 
            'qty_req'                 => $request->qty_req,
            'line_machine'            => $lineMachineText,
            'requestor'               => $user->name, 
            'production_signature'    => $finalSignature,
            'status'                  => $statusAkhir,
        ]);

        $pesanSukses = ($statusAkhir === 'Draft') 
            ? 'Draft Request No: ' . $requestProd->request_no . ' berhasil diperbarui!'
            : 'Draft Request No: ' . $requestProd->request_no . ' resmi diajukan ke Engineering!';

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