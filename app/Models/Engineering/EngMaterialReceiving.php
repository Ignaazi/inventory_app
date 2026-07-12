<?php

namespace App\Models\Engineering; // Wajib ada \Engineering karena di dalam subfolder!

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngMaterialReceiving extends Model
{
    use HasFactory;

    protected $table = 'eng_material_receivings';

    protected $fillable = [
        'receiving_code',
        'pr_code',
        'item_name',
        'qty_received',
        'lot_no',
        'supplier_name',
        'status',
        
        // File TTD & Stempel dari Costing
        'costing_signature_path',
        'costing_stamp_path',
        'costing_notes',

        // File TTD dari Engineering Staff & SPV
        'eng_signature_path',
        'eng_spv_signature_path',
        'engineering_notes',
        
        'created_by_nik',
        'created_by_name',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}