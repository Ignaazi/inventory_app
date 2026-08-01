<?php

namespace App\Models\Costing; // Menyesuaikan subfolder Costing biar rapi

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostingMaterialReceiving extends Model
{
    use HasFactory;


    protected $table = 'costing_material_receivings';

    // Kolom yang diizinkan untuk mass assignment
    protected $fillable = [
        'receiving_id',
        'material_code',
        'material_name',
        'lot_no',
        'qty_received',
        'nik_receiver',
        'status',
        'comment'
    ];

    // Mengatur agar casts tanggal otomatis
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}