<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryApproval extends Model
{
    use HasFactory;

    protected $table = 'history_approvals';

    protected $fillable = [
        'request_no',
        'sparepart_name',
        'sap_code',
        'qty_req',
        'line_machine',
        'requestor',
        'approved_by',
        
        // 🎯 FIX: Update daftar fillable agar sesuai dengan kolom baru di migration
        'staff_signature', 
        'spv_signature',
        'spv_name',
        
        // Tetap simpan jika masih dipakai di bagian lain
        'signature_image', 
        
        'status',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel production_requests
     */
    public function productionRequest()
    {
        return $this->belongsTo(\App\Models\Production\RequestProd::class, 'request_no', 'request_no');
    }
}