<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Production\RequestProd; 
use App\Models\Engineering\HistoryApproval; 
use App\Models\ListSparepartEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class ApprovalEngController extends Controller
{
    /**
     * Menampilkan daftar request dari Production yang butuh approval Engineering.
     */
    public function index(Request $request)
    {
        // Eager loading relasi sesuai DB baru
        $query = RequestProd::with(['user', 'sparepart', 'lineProduction'])
            ->whereIn('status', ['Pending', 'Checked by Staff']);

        // Live search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('request_no', 'LIKE', "%{$search}%")
                  ->orWhere('status', 'LIKE', "%{$search}%")
                  ->orWhere('remark', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%")->orWhere('nik', 'LIKE', "%{$search}%");
                  })
                  ->orWhere('sparepart_id', 'LIKE', "%{$search}%"); 
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
        // Eager load data user untuk memastikan signature_path pembuat request terambil sempurna
        $req = RequestProd::with(['user', 'sparepart', 'lineProduction'])->findOrFail($id);
        
        // Inject fallback langsung ke object model jika production_signature kosong tapi user memilikinya
        if (empty($req->production_signature) && $req->user && !empty($req->user->signature_path)) {
            $req->production_signature = $req->user->signature_path;
        }

        return view('stock_eng.process_req.approveform', compact('req'));
    }

    /**
     * Memproses logika Tanda Tangan Berjenjang (Staff -> Admin) sesuai DB Baru
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'signature_image' => 'nullable|string',
            'stamp_image'     => 'nullable|string',
            'signer_role'     => 'required|string|in:staff,admin' 
        ]);

        $requestData = RequestProd::with(['user', 'sparepart', 'lineProduction'])->findOrFail($id);
        $role = $request->input('signer_role');
        $user = Auth::user();
        
        $signaturePath = null;
        $stampPath = null;

        // 1. PROSES INPUT DATA TANDA TANGAN (Base64 vs String Path)
        if ($request->filled('signature_image')) {
            if (str_starts_with($request->signature_image, 'data:image')) {
                // Jika user menggambar manual di canvas (Base64)
                $image_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_image);
                $fileName = 'sig_' . $role . '_' . str_replace('/', '-', $requestData->request_no) . '_' . time() . '.png';
                $folderPath = public_path('uploads/signatures');
                if (!file_exists($folderPath)) mkdir($folderPath, 0777, true);
                file_put_contents($folderPath . '/' . $fileName, base64_decode($image_data));
                $signaturePath = 'uploads/signatures/' . $fileName;
            } else {
                // Jika menerima string path asli dari profile user yang dikirim Blade
                $signaturePath = $request->signature_image;
            }
        }

        // 2. PROSES INPUT CANVAS CAP/STAMP
        if ($request->filled('stamp_image') && !str_starts_with($request->stamp_image, 'http')) {
            $stamp_data = preg_replace('/^data:image\/\w+;base64,/', '', $request->stamp_image);
            $stamp_data = str_replace(' ', '+', $stamp_data);
            $stampName = 'stamp_' . $role . '_' . str_replace('/', '-', $requestData->request_no) . '_' . time() . '.png';
            $stampFolderPath = public_path('uploads/stamps');
            if (!file_exists($stampFolderPath)) mkdir($stampFolderPath, 0777, true);
            file_put_contents($stampFolderPath . '/' . $stampName, base64_decode($stamp_data));
            $stampPath = 'uploads/stamps/' . $stampName;
        }

        // Penentuan data teks pendukung Log History
        $approverName = $user ? $user->name : (($role === 'staff') ? 'Engineering Staff' : 'Admin Engineering');
        $requestorName = optional($requestData->user)->name ?? 'Production Staff';
        $sparepartName = $requestData->sparepart_id ?? '-'; 
        
        $lineNo = optional($requestData->lineProduction)->no_line ?? '-';
        $machineName = optional($requestData->lineProduction)->name_machine ?? '-';
        $lineMachineText = $lineNo . ' - ' . $machineName;

        // ==========================================
        // ALUR PERTAMA: JIKA DI-APPROVE OLEH STAFF
        // ==========================================
        if ($role === 'staff') {
            
            // Amankan data TTD Engineering Staff yang lama jika ada di DB
            $oldSignature = $requestData->engineering_signature;
            if ($oldSignature) {
                $oldSignature = str_replace(url('/'), '', $oldSignature);
                $oldSignature = ltrim($oldSignature, '/');
            }

            // Amankan data Stamp Staff yang lama
            $oldStamp = $requestData->staff_stamp;
            if ($oldStamp) {
                $oldStamp = str_replace(url('/'), '', $oldStamp);
                $oldStamp = ltrim($oldStamp, '/');
            }

            // Jika signaturePath kosong, coba ambil fallback dari profile user, kalau zonk pakai yang lama
            if (!$signaturePath) {
                $signaturePath = $user && $user->signature_path ? $user->signature_path : $oldSignature;
            }

            // Update status dan kolom sesuai DB Terbaru (engineering_signature)
            $requestData->update([
                'status'                => 'Checked by Staff',
                'staff_name'            => $approverName,
                'engineering_signature' => $signaturePath ? $signaturePath : $oldSignature, 
                'staff_stamp'           => $stampPath ? $stampPath : $oldStamp               
            ]);

            // Buat log data ke history (Lengkap dengan sparepart_id)
            HistoryApproval::create([
                'request_no'      => $requestData->request_no,
                'sparepart_id'    => $requestData->sparepart_id, 
                'sparepart_name'  => $sparepartName, 
                'sap_code'        => '-', 
                'qty_req'         => $requestData->qty_req,
                'line_machine'    => $lineMachineText,
                'requestor'       => $requestorName, 
                'approved_by'     => $approverName, 
                'staff_signature' => $signaturePath,
                'status'          => 'Checked by Staff',
                'processed_at'    => now(),
            ]);

            return redirect()->route('eng.approval')->with('success', "Form Request berhasil diverifikasi oleh Staff Engineering!");

        // ==========================================
        // ALUR KEDUA: JIKA DI-APPROVE OLEH ADMIN (FINAL)
        // ==========================================
        } else if ($role === 'admin') {

            // Amankan data TTD Admin/SPV yang lama di DB
            $oldSignature = $requestData->spv_signature;
            if ($oldSignature) {
                $oldSignature = str_replace(url('/'), '', $oldSignature);
                $oldSignature = ltrim($oldSignature, '/');
            }

            // Amankan data Stamp Admin yang lama
            $oldStamp = $requestData->spv_stamp;
            if ($oldStamp) {
                $oldStamp = str_replace(url('/'), '', $oldStamp);
                $oldStamp = ltrim($oldStamp, '/');
            }

            // Jika signaturePath kosong, coba ambil fallback dari profile admin, kalau zonk pakai yang lama
            if (!$signaturePath) {
                $signaturePath = $user && $user->signature_path ? $user->signature_path : $oldSignature;
            }

            // Update status ke FINAL APPROVED
            $requestData->update([
                'status'         => 'Approved',
                'spv_name'       => $approverName,
                'spv_signature'  => $signaturePath ? $signaturePath : $oldSignature, 
                'spv_stamp'      => $stampPath ? $stampPath : $oldStamp,             
                'approved_by'    => $approverName,
            ]);

            // Perbarui data history tahap Staff sebelumnya menjadi Approved secara berkala
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
        
        $requestorName = optional($requestData->user)->name ?? 'Production Staff';
        $sparepartName = $requestData->sparepart_id ?? '-';
        
        $lineNo = optional($requestData->lineProduction)->no_line ?? '-';
        $machineName = optional($requestData->lineProduction)->name_machine ?? '-';
        $lineMachineText = $lineNo . ' - ' . $machineName;

        // Ditambahkan sparepart_id agar log reject tidak memicu error database
        HistoryApproval::create([
            'request_no'     => $requestData->request_no,
            'sparepart_id'   => $requestData->sparepart_id,
            'sparepart_name' => $sparepartName,
            'sap_code'       => '-',
            'qty_req'        => $requestData->qty_req,
            'line_machine'   => $lineMachineText,
            'requestor'      => $requestorName,
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