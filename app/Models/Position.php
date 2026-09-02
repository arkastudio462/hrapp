<?php

namespace App\Models;

use Database\Factories\PositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Traits\TenantAware;

class Position extends Model
{
    /** @use HasFactory<PositionFactory> */
    use HasFactory, TenantAware;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'level',
        'salary_grade',
    ];

    /**
     * Get the employees with this position.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
