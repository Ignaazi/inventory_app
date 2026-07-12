<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;     
use App\Models\Engineering\EngMaterialReceiving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Support\Facades\File;

class EngMaterialReceivingController extends Controller
{
    /**
     * 📊 1. HALAMAN LIST DATA UTAMA ENGINEERING
     */
    public function index()
    {
        // Menampilkan data yang berstatus dari Costing (submitted_by_costing) atau yang baru di-check Staff (approved_by_spv / Checked by Staff)
        $receivings = EngMaterialReceiving::orderBy('created_at', 'desc')->paginate(10);
        return view('stock_eng.material_received.eng_list_material_received', compact('receivings'));
    }

    /**
     * 📝 2. HALAMAN FORM CONFIRM / PROSES DATA (Membawa Data Awal Costing)
     */
    public function create(Request $request)
    {
        $id = $request->query('id') ?? $request->id;
        $receiving = EngMaterialReceiving::findOrFail($id);

        return view('stock_eng.material_received.eng_confirm_material_received', compact('receiving'));
    }

    /**
     * 👁️ 3. HALAMAN PREVIEW DOKUMEN CETAK (Aman & Sinkron memakai $receiving)
     */
    public function show($id)
    {
        $receiving = EngMaterialReceiving::findOrFail($id);
        return view('stock_eng.material_received.eng_preview_material_received', compact('receiving'));
    }

    /**
     * ✍️ 4. PROSES STORE UPDATE TTD LANJUTAN BERDASARKAN SIGNER_ROLE (SIMULASI)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id'             => 'required|integer',
            'action'         => 'required|string|in:confirm,reject',
            'signature_data' => 'required_if:action,confirm|string|nullable',
            'signer_role'    => 'required|string|in:staff,spv',
            'notes'          => 'nullable|string'
        ]);

        $receiving = EngMaterialReceiving::findOrFail($request->id);
        $role = $request->input('signer_role');

        // JIKA ACTION REJECT
        if ($request->action === 'reject') {
            $receiving->update([
                'signature_status'  => 'rejected',
                'status'            => 'rejected',
                'engineering_notes' => $request->notes ?? 'Rejected by Engineering'
            ]);
            return redirect()->route('eng.material.receiving.index')->with('error', 'Material Received berhasil di-reject.');
        }

        // PROSES PACKING BASE64 CANVAS TTD MENJADI FILE FISIK .PNG
        $signaturePath = null;
        if ($request->filled('signature_data') && str_contains($request->signature_data, 'base64')) {
            $image_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_data);
            
            // Nama file dinamis berdasar signer_role (sig_staff_... atau sig_spv_...)
            $fileName = 'sig_' . $role . '_' . str_replace('/', '-', $receiving->pr_code) . '_' . time() . '.png';
            $folderPath = public_path('storage/signatures/eng_materials/');
            
            if (!File::exists($folderPath)) { 
                File::makeDirectory($folderPath, 0755, true, true); 
            }
            File::put($folderPath . $fileName, base64_decode($image_data));
            $signaturePath = 'storage/signatures/eng_materials/' . $fileName; 
        }

        $approverName = Auth::check() ? Auth::user()->name : (($role === 'staff') ? 'Engineering Staff Admin' : 'Engineering SPV Admin');

        // PEMBAGIAN LOGIC BERDASARKAN SIMULASI ROLE YANG DIKIRIM FORM
        if ($role === 'staff') {
            // Jalur TTD Kedua (Staff)
            $oldSignature = $receiving->engineering_signature_path ?? $receiving->eng_signature_path;

            $receiving->update([
                'status'                     => 'approved_by_spv', // Naik level ke SPV
                'signature_status'           => 'approved_by_spv',
                'engineering_staff_name'     => $approverName,
                'engineering_signature_path' => $signaturePath ? $signaturePath : $oldSignature,
                'eng_signature_path'         => $signaturePath ? $signaturePath : $oldSignature,
                'engineering_notes'          => $request->notes,
            ]);

            return redirect()->route('eng.material.receiving.index')->with('success', 'Material Received Berhasil di-Check oleh Staff!');

        } else if ($role === 'spv') {
            // Jalur TTD Ketiga / Final Level (SPV)
            $oldSignature = $receiving->engineering_spv_signature_path ?? $receiving->eng_spv_signature_path;

            $receiving->update([
                'status'                         => 'completed', // Selesai Full Workflow
                'signature_status'               => 'completed',
                'engineering_spv_name'           => $approverName,
                'engineering_spv_signature_path' => $signaturePath ? $signaturePath : $oldSignature,
                'eng_spv_signature_path'         => $signaturePath ? $signaturePath : $oldSignature,
                'engineering_notes'              => $request->notes,
            ]);

            // Otomatis kunci status Purchase Request utama milik Engineering menjadi Done
            PurchaseRequestEng::where('pr_code', $receiving->pr_code)->update(['status' => 'done']);

            return redirect()->route('eng.material.receiving.index')->with('success', 'Laporan FULLY APPROVED oleh Supervisor!');
        }
    }
}