<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\StockEng;

class TransactionDisposal extends Model
{
    // 1. Mengarahkan model secara manual ke nama tabel di database
    protected $table = 'stock_eng_transactions';

    // 2. Mendaftarkan semua kolom yang bisa diisi (Mass Assignment) sesuai skema SQL
    protected $fillable = [
        'tx_id',
        'users_id',
        'stock_engs_id',
        'db_barcodes_id',
        'production_request_id',
        'tx_type',
        'qty_transaction',
        'process_type',
        'photo_path',
        'status',
        'remark'
    ];

    // 3. Cast tipe data enum/timestamp jika diperlukan
    protected $casts = [
        'qty_transaction' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * RELATIONSHIPS (Menghubungkan Foreign Key sesuai Constraints SQL)
     */

    /**
     * Relasi ke tabel 'users' via 'users_id'
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relasi ke tabel 'stock_engs' via 'stock_engs_id'
     */
    public function stockEng(): BelongsTo
    {
        return $this->belongsTo(StockEng::class, 'stock_engs_id');
    }

    /**
     * Relasi ke tabel 'db_barcodes' via 'db_barcodes_id' (Nama method: barcode)
     */
    public function barcode(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DbBarcode::class, 'db_barcodes_id');
    }

    /**
     * Alias relasi ke tabel 'db_barcodes' via 'db_barcodes_id' (Nama method: dbBarcode)
     */
    public function dbBarcode(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DbBarcode::class, 'db_barcodes_id');
    }

    /**
     * Relasi ke tabel 'production_requests' via 'production_request_id' (Nullable)
     */
    public function productionRequest(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Production\RequestProd::class, 'production_request_id');
    }
}