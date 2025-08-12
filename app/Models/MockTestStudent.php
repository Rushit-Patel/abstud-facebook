<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MockTestStudent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mock_test_id',
        'client_coaching_student_id',
        'result_date'
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function getMockTestResuals()
    {
        return $this->hasMany(MockTestStudentResult::class, 'mock_test_student_id', 'id');
    }

}
