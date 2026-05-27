<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalEng extends Model
{
    use HasFactory;

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
        'staff_name',
        'staff_signature',
        'staff_stamp',
        'spv_name',
        'spv_signature',
        'spv_stamp',
        'approved_by', 
        'signature_path'
    ];
}