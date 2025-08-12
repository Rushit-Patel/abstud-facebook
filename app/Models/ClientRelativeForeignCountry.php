<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientRelativeForeignCountry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'relative_relationship',
        'relative_country',
        'visa_type',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function getRelationship()
    {
        return $this->belongsTo(TypeOfRelative::class, 'relative_relationship', 'id');
    }

    public function getCountry()
    {
        return $this->belongsTo(ForeignCountry::class, 'relative_country', 'id');
    }

    public function getTypeOfVisa()
    {
        return $this->belongsTo(OtherVisaType::class, 'visa_type', 'id');
    }
}
