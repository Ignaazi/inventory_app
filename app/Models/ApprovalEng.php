<?php

namespace App\Models\Production; // Sesuaikan namespace ini jika berada di dalam folder Models/Production

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestProd extends Model
{
    use HasFactory;

    // Menunjuk ke tabel yang sama
    protected $table = 'production_requests'; 

    // WAJIB dimasukkan semua kolom yang di-update oleh Controller agar tidak terkena Mass Assignment Protection
    protected $fillable = [
        'user_id',
        'list_line_production_id',
        'sparepart_id',
        'request_no',
        'remark',
        'qty_req',
        'production_signature',
        'engineering_signature', 
        'spv_signature',
        'staff_name',       // FIX: Tambahkan ini agar nama staff tersimpan
        'spv_name',         // FIX: Tambahkan ini agar nama SPV tersimpan
        'staff_stamp',      // FIX: Tambahkan ini jika menggunakan cap/stempel staff
        'spv_stamp',        // FIX: Tambahkan ini jika menggunakan cap/stempel SPV
        'status',
        'reject_remark'
    ];

    /**
     * Relasi ke pembuat request (User)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Menghubungkan foreign key 'sparepart_id' langsung ke primary key 'id' master sparepart
     */
    public function sparepart()
    {
        return $this->belongsTo(\App\Models\ListSparepartEng::class, 'sparepart_id', 'id');
    }

    /**
     * Relasi ke data Line Produksi
     */
    public function lineProduction()
    {
        return $this->belongsTo(\App\Models\Production\ListLineProduction::class, 'list_line_production_id');
    }
}