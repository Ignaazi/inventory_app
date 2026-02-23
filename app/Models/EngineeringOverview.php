<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineeringOverview extends Model
{
    use HasFactory;

    // Tambahkan ini agar bisa simpan data ke database
    protected $fillable = [
        'sap_code',
        'part_name',
        'nozzle_type',
        'current_stock',
        'min_stock_threshold',
        'rack_position',
        'status'
    ];
}