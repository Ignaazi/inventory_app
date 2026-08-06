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
     * Tabel `spareparts` menggunakan kolom `id` sebagai primary key.
     * `sparepart_id` adalah kode item, bukan kunci relasi.
     *
     * @var string
     */
    protected $primaryKey = 'id';

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
