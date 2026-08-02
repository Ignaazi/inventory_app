<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class stock_prod extends Model
{
    use HasFactory;

    // 1. Tentukan nama tabel
    protected $table = 'stock_prods'; 

    // 2. Kolom Baru Sesuai Konsep Database Baru (Ramping & Terpusat)
    protected $fillable = [
        'line_id',
        'sparepart_id',
        'qty',
        'min_stock',
    ];

    /**
     * 1. LOGIKA STATUS WARNA (Merah, Kuning, Hijau)
     * Tetap dipertahankan menggunakan Aksesor Laravel untuk View Blade
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
     * 2. RELASI KE TABEL MASTER LINE PRODUCTION
     * Menghubungkan foreign key stock_prods.line_id ke primary key list_line_productions.id
     */
    public function line()
    {
        $modelUtama = 'App\\Models\\ListLineProduction';
        $modelSubFolder = 'App\\Models\\Production\\ListLineProduction';

        $chosenModel = class_exists($modelUtama) ? $modelUtama : $modelSubFolder;

        // Mengarah ke 'id' karena line_id di migration baru adalah foreignId
        return $this->belongsTo($chosenModel, 'line_id', 'id');
    }

    /**
     * 3. RELASI KE TABEL MASTER SPAREPARTS
     * Sekarang langsung terhubung via sparepart_id (Sama persis seperti stock_engs)
     */
    public function sparepart()
    {
        $modelUtama = 'App\\Models\\ListSparepartEng';
        $modelEngineering = 'App\\Models\\ListSparepartEng';
        
        $chosenModel = class_exists($modelEngineering) ? $modelEngineering : $modelUtama;

        return $this->belongsTo($chosenModel, 'sparepart_id', 'id');
    }

    /**
     * 4. RELASI KE MASTER STOCK ENGINEERING (StockEng)
     * Menampilkan data stok engineering yang memiliki sparepart yang sama.
     * Karena sap_code di tabel ini sudah dihapus, relasi dicari menjembatani lewat sparepart_id.
     */
    public function stockEngs()
    {
        $modelUtama = 'App\\Models\\StockEng';
        $modelEngineering = 'App\\Models\\Engineering\\StockEng';

        $chosenModel = class_exists($modelEngineering) ? $modelEngineering : $modelUtama;

        // Menghubungkan kesamaan sparepart_id antara Stock Prod dan Stock Eng
        return $this->hasMany($chosenModel, 'sparepart_id', 'sparepart_id');
    }
}