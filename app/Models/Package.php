<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'base_price',
        'is_custom_price',
        'features',
        'delivery_time',
        'revision_limit',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'base_price' => 'decimal:0',
        'is_custom_price' => 'boolean',
        'features' => 'array',
        'is_active' => 'boolean',
        'delivery_time' => 'integer',
        'revision_limit' => 'integer',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Package type constants
     */
    const TYPE_USAHA_KECIL = 'usaha_kecil';
    const TYPE_BISNIS_MENENGAH = 'bisnis_menengah';
    const TYPE_BISNIS = 'bisnis';
    const TYPE_E_COMMERCE = 'e_commerce';

    /**
     * Get all package types
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_USAHA_KECIL => 'Usaha Kecil',
            self::TYPE_BISNIS_MENENGAH => 'Bisnis Menengah',
            self::TYPE_BISNIS => 'Bisnis Premium',
            self::TYPE_E_COMMERCE => 'E-commerce',
        ];
    }

    /**
     * Get package type display name
     */
    public function getTypeDisplayName(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Scope active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if package has custom pricing
     */
    public function hasCustomPrice(): bool
    {
        return $this->is_custom_price;
    }

    /**
     * Get display price
     */
    public function getDisplayPriceAttribute(): string
    {
        if ($this->hasCustomPrice()) {
            return 'Custom Price';
        }

        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }

    /**
     * Get display delivery time
     */
    public function getDisplayDeliveryTimeAttribute(): string
    {
        return $this->delivery_time . ' hari';
    }

    /**
     * Get features as array
     */
    public function getFeaturesListAttribute(): array
    {
        return $this->features ?? [];
    }

    /**
     * Relationship with orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if package is available for ordering
     */
    public function isAvailable(): bool
    {
        return $this->is_active && !$this->trashed();
    }
}
