<?php

namespace App\Http\Controllers\Engineering;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;
use App\Models\ListSparepartEng; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PurchaseRequestEngController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::now();
        $datePrefix = $today->format('Y/m/d'); 
        
        // Pola pencarian disesuaikan dengan kode PR/
        $searchPattern = "PR/ENG/RFSP/" . $datePrefix . "/%";

        try {
            $lastPr = PurchaseRequestEng::where('no_pr', 'like', $searchPattern)
                ->orderBy('id', 'desc')
                ->first();

            if ($lastPr) {
                $lastNumber = (int) substr($lastPr->no_pr, -4);
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }
        } catch (\Exception $e) {
            $nextNumber = '0001';
        }

        $generatedPrCode = "PR/ENG/RFSP/" . $datePrefix . "/" . $nextNumber;

        try {
            $spareparts = ListSparepartEng::all(); 
        } catch (\Exception $e) {
            $spareparts = collect([]);
        }

        return view('stock_eng.purchase_request', compact('generatedPrCode', 'spareparts'));
    }

    public function store(Request $request)
    {
        // Validasi input form PR
        $request->validate([
            'no_pr'                 => 'required|unique:purchase_requests,no_pr', 
            'sparepart_id'          => ['required', Rule::exists(ListSparepartEng::class, 'id')], 
            'qty_pr'                => 'required|integer|min:1',
            'priority'              => 'required|in:normal,urgent',
            'request_date'          => 'required|date',
            'expected_arrival_date' => 'required|date|after_or_equal:request_date', 
            'destination'           => 'required|string',
            'remark'                => 'required|string', 
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sesi login Anda telah habis. Silakan login kembali.');
        }

        $userSignature = $user->signature_path ?? $user->signature ?? null;

        try {
            PurchaseRequestEng::create([
                'no_pr'                 => $request->no_pr,
                'user_id'               => $user->id, 
                'sparepart_id'          => $request->sparepart_id, 
                'qty_pr'                => $request->qty_pr,
                'priority'              => $request->priority,
                'request_date'          => $request->request_date,
                'expected_arrival_date' => $request->expected_arrival_date,
                'destination'           => $request->destination,
                'remark'                => $request->remark,
                'status'                => 'pending', 
                'prepared_signature'    => $userSignature, 
            ]);

            return redirect()->route('purchase.request.history')
                ->with('success', 'Purchase Request ' . $request->no_pr . ' berhasil diajukan!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memproses pengajuan! Error: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 🛠️ ALUR MEJA KERJA TIM 1: CHECKER & ADMIN (STATUS: PENDING -> CHECKED/REJECT)
    // =========================================================================

    /**
     * Menampilkan daftar PR yang masih berstatus 'pending' (baru dibuat oleh user).
     */
    public function listRequests(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10); 
        
        $requests = PurchaseRequestEng::with(['user', 'sparepart'])
            ->where('status', 'pending')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('no_pr', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($qUser) use ($search) {
                          $qUser->where('name', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%");
                      })
                      ->orWhereHas('sparepart', function ($qPart) use ($search) {
                          $qPart->where('sparepart_id', 'like', "%{$search}%")
                                ->orWhere('part_number', 'like', "%{$search}%")
                                ->orWhere('sap_code', 'like', "%{$search}%")
                                ->orWhere('category', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('stock_eng.purchase_request.listPurchaseRequest', compact('requests'));
    }

    /**
     * Menampilkan halaman Blade khusus peninjauan (Checked View)
     */
    public function checkedView($id)
    {
        $pr = PurchaseRequestEng::with(['user', 'sparepart'])->findOrFail($id);
        return view('stock_eng.purchase_request.purchase_request_checked', compact('pr'));
    }

    /**
     * Memproses verifikasi PR awal dari 'pending' menjadi 'checked'
     */
    public function checkRequest($id)
    {
        try {
            $pr = PurchaseRequestEng::findOrFail($id);
            $user = Auth::user();

            if (!$user) {
                return redirect()->back()->with('error', 'Sesi login habis.');
            }

            $checkerSignature = $user->signature_path ?? $user->signature ?? null;

            $pr->update([
                'status' => 'checked',
                'checked_signature' => $checkerSignature
            ]);

            return redirect()->route('purchase.request.list')->with('success', 'Purchase Request ' . $pr->no_pr . ' berhasil di-Check! Berkas diteruskan ke Costing.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memverifikasi dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Menolak berkas Purchase Request (Status berubah jadi 'rejected')
     */
    public function rejectRequest($id)
    {
        try {
            $pr = PurchaseRequestEng::findOrFail($id);
            
            $pr->update([
                'status' => 'rejected'
            ]);

            return redirect()->back()->with('success', 'Purchase Request ' . $pr->no_pr . ' telah ditolak (Rejected).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses penolakan: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // 🛠️ ALUR MEJA KERJA TIM 2: COSTING & MANAGER (STATUS: CHECKED -> APPROVED)
    // =========================================================================

    /**
     * Menampilkan daftar PR yang sudah lolos tahap satu (status 'checked') untuk di-approve akhir.
     */
    public function listCheckedRequests(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $requests = PurchaseRequestEng::with(['user', 'sparepart'])
            ->where('status', 'checked')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('no_pr', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($qUser) use ($search) {
                          $qUser->where('name', 'like', "%{$search}%")
                                ->orWhere('nik', 'like', "%{$search}%")
                                ->orWhere('nim', 'like', "%{$search}%");
                      })
                      ->orWhereHas('sparepart', function ($qPart) use ($search) {
                          $qPart->where('sparepart_id', 'like', "%{$search}%")
                                ->orWhere('part_number', 'like', "%{$search}%")
                                ->orWhere('sap_code', 'like', "%{$search}%")
                                ->orWhere('category', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        return view('stock_eng.purchase_request.listPurchaseRequestChecked', compact('requests'));
    }

    /**
     * Memberikan persetujuan akhir (Approved)
     */
    public function approveRequest($id)
    {
        try {
            $pr = PurchaseRequestEng::findOrFail($id);
            $user = Auth::user();

            if (!$user) {
                return redirect()->back()->with('error', 'Sesi login habis.');
            }

            $approverSignature = $user->signature_path ?? $user->signature ?? null;

            $pr->update([
                'status' => 'approved',
                'approved_signature' => $approverSignature
            ]);

            return redirect()->back()->with('success', 'Purchase Request ' . $pr->no_pr . ' resmi disetujui sepenuhnya (Approved)!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses approval akhir: ' . $e->getMessage());
        }
    }
}