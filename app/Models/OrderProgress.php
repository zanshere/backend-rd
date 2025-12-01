<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProgress extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'order_progress';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'title',
        'description',
        'percentage',
        'status',
        'started_at',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'percentage' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'Belum Dimulai',
            'in_progress' => 'Dalam Pengerjaan',
            'completed' => 'Selesai',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'not_started' => 'gray',
            'in_progress' => 'blue',
            'completed' => 'green',
            default => 'gray',
        };
    }

    /**
     * Check if this is the final progress update (100%)
     */
    public function isFinalUpdate(): bool
    {
        return $this->percentage === 100;
    }

    /**
     * Scope for recent updates
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for completed updates
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for in progress updates
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Get progress label based on percentage
     */
    public function getProgressLabelAttribute(): string
    {
        if ($this->percentage === 0) {
            return 'Menunggu Konfirmasi';
        } elseif ($this->percentage < 25) {
            return 'Analisis Kebutuhan';
        } elseif ($this->percentage < 50) {
            return 'Desain & Wireframe';
        } elseif ($this->percentage < 75) {
            return 'Development';
        } elseif ($this->percentage < 100) {
            return 'Testing & Revisi';
        } else {
            return 'Selesai & Delivery';
        }
    }
}
