<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestEng extends Model
{
    use HasFactory;

    // Menghubungkan model ke tabel purchase_requests di database
    protected $table = 'purchase_requests';

    // Menentukan primary key eksplisit bawaan tabel
    protected $primaryKey = 'id';

    // Mendaftarkan semua kolom agar bisa diisi menggunakan metode Mass Assignment (create/update)
    protected $fillable = [
        'pr_code',
        'name',
        'nik',
        'product',
        'type_product',
        
        // 🌟 TAMBAHAN: Mendaftarkan kolom qty agar diizinkan masuk ke database
        'qty', 
        
        'priority',
        'request_by',
        'request_date',
        'destination',
        'notes',
        'status',
    ];

    // Mengonversi format datetime otomatis saat diakses di Blade atau Controller
    protected $casts = [
        'request_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // Tipe data qty dikunci ke integer agar nilainya konsisten angka
        'qty' => 'integer', 
    ];
}