<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryApproval extends Model
{
    use HasFactory;

    protected $table = 'history_approvals';

    // Pastikan fillable mencakup semua kolom target agar tidak diblokir Laravel saat insert massal
    protected $fillable = [
        'production_request_id', 
        'request_no',
        'nik',
        'approver_name',
        'sparepart_name',
        'qty_req',
        'line_machine',
        'status',
        'remark',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Relasi ke model RequestProd (tabel production_requests)
     */
    public function productionRequest()
    {
        // Parameter kedua menegaskan foreign key di tabel history_approvals
        // Parameter ketiga menegaskan primary key di tabel production_requests
        return $this->belongsTo(\App\Models\Production\RequestProd::class, 'production_request_id', 'id');
    }
}