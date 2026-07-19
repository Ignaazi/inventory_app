<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEng extends Model
{
    use HasFactory;
    protected $table = 'stock_engs';
    protected $fillable = [
        'rak_id',
        'sparepart_id', 
        'qty',
        'min_stock',
    ];

    /**
     * Relasi balik ke tabel Master Sparepart (ListSparepartEng)
     */
    public function sparepart()
    {
        return $this->belongsTo(ListSparepartEng::class, 'sparepart_id');
    }
    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id');
    }
}