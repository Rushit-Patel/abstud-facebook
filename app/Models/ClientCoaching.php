<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientCoaching extends Model
{
    use HasFactory, SoftDeletes;
        protected $fillable = [
            'client_id',
            'client_lead_id',
            'client_lead_reg_id',
            'branch_id',
            'coaching_id',
            'batch_id',
            'joining_date',
            'faculty',
            'coaching_length',
            'added_by',
            'is_complete_coaching',
            'is_drop_coaching',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function clientLead()
    {
        return $this->belongsTo(ClientLead::class, 'client_lead_id', 'id');
    }

    public function clientLeadDetails()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id', 'id');
    }

    public function getFaculty()
    {
        return $this->belongsTo(User::class, 'faculty', 'id');
    }

    public function getBatch()
    {
        return $this->belongsTo(Batch::class, 'batch_id', 'id');
    }

    public function getCoaching()
    {
        return $this->belongsTo(Coaching::class, 'coaching_id', 'id');
    }

    public function getAttendances()
    {
        return $this->hasMany(ClientCoachingAttendance::class, 'client_coaching_id', 'id');
    }

    public function getMockTestStudent()
    {
        return $this->hasOne(MockTestStudent::class, 'client_coaching_student_id', 'id')
            ->where('mock_test_id', request()->route('id'));
    }

    public function getCoachingMaterial()
    {
        return $this->hasMany(ClientCoachingMaterial::class, 'client_coaching_id', 'id');
    }

    public function clientLeadRegistration()
    {
        return $this->belongsTo(ClientLeadRegistration::class, 'client_lead_reg_id', 'id');
    }

}

