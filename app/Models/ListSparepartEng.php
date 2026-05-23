<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListSparepartEng extends Model
{
    protected $table = 'spareparts'; 
    protected $fillable = [
        'name',
        'category',
        'image',
        'length',
        'width',
        'thickness',
    ];
}