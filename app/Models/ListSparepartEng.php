<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListSparepartEng extends Model
{
    use HasFactory;

    /**
     * Nama tabel fisik yang digunakan di database.
     *
     * @var string
     */
    protected $table = 'spareparts';

    /**
     * Menentukan kunci utama (Primary Key) dari tabel ini.
     * Karena menggunakan 'sparepart_id', bukan 'id'.
     *
     * @var string
     */
    protected $primaryKey = 'sparepart_id';

    /**
     * Jika 'sparepart_id' di database kamu berupa STRING (bukan angka/integer auto-increment),
     * aktifkan 2 baris di bawah ini dengan melepas tanda komentar (//):
     */
    // public $incrementing = false;
    // protected $keyType = 'string';

    /**
     * Kolom-kolom yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sap_code',
        'part_number',
        'sparepart_id',
        'category',
        'image',
        'length',
        'width',
        'thickness',
    ];

    /**
     * Relasi ke tabel StockEng (Satu sparepart bisa memiliki banyak data stock/history stock)
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(StockEng::class, 'sparepart_id');
    }
}   