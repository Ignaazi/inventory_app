<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\ApprovalEng; // Tetap pakai model asli milik lo, bro
use Illuminate\Http\Request;

class ApprovalEngController extends Controller
{
    // 1. Tampilkan antrean request status Pending
    public function index()
    {
        $requests = ApprovalEng::where('status', 'Pending')
            ->latest()
            ->paginate(10);

        // Mengarah ke view kustom milik lo
        return view('stock_eng.process_req.approval', compact('requests'));
    }

    // 2. Aksi Terima Permintaan (Approve) dengan Tanda Tangan Digital
    public function approve(Request $request, $id)
    {
        // Validasi input nama penanggung jawab dan kiriman string base64 gambar canvas
        $request->validate([
            'approved_by' => 'required|string|max:255',
            'signature_image' => 'required|string' 
        ]);

        $requestData = ApprovalEng::findOrFail($id);

        // --- PROSES DEKODE GAMBAR TANDA TANGAN BASE64 ---
        $image_data = $request->signature_image;
        $image_data = str_replace('data:image/png;base64,', '', $image_data);
        $image_data = str_replace(' ', '+', $image_data);
        
        // Buat nama berkas gambar unik berbasis nomor request dan waktu
        $fileName = 'sig_' . str_replace('/', '-', $requestData->request_no) . '_' . time() . '.png';
        
        // Simpan file tanda tangan fisik ke dalam folder public/uploads/signatures/ di Mac lo
        $folderPath = public_path('uploads/signatures');
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }
        file_put_contents($folderPath . '/' . $fileName, base64_decode($image_data));

        // Update status beserta penanggung jawab & path file tanda tangannya
        $requestData->update([
            'status' => 'Approved',
            'approved_by' => $request->approved_by,
            'signature_path' => 'uploads/signatures/' . $fileName
        ]);

        // FIX: Menggunakan back() agar anti-eror route name mismatch
        return redirect()->back()
            ->with('success', "Request {$requestData->request_no} berhasil di-APPROVE oleh {$request->approved_by}!");
    }

    // 3. Aksi Tolak Permintaan (Reject)
    public function reject(Request $request, $id)
    {
        $requestData = ApprovalEng::findOrFail($id);
        
        $requestData->update([
            'status' => 'Rejected',
            'reject_remark' => $request->input('reason', 'Ditolak oleh Engineering')
        ]);

        // FIX: Menggunakan back() agar anti-eror route name mismatch
        return redirect()->back()
            ->with('success', "Request {$requestData->request_no} telah di-REJECT.");
    }
}