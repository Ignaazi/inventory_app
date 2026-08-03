<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class inProd extends Model
{
    // 1. Ubah nama tabel ke tabel transaksi produksi yang baru
    protected $table = 'stock_prod_transactions';

    // 2. Sesuaikan primary key menjadi 'id' sesuai dengan skema tabel baru
    protected $primaryKey = 'id';

    // 3. Kolom baru yang diizinkan untuk diisi secara massal (Mass Assignment)
    protected $fillable = [
        'tx_id',
        'users_id',
        'stock_prods_id',
        'stock_eng_tx_id',
        'db_barcodes_id',
        'production_request_id',
        'tx_type',
        'out_category',
        'nik_karyawan',
        'qty_transaction',
        'process_type',
        'photo_path',
        'status',
        'remark'
    ];

    /**
     * RELASI KE TABEL USERS (Operator yang melakukan scan)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'users_id', 'id');
    }

    /**
     * RELASI KE DATA STOK PRODUKSI PER LINI
     * Menghubungkan log transaksi ke item nozzle/sparepart di lini produksi terkait
     */
    public function stockProd(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Production\stock_prod::class, 'stock_prods_id', 'id');
    }

    /**
     * RELASI KE TRANSAKSI HULU GUDANG ENGINEERING (Jembatan Audit)
     * Digunakan untuk melacak dari mana asal barang yang masuk ke produksi ini
     */
    public function stockEngTransaction(): BelongsTo
    {
        // Asumsi nama model engineering transaksi lu adalah StockEngTransaction
        return $this->belongsTo(\App\Models\Engineering\StockEngTransaction::class, 'stock_eng_tx_id', 'id');
    }

    /**
     * RELASI KE TABEL BARCODE MASTER
     * Berfungsi memetakan siklus hidup QR code yang discan
     */
    public function barcode(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DbBarcode::class, 'db_barcodes_id', 'id');
    }

    /**
     * RELASI KE DOKUMEN PERMINTAAN PRODUKSI
     * Menghubungkan log masuk dengan dokumen request aslinya
     */
    public function productionRequest(): BelongsTo
    {
        // Asumsi nama model production request lu adalah ProductionRequest
        return $this->belongsTo(\App\Models\Production\RequestProd::class, 'production_request_id', 'id');
    }
}