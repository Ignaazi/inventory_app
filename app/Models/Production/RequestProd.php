<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;

class RequestProd extends Model
{
    // Mengarahkan model ke nama tabel yang benar di database
    protected $table = 'production_requests';

    // Bersihkan fillable, sisakan hanya kolom-kolom hasil skema database baru
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
        'status',
        'reject_remark'
    ];

    /**
     * Relasi ke master data User (Pembuat Request)
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Relasi ke master data Sparepart Engineering
     * FIX: Menghubungkan foreign key 'sparepart_id' di tabel ini langsung ke primary key 'id' (integer) di master sparepart
     */
    public function sparepart()
    {
        return $this->belongsTo(\App\Models\ListSparepartEng::class, 'sparepart_id', 'id');
    }

    /**
     * Relasi ke master data Line Produksi
     */
    public function lineProduction()
    {
        return $this->belongsTo(\App\Models\Production\ListLineProduction::class, 'list_line_production_id');
    }
}