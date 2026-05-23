<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestProd extends Model
{
    use HasFactory;

    protected $table = 'production_requests';

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
        'approved_by',
        'signature_path',
        'reject_remark',
        'created_at',
        'updated_at'
    ];
}