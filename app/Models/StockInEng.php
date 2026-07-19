<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockInEng extends Model
{
    use HasFactory;

    // 1. Tentukan primary key sesuai migration kamu
    protected $primaryKey = 'inproduction_id';

    // 2. 🌟 PAKSA MODEL UNTUK MENGGUNAKAN TABEL YANG BENAR
    protected $table = 'inProd_logs';

    // 3. Daftarkan semua kolom agar diizinkan Mass Assignment oleh Laravel
    protected $fillable = [
        'nik',
        'line_id',
        'no_nozzle',
        'transaction_out_id',
        'request_no',
        'barcode_id',
        'stock_prod_id',
        'qty_in',
        'status',
        'remark',
        'comment'
    ];

    // Relasi ke Stock utama (sesuaikan nama class model kamu jika berbeda)
    public function stockEng()
    {
        return $this->belongsTo(StockEng::class, 'stock_prod_id', 'id');
    }

    // Relasi ke PR Costing / Receiving jika dibutuhkan
    public function engMaterialReceiving()
    {
        return $this->belongsTo(\App\Models\Engineering\EngMaterialReceiving::class, 'eng_material_receiving_id', 'id');
    }
}