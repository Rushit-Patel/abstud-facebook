<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientEnglishProficiencyTestBooking extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_id',
            'client_lead_id',
            'client_coaching_id',
            'exam_way',
            'english_proficiency_test_id',
            'exam_mode_id',
            'exam_date',
            'exam_center',
            'result_date',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function clientLead()
    {
        return $this->belongsTo(ClientLead::class, 'client_lead_id', 'id');
    }

    public function clientLeadDetails()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id', 'id');
    }

    public function clientCoaching()
    {
        return $this->belongsTo(ClientCoaching::class, 'client_coaching_id', 'id');
    }
    public function englishProficiencyTest()
    {
        return $this->belongsTo(EnglishProficiencyTest::class, 'english_proficiency_test_id');
    }

    public function results()
    {
        return $this->hasMany(Client_E_P_T_Booking_Result::class, 'client_e_p_t_booking_result_id', 'id');
    }

}
