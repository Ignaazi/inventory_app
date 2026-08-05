<?php

namespace App\Http\Controllers\Costing;

use App\Http\Controllers\Controller;
use App\Models\Engineering\PurchaseRequestEng;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf; // Package dompdf untuk render Blade ke PDF

class ApprovalController extends Controller
{
    /**
     * Menampilkan daftar Purchase Request yang berstatus 'checked'
     * (Menunggu persetujuan akhir dari pihak Costing)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Costing hanya memproses data yang statusnya sudah 'checked' oleh Engineering
        $query = PurchaseRequestEng::with(['user', 'sparepart'])
                                   ->where('status', 'checked');

        // Fitur pencarian tembus ke tabel relasi
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_pr', 'LIKE', "%{$search}%")
                  ->orWhere('destination', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('nik', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('sparepart', function($partQuery) use ($search) {
                      $partQuery->where('sparepart_id', 'LIKE', "%{$search}%")
                                ->orWhere('part_number', 'LIKE', "%{$search}%")
                                ->orWhere('sap_code', 'LIKE', "%{$search}%")
                                ->orWhere('category', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan skala prioritas URGENT dulu, lalu tanggal dibuat terbaru
        $pendingPr = $query->orderByRaw("FIELD(priority, 'urgent', 'normal') ASC")
                           ->orderBy('created_at', 'DESC')
                           ->paginate($perPage);

        return view('cost_section.approval_pr', compact('pendingPr', 'search'));
    }

    /**
     * Menampilkan Form Halaman Baru untuk Approval Costing
     * (Membuka views/cost_section/approve_PrForm.blade.php)
     */
    public function approveForm($id)
    {
        // Eager load user dan sparepart agar data di form baru lengkap
        $pr = PurchaseRequestEng::with(['user', 'sparepart'])->findOrFail($id);

        return view('cost_section.approve_PrForm', compact('pr'));
    }

    /**
     * Mengubah status Purchase Request menjadi 'approved'
     * Menyimpan tanda tangan digital & email tujuan ke database, lalu mengirim notifikasi email + PDF Attachment
     */
    public function approve(Request $request, $id)
    {
        $pr = PurchaseRequestEng::with(['user', 'sparepart'])->findOrFail($id);
        $user = Auth::user();

        if (!$user) {
            return redirect()->back()->with('error', 'Sesi login Anda telah habis.');
        }

        // 1. Validasi Input Email Tujuan
        $request->validate([
            'notification_email' => 'required|email',
        ], [
            'notification_email.required' => 'Email tujuan notifikasi wajib diisi.',
            'notification_email.email'    => 'Format email tujuan tidak valid.',
        ]);

        try {
            // Ambil path tanda tangan milik user Costing yang sedang login
            $approverSignature = $user->signature_path ?? $user->signature ?? null;

            // 2. Simpan Status Approval & Email Tujuan ke Database
            $updateData = [
                'status'             => 'approved',
                'approved_signature' => $approverSignature,
                'notification_email' => $request->notification_email, // Tersimpan ke DB
            ];

            $pr->update($updateData);

            // 3. Menentukan Email Pengirim & Email Penerima
            $targetEmail = $request->notification_email; // Penerima Email (dari Form)
            
            // Pengirim Email: Menggunakan Email & Nama User Login (Fallback ke .env jika email user kosong)
            $senderEmail = !empty($user->email) ? $user->email : config('mail.from.address');
            $senderName  = !empty($user->name) ? $user->name : config('mail.from.name', 'Costing Auditor');

            $emailSentNotice = '';

            try {
                // A. Generate PDF dari view pdf_pr.blade.php (View khusus dokumen cetak murni)
                $pdf = Pdf::loadView('cost_section.pdf_pr', compact('pr'))
                          ->setPaper('a4', 'portrait');

                // B. Menyusun Data Teks Email dengan Penulisan Rapi & Professional
                $emailData = [
                    'no_pr'       => $pr->no_pr,
                    'requester'   => optional($pr->user)->name ?? '-',
                    'nik'         => optional($pr->user)->nik ?? optional($pr->user)->nim ?? '-',
                    'sparepart_id'=> optional($pr->sparepart)->sparepart_id ?? $pr->sparepart_id,
                    'part_number' => optional($pr->sparepart)->part_number ?? '-',
                    'sap_code'    => optional($pr->sparepart)->sap_code ?? '-',
                    'category'    => optional($pr->sparepart)->category ?? '-',
                    'qty_pr'      => $pr->qty_pr,
                    'priority'    => ucfirst(strtolower($pr->priority)),
                    'destination' => $pr->destination ?? 'Costing Dept & Purchasing Dept',
                    'approved_by' => $senderName . ' (' . $senderEmail . ')',
                    'date'        => now()->format('d/m/Y H:i') . ' WIB',
                ];

                // Menyusun Isi Pesan Email (Teks Rapi & Rincian Lengkap)
                $emailMessageBody = 
                    "SISTEM NOTIFIKASI PURCHASE REQUEST - FULL APPROVED\n" .
                    "======================================================================\n\n" .
                    "Kepada Yth. Bapak/Ibu,\n\n" .
                    "Persetujuan akhir (Full Approval) untuk Purchase Request berikut telah berhasil diproses:\n\n" .
                    "  • Nomor PR Reference : {$emailData['no_pr']}\n" .
                    "  • Nama Pemohon       : {$emailData['requester']} (NIK: {$emailData['nik']})\n" .
                    "  • ID Sparepart       : {$emailData['sparepart_id']}\n" .
                    "  • Nomor Part         : {$emailData['part_number']}\n" .
                    "  • Kode SAP           : {$emailData['sap_code']}\n" .
                    "  • Kategori           : {$emailData['category']}\n" .
                    "  • Jumlah (QTY)       : {$emailData['qty_pr']} Pcs\n" .
                    "  • Tingkat Prioritas  : {$emailData['priority']}\n" .
                    "  • Tujuan Pengiriman  : {$emailData['destination']}\n" .
                    "  • Disetujui Oleh     : {$emailData['approved_by']}\n" .
                    "  • Waktu Persetujuan  : {$emailData['date']}\n\n" .
                    "Status Purchase Request ini telah resmi dinyatakan disetujui (FULL APPROVED). File salinan dokumen dalam bentuk PDF telah terlampir secara otomatis dalam email ini untuk dipergunakan sebagaimana mestinya.\n\n" .
                    "Terima kasih,\n" .
                    "Costing Department - PT. SIIX EMS KARAWANG\n\n" .
                    "======================================================================\n" .
                    "PERHATIAN / CONFIDENTIALITY NOTICE:\n" .
                    "Email ini beserta seluruh berkas lampirannya (termasuk PDF Purchase Request) bersifat RAHASIA dan hanya diperuntukkan bagi kepentingan internal PT. SIIX EMS KARAWANG.\n" .
                    "Dilarang keras menggandakan, menyebarluaskan, memperjualbelikan, atau mengirimi email ini ke pihak luar tanpa izin resmi tertulis dari manajemen perusahaan.";

                // Nama File Lampiran PDF (Karakter garis miring pada No PR diganti underscore agar aman)
                $sanitizedNoPr = str_replace(['/', '\\'], '_', $pr->no_pr);
                $pdfFileName   = "Purchase_Request_" . $sanitizedNoPr . ".pdf";

                // C. Proses Pengiriman Email + Lampiran PDF
                Mail::raw($emailMessageBody, function ($message) use ($targetEmail, $senderEmail, $senderName, $pr, $pdf, $pdfFileName) {
                    $message->to($targetEmail)
                            ->from(config('mail.from.address'), $senderName) // Email pengirim resmi dari server SMTP
                            ->replyTo($senderEmail, $senderName)             // Balasan ditujukan ke email user Costing
                            ->subject("[FULL APPROVED] Purchase Request - " . $pr->no_pr)
                            ->attachData($pdf->output(), $pdfFileName, [
                                'mime' => 'application/pdf',
                            ]);
                });

                $emailSentNotice = ' & Email notifikasi + PDF berhasil dikirim ke ' . $targetEmail;
            } catch (\Exception $mailEx) {
                // Jika pengiriman mail/PDF gagal, status approval DB tetap aman tersimpan
                $emailSentNotice = ' (Approval DB sukses, tetapi pengiriman email/PDF gagal: ' . $mailEx->getMessage() . ')';
            }

            // Redirect dikembalikan ke halaman INDEX antrean utama
            return redirect()->route('costing.pr.index')->with('success', 'PR ' . $pr->no_pr . ' telah berhasil disetujui (Approved)' . $emailSentNotice);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memproses approval akhir! Error: ' . $e->getMessage());
        }
    }

    /**
     * Mengubah status Purchase Request menjadi 'rejected'
     */
    public function reject($id)
    {
        $pr = PurchaseRequestEng::findOrFail($id);

        try {
            $pr->update(['status' => 'rejected']);
            
            return redirect()->back()->with('success', 'PR ' . $pr->no_pr . ' telah berhasil ditolak (Rejected).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses penolakan! Error: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat Purchase Request yang sudah selesai diproses oleh Costing
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        // Menampilkan data yang sudah final (approved / rejected)
        $query = PurchaseRequestEng::with(['user', 'sparepart'])
                                   ->whereIn('status', ['approved', 'rejected']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_pr', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan riwayat terbaru saat dokumen diperbarui
        $historyPr = $query->orderBy('updated_at', 'DESC')->paginate($perPage);

        return view('cost_section.history_pr', compact('historyPr', 'search'));
    }

    /**
     * Menampilkan Detail Arsip Purchase Request untuk Preview Log di Halaman History
     * (Membuka views/cost_section/preview_approval_pr.blade.php)
     */
    public function previewApproval($id)
    {
        // Eager load hanya 'user' dan 'sparepart'
        $pr = PurchaseRequestEng::with(['user', 'sparepart'])->findOrFail($id);

        return view('cost_section.preview_approval_pr', compact('pr'));
    }

    /**
     * Alias method show() untuk berjaga-jaga jika digunakan di tempat lain
     */
    public function show($id)
    {
        return $this->previewApproval($id);
    }
}