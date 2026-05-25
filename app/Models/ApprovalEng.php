<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalEng extends Model
{
    use HasFactory;

    // Tabel ini merujuk ke tabel utama produksi
    protected $table = 'production_requests'; 

    protected $fillable = [
        'request_no',
        'sparepart_name',
        'sap_code',
        'qty_req',
        'line_machine',
        'requestor',
        'status',
        'reject_remark',
        
        // 🎯 FIX: Tambahkan kolom-kolom otorisasi baru agar bisa di-update oleh Controller
        'staff_name',
        'staff_signature',
        'staff_stamp',
        'spv_name',
        'spv_signature',
        'spv_stamp',
        
        // Tetap simpan kolom lama agar sistem tidak error (legacy support)
        'approved_by', 
        'signature_path'
    ];
}