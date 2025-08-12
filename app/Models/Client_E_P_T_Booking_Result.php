<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Client_E_P_T_Booking_Result extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'client_e_p_t_booking_results';
        protected $fillable = [
            'client_e_p_t_booking_result_id',
            'exam_modual_id',
            'score',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
     // Belongs to Booking
    public function booking()
    {
        return $this->belongsTo(ClientEnglishProficiencyTestBooking::class, 'client_e_p_t_booking_result_id');
    }

    // Belongs to Module
    public function modual()
    {
        return $this->belongsTo(EnglishProficiencyTestModual::class, 'exam_modual_id');
    }

}
