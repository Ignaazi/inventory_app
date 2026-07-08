<?php

namespace App\Models\Costing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialReceivedSignature extends Model
{
    use HasFactory;

    protected $table = 'material_received_signatures';

    // 🌟 Semua kolom baru wajib didaftarkan di sini agar bisa disimpan ke DB
    protected $fillable = [
        'pr_code',
        'qty_received',
        'lot_no',
        'signature_status',
        'costing_notes',
        'engineering_notes',
        
        // Layer 1: Costing
        'costing_staff_nik',
        'costing_staff_name',
        'costing_signed_at',
        'costing_signature_path', 
        
        // Layer 2: Staff Eng
        'engineering_staff_nik',
        'engineering_staff_name',
        'engineering_signed_at',
        'engineering_signature_path', 
        
        // Layer 3: SPV Eng
        'engineering_spv_nik',
        'engineering_spv_name',
        'engineering_spv_signed_at',
        'engineering_spv_signature_path', 
    ];

    protected $casts = [
        'costing_signed_at' => 'datetime',
        'engineering_signed_at' => 'datetime',
        'engineering_spv_signed_at' => 'datetime',
    ];
}