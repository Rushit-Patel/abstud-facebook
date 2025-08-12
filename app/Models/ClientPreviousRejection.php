<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientPreviousRejection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'rejection_country',
        'rejection_month_year',
        'rejection_visa_type',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    
    public function getCountry()
    {
        return $this->belongsTo(ForeignCountry::class, 'rejection_country', 'id');
    }

    public function getTypeOfVisa()
    {
        return $this->belongsTo(OtherVisaType::class, 'rejection_visa_type', 'id');
    }
}
