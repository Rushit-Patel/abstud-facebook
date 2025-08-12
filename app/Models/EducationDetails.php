<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'education_level',
        'education_board',
        'language',
        'education_stream',
        'passing_year',
        'result',
        'no_of_backlog',
        'institute'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function leads()
    {
        return $this->hasMany(ClientLead::class, 'client_id');
    }

    public function getEducationLevel()
    {
        return $this->belongsTo(EducationLevel::class, 'education_level');
    }

    public function getEducationStream()
    {
        return $this->belongsTo(EducationStream::class, 'education_stream');
    }

    public function getEducationBoard()
    {
        return $this->belongsTo(EducationBoard::class, 'education_board');
    }

}
