<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\ApprovalEng; // Model antrean aktif asli lo
use App\Models\Engineering\HistoryApproval; // Model history aktif lo
use Illuminate\Http\Request;
// --- TAMBAHKAN IMPORT RESMI INI BIAR METHOD USER/CHECK TIDAK EROR ---
use Illuminate\Support\Facades\Auth; 

class ApprovalEngController extends Controller
{
    // 1. Tampilkan antrean request status Pending
    public function index()
    {
        $requests = ApprovalEng::where('status', 'Pending')
            ->latest()
            ->paginate(10);

        return view('stock_eng.process_req.approval', compact('requests'));
    }

    // 2. Fungsi untuk Pindah ke Halaman Review Form Cetak SIIX (New Page)
    public function review($id)
    {
        $req = ApprovalEng::findOrFail($id);
        
        return view('stock_eng.process_req.approveform', compact('req'));
    }

    // 3. Aksi Terima Permintaan (Approve) dengan Tanda Tangan Digital
    public function approve(Request $request, $id)
    {
        // Validasi kiriman string base64 gambar canvas dari halaman review
        $request->validate([
            'signature_image' => 'required|string' 
        ]);

        $requestData = ApprovalEng::findOrFail($id);

        // --- PROSES DEKODE GAMBAR TANDA TANGAN BASE64 ---
        $image_data = $request->signature_image;
        $image_data = str_replace('data:image/png;base64,', '', $image_data);
        $image_data = str_replace(' ', '+', $image_data);
        
        $fileName = 'sig_' . str_replace('/', '-', $requestData->request_no) . '_' . time() . '.png';
        
        $folderPath = public_path('uploads/signatures');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        file_put_contents($folderPath . '/' . $fileName, base64_decode($image_data));

        $signaturePath = 'uploads/signatures/' . $fileName;

        // --- FIX EROR: Ambil nama user login menggunakan Facade Auth resmi ---
        $approverName = Auth::check() ? Auth::user()->name : 'Engineering Staff';

        // -----------------------------------------------------------------
        // Simpan log ke tabel history_approvals via Model bawaan lo
        // -----------------------------------------------------------------
        HistoryApproval::create([
            'request_no'     => $requestData->request_no,
            'sparepart_name' => $requestData->sparepart_name,
            'sap_code'       => $requestData->sap_code,
            'qty_req'        => $requestData->qty_req,
            'line_machine'   => $requestData->line_machine,
            'requestor'      => $requestData->requestor ?? 'Production Staff', 
            'approved_by'    => $approverName, 
            'signature_image'=> asset($signaturePath), 
            'status'         => 'APPROVED',
            'processed_at'   => now(),
        ]);

        // Update status berkas utama agar hilang dari antrean pending
        $requestData->update([
            'status' => 'Approved',
            'approved_by' => $approverName,
            'signature_path' => $signaturePath
        ]);

        return redirect()->route('eng.approval')
            ->with('success', "Request {$requestData->request_no} berhasil di-APPROVE oleh {$approverName}!");
    }

    // 4. Aksi Tolak Permintaan (Reject)
    public function reject(Request $request, $id)
    {
        $requestData = ApprovalEng::findOrFail($id);
        
        // --- FIX EROR: Ambil nama user login menggunakan Facade Auth resmi ---
        $approverName = Auth::check() ? Auth::user()->name : 'Engineering Staff';
        
        // -----------------------------------------------------------------
        // Simpan log ke tabel history_approvals dengan status REJECTED
        // -----------------------------------------------------------------
        HistoryApproval::create([
            'request_no'     => $requestData->request_no,
            'sparepart_name' => $requestData->sparepart_name,
            'sap_code'       => $requestData->sap_code,
            'qty_req'        => $requestData->qty_req,
            'line_machine'   => $requestData->line_machine,
            'requestor'      => $requestData->requestor ?? 'Production Staff',
            'approved_by'    => $approverName,
            'signature_image'=> null, 
            'status'         => 'REJECTED',
            'processed_at'   => now(),
        ]);

        // Update status berkas utama
        $requestData->update([
            'status' => 'Rejected',
            'reject_remark' => $request->input('reason', 'Ditolak oleh Engineering')
        ]);

        return redirect()->route('eng.approval')
            ->with('success', "Request {$requestData->request_no} telah di-REJECT.");
    }
}