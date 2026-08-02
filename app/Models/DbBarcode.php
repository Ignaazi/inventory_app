<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DbBarcode extends Model
{
    use HasFactory;

    protected $table = 'db_barcodes';

    /**
     * Properti yang bisa diisi secara massal (Mass Assignable)
     * Sudah disesuaikan dengan kolom fisik tabel db_barcodes terbaru
     */
    protected $fillable = [
        'barcode_id', 
        'users_id',          // 🔑 Menggantikan creator_nik lama yang sudah dibuang
        'barcode_type',
        'barcode_size',
        'final_content',
        'stock_eng_id',      // 🔑 Relasi ke master stok engineering
        'current_lifecycle'
    ];

    /**
     * Relasi ke tabel Users (Pembuat / Pembuat Barcode)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relasi ke master data Stock Engineering
     */
    public function stockEng(): BelongsTo
    {
        return $this->belongsTo(StockEng::class, 'stock_eng_id');
    }

    /**
     * Relasi ke komponen pecahan barcode (Tabel type_barcodes yang kita hubungkan tadi)
     */
    public function typeBarcodes(): HasMany
    {
        return $this->hasMany(TypeBarcode::class, 'db_barcode_id');
    }
}