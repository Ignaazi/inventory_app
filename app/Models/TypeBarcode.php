<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeBarcode extends Model
{
    protected $table = 'type_barcodes';

    // Buka proteksi agar data JSON dan array bebas masuk
    protected $guarded = [];
}