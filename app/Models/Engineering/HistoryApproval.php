<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;               // Mengarah ke model User Anda
use App\Models\ListSparepartEng;   // 🎯 UPDATE: Import model sparepart yang benar

class HistoryApproval extends Model
{
    use HasFactory;

    protected $table = 'history_approvals';

    protected $fillable = [
        'request_no',
        'sparepart_id',    // 🎯 Foreign Key yang merujuk ke sparepart_id di tabel spareparts
        'sap_code',
        'qty_req',
        'line_machine',
        'user_id',         // Foreign Key untuk relasi user (NIK & Name)
        
        // 1. Bagian Staff Engineering
        'approved_by',     // Nama Staff
        'staff_signature', 
        
        // 2. Bagian SPV Engineering
        'spv_name',
        'spv_signature',
        
        // 3. Status
        'status',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel users (Mengetahui NIK dan Nama Requestor secara rinci)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke tabel spareparts (ListSparepartEng)
     */
    public function sparepart()
    {
        /**
         * 🎯 FIX: Karena primary key ListSparepartEng adalah 'sparepart_id',
         * kita definisikan relasinya secara eksplisit:
         * Parameter 2: Foreign Key di tabel history_approvals ('sparepart_id')
         * Parameter 3: Owner Key / Primary Key di tabel spareparts ('sparepart_id')
         */
        return $this->belongsTo(ListSparepartEng::class, 'sparepart_id', 'sparepart_id');
    }

    /**
     * Relasi ke tabel production_requests
     */
    public function productionRequest()
    {
        return $this->belongsTo(\App\Models\Production\RequestProd::class, 'request_no', 'request_no');
    }
}