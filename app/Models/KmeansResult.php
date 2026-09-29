<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KmeansResult extends Model
{
    use HasFactory;

    protected $table = 'kmeans_results';

    protected $fillable = [
        'vehicle_id',
        'cluster_label',
        'cluster_name',
        'frequency',
        'duration',
        'intensity',
        'distance',
        'iteration',
    ];

    protected $casts = [
        'frequency' => 'float',
        'duration'  => 'float',
        'intensity' => 'float',
        'distance'  => 'float',
    ];

    // ===============================================
    // ELOQUENT RELATIONSHIPS
    // ===============================================

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // ===============================================
    // ACCESSORS
    // ===============================================

    /**
     * Warna badge untuk cluster.
     */
    public function getClusterColorAttribute(): string
    {
        return match ($this->cluster_label) {
            0 => 'red',    // Tinggi
            1 => 'yellow', // Sedang
            2 => 'green',  // Rendah
            default => 'gray',
        };
    }
}