<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MockTestStudentResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mock_test_student_id',
        'modual_id',
        'score'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function mockTestStudent()
    {
        return $this->belongsTo(MockTestStudent::class, 'mock_test_student_id');
    }

    public function module()
    {
        return $this->belongsTo(EnglishProficiencyTestModual::class, 'modual_id', 'id');
    }


}
