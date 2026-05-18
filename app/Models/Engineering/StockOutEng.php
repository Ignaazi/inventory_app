<?php

namespace App\Models\Engineering;

use App\Models\StockEng; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockOutEng extends Model
{
    use HasFactory;

    // KUNCI KE NAMA TABEL BARU SANGAT WAJIB DI SINI, COY!
    protected $table = 'stock_out_logs';

    protected $fillable = [
        'transaction_out_id',
        'nik',
        'request_sparepart_id',
        'barcode_id',
        'stock_eng_id',
        'qty_out',
        'status',
        'remark',
        'comment'
    ];

    /**
     * Relasi Balik ke Master Stock Engineering
     */
    public function stockEng()
    {
        return $this->belongsTo(StockEng::class, 'stock_eng_id', 'id');
    }
}