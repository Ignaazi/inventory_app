<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryApproval extends Model
{
    use HasFactory;

    protected $table = 'history_approvals';

    protected $fillable = [
        'production_request_id', // Foreign Key utama terhubung ke tabel production_requests
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
     * Relasi utama ke tabel production_requests
     */
    public function productionRequest()
    {
        // Berubah menggunakan field production_request_id agar presisi sesuai migration terbaru
        return $this->belongsTo(\App\Models\Production\RequestProd::class, 'production_request_id');
    }
}