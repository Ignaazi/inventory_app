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
     */
    public function index(Request $request)
    {
        $query = RequestProd::with(['user', 'sparepart', 'lineProduction'])
            ->whereIn('status', ['Pending', 'Checked by Staff']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('request_no', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('remark', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%")->orWhere('nik', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('sparepart', function($s) use ($search) {
                      $s->where('sparepart_id', 'LIKE', "%{$search}%");
                  });
            });
        }

        $requests = $query->orderBy('created_at', 'asc')->paginate(10);

        return view('stock_eng.process_req.approval', compact('requests'));
    }

    /**
     * Membuka form review/detail approval request.
     */
    public function review($id)
    {
        $req = RequestProd::with(['user', 'sparepart', 'lineProduction'])->findOrFail($id);
        
        if (empty($req->production_signature) && $req->user && !empty($req->user->signature_path)) {
            $req->production_signature = $req->user->signature_path;
        }

        return view('stock_eng.process_req.approveform', compact('req'));
    }

    /**
     * Memproses logika Tanda Tangan Berjenjang (Staff -> Admin)
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'signature_image' => 'nullable|string',
            'signer_role'     => 'required|string|in:staff,admin' 
        ]);

        // Eager load relasi sparepart agar namanya bisa diambil untuk history
        $requestData = RequestProd::with(['user', 'sparepart', 'lineProduction'])->findOrFail($id);
        $role = $request->input('signer_role');
        $user = Auth::user();
        
        $signaturePath = null;

        if ($request->filled('signature_image')) {
            if (str_starts_with($request->signature_image, 'data:image')) {
                $image_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_image);
                $fileName = 'sig_' . $role . '_' . str_replace('/', '-', $requestData->request_no) . '_' . time() . '.png';
                $folderPath = public_path('uploads/signatures');
                if (!file_exists($folderPath)) mkdir($folderPath, 0777, true);
                file_put_contents($folderPath . '/' . $fileName, base64_decode($image_data));
                $signaturePath = 'uploads/signatures/' . $fileName;
            } else {
                $signaturePath = $request->signature_image;
            }
        }

        $approverName = $user ? $user->name : (($role === 'staff') ? 'Engineering Staff' : 'Admin Engineering');
        
        // Ambil identifier sparepart dari tabel master lewat relasi model lu
        $sparepartName = $requestData->sparepart 
            ? ($requestData->sparepart->sparepart_id ?? $requestData->sparepart->id ?? '-') 
            : '-'; 
        
        $lineNo = optional($requestData->lineProduction)->no_line ?? '-';
        $machineName = optional($requestData->lineProduction)->name_machine ?? '-';
        $lineMachineText = $lineNo . ' - ' . $machineName;

        // ==========================================
        // ALUR PERTAMA: JIKA DI-APPROVE OLEH STAFF
        // ==========================================
        if ($role === 'staff') {
            
            $oldSignature = $requestData->engineering_signature;
            if ($oldSignature) {
                $oldSignature = str_replace(url('/'), '', $oldSignature);
                $oldSignature = ltrim($oldSignature, '/');
            }

            if (!$signaturePath) {
                $signaturePath = $user && $user->signature_path ? $user->signature_path : $oldSignature;
            }

            // Update hanya kolom yang ada di fillable RequestProd lu
            $requestData->update([
                'status'                => 'Checked by Staff',
                'engineering_signature' => $signaturePath ? $signaturePath : $oldSignature, 
            ]);

            // Simpan log lengkap ke tabel history flat
            HistoryApproval::create([
                'production_request_id' => $requestData->id,
                'request_no'            => $requestData->request_no,
                'nik'                   => optional($requestData->user)->nik ?? '-', 
                'approver_name'         => $approverName,
                'sparepart_name'        => $sparepartName, 
                'qty_req'               => $requestData->qty_req,
                'line_machine'          => $lineMachineText,
                'status'                => 'Checked by Staff',
                'remark'                => $requestData->remark,
                'processed_at'          => now(),
            ]);

            return redirect()->route('eng.approval')->with('success', "Form Request berhasil diverifikasi oleh Staff Engineering!");

        // ==========================================
        // ALUR KEDUA: JIKA DI-APPROVE OLEH ADMIN (FINAL)
        // ==========================================
        } else if ($role === 'admin') {

            $oldSignature = $requestData->spv_signature;
            if ($oldSignature) {
                $oldSignature = str_replace(url('/'), '', $oldSignature);
                $oldSignature = ltrim($oldSignature, '/');
            }

            if (!$signaturePath) {
                $signaturePath = $user && $user->signature_path ? $user->signature_path : $oldSignature;
            }

            // Update hanya kolom spv_signature & status sesuai fillable RequestProd lu
            $requestData->update([
                'status'        => 'Approved',
                'spv_signature' => $signaturePath ? $signaturePath : $oldSignature, 
            ]);

            $history = HistoryApproval::where('request_no', $requestData->request_no)
                                       ->where('status', 'Checked by Staff')
                                       ->latest()
                                       ->first();

            if ($history) {
                $history->update([
                    'status'        => 'Approved',
                    'approver_name' => $approverName,
                    'processed_at'  => now(),
                ]);
            } else {
                HistoryApproval::create([
                    'production_request_id' => $requestData->id,
                    'request_no'            => $requestData->request_no,
                    'nik'                   => optional($requestData->user)->nik ?? '-',
                    'approver_name'         => $approverName,
                    'sparepart_name'        => $sparepartName,
                    'qty_req'               => $requestData->qty_req,
                    'line_machine'          => $lineMachineText,
                    'status'                => 'Approved',
                    'remark'                => $requestData->remark,
                    'processed_at'          => now(),
                ]);
            }

            return redirect()->route('eng.approval')->with('success', "FULLY APPROVED! Dokumen Sparepart resmi disetujui Admin Engineering!");
        }
    }

    /**
     * Membatalkan / me-reject ajuan form request
     */
    public function reject(Request $request, $id)
    {
        $requestData = RequestProd::with(['user', 'sparepart', 'lineProduction'])->findOrFail($id);
        $approverName = Auth::check() ? Auth::user()->name : 'Engineering Reviewer';
        
        $sparepartName = $requestData->sparepart 
            ? ($requestData->sparepart->sparepart_id ?? $requestData->sparepart->id ?? '-') 
            : '-';

        $lineNo = optional($requestData->lineProduction)->no_line ?? '-';
        $machineName = optional($requestData->lineProduction)->name_machine ?? '-';
        $lineMachineText = $lineNo . ' - ' . $machineName;
        $reason = $request->input('reason', 'Ditolak oleh Engineering');

        HistoryApproval::create([
            'production_request_id' => $requestData->id,
            'request_no'            => $requestData->request_no,
            'nik'                   => optional($requestData->user)->nik ?? '-',
            'approver_name'         => $approverName,
            'sparepart_name'        => $sparepartName,
            'qty_req'               => $requestData->qty_req,
            'line_machine'          => $lineMachineText,
            'status'                => 'Rejected',
            'remark'                => $reason,
            'processed_at'          => now(),
        ]);

        // Sesuai dengan isi fillable model RequestProd lu
        $requestData->update([
            'status'        => 'Rejected',
            'reject_remark' => $reason
        ]);

        return redirect()->route('eng.approval')->with('success', "Request telah berhasil di-REJECT.");
    }
}