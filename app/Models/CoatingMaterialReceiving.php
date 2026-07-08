<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoatingMaterialReceiving extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit
    protected $table = 'coating_material_receivings';

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

    // Mengatur agar casts tanggal otomatis (opsional untuk Laravel modern)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}