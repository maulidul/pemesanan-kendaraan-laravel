<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';

    protected $fillable = [
        'nama_kendaraan',
        'plat_nomor',
        'jenis_kendaraan',
        'kapasitas_penumpup',
        'status_ketersediaan',
        'keterangan',
    ];

    // ===============================================
    // ELOQUENT RELATIONSHIPS
    // ===============================================

    /**
     * Satu kendaraan bisa memiliki banyak pemesanan.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Hasil clustering K-Means untuk kendaraan ini.
     */
    public function kmeansResult()
    {
        return $this->hasOne(KmeansResult::class)->latestOfMany('iteration');
    }

    /**
     * Seluruh history clustering kendaraan ini.
     */
    public function kmeansResults(): HasMany
    {
        return $this->hasMany(KmeansResult::class);
    }

    // ===============================================
    // SCOPES
    // ===============================================

    public function scopeTersedia($query)
    {
        return $query->where('status_ketersediaan', 'Tersedia');
    }

    // ===============================================
    // ACCESSORS
    // ===============================================

    /**
     * Warna badge untuk status ketersediaan.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status_ketersediaan) {
            'Tersedia'         => 'green',
            'Tidak Tersedia'   => 'red',
            'Dalam Perbaikan'  => 'yellow',
            'Digunakan'        => 'blue',
            default            => 'gray',
        };
    }
}