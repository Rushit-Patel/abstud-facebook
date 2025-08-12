<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class ClientEmploymentDetails extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_id',
            'company_name',
            'designation',
            'start_date',
            'end_date',
            'no_of_year',
            'is_working',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
