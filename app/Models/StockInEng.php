<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockInEng extends Model
{
    // Nama tabel harus sesuai dengan yang di migration
    protected $table = 'stock_in_logs'; 

    protected $fillable = [
        'stock_eng_id', 
        'nik', 
        'qty_added', 
        'status', 
        'remark',
        'comment'
    ];

    // Relasi balik ke tabel master Stock Engineering
    public function stockEng(): BelongsTo
    {
        return $this->belongsTo(StockEng::class, 'stock_eng_id');
    }
}