<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientLead extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'client_id',
        'client_date',
        'lead_type',
        'purpose',
        'country',
        'second_country',
        'coaching',
        'branch',
        'assign_owner',
        'added_by',
        'source',
        'tag',
        'status',
        'sub_status',
        'remark',
        'genral_remark',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id');
    }

    public function getBranch()
    {
        return $this->belongsTo(Branch::class, 'branch', 'id');
    }

    public function assignedOwner()
    {
        return $this->belongsTo(User::class, 'assign_owner', 'id');
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

    public function getStatus()
    {
        return $this->belongsTo(LeadStatus::class, 'status','id');
    }

    public function getSubStatus()
    {
        return $this->belongsTo(LeadSubStatus::class, 'sub_status','id');
    }

    public function getFollowUps()
    {
        return $this->hasMany(LeadFollowUp::class, 'client_lead_id', 'id');
    }

    public function getRegister()
    {
        return $this->hasMany(ClientLeadRegistration::class, 'client_lead_id', 'id');
    }

    public function examData()
    {
        return $this->hasMany(ClientEnglishProficiencyTest::class, 'client_lead_id');
    }

    public function getSource()
    {
        return $this->belongsTo(Source::class, 'source', 'id');
    }
    public function getLeadType()
    {
        return $this->belongsTo(LeadType::class, 'lead_type', 'id');
    }

}
