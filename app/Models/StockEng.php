<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockEng extends Model
{
    use HasFactory;

    protected $table = 'stock_engs';

    protected $fillable = [
        'rak_id',      // Kita ganti id_rak (string) jadi rak_id (foreign key)
        'no_nozzle',
        'part_no',
        'sap_code',
        'category',
        'qty',
        'min_stock'
    ];

    /**
     * Relasi ke Model Rak
     * Satu data stock ini dimiliki oleh satu Rak
     */
    public function rak()
    {
        // Pastikan nanti nama modelnya adalah 'Rak'
        return $this->belongsTo(Rak::class, 'rak_id');
    }
}