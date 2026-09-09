<?php

namespace App\Models;

use Database\Factories\PayrollComponentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollComponent extends Model
{
    /** @use HasFactory<PayrollComponentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'type',
        'calculation_type',
        'default_value',
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
            'default_value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the payroll items using this component.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'component_code', 'code');
    }
}
