<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientPassportDetails extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_id',
            'passport_number',
            'passport_copy',
            'passport_expiry_date'
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
