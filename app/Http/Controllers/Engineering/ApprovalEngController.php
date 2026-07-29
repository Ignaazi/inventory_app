<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Production\RequestProd; 
use App\Models\Engineering\HistoryApproval; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class ApprovalEngController extends Controller
{
    /**
     * Menampilkan daftar request dari Production yang butuh approval Engineering.
     * Mengambil status 'Pending' (untuk Staff) dan 'Checked by Staff' (untuk SPV).
     */
    public function index()
    {
        $requests = RequestProd::whereIn('status', ['Pending', 'Checked by Staff'])
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('stock_eng.process_req.approval', compact('requests'));
    }

    /**
     * Membuka form review/detail approval request.
     */
    public function review($id)
    {
        $req = RequestProd::findOrFail($id);
        return view('stock_eng.process_req.approveform', compact('req'));
    }

    /**
     * Memproses logika Tanda Tangan Berjenjang (Staff -> SPV)
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'signature_image' => 'nullable|string',
            'stamp_image'     => 'nullable|string',
            'signer_role'     => 'required|string|in:staff,spv'
        ]);

        $requestData = RequestProd::findOrFail($id);
        $role = $request->input('signer_role');
        $user = Auth::user();
        
        $signaturePath = null;
        $stampPath = null;

        // 1. PROSES IPUT CANVAS TTD JIKA OPERATOR MENGGAMBAR MANUAL
        if ($request->filled('signature_image') && !str_starts_with($request->signature_image, 'http')) {
            $image_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_image);
            $fileName = 'sig_' . $role . '_' . str_replace('/', '-', $requestData->request_no) . '_' . time() . '.png';
            $folderPath = public_path('uploads/signatures');
            if (!file_exists($folderPath)) mkdir($folderPath, 0777, true);
            file_put_contents($folderPath . '/' . $fileName, base64_decode($image_data));
            $signaturePath = 'uploads/signatures/' . $fileName;
        }

        // 2. PROSES INPUT CANVAS CAP/STAMP JIKA ADA
        if ($request->filled('stamp_image') && !str_starts_with($request->stamp_image, 'http')) {
            $stamp_data = preg_replace('/^data:image\/\w+;base64,/', '', $request->stamp_image);
            $stamp_data = str_replace(' ', '+', $stamp_data);
            $stampName = 'stamp_' . $role . '_' . str_replace('/', '-', $requestData->request_no) . '_' . time() . '.png';
            $stampFolderPath = public_path('uploads/stamps');
            if (!file_exists($stampFolderPath)) mkdir($stampFolderPath, 0777, true);
            file_put_contents($stampFolderPath . '/' . $stampName, base64_decode($stamp_data));
            $stampPath = 'uploads/stamps/' . $stampName;
        }

        // Penentuan Nama Approver
        $approverName = $user ? $user->name : (($role === 'staff') ? 'Engineering Staff' : 'Engineering SPV');

        // ==========================================
        // ALUR KEDUA: JIKA DI-APPROVE OLEH STAFF
        // ==========================================
        if ($role === 'staff') {
            
            // JIKA tidak menggambar di canvas, ambil otomatis TTD dari profile user yang login
            if (!$signaturePath) {
                $signaturePath = $user ? $user->signature_path : $requestData->staff_signature;
            }

            $oldStamp = $requestData->staff_stamp;
            if ($oldStamp) {
                $oldStamp = str_replace(url('/'), '', $oldStamp);
                $oldStamp = ltrim($oldStamp, '/');
            }

            // Naikkan status dari 'Pending' menjadi 'Checked by Staff'
            $requestData->update([
                'status'          => 'Checked by Staff',
                'staff_name'      => $approverName,
                'staff_signature' => $signaturePath, 
                'staff_stamp'     => $stampPath ? $stampPath : $oldStamp               
            ]);

            // Catat log record ke table history approval
            HistoryApproval::create([
                'request_no'      => $requestData->request_no,
                'sparepart_name'  => $requestData->sparepart_name,
                'sap_code'        => $requestData->sap_code ?? '-',
                'qty_req'         => $requestData->qty_req,
                'line_machine'    => $requestData->line_machine,
                'requestor'       => $requestData->requestor ?? 'Production Staff', 
                'approved_by'     => $approverName, 
                'staff_signature' => $signaturePath,
                'status'          => 'Checked by Staff',
                'processed_at'    => now(),
            ]);

            return redirect()->route('eng.approval')->with('success', "Form Request berhasil diverifikasi oleh Staff Engineering!");

        // ==========================================
        // ALUR KETIGA: JIKA DI-APPROVE OLEH SPV (FINAL)
        // ==========================================
        } else if ($role === 'spv') {

            // JIKA tidak menggambar di canvas, ambil otomatis TTD dari profile SPV yang login
            if (!$signaturePath) {
                $signaturePath = $user ? $user->signature_path : $requestData->spv_signature;
            }

            $oldStamp = $requestData->spv_stamp;
            if ($oldStamp) {
                $oldStamp = str_replace(url('/'), '', $oldStamp);
                $oldStamp = ltrim($oldStamp, '/');
            }

            // Ubah status final menjadi 'Approved'
            $requestData->update([
                'status'         => 'Approved',
                'spv_name'       => $approverName,
                'spv_signature'  => $signaturePath, 
                'spv_stamp'      => $stampPath ? $stampPath : $oldStamp,             
                'approved_by'    => $approverName,
                'signature_path' => $signaturePath ? $signaturePath : $requestData->signature_path
            ]);

            // Sync update status terakhir pada tabel history
            $history = HistoryApproval::where('request_no', $requestData->request_no)
                                       ->where('status', 'Checked by Staff')
                                       ->latest()
                                       ->first();

            if ($history) {
                $history->update([
                    'status'        => 'Approved',
                    'spv_name'      => $approverName,
                    'spv_signature' => $signaturePath
                ]);
            }

            return redirect()->route('eng.approval')->with('success', "FULLY APPROVED! Dokumen Sparepart resmi disetujui SPV!");
        }
    }

    /**
     * Membatalkan / me-reject ajuan form request
     */
    public function reject(Request $request, $id)
    {
        $requestData = RequestProd::findOrFail($id);
        $approverName = Auth::check() ? Auth::user()->name : 'Engineering Staff';
        
        HistoryApproval::create([
            'request_no'     => $requestData->request_no,
            'sparepart_name' => $requestData->sparepart_name,
            'sap_code'       => $requestData->sap_code ?? '-',
            'qty_req'        => $requestData->qty_req,
            'line_machine'   => $requestData->line_machine,
            'requestor'      => $requestData->requestor ?? 'Production Staff',
            'approved_by'    => $approverName,
            'status'         => 'Rejected',
            'processed_at'   => now(),
        ]);

        $requestData->update([
            'status'        => 'Rejected',
            'reject_remark' => $request->input('reason', 'Ditolak oleh Engineering')
        ]);

        return redirect()->route('eng.approval')->with('success', "Request telah berhasil di-REJECT.");
    }
}