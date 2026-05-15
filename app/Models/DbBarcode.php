<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DbBarcode extends Model
{
    // Menentukan nama tabel di MySQL secara eksplisit
    protected $table = 'db_barcodes';

    // Membuka proteksi mass assignment agar data dari form/AJAX bisa langsung masuk database
    protected $guarded = [];
}