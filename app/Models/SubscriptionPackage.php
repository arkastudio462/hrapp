<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPackage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'price_monthly',
        'price_yearly',
        'max_employees',
        'max_storage_gb',
        'features',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'max_employees' => 'integer',
            'max_storage_gb' => 'integer',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the tenants using this package.
     */
    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'package_id');
    }

    /**
     * Get the formatted monthly price.
     */
    public function getFormattedMonthlyPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_monthly, 0, ',', '.');
    }

    /**
     * Get the formatted yearly price.
     */
    public function getFormattedYearlyPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price_yearly, 0, ',', '.');
    }

    /**
     * Check if the package is the free trial.
     */
    public function isFreeTrial(): bool
    {
        return $this->price_monthly === 0 && $this->price_yearly === 0;
    }
}
