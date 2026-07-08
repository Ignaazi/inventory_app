<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use App\Models\CoatingMaterialReceiving; 
use App\Models\Engineering\PurchaseRequestEng;     
use App\Models\Costing\MaterialReceivedSignature;   
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    
use Carbon\Carbon;

class MaterialReceivedController extends Controller
{
    public function index()
    {
        $signatures = MaterialReceivedSignature::orderBy('created_at', 'desc')->paginate(10);
        return view('cost_section.material_received_list', compact('signatures'));
    }

    public function create()
    {
        $availablePRs = PurchaseRequestEng::whereIn('status', ['approved', 'done'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('cost_section.material_received', compact('availablePRs'));
    }

    public function show($id)
    {
        $signature = MaterialReceivedSignature::findOrFail($id);
        return view('cost_section.material_received_preview', compact('signature'));
    }

    /**
     * 📝 SUBMIT STEP 1: Costing Staff Create Form
     */
    public function storeCostingSignature(Request $request)
    {
        $request->validate([
            'pr_code'        => 'required|string',
            'qty_received'   => 'required|numeric|min:1',
            'lot_no'         => 'required|string',
            'signature_data' => 'required|string', 
            'costing_notes'  => 'nullable|string'
        ]);

        $signaturePath = null;
        if ($request->filled('signature_data') && str_contains($request->signature_data, 'base64')) {
            $image_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_data);
            $fileName = 'sig_costing_' . str_replace('/', '-', $request->pr_code) . '_' . time() . '.png';
            $folderPath = public_path('uploads/signatures');
            
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            
            file_put_contents($folderPath . '/' . $fileName, base64_decode($image_data));
            $signaturePath = 'uploads/signatures/' . $fileName; 
        }

        MaterialReceivedSignature::create([
            'pr_code'                => $request->pr_code,
            'qty_received'           => $request->qty_received,
            'lot_no'                 => $request->lot_no,
            'costing_staff_nik'      => Auth::user()->nik ?? 'N/A',
            'costing_staff_name'     => Auth::user()->name,
            'costing_signed_at'      => Carbon::now(),
            'costing_signature_path' => $signaturePath, // 🌟 Tersimpan Aktual ke DB
            'signature_status'       => 'incoming', 
            'costing_notes'          => $request->costing_notes,
        ]);

        return redirect()->route('costing.material.list')->with('success', 'Form Material Received berhasil dibuat!');
    }

    /**
     * 📝 SUBMIT STEP 2: Staff Engineering Sign Check
     */
    public function signEngineeringStaff(Request $request, $id)
    {
        $signature = MaterialReceivedSignature::findOrFail($id);

        if ($request->action === 'reject') {
            $signature->update([
                'signature_status'  => 'rejected',
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
            $fileName = 'sig_eng_staff_' . str_replace('/', '-', $signature->pr_code) . '_' . time() . '.png';
            $folderPath = public_path('uploads/signatures');
            
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            
            file_put_contents($folderPath . '/' . $fileName, base64_decode($image_data));
            $signaturePath = 'uploads/signatures/' . $fileName; 
        }

        $signature->update([
            'engineering_staff_nik'      => Auth::user()->nik ?? 'N/A',
            'engineering_staff_name'     => Auth::user()->name,
            'engineering_signed_at'      => Carbon::now(),
            'engineering_signature_path' => $signaturePath, // 🌟 Tersimpan Aktual ke DB
            'signature_status'           => 'pending_approval', 
            'engineering_notes'          => $request->engineering_notes,
        ]);

        return redirect()->back()->with('success', 'Verifikasi staff berhasil disubmit!');
    }

    /**
     * 📝 SUBMIT STEP 3: Supervisor Engineering Full Approval
     */
    public function approveEngineeringSpv(Request $request, $id)
    {
        $signature = MaterialReceivedSignature::findOrFail($id);

        $request->validate([
            'signature_data' => 'required|string'
        ]);

        $signaturePath = null;
        if ($request->filled('signature_data') && str_contains($request->signature_data, 'base64')) {
            $image_data = str_replace(['data:image/png;base64,', ' '], ['', '+'], $request->signature_data);
            $fileName = 'sig_eng_spv_' . str_replace('/', '-', $signature->pr_code) . '_' . time() . '.png';
            $folderPath = public_path('uploads/signatures');
            
            if (!file_exists($folderPath)) {
                mkdir($folderPath, 0777, true);
            }
            
            file_put_contents($folderPath . '/' . $fileName, base64_decode($image_data));
            $signaturePath = 'uploads/signatures/' . $fileName; 
        }

        $signature->update([
            'engineering_spv_nik'            => Auth::user()->nik ?? 'N/A',
            'engineering_spv_name'           => Auth::user()->name,
            'engineering_spv_signed_at'      => Carbon::now(),
            'engineering_spv_signature_path' => $signaturePath, // 🌟 Tersimpan Aktual ke DB
            'signature_status'               => 'completed', 
        ]);

        PurchaseRequestEng::where('pr_code', $signature->pr_code)->update(['status' => 'done']);

        return redirect()->back()->with('success', 'Form Penerimaan Selesai Disetujui Sepenuhnya!');
    }

    /**
     * 🗑️ ACTION METHOD: Delete Data & Hapus Berkas Gambar TTD Fisik dari Server
     */
    public function destroy($id)
    {
        $signature = MaterialReceivedSignature::findOrFail($id);

        // Kumpulkan semua path gambar ttd yang berpotensi tersimpan
        $paths = [
            $signature->costing_signature_path,
            $signature->engineering_signature_path,
            $signature->engineering_spv_signature_path
        ];

        // Hapus file gambar fisik (.png) jika filenya ada di server
        foreach ($paths as $path) {
            if ($path && file_exists(public_path($path))) {
                @unlink(public_path($path));
            }
        }

        // Hapus record data dari DB
        $signature->delete();

        return redirect()->route('costing.material.list')->with('success', 'Data Material Received dan berkas file tanda tangan terkait berhasil dihapus dari sistem!');
    }

    public function coatingIndex() { /* ... kode coating bawaanmu ... */ }
    public function store(Request $request) { /* ... kode coating bawaanmu ... */ }
}