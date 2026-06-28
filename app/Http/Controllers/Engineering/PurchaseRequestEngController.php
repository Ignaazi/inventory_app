<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseRequestEngController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::now();
        $datePrefix = $today->format('Y/m/d'); 
        $searchPattern = "RR/ENG/RFSP/%";

        $lastPr = null;
        try {
            $lastPr = PurchaseRequestEng::where('pr_code', 'like', $searchPattern)
                ->orderBy('id', 'desc')
                ->first();
        } catch (\Exception $e) {
            $lastPr = null;
        }

        if ($lastPr) {
            $lastNumber = (int) substr($lastPr->pr_code, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }
        $generatedPrCode = "RR/ENG/RFSP/" . $datePrefix . "/" . $nextNumber;

        try {
            $spareparts = DB::table('spareparts')->get(); 
        } catch (\Exception $e) {
            $spareparts = collect([]);
        }

        return view('stock_eng.purchase_request', compact('generatedPrCode', 'spareparts'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'pr_code'      => 'required',
            'product'      => 'required',
            'type_product' => 'required',
            'qty'          => 'required|integer|min:1',
            
            'priority'     => 'required|in:normal,urgent',
            'request_by'   => 'required',
            'request_date' => 'required',
            'destination'  => 'required',
            'notes'        => 'required',
        ]);
        $userLogin = Auth::user();
        $namaUser  = $userLogin ? $userLogin->name : 'muhammad ignazi';
        $nikUser   = $userLogin ? ($userLogin->nim ?? $userLogin->nik) : '20260001';

        try {
            $pr = PurchaseRequestEng::create([
                'pr_code'      => $request->pr_code,
                'name'         => $namaUser, 
                'nik'          => $nikUser,   
                'product'      => $request->product,
                'type_product' => $request->type_product,
                'qty'          => $request->qty,
                
                'priority'     => $request->priority,
                'request_by'   => $request->request_by,
                'request_date' => $request->request_date,
                'destination'  => $request->destination,
                'notes'        => $request->notes,
                'status'       => 'waiting approval',
            ]);
            if ($pr->status !== 'waiting approval') {
                $pr->status = 'waiting approval';
                $pr->saveQuietly();
            }
            return redirect()->route('purchase.request.history')->with('success', 'Purchase Request ' . $request->pr_code . ' berhasil diajukan dan saat ini berstatus Menunggu Persetujuan (Waiting Approval)!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses pengajuan! Error: ' . $e->getMessage());
        }
    }
}