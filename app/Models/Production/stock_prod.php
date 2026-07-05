<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class stock_prod extends Model
{
    use HasFactory;

    // 1. Tentukan nama tabel
    protected $table = 'stock_prods'; 

    // 2. Kolom Baru Sesuai Konsep Database Baru Lu (Kolom sampah lama resmi DIBUANG)
    protected $fillable = [
        'line_id',
        'no_nozzle',
        'part_no',
        'sap_code',
        'category', // Ditambahkan menampung kategori dari stock_engs
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
     * FIXED: Menghubungkan foreign key stock_prods.line_id ke master list_line_productions.line_id
     */
    public function line()
    {
        // Deteksi letak model ListLineProduction kamu (Luar atau dalam sub-folder Production)
        $modelUtama = 'App\\Models\\ListLineProduction';
        $modelSubFolder = 'App\\Models\\Production\\ListLineProduction';

        $chosenModel = class_exists($modelUtama) ? $modelUtama : $modelSubFolder;

        return $this->belongsTo($chosenModel, 'line_id', 'line_id');
    }

    /**
     * 3. RELASI KE TABEL MASTER SPAREPARTS (Data Nozzle)
     * Menghubungkan kolom 'no_nozzle' di tabel ini ke kolom 'name' di tabel spareparts
     */
    public function sparepart()
    {
        // Deteksi letak model Sparepart di aplikasi lu
        $modelUtama = 'App\\Models\\Sparepart';
        $modelEngineering = 'App\\Models\\Engineering\\Sparepart';
        
        $chosenModel = class_exists($modelEngineering) ? $modelEngineering : $modelUtama;

        return $this->belongsTo($chosenModel, 'no_nozzle', 'name');
    }

    /**
     * 4. RELASI KE MASTER STOCK ENGINEERING (StockEng)
     * Menghubungkan kolom 'sap_code' di tabel ini ke master stock engineering
     */
    public function stockEng()
    {
        // Deteksi letak model StockEng di aplikasi lu
        $modelUtama = 'App\\Models\\StockEng';
        $modelEngineering = 'App\\Models\\Engineering\\StockEng';

        $chosenModel = class_exists($modelEngineering) ? $modelEngineering : $modelUtama;

        return $this->belongsTo($chosenModel, 'sap_code', 'sap_code')
            ->withDefault([
                'part_no' => $this->part_no ?? '-',
                'sap_code' => $this->sap_code ?? '-',
                'category' => $this->category ?? '-'
            ]);
    }
}