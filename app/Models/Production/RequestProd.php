<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;

class RequestProd extends Model
{
    // WAJIB TAMBAHKAN BARIS INI:
    protected $table = 'production_requests';

    // Pastikan fillable mencantumkan semua field yang ada di struktur database baru
    protected $fillable = [
        'list_line_production_id',
        'sparepart_id',
        'request_no',
        'sparepart_name',
        'sap_code',
        'remark',
        'qty_req',
        'line_machine',
        'requestor',
        'production_signature',
        'production_stamp',
        'status',
        'staff_name',
        'staff_signature',
        'staff_stamp',
        'spv_name',
        'spv_signature',
        'spv_stamp',
        'approved_by',
        'signature_path',
        'reject_remark'
    ];
}