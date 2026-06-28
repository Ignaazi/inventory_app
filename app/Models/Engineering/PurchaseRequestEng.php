<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestEng extends Model
{
    use HasFactory;

    protected $table = 'purchase_requests';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pr_code',
        'name',
        'nik',
        'product',
        'type_product',
        'qty', 
        'priority',
        'request_by',
        'request_date',
        'destination',
        'notes',
        'status',
    ];
    protected $casts = [
        'request_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'qty' => 'integer', 
    ];
}