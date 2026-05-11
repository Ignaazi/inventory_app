<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rak extends Model
{
    use HasFactory;

    // Pastikan nama tabelnya benar
    protected $table = 'raks';

    // Sesuaikan fillable dengan kolom yang ada di migration
    protected $fillable = [
        'nama_rak', 
        'lokasi' // Gue ganti dari 'lokasi' ke 'keterangan' biar sinkron sama Controller
    ];

    /**
     * Relasi ke StockEng
     * Satu Rak memiliki banyak Nozzle (Stock)
     */
    public function stocks()
    {
        // Pastikan foreign key-nya adalah 'rak_id'
        return $this->hasMany(StockEng::class, 'rak_id');
    }
}