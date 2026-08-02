<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'stock_eng_transactions';

    protected $fillable = [
        'tx_id',
        'users_id',
        'stock_engs_id',
        'db_barcodes_id',
        'production_request_id',
        'tx_type',
        'qty_transaction',
        'process_type',
        'photo_path',
        'status',
        'remark'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}