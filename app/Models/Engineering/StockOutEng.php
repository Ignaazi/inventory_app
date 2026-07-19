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

    /**
     * Relasi ke Master Stock Engineering
     */
    public function stockEng()
    {
        return $this->belongsTo(StockEng::class, 'stock_eng_id', 'id');
    }

    /**
     * Relasi Langsung ke Master Rak
     */
    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id', 'id');
    }

    /**
     * Relasi ke Master DB Barcode jika log menyimpan ID Angka (Integer)
     */
    public function dbBarcode()
    {
        return $this->belongsTo(DbBarcode::class, 'barcode_id', 'id');
    }

    /**
     * Relasi ke Master DB Barcode jika log menyimpan STRING CODE (Misal: SIIXENG002)
     */
    public function dbBarcodeByCode()
    {
        return $this->belongsTo(DbBarcode::class, 'barcode_id', 'barcode_id');
    }

    /**
     * Relasi ke Barcode Parsing jika log menyimpan ID Angka langsung
     */
    public function barcodeParsing()
    {
        return $this->belongsTo(BarcodeParsing::class, 'barcode_id', 'id');
    }

    /**
     * Relasi Langsung ke Production Request (Menggunakan String request_no)
     */
    public function productionRequest()
    {
        return $this->belongsTo(RequestProd::class, 'request_sparepart_id', 'request_no');
    }

    /**
     * Alias untuk requestProd agar tidak merusak view lama
     */
    public function requestProd()
    {
        return $this->productionRequest();
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR PINTAR (JEMBATAN DATA OTOMATIS)
    |--------------------------------------------------------------------------
    */

    /**
     * Accessor untuk melacak REQ SPAREPART ID secara otomatis dari segala jalur
     * Panggil di Blade cukup dengan: $log->auto_request_no
     */
    public function getAutoRequestNoAttribute()
    {
        // Jalur 1: Jika field request_sparepart_id di log langsung ada isinya
        if (!empty($this->request_sparepart_id) && $this->request_sparepart_id !== '-') {
            return $this->request_sparepart_id;
        }

        // Jalur 2: Cek lewat relasi direct productionRequest
        if ($this->productionRequest) {
            return $this->productionRequest->request_no;
        }

        // Jalur 3: Lacak dari String Barcode (SIIXENG002) -> db_barcodes -> barcode_parsings -> production_request
        if ($this->dbBarcodeByCode) {
            // Memanggil relasi barcodeParsing dari model DbBarcode (Pastikan relasi ini terdefinisi di model DbBarcode)
            $parsing = $this->dbBarcodeByCode->barcodeParsing ?? BarcodeParsing::where('barcode_db_id', $this->dbBarcodeByCode->id)->first();
            
            if ($parsing && $parsing->productionRequest) {
                return $parsing->productionRequest->request_no ?? '-';
            }
        }

        // Jalur 4: Lacak jika barcode_id di log ternyata adalah ID Integer dari tabel barcode_parsings
        if ($this->barcodeParsing && $this->barcodeParsing->productionRequest) {
            return $this->barcodeParsing->productionRequest->request_no ?? '-';
        }

        return '-';
    }

    /**
     * Accessor untuk melacak NO NOZZLE secara otomatis dari segala jalur
     * Panggil di Blade cukup dengan: $log->auto_no_nozzle
     */
    public function getAutoNoNozzleAttribute()
    {
        // Jalur 1: Jika field no_nozzle di log langsung ada isinya
        if (!empty($this->no_nozzle) && $this->no_nozzle !== '-') {
            return $this->no_nozzle;
        }

        // Jalur 2: Ambil dari master stock engineering
        if ($this->stockEng && !empty($this->stockEng->no_nozzle)) {
            return $this->stockEng->no_nozzle;
        }

        // Jalur 3: Ambil dari data request produksi via jembatan barcode parsing string
        if ($this->dbBarcodeByCode) {
            $parsing = $this->dbBarcodeByCode->barcodeParsing ?? BarcodeParsing::where('barcode_db_id', $this->dbBarcodeByCode->id)->first();
            
            if ($parsing && $parsing->productionRequest) {
                return $parsing->productionRequest->no_nozzle ?? '-';
            }
        }

        return '-';
    }
}