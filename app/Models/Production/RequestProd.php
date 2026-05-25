<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Engineering\HistoryApproval;

class RequestProd extends Model
{
    use HasFactory;

    // Mengarah langsung ke tabel milik produksi
    protected $table = 'production_requests';

    // 🎯 DAFTAR KOLOM YANG BOLEH DIISI (Mass Assignment)
    protected $fillable = [
        'request_no',
        'sparepart_name',
        'sap_code',
        'remark',
        'qty_req',
        'line_machine',
        'requestor',
        'status',
        // Tanda tangan & stempel Produksi
        'production_signature',
        'production_stamp',
        // Tanda tangan & stempel Engineering (Staff)
        'staff_name',
        'staff_signature',
        'staff_stamp', 
        // Tanda tangan & stempel Engineering (SPV)
        'spv_name',
        'spv_signature',
        'spv_stamp',
        // Metadata lainnya
        'approved_by',
        'signature_path',
        'reject_remark'
    ];

    /**
     * RELASI: Satu request bisa punya banyak history/audit trail
     * (Berguna untuk melacak status jika ada perubahan)
     */
    public function history()
    {
        return $this->hasMany(HistoryApproval::class, 'request_no', 'request_no');
    }
}