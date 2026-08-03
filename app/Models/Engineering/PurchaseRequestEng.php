<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestEng extends Model
{
    use HasFactory;

    protected $table = 'purchase_requests';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_pr',
        'user_id',
        'sparepart_id',
        'qty_pr', 
        'priority',
        'request_date',
        'expected_arrival_date',
        'destination',
        'remark',
        'status',
        'prepared_signature',  // Step 1: Role Engineering (Requester)
        'checked_signature',   // Step 2: Role Admin Engineering (Checker)
        'approved_signature',  // Step 3: Role Costing & Procurement (Approver)
    ];

    protected $casts = [
        'request_date'          => 'datetime',
        'expected_arrival_date' => 'datetime',
        'created_at'            => 'datetime',
        'updated_at'            => 'datetime',
        'qty_pr'                => 'integer', 
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
    
    /**
     * RELASI: Ke model ListSparepartEng (FIXED)
     * Menghubungkan kolom 'sparepart_id' milik PR ke Primary Key 'id' milik tabel spareparts.
     */
    public function sparepart()
    {
        return $this->belongsTo(\App\Models\ListSparepartEng::class, 'sparepart_id', 'id');
    }

    public function getPriorityBadgeAttribute()
    {
        return $this->priority === 'urgent' 
            ? 'bg-red-50 text-red-600 border-red-200 dark:bg-red-950/40 dark:text-red-400' 
            : 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300';
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'approved':
                return 'bg-emerald-50 text-emerald-600 border-emerald-200';
            case 'rejected':
                return 'bg-rose-50 text-rose-600 border-rose-200';
            default: // pending
                return 'bg-amber-50 text-amber-600 border-amber-200';
        }
    }
}