<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
class ClientEnglishProficiencyTest extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
        'client_id',
        'client_lead_id',
        'exam_id',
        'exam_date',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function exam_dataScore()
    {
        return $this->hasMany(ClientEnglishProficiencyTestScore::class, 'client_test_id');
    }

    public function getExam()
    {
        return $this->belongsTo(EnglishProficiencyTest::class, 'exam_id');
    }

}
