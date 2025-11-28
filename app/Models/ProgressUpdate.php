<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressUpdate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'progress_percentage',
        'notes',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'progress_percentage' => 'integer',
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
     * Relationship with user who updated the progress
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get progress label based on percentage
     */
    public function getProgressLabelAttribute(): string
    {
        if ($this->progress_percentage === 0) {
            return 'Menunggu Konfirmasi';
        } elseif ($this->progress_percentage < 25) {
            return 'Persiapan';
        } elseif ($this->progress_percentage < 50) {
            return 'Desain';
        } elseif ($this->progress_percentage < 75) {
            return 'Development';
        } elseif ($this->progress_percentage < 100) {
            return 'Testing & Revisi';
        } else {
            return 'Selesai';
        }
    }

    /**
     * Check if this is the final progress update (100%)
     */
    public function isFinalUpdate(): bool
    {
        return $this->progress_percentage === 100;
    }

    /**
     * Scope for recent updates
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
