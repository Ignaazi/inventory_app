<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListLineProduction extends Model
{
    use HasFactory;

    // Definisikan nama tabel secara eksplisit
    protected $table = 'list_line_productions';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'line_id',
        'no_line',
        'name_machine'
    ];
}