<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class inProd extends Model
{
    // Nama tabel log sesuai rancangan migrasi
    protected $table = 'inProd_logs';

    // Primary key utama tabel ini
    protected $primaryKey = 'inproduction_id';

    // Kolom yang diizinkan diisi secara massal saat store transaksi
    protected $fillable = [
        'nik',
        'line_id',
        'no_nozzle',          // 🔥 Tambahkan ini
        'transaction_out_id',
        'request_no',         // 🔥 Tambahkan ini
        'barcode_id',
        'stock_prod_id',
        'qty_in',
        'status',
        'remark',
        'comment'
    ];

    /**
     * 1. RELASI KE TABEL MASTER LINE PRODUCTION
     * Lokasi Model: Models/Production/ListLineProduction.php
     */
    public function line(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Production\ListLineProduction::class, 'line_id', 'line_id');
    }

    /**
     * 2. RELASI KE TABEL STOCK OUT ENGINEERING
     * Lokasi Model: Models/Engineering/StockOutEng.php
     * Berfungsi mengambil data No Nozzle dan Request No secara berantai
     */
    public function stockOutLog(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Engineering\StockOutEng::class, 'transaction_out_id', 'transaction_out_id');
    }

    /**
     * 3. RELASI KE TABEL BARCODE MASTER
     * Lokasi Model: Models/DbBarcode.php
     */
    public function barcode(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DbBarcode::class, 'barcode_id', 'barcode_id');
    }

    /**
     * 4. RELASI KE TABEL STOCK PRODUCTION INTERNAL LINE
     * Lokasi Model: Models/Production/stock_prod.php
     * Digunakan untuk skema berantai mengambil part_no & sap_code, serta trigger penambahan qty
     */
    public function stockProd(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Production\stock_prod::class, 'stock_prod_id', 'id');
    }
}