<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Intake extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'month',
        'year',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function getCountry()
    {
        return $this->hasOne(ForeignCountry::class, 'id');
    }

    /**
     * Scope: Get only active lead types
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
