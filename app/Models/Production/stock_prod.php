<?php

namespace App\Models\production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class stock_prod extends Model
{
    use HasFactory;

    protected $table = 'stock_prods'; 

    protected $fillable = [
        'line_id',
        'no_nozzle',
        'request_no',
        'part_no',
        'sap_code',
        'barcode_id',
        'transaction_out_id',
        'qty',
        'min_stock',
    ];

    /**
     * 1. LOGIKA STATUS WARNA (Merah, Kuning, Hijau)
     * Menggunakan Aksesor Laravel agar bisa langsung dipanggil di View Blade
     */
    public function getStatusLabelAttribute()
    {
        if ($this->qty <= 0) {
            return [
                'color' => 'bg-red-500', 
                'text' => 'Habis'
            ];
        } elseif ($this->qty <= $this->min_stock) {
            return [
                'color' => 'bg-yellow-500', 
                'text' => 'Warning'
            ];
        }
        
        return [
            'color' => 'bg-green-500', 
            'text' => 'Aman'
        ];
    }

    /**
     * 2. Relasi ke tabel Line Line Production (Mengambil No Line)
     */
    public function line()
    {
        // Fallback deteksi letak model LineLineProduction kamu
        $modelUtama = 'App\\Models\\LineLineProduction';
        $modelSubFolder = 'App\\Models\\Production\\LineLineProduction';

        if (class_exists($modelUtama)) {
            return $this->belongsTo($modelUtama, 'line_id', 'id');
        }
        return $this->belongsTo($modelSubFolder, 'line_id', 'id');
    }
}