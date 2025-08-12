<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientEnglishProficiencyTestScore extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_test_id',
            'exam_modual_id',
            'score'
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function getTestScoreName()
    {
        return $this->belongsTo(EnglishProficiencyTestModual::class, 'exam_modual_id');
    }
}
