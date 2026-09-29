<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'driver_id',
        'tanggal_pemesanan',
        'tanggal_mulai',
        'tanggal_selesai',
        'tujuan',
        'keperluan',
        'jumlah_penumpup',
        'status_persetujuan',
        'catatan_persetujuan',
        'tanggal_diseputujui',
    ];

    protected $casts = [
        'tanggal_pemesanan'    => 'date',
        'tanggal_mulai'        => 'date',
        'tanggal_selesai'      => 'date',
        'tanggal_diseputujui'  => 'datetime',
    ];

    // ===============================================
    // ELOQUENT RELATIONSHIPS
    // ===============================================

    /**
     * Booking milik satu User (pemohon).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Booking untuk satu Vehicle.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Booking ditangani satu Driver.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    // ===============================================
    // SCOPES
    // ===============================================

    public function scopePending($query)
    {
        return $query->where('status_persetujuan', 'pending');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status_persetujuan', 'disetujui');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status_persetujuan', 'selesai');
    }

    // ===============================================
    // ACCESSORS / HELPERS
    // ===============================================

    /**
     * Durasi pemakaian dalam hari (inklusif).
     */
    public function getDurasiAttribute(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    /**
     * Warna badge status.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status_persetujuan) {
            'pending'   => 'yellow',
            'disetujui' => 'green',
            'ditolak'   => 'red',
            'selesai'   => 'blue',
            default     => 'gray',
        };
    }

    /**
     * Label status versi Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_persetujuan) {
            'pending'   => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            'selesai'   => 'Selesai',
            default     => ucfirst($this->status_persetujuan),
        };
    }
}