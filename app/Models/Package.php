<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Package types
     */
    const TYPE_USAHA_KECIL = 'usaha_kecil';
    const TYPE_BISNIS_MENENGAH = 'bisnis_menengah';
    const TYPE_BISNIS = 'bisnis';
    const TYPE_E_COMMERCE = 'e_commerce';

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
        'features' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Default feature list for packages
     */
    protected $attributes = [
        'features' => '[]',
    ];

    /**
     * Get display price with currency format
     */
    public function getDisplayPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }

    /**
     * Check if package is e-commerce (custom pricing)
     */
    public function isEcommerce(): bool
    {
        return $this->type === self::TYPE_E_COMMERCE;
    }

    /**
     * Check if package has fixed pricing
     */
    public function hasFixedPrice(): bool
    {
        return !$this->isEcommerce();
    }

    /**
     * Scope for active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered packages
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('base_price');
    }

    /**
     * Relationship with orders (package can be in many orders)
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get popular packages (most ordered)
     */
    public function scopePopular($query, $limit = 4)
    {
        return $query->withCount('orders')
                    ->orderBy('orders_count', 'desc')
                    ->limit($limit);
    }

    /**
     * Get feature list as array
     */
    public function getFeatureListAttribute(): array
    {
        return $this->features ?? [];
    }

    /**
     * Check if package has specific feature
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->feature_list);
    }
}
