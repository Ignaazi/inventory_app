<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListSparepartEng extends Model
{
    // KUNCI NAMA TABELNYA DI SINI, BRO!
    protected $table = 'spareparts'; 

    // Pastikan fillable sudah pakai kolom dimensi baru
    protected $fillable = [
        'name',
        'category',
        'image',
        'length',
        'width',
        'thickness',
    ];
}