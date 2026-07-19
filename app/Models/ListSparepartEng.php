<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListSparepartEng extends Model
{
    use HasFactory;

    // 🌟 Beritahu Laravel kalau model ini memakai tabel 'spareparts'
    protected $table = 'spareparts';

    // Pastikan fillable-nya juga sudah mencakup kolom baru ya!
    protected $fillable = [
        'sap_code',
        'part_number',
        'name',
        'category',
        'image',
        'length',
        'width',
        'thickness',
    ];
}