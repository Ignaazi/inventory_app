<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestProd extends Model
{
    use HasFactory;

    // Mengarah langsung ke tabel milik produksi
    protected $table = 'production_requests';

    // 🎯 DAFTAR KOLOM YANG BOLEH DIISI (Mass Assignment)
    protected $fillable = [
        'request_no',
        'sparepart_name',
        'sap_code',
        'remark',
        'qty_req',
        'line_machine',
        'requestor',
        'status',
        'production_signature',
        'production_stamp',
        'staff_name',
        'staff_signature',
        'spv_name',
        'spv_signature',
        'approved_by',
        'signature_path',
        'reject_remark'
    ];
}