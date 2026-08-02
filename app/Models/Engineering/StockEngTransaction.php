<?php

namespace App\Models\Engineering;

use App\Models\User;
use App\Models\DbBarcode;
use App\Models\StockEng;
use App\Models\Production\RequestProd; // 🔑 Namespace Dokumen Permintaan Produksi
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEngTransaction extends Model
{
    use HasFactory;

    /**
     * Nama tabel fisik yang digunakan di database.
     */
    protected $table = 'stock_eng_transactions';

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     * Sudah diselaraskan dengan kolom baru di database.
     */
    protected $fillable = [
        'tx_id',
        'users_id',
        'stock_engs_id',
        'db_barcodes_id',
        'production_request_id', // 🔑 Kolom baru penghubung ke dokumen asal
        'tx_type',
        'qty_transaction',
        'process_type',
        'photo_path',
        'status',
        'remark',
    ];

    /**
     * Relasi ke tabel Users.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    /**
     * Relasi ke tabel StockEng (Master Saldo Gudang).
     */
    public function stockEng(): BelongsTo
    {
        return $this->belongsTo(StockEng::class, 'stock_engs_id');
    }

    /**
     * Relasi ke tabel DB Barcodes.
     */
    public function barcode(): BelongsTo
    {
        return $this->belongsTo(DbBarcode::class, 'db_barcodes_id');
    }

    /**
     * Relasi Langsung ke Dokumen Permintaan Produksi (Production Request).
     * Memungkinkan penarikan data request_no secara real-time tanpa lewat perantara.
     */
    public function productionRequest(): BelongsTo
    {
        return $this->belongsTo(RequestProd::class, 'production_request_id');
    }
}