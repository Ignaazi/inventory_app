<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\RequestProd;
use App\Models\Production\ListLineProduction; 
use App\Models\ListSparepartEng; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestProdController extends Controller
{
    public function index() 
    {
        // Menambahkan eager loading user pencetak approval (staff/spv jika direlasikan di model)
        $requests = RequestProd::with(['user', 'sparepart', 'lineProduction'])
            ->orderBy('created_at', 'desc')
            ->paginate(25); 
        
        return view('stock_prod.process_req.listRequestProd', compact('requests'));
    }

    public function listRequest()
    {
        $requests = RequestProd::with(['user', 'sparepart', 'lineProduction'])
            ->orderBy('created_at', 'desc')
            ->paginate(25);
        
        return view('stock_prod.process_req.listRequestProd', compact('requests'));
    }

    public function fetchUpdates()
    {
        $requests = RequestProd::with(['user', 'sparepart', 'lineProduction'])
            ->orderBy('updated_at', 'desc')
            ->take(15)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $requests
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        // Cari data line otomatis berdasarkan NIK karyawan yang sedang login
        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();

        // JIKA ADMIN: Ambil fallback line pertama
        if (!$activeLine && $user->role === 'admin') {
            $activeLine = ListLineProduction::first();
        }

        $spareparts = ListSparepartEng::all();
        
        // FIX: Ambil seluruh data master line untuk disuplai ke dropdown Blade
        $productionLines = ListLineProduction::all();

        return view('stock_prod.process_req.requestProd', compact('activeLine', 'spareparts', 'productionLines'));
    }

    public function store(Request $request)
    {
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
            return dd("Data Sparepart TIDAK DITEMUKAN di database.");
        }

        // FIX DROPDOWN CAPTURE: Tangkap ID Line dari pilihan dropdown form, jika kosong baru cari otomatis
        $lineId = $request->input('list_line_production_id');
        if (!$lineId) {
            $activeLine = ListLineProduction::where('user_id', $user->nik)->first();
            if (!$activeLine && $user->role === 'admin') {
                $activeLine = ListLineProduction::first();
            }
            $lineId = $activeLine ? $activeLine->id : null;
        }

        // ====================================================
        // GENERATE NUMBER: REQPROD001 TILL REQPROD999999
        // ====================================================
        $lastRequest = RequestProd::where('request_no', 'LIKE', 'REQPROD%')
                                    ->orderBy('id', 'desc')
                                    ->first();

        if ($lastRequest) {
            $lastNumber = (int) filter_var($lastRequest->request_no, FILTER_SANITIZE_NUMBER_INT);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $length = $nextNumber > 999 ? strlen((string)$nextNumber) : 3;
        $requestNo = 'REQPROD' . str_pad($nextNumber, $length, '0', STR_PAD_LEFT);   
        
        // FIX STATUS: Disamakan dengan kondisi deteksi giliran blade ('Draft Submit' / 'Pending')
        $statusAkhir = ($actionType === 'draft') ? 'Draft Submit' : 'Pending';

        // Tanda tangan otomatis production diambil dari tabel users lewat Auth jika langsung di-submit
        $prodSignature = ($statusAkhir === 'Pending') ? $user->signature_path : null;
        $primarySparepartId = isset($sparepartItem->id) ? $sparepartItem->id : $sparepartItem->sparepart_id;

        RequestProd::create([
            'user_id'                 => $user->id,
            'list_line_production_id' => $lineId,
            'sparepart_id'            => $primarySparepartId,
            'request_no'              => $requestNo,
            'remark'                  => $request->remark, 
            'qty_req'                 => $request->qty_req,
            'production_signature'    => $prodSignature, 
            'status'                  => $statusAkhir,
            'engineering_signature'   => null,
            'spv_signature'           => null,
            'reject_remark'           => null,
        ]);

        $pesanSukses = ($statusAkhir === 'Draft Submit') 
            ? 'Form Request berhasil disimpan sebagai Draft dengan Nomor Dokumen: ' . $requestNo
            : 'Form Request Sparepart berhasil diajukan ke Engineering dengan Nomor Dokumen: ' . $requestNo;

        return redirect()->route('prod.request.list')->with('success', $pesanSukses);
    }

    public function editDraft($id)
    {
        $requestData = RequestProd::findOrFail($id);
        
        // FIX STATUS: Validasi status mengikuti nilai string baru
        if (!in_array($requestData->status, ['Draft', 'Draft Submit'])) {
            return redirect()->route('prod.request.list')->with('error', 'Hanya data dengan status Draft yang bisa diedit kembali!');
        }

        $user = Auth::user();
        $activeLine = ListLineProduction::where('user_id', $user->nik)->first();
        if (!$activeLine && $user->role === 'admin') {
            $activeLine = ListLineProduction::first();
        }

        $spareparts = ListSparepartEng::all();
        
        // FIX: Ambil seluruh data master line untuk disuplai ke dropdown Blade saat Edit Draft
        $productionLines = ListLineProduction::all();

        return view('stock_prod.process_req.requestProd', compact('requestData', 'activeLine', 'spareparts', 'productionLines'));
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

        // FIX DROPDOWN CAPTURE: Tangkap ID Line dari pilihan dropdown form saat update draft
        $lineId = $request->input('list_line_production_id');
        if (!$lineId) {
            $activeLine = ListLineProduction::where('user_id', $user->nik)->first();
            if (!$activeLine && $user->role === 'admin') {
                $activeLine = ListLineProduction::first();
            }
            $lineId = $activeLine ? $activeLine->id : null;
        }

        // FIX STATUS: Disinkronkan ke 'Draft Submit'
        $statusAkhir = ($actionType === 'draft') ? 'Draft Submit' : 'Pending';
        $finalSignature = ($statusAkhir === 'Pending') ? $user->signature_path : $requestProd->production_signature;
        $primarySparepartId = isset($sparepartItem->id) ? $sparepartItem->id : $sparepartItem->sparepart_id;

        $requestProd->update([
            'user_id'                 => $user->id,
            'list_line_production_id' => $lineId,
            'sparepart_id'            => $primarySparepartId,
            'remark'                  => $request->remark, 
            'qty_req'                 => $request->qty_req,
            'production_signature'    => $finalSignature,
            'status'                  => $statusAkhir,
        ]);

        $pesanSukses = ($statusAkhir === 'Draft Submit') 
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
        $requestData = RequestProd::with(['user', 'sparepart', 'lineProduction'])->findOrFail($id);
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