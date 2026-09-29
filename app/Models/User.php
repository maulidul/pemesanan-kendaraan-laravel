<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data atribut.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ===============================================
    // ELOQUENT RELATIONSHIPS
    // ===============================================

    /**
     * Satu user bisa memiliki banyak booking.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ===============================================
    // HELPER METHODS
    // ===============================================

    /**
     * Cek apakah user punya role tertentu.
     */
    public function hasRole(string|array $role): bool
    {
        if (is_array($role)) {
            return in_array($this->role, $role);
        }
        return $this->role === $role;
    }

    /**
     * Label role yang lebih ramah pengguna.
     */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin'       => 'Administrator',
            'pemohon'     => 'Pemohon',
            'operasional' => 'Operasional',
            'manajemen'   => 'Manajemen / Atasan',
            default       => ucfirst($this->role),
        };
    }
}