<?php

namespace App\Models\Engineering;

use App\Models\StockEng;
use App\Models\DbBarcode; 
use App\Models\Rak;
use App\Models\BarcodeParsing; 
use App\Models\Production\RequestProd; 
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
        'stock_eng_id', 
        'no_nozzle',    
        'rak_id',      
        'qty_out',
        'status',
        'remark',
        'comment'
    ];

    public function stockEng()
    {
        return $this->belongsTo(StockEng::class, 'stock_eng_id', 'id');
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id', 'id');
    }

    public function dbBarcode()
    {
        return $this->belongsTo(DbBarcode::class, 'barcode_id', 'id');
    }

    public function dbBarcodeByCode()
    {
        return $this->belongsTo(DbBarcode::class, 'barcode_id', 'barcode_id');
    }

    public function barcodeParsing()
    {
        return $this->belongsTo(BarcodeParsing::class, 'barcode_id', 'id');
    }

    public function productionRequest()
    {
        return $this->belongsTo(RequestProd::class, 'request_sparepart_id', 'request_no');
    }

    public function requestProd()
    {
        return $this->productionRequest();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR PINTAR (SINKRONISASI BATCH PARSING BARU)
    |--------------------------------------------------------------------------
    */

    public function getAutoRequestNoAttribute()
    {
        if (!empty($this->request_sparepart_id) && $this->request_sparepart_id !== '-') {
            return $this->request_sparepart_id;
        }

        if ($this->productionRequest) {
            return $this->productionRequest->request_no;
        }

        // SINKRONISASI BARU: Lacak menggunakan barcode_out_id atau barcode_in_id
        if ($this->dbBarcodeByCode) {
            $parsing = BarcodeParsing::where('barcode_out_id', $this->dbBarcodeByCode->id)
                                     ->orWhere('barcode_in_id', $this->dbBarcodeByCode->id)
                                     ->first();
            
            if ($parsing && $parsing->productionRequest) {
                return $parsing->productionRequest->request_no ?? '-';
            }
        }

        if ($this->barcodeParsing && $this->barcodeParsing->productionRequest) {
            return $this->barcodeParsing->productionRequest->request_no ?? '-';
        }

        return '-';
    }

    public function getAutoNoNozzleAttribute()
    {
        if (!empty($this->no_nozzle) && $this->no_nozzle !== '-') {
            return $this->no_nozzle;
        }

        if ($this->stockEng && !empty($this->stockEng->no_nozzle)) {
            return $this->stockEng->no_nozzle;
        }

        // SINKRONISASI BARU: Lacak nozzle dari relasi parsing out/in yang valid
        if ($this->dbBarcodeByCode) {
            $parsing = BarcodeParsing::where('barcode_out_id', $this->dbBarcodeByCode->id)
                                     ->orWhere('barcode_in_id', $this->dbBarcodeByCode->id)
                                     ->first();
            
            if ($parsing && $parsing->productionRequest) {
                return $parsing->productionRequest->no_nozzle ?? '-';
            }
        }

        return '-';
    }
}