<?php

namespace App\Models\Engineering;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class DisposalEng extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan secara bersama (Shared Transaction Table)
     * @var string
     */
    protected $table = 'stock_eng_transactions';

    /**
     * Atribut yang dapat diisi melalui Mass Assignment
     * @var array<int, string>
     */
    protected $fillable = [
        'tx_id',
        'users_id',
        'stock_engs_id',
        'db_barcodes_id',
        'production_request_id',
        'tx_type',
        'qty_transaction',
        'process_type',
        'photo_path',
        'status',
        'remark'
    ];

    /**
     * Konversi tipe data otomatis saat diakses dari database
     * @var array<string, string>
     */
    protected $casts = [
        'qty_transaction' => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * NIK PIC hilang disimpan di remark karena sumbernya adalah Production OUT.
     */
    public function getNikKaryawanAttribute(): ?string
    {
        if (preg_match('/NIK KARYAWAN YANG MENGHILANGKAN:\s*([^|]+)/i', (string) $this->remark, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    // =========================================================================
    // LOCAL SCOPES (Penyederhanaan Query Builder)
    // =========================================================================

    /**
     * Scope khusus untuk memfilter transaksi bertipe DISPOSAL secara otomatis
     * Penggunaan di Controller: DisposalEng::disposal()->with(...)
     */
    public function scopeDisposal(Builder $query): Builder
    {
        return $query->where('tx_type', 'disposal');
    }

    // =========================================================================
    // ELOQUENT RELATIONSHIPS (Jembatan Relasi Data)
    // =========================================================================

    /**
     * Relasi Balik ke Model Master Stock Engineering
     * Digunakan untuk menarik data visual: Part Number & SAP Code pada Blade.
     */
    public function stockEng()
    {
        // Sesuaikan target class path jika model StockEng Anda berada di folder / sub-folder lain
        return $this->belongsTo(\App\Models\StockEng::class, 'stock_engs_id', 'id')
                    ->withDefault([
                        'part_no'  => 'N/A',
                        'sap_code' => '-'
                    ]);
    }

    /**
     * Relasi Balik ke Model User (Aktor Eksekutor Lapangan)
     * Digunakan untuk mendeteksi siapa operator yang menembak barcode disposal.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id')
                    ->withDefault([
                        'name' => 'System Automated'
                    ]);
    }

    /**
     * Relasi Balik ke Master Kode Unik (Tabel db_barcodes)
     * Digunakan jika sewaktu-waktu ingin melacak spesifikasi lot/serial number barcode asal.
     */
    public function barcode()
    {
        return $this->belongsTo(\App\Models\DbBarcode::class, 'db_barcodes_id', 'id');
    }
}
