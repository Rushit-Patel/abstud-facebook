<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'date',
        'name',
        'mobile_no',
        'email_id',
        'call_status',
        'purpose',
        'country',
        'coaching',
        'branch',
        'lead_type',
        'assign_to',
        'source',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
