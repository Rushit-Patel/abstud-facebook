<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientCoachingMaterial extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_coaching_id',
            'material_id',
            'is_provided',
            'added_by',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

}
