<?php

namespace App\Models\Engineering;

use App\Models\StockEng;
use App\Models\DbBarcode; 
use App\Models\Rak;       
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockOutEng extends Model
{
    use HasFactory;

    protected $table = 'stock_out_logs';

    protected $fillable = [
        'transaction_out_id',
        'nik',
        'request_sparepart_id',
        'barcode_id', 
        'stock_eng_id', // 🌟 WAJIB TETAP ADA: Untuk menyimpan ID relational ke tabel stock_engs
        'no_nozzle',    // 🌟 SEBAGAI HISTORI: Menyimpan nilai '110' langsung di log transaksi
        'rak_id',      
        'qty_out',
        'status',
        'remark',
        'comment'
    ];

    /**
     * Relasi ke Master Stock Engineering
     */
    public function stockEng()
    {
        return $this->belongsTo(StockEng::class, 'stock_eng_id', 'id');
    }

    /**
     * Relasi ke Master DB Barcode
     */
    public function dbBarcode()
    {
        return $this->belongsTo(DbBarcode::class, 'barcode_id', 'id');
    }

    /**
     * Relasi Langsung ke Master Rak
     */
    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id', 'id');
    }
}