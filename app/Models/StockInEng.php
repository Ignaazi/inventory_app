<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Engineering\EngMaterialReceiving;

class StockInEng extends Model
{
    use HasFactory;

    // Arahkan ke nama tabel dari migration terbaru kamu
    protected $table = 'stock_in_logs';

    // Primary key menggunakan standar bawaan blueprint Laravel
    protected $primaryKey = 'id';

    // Seluruh kolom diizinkan untuk diisi secara Mass Assignment
    protected $fillable = [
        'stock_eng_id',
        'eng_material_receiving_id',
        'nik',
        'qty_added',
        'status',
        'remark',
        'comment'
    ];

    /**
     * Relasi ke data Master Stok Gudang Utama (stock_engs)
     */
    public function stockEng()
    {
        return $this->belongsTo(StockEng::class, 'stock_eng_id');
    }

    /**
     * Relasi ke data Dokumen Costing / PR (eng_material_receivings)
     */
    public function engMaterialReceiving()
    {
        return $this->belongsTo(EngMaterialReceiving::class, 'eng_material_receiving_id');
    }
}