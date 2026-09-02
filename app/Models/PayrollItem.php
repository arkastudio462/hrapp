<?php

namespace App\Models;

use Database\Factories\PayrollItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Traits\TenantAware;

class PayrollItem extends Model
{
    /** @use HasFactory<PayrollItemFactory> */
    use HasFactory, TenantAware;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payroll_id',
        'component_code',
        'amount',
        'type',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Get the payroll that owns the item.
     */
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class);
    }

    /**
     * Get the component that owns the item.
     */
    public function component(): BelongsTo
    {
        return $this->belongsTo(PayrollComponent::class, 'component_code', 'code');
    }
}
