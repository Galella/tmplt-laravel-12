<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Office extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'address',
        'phone',
        'email',
        'parent_office_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'assigned_date' => 'date',
    ];

    /**
     * Get the parent office for this office (for hierarchical relationship).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Office::class, 'parent_office_id');
    }

    /**
     * Get the child offices for this office (for hierarchical relationship).
     */
    public function children()
    {
        return $this->hasMany(Office::class, 'parent_office_id');
    }

    /**
     * Get the users assigned to this office.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'office_user')
                    ->withPivot(['position', 'assigned_date', 'is_primary', 'is_active'])
                    ->withTimestamps();
    }

    /**
     * Scope to get only active offices.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get offices by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get headquarters.
     */
    public function scopeHeadquarters($query)
    {
        return $query->where('type', 'headquarters');
    }

    /**
     * Scope to get regional offices.
     */
    public function scopeRegional($query)
    {
        return $query->where('type', 'regional');
    }

    /**
     * Scope to get area offices.
     */
    public function scopeArea($query)
    {
        return $query->where('type', 'area');
    }

    /**
     * Check if this office is headquarters.
     */
    public function isHeadquarters(): bool
    {
        return $this->type === 'headquarters';
    }

    /**
     * Check if this office is regional.
     */
    public function isRegional(): bool
    {
        return $this->type === 'regional';
    }

    /**
     * Check if this office is area office.
     */
    public function isArea(): bool
    {
        return $this->type === 'area';
    }
}
