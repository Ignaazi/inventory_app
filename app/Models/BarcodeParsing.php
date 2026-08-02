<?php

namespace App\Models;

use App\Models\Production\RequestProd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeParsing extends Model
{
    use HasFactory;

    protected $table = 'barcode_parsings';

    /**
     * Properti yang bisa diisi secara massal (Mass Assignable)
     * Sudah disesuaikan dengan kolom fisik tabel terbaru
     */
    protected $fillable = [
        'users_id',
        'production_request_id',
        'barcode_in_id',
        'barcode_out_id',
        'qty_parsed',
        'status',
        'remark'
    ];

    /**
     * Relasi ke tabel Users (Operator Penanggung Jawab)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    /**
     * Relasi ke Production Request menggunakan ID
     */
    public function productionRequest()
    {
        return $this->belongsTo(RequestProd::class, 'production_request_id', 'id');
    }

    /**
     * Alias Relasi ke Production Request (Tetap dipertahankan bawaan lo)
     */
    public function requestProd()
    {
        return $this->productionRequest();
    }

    /**
     * Relasi ke DB Barcode untuk Barcode IN (Stiker Gudang Engineering)
     */
    public function barcodeIn()
    {
        return $this->belongsTo(DbBarcode::class, 'barcode_in_id', 'id');
    }

    /**
     * Relasi ke DB Barcode untuk Barcode OUT (Stiker Baru Khusus Line Produksi)
     */
    public function barcodeOut()
    {
        return $this->belongsTo(DbBarcode::class, 'barcode_out_id', 'id');
    }
}