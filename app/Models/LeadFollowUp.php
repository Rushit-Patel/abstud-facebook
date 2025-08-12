<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadFollowUp extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_lead_id',
        'followup_date',
        'remarks',
        'status',
        'communication',
        'updated_by',
        'created_by',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'followup_date' => 'date',
    ];

    public function clientLead()
    {
        return $this->belongsTo(ClientLead::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
