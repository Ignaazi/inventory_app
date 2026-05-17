<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryApproval extends Model
{
    use HasFactory;

    // Definisikan nama tabel secara eksplisit karena berada di dalam sub-folder namespace
    protected $table = 'history_approvals';

    protected $fillable = [
        'request_no',
        'sparepart_name',
        'sap_code',
        'qty_req',
        'line_machine',
        'requestor',
        'production_signature', // Tetap terdaftar di fillable
        'approved_by',
        'signature_image',      // Untuk tanda tangan Engineering
        'status',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * RELASI BARU (SUDAH DIUPDATE)
     * Menghubungkan tabel history_approvals ke tabel production_requests via model RequestProd
     */
    public function productionRequest()
    {
        // Diarahkan langsung ke namespace model RequestProd lu yang benar!
        return $this->belongsTo('App\Models\Production\RequestProd', 'request_no', 'request_no');
    }
}