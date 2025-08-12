<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientLeadRegistration extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_id',
            'client_lead_id',
            'reg_date',
            'added_by',
            'reg_owner',
            'assign_owner',
            'purpose',
            'country',
            'coaching',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function assignedOwner()
    {
        return $this->belongsTo(User::class, 'assign_owner', 'id');
    }

    public function clientLead()
    {
        return $this->belongsTo(ClientLead::class, 'client_lead_id', 'id');
    }

    public function clientLeadDetails()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id', 'id');
    }

    public function clientCoachingReg()
    {
        return $this->hasMany(ClientCoaching::class, 'client_lead_reg_id', 'id');
    }

    public function getPurpose()
    {
        return $this->belongsTo(Purpose::class, 'purpose', 'id');
    }
    public function getForeignCountry()
    {
        return $this->belongsTo( ForeignCountry::class, 'country', 'id');
    }
    public function getCoaching()
    {
        return $this->belongsTo( Coaching::class, 'coaching', 'id');
    }

}
