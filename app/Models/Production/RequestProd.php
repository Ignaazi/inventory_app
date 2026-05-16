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
        'qty_req',
        'line_machine',
        'requestor',
        'status',
        'reject_remark'
    ];
}