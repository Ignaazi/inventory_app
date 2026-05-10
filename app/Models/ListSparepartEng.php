<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListSparepartEng extends Model
{
    // Paksa model menggunakan nama tabel ini
    protected $table = 'list_sparepart_engs';

    protected $fillable = [
        'name',
        'image',
        'category',
        'qty'
    ];
}