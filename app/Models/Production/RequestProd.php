<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Engineering\HistoryApproval;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RequestProd extends Model
{
    use HasFactory;

    // Mengarah langsung ke tabel milik produksi
    protected $table = 'production_requests';

    // 🎯 DAFTAR KOLOM YANG BOLEH DIISI (Mass Assignment)
    protected $fillable = [
        'list_line_production_id', // Relasi ke ID tabel list_line_productions
        'sparepart_id',            // Relasi ke ID tabel spareparts
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
     * RELASI: Menghubungkan otomatis ke data Master Line Production
     * (Berada di folder yang sama: App\Models\Production)
     */
    public function lineProduction()
    {
        return $this->belongsTo(ListLineProduction::class, 'list_line_production_id');
    }

    /**
     * RELASI: Menghubungkan otomatis ke data Master Sparepart 
     * (Menggunakan model ListSparepartEng di folder App\Models)
     */
    public function sparepart()
    {
        return $this->belongsTo(\App\Models\ListSparepartEng::class, 'sparepart_id');
    }

    /**
     * RELASI: Satu request bisa punya banyak history/audit trail
     */
    public function history()
    {
        return $this->hasMany(HistoryApproval::class, 'request_no', 'request_no');
    }

    /**
     * 💡 FITUR TAMBAHAN: ACCESSORS UNTUK URL TANDA TANGAN OLEH SYSTEM
     */
    public function getProductionSignatureUrlAttribute()
    {
        if (!$this->production_signature) return null;
        
        if (filter_var($this->production_signature, FILTER_VALIDATE_URL)) {
            return $this->production_signature;
        }
        
        return asset('storage/' . $this->production_signature);
    }

    public function getStaffSignatureUrlAttribute()
    {
        if (!$this->staff_signature) return null;
        return asset('storage/' . $this->staff_signature);
    }

    public function getSpvSignatureUrlAttribute()
    {
        if (!$this->spv_signature) return null;
        return asset('storage/' . $this->spv_signature);
    }
}