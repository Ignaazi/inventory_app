<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListLineProduction extends Model
{
    use HasFactory;

    // Definisikan nama tabel secara eksplisit
    protected $table = 'list_line_productions';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'user_id', // 🌟 WAJIB DITAMBAHKAN agar data NIM tidak terblokir dan jadi null lagi!
        'line_id',
        'no_line',
        'name_machine'
    ];

    /**
     * Hubungan relasi murni menggunakan NIM ke tabel users
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'nim');
    }
}