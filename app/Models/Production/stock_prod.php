<?php

namespace App\Models\Production;

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
     * 2. RELASI KE TABEL MASTER LINE PRODUCTION (AMBIL NO LINE)
     * FIXED: Menggunakan nama model 'ListLineProduction' sesuai tabel 'list_line_productions'
     */
    public function line()
    {
        // Fallback deteksi letak model ListLineProduction kamu (Luar atau dalam sub-folder Production)
        $modelUtama = 'App\\Models\\ListLineProduction';
        $modelSubFolder = 'App\\Models\\Production\\ListLineProduction';

        // Menentukan class model mana yang aktif di aplikasi skripsi kamu
        $chosenModel = class_exists($modelUtama) ? $modelUtama : $modelSubFolder;

        // Hubungkan foreign key stock_prods.line_id ke owner key list_line_productions.line_id
        return $this->belongsTo($chosenModel, 'line_id', 'line_id');
    }

    /**
     * 🔗 3. RELASI BERANTAI KE MASTER STOCK ENGINEERING (StockEng)
     * Ditambahkan agar pemanggilan $log->stockProd->stockEng->part_no di Blade aman.
     * Menggunakan dengan default data lokal milik stock_prod itu sendiri sebagai cadangan.
     */
    public function stockEng()
    {
        return $this->belongsTo(\App\Models\StockEng::class, 'sap_code', 'sap_code')
            ->withDefault([
                'part_no' => $this->part_no ?? '-',
                'sap_code' => $this->sap_code ?? '-'
            ]);
    }
}