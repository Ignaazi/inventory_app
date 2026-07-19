<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;     
use App\Models\Engineering\EngMaterialReceiving; // Mengarah ke tabel tunggal
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MaterialReceivedController extends Controller
{
    /**
     * 📊 1. HALAMAN LIST DATA / MONITORING HISTORY (Sisi Costing)
     */
    public function index()
    {
        $signatures = EngMaterialReceiving::orderBy('created_at', 'desc')->paginate(10);
        return view('cost_section.material_received_list', compact('signatures'));
    }

    /**
     * 📝 2. HALAMAN FORM INPUT BARU
     */
    public function create()
    {
        // 🌟 FIX: Ambil semua pr_code yang SUDAH PERNAH digunakan (kecuali yang di-reject)
        $usedPRCodes = EngMaterialReceiving::where('status', '!=', 'rejected')
            ->pluck('pr_code')
            ->toArray();

        // 🌟 FIX: Tampilkan PR approved/done yang BELUM PERNAH dipakai sama sekali
        $availablePRs = PurchaseRequestEng::whereIn('status', ['approved', 'done'])
            ->whereNotIn('pr_code', $usedPRCodes)
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('cost_section.material_received', compact('availablePRs'));
    }

    /**
     * 👁️ 3. HALAMAN PREVIEW DOKUMEN CETAK (Klik Mata)
     */
    public function show($id)
    {
        $signature = EngMaterialReceiving::findOrFail($id);
        return view('cost_section.material_received_preview', compact('signature'));
    }

    /**
     * ✍️ 4. SUBMIT STEP 1: Costing Staff Create Form & Simpan TTD Awal
     */
    public function storeCostingSignature(Request $request)
    {
        $request->validate([
            'pr_code'        => 'required|string',
            'qty_received'   => 'required|numeric|min:1',
            'lot_no'         => 'required|string',
            'signature_data' => 'required|string', 
            'stamp_data'     => 'nullable|string',
            'costing_notes'  => 'nullable|string'
        ]);

        // 🌟 FIX VALIDASI LOCK: Cek double input di database untuk pr_code aktif
        $isAlreadyUsed = EngMaterialReceiving::where('pr_code', $request->pr_code)
            ->where('status', '!=', 'rejected')
            ->exists();

        if ($isAlreadyUsed) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal! Kode Purchase Request (PR) ini sudah pernah diproses sebelumnya.');
        }

        // A. Simpan TTD Costing
        $signaturePath = null;
        if ($request->filled('signature_data') && str_contains($request->signature_data, 'base64')) {
            $image_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_data);
            $fileName = 'sig_costing_' . str_replace('/', '-', $request->pr_code) . '_' . time() . '.png';
            $folderPath = public_path('storage/signatures/costing/');
            
            if (!File::exists($folderPath)) { File::makeDirectory($folderPath, 0755, true, true); }
            File::put($folderPath . $fileName, base64_decode($image_data));
            $signaturePath = 'storage/signatures/costing/' . $fileName; 
        }

        // B. Simpan Stempel Costing (Jika Ada)
        $stampPath = null;
        if ($request->filled('stamp_data') && str_contains($request->stamp_data, 'base64')) {
            $stamp_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->stamp_data);
            $stampName = 'stamp_costing_' . str_replace('/', '-', $request->pr_code) . '_' . time() . '.png';
            $stampFolderPath = public_path('storage/stamps/costing/');
            
            if (!File::exists($stampFolderPath)) { File::makeDirectory($stampFolderPath, 0755, true, true); }
            File::put($stampFolderPath . $stampName, base64_decode($stamp_data));
            $stampPath = 'storage/stamps/costing/' . $stampName;
        }

        // C. Buat Data Baru Langsung di Tabel Utama Engineering
        $receiving = new EngMaterialReceiving();
        $receiving->receiving_code = 'EMR-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        $receiving->pr_code = $request->pr_code;
        $receiving->qty_received = $request->qty_received;
        $receiving->lot_no = $request->lot_no;
        
        $receiving->created_by_nik = Auth::user()->nik ?? 'ADMIN-SIMULASI';
        $receiving->created_by_name = Auth::user()->name;
        
        $receiving->costing_notes = $request->costing_notes;
        $receiving->costing_signature_path = $signaturePath;
        $receiving->costing_stamp_path = $stampPath;
        
        $receiving->signature_status = 'submitted_by_costing'; 
        $receiving->status = 'submitted_by_costing'; 
        $receiving->save();

        return redirect()->route('costing.material.list')->with('success', 'Form Material Received berhasil diteruskan ke Engineering!');
    }

    /**
     * ✍️ 5. SUBMIT STEP 2: Staff Engineering Sign Check (Meneruskan TTD Kedua)
     */
    public function signEngineeringStaff(Request $request, $id)
    {
        $receiving = EngMaterialReceiving::findOrFail($id);

        if ($request->action === 'reject') {
            $receiving->update([
                'signature_status' => 'rejected',
                'status'           => 'rejected',
                'engineering_notes' => $request->engineering_notes ?? 'Rejected by Engineering Staff'
            ]);
            return redirect()->back()->with('error', 'Material Received berhasil di-reject.');
        }

        $request->validate([
            'signature_data' => 'required|string'
        ]);

        $signaturePath = null;
        if ($request->filled('signature_data') && str_contains($request->signature_data, 'base64')) {
            $image_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_data);
            $fileName = 'sig_eng_staff_' . str_replace('/', '-', $receiving->pr_code) . '_' . time() . '.png';
            $folderPath = public_path('storage/signatures/eng_materials/');
            
            if (!File::exists($folderPath)) { File::makeDirectory($folderPath, 0755, true, true); }
            File::put($folderPath . $fileName, base64_decode($image_data));
            $signaturePath = 'storage/signatures/eng_materials/' . $fileName; 
        }

        $receiving->update([
            'engineering_signature_path' => $signaturePath,
            'eng_signature_path'         => $signaturePath,
            'signature_status'           => 'approved_by_spv', 
            'status'                     => 'approved_by_spv',
            'engineering_notes'          => $request->engineering_notes,
        ]);

        return redirect()->back()->with('success', 'Verifikasi staff berhasil disubmit!');
    }

    /**
     * ✍️ 6. SUBMIT STEP 3: Supervisor Engineering Full Approval (TTD Ketiga)
     */
    public function approveEngineeringSpv(Request $request, $id)
    {
        $receiving = EngMaterialReceiving::findOrFail($id);

        $request->validate([
            'signature_data' => 'required|string'
        ]);

        $signaturePath = null;
        if ($request->filled('signature_data') && str_contains($request->signature_data, 'base64')) {
            $image_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_data);
            $fileName = 'sig_eng_spv_' . str_replace('/', '-', $receiving->pr_code) . '_' . time() . '.png';
            $folderPath = public_path('storage/signatures/eng_materials/');
            
            if (!File::exists($folderPath)) { File::makeDirectory($folderPath, 0755, true, true); }
            File::put($folderPath . $fileName, base64_decode($image_data));
            $signaturePath = 'storage/signatures/eng_materials/' . $fileName; 
        }

        $receiving->update([
            'engineering_spv_signature_path' => $signaturePath,
            'eng_spv_signature_path'         => $signaturePath,
            'signature_status'               => 'completed', 
            'status'                         => 'completed',
        ]);

        PurchaseRequestEng::where('pr_code', $receiving->pr_code)->update(['status' => 'done']);

        return redirect()->back()->with('success', 'Form Penerimaan Selesai Disetujui Sepenuhnya!');
    }

    /**
     * 🗑️ 7. ACTION METHOD: Delete Data & File Fisik dari Storage
     */
    public function destroy($id)
    {
        $receiving = EngMaterialReceiving::findOrFail($id);

        $paths = [
            $receiving->costing_signature_path,
            $receiving->costing_stamp_path,
            $receiving->engineering_signature_path,
            $receiving->eng_signature_path,
            $receiving->engineering_spv_signature_path,
            $receiving->eng_spv_signature_path
        ];

        foreach ($paths as $path) {
            if ($path && File::exists(public_path($path))) {
                File::delete(public_path($path));
            }
        }

        $receiving->delete();

        return redirect()->route('costing.material.list')->with('success', 'Data Material Received & file berkas ttd berhasil dibersihkan.');
    }
}