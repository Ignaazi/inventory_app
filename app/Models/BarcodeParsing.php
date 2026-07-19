<?php

namespace App\Models;

use App\Models\Production\RequestProd;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeParsing extends Model
{
    use HasFactory;

    protected $table = 'barcode_parsings';

    protected $fillable = [
        'barcode_db_id',
        'production_request_id',
        'nik',
        'qty_parsed',
        'description'
    ];

    /**
     * Relasi ke Master DB Barcode
     */
    public function dbBarcode()
    {
        return $this->belongsTo(DbBarcode::class, 'barcode_db_id', 'id');
    }

    /**
     * Relasi ke Production Request menggunakan ID
     */
    public function productionRequest()
    {
        return $this->belongsTo(RequestProd::class, 'production_request_id', 'id');
    }

    
    public function requestProd()
    {
        return $this->productionRequest();
    }
}