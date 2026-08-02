<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypeBarcode extends Model
{
    use HasFactory;

    protected $table = 'type_barcodes';

    // Buka proteksi agar data JSON dan array bebas masuk [source: 6]
    protected $guarded = [];

    /**
     * Otomatis mengubah string JSON dari database menjadi Array PHP siap pakai [source: 6]
     */
    protected $casts = [
        'components_json' => 'array', // 💡 Biar kodingan lo gak perlu json_decode() manual lagi
    ];

    /**
     * Relasi balik ke Master DB Barcode
     * Setiap potongan komponen di sini mengikat ke satu stiker induk di db_barcodes [source: 6]
     */
    public function dbBarcode(): BelongsTo
    {
        return $this->belongsTo(DbBarcode::class, 'db_barcode_id');
    }
}