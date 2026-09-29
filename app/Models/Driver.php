<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';

    protected $fillable = [
        'nama_driver',
        'nomor_telepon',
        'nomor_sim',
        'alamat',
        'status',
    ];

    // ===============================================
    // ELOQUENT RELATIONSHIPS
    // ===============================================

    /**
     * Satu driver bisa menangani banyak pemesanan.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ===============================================
    // SCOPES
    // ===============================================

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    // ===============================================
    // ACCESSORS
    // ===============================================

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Aktif'       => 'green',
            'Tidak Aktif' => 'red',
            'Cuti'        => 'yellow',
            default       => 'gray',
        };
    }
}