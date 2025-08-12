<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory ,SoftDeletes;

    protected $fillable = [
        'branch_code',
        'branch_name',
        'address',
        'city',
        'state',
        'country',
        'country_id',
        'state_id',
        'city_id',
        'postal_code',
        'phone',
        'email',
        'map_link',
        'timezone',
        'manager_name',
        'is_main_branch',
        'is_active',
    ];

    protected $casts = [
        'is_main_branch' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Users belonging to this branch
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all batches belonging to this branch
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    /**
     * Scope: Get only active branches
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Generate unique branch code
     */
    public static function generateBranchCode(): string
    {
        $lastBranch = self::latest('id')->first();
        $lastId = $lastBranch ? (int) substr($lastBranch->branch_code, 2) : 0;
        return 'BR' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the main branch
     */
    public static function getMainBranch()
    {
        return self::where('is_main_branch', true)->first();
    }

    /**
     * Get the associated country
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the associated state
     */
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the associated city
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
