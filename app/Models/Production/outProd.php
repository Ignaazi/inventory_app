<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;

class outProd extends Model
{
    // Mengarahkan model ke tabel outProd_logs hasil migrations sebelumnya
    protected $table = 'outProd_logs';

    // Menentukan Primary Key kustom sesuai file migrations
    protected $primaryKey = 'outproduction_id';

    // Karena primary key kita menggunakan BigInt Auto Increment, set true
    public $incrementing = true;

    // Kolom-kolom yang diizinkan untuk diisi secara massal (Mass-Fillable)
    protected $fillable = [
        'inproduction_id', // 🔥 Sudah ditambahkan untuk mengikat jejak data IN (Traceability buat dosen)
        'nik',
        'line_id',
        'no_nozzle',
        'transaction_out_id',
        'request_no',
        'barcode_id',
        'stock_prod_id',
        'qty_out',
        'status',
        'remark',
        'comment'
    ];
}