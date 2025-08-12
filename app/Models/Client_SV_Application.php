<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client_SV_Application extends Model
{
    use HasFactory, SoftDeletes;
        protected $fillable = [
            'client_id',
            'client_lead_reg_id',
            'date',
            'country',
            'university',
            'campus',
            'credentials',
            'program',
            'application_type',
            'intake',
            'application_through',
            'added_by',
            'remarks',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function clientLeadDetails()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id', 'id');
    }

    public function clientLeadRegistration()
    {
        return $this->belongsTo(ClientLeadRegistration::class, 'client_lead_reg_id', 'id');
    }
}
