<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnglishProficiencyTestModual extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'minimum_score',
        'maximum_score',
        'range_score',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope: Get only active lead types
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Active' : 'Inactive';
    }

    public function test()
    {
        return $this->belongsTo(EnglishProficiencyTest::class, 'english_proficiency_tests_id');
    }

    public function bookingResult()
    {
        return $this->hasOne(Client_E_P_T_Booking_Result::class, 'exam_modual_id', 'id')
            ->where('client_e_p_t_booking_result_id', request()->route('id'));
    }

    // public function MockTestResult()
    // {
    //     return $this->hasOne(MockTestStudentResult::class, 'modual_id', 'id')
    //         ->where('mock_test_student_id', request()->route('client'));
    // }

    public function mockTestResult()
    {
        return $this->hasOne(MockTestStudentResult::class, 'modual_id', 'id')
            ->whereHas('mockTestStudent', function ($query) {
                $query->where('mock_test_id', request()->route('id'))
                    ->where('client_coaching_student_id', request()->route('client'));
            });
    }

    public function results()
    {
        return $this->hasMany(Client_E_P_T_Booking_Result::class, 'exam_modual_id', 'id');
    }


}
