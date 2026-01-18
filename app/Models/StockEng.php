<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEng extends Model
{
    use HasFactory;

    protected $table = 'stock_engs';
    protected $fillable = ['id_rak', 'item_name', 'qty', 'min_stock'];
}