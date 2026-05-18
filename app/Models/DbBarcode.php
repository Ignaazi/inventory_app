<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DbBarcode extends Model
{
    use HasFactory;

    protected $table = 'db_barcodes';

    protected $fillable = [
        'barcode_id', // SIIXENG001, SIIXENG002, dst.
        'barcode_type',
        'barcode_size',
        'final_content',
        'current_lifecycle'
    ];
}