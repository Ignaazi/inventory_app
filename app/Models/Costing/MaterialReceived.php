<?php

namespace App\Models\Costing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
// PERBAIKAN: Arahkan ke subfolder Engineering dan panggil kelas PurchaseRequestEng
use App\Models\Engineering\PurchaseRequestEng; 

class MaterialReceived extends Model
{
    protected $table = 'material_received';
    
    protected $fillable = [
        'no_mr',
        'purchase_request_id',
        'user_id',
        'qty_received',
        'qty_status',
        'remark',
        'status',
        'prepared_signature',
        'checked_signature',
        'approved_signature'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $latest = self::orderBy('id', 'desc')->first();
            $number = $latest ? ((int) substr($latest->no_mr, 2)) + 1 : 1;
            $model->no_mr = 'MR' . str_pad($number, 6, '0', STR_PAD_LEFT);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * PERBAIKAN: Hubungkan relasi ke model PurchaseRequestEng
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequestEng::class, 'purchase_request_id');
    }
}