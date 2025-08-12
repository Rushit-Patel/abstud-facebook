<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientVisitedCountry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'visited_country',
        'visited_visa_type',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function getVisitedCountry()
    {
        return $this->belongsTo(ForeignCountry::class, 'visited_country', 'id');
    }
    public function getVisitedVisaType()
    {
        return $this->belongsTo(OtherVisaType::class, 'visited_visa_type', 'id');
    }
}
