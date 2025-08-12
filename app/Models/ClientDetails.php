<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClientDetails extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'client_code',
        'first_name',
        'middle_name',
        'last_name',
        'mobile_no',
        'country_code',
        'email_id',
        'branch',
        'country',
        'state',
        'city',
        'whatsapp_no',
        'whatsapp_country_code',
        'source',
        'address',
        'lead_type',
        'gender',
        'maratial_status',
        'date_of_birth',
        'client_profile_photo',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function generateClientCode()
    {
        $branch = Branch::find($this->branch);
        if ($branch) {
            $this->client_code = $branch->branch_code . '_' . $this->id;
            $this->save();
        }
    }

    public function leads()
    {
        return $this->hasMany(ClientLead::class, 'client_id');
    }

    public function getInvoice()
    {
        return $this->hasMany(ClientInvoice::class, 'client_id', 'id');
    }

    public function leadLastest()
    {
        return $this->hasOne(ClientLead::class, 'client_id')->orderBy('id', 'desc');
    }

    public function getSource()
    {
        return $this->belongsTo(Source::class, 'source', 'id');
    }

    public function educationDetails()
    {
        return $this->hasMany(EducationDetails::class, 'client_id');
    }
    public function educationDetailsLast()
    {
        return $this->hasOne(EducationDetails::class, 'client_id')->orderBy('id', 'desc');
    }
    public function passportDetails()
    {
        return $this->hasOne(ClientPassportDetails::class, 'client_id');
    }

    public function getClientRelativeDetails()
    {
        return $this->hasOne(ClientRelativeForeignCountry::class, 'client_id');
    }

    public function visaRejectionDetails()
    {
        return $this->hasMany(ClientPreviousRejection::class, 'client_id');
    }

    public function visaRejectionDetailsLatest()
    {
        return $this->hasOne(ClientPreviousRejection::class, 'client_id')->orderBy('id', 'desc');
    }

    public function anyVisitedDetails()
    {
        return $this->hasMany(ClientVisitedCountry::class, 'client_id');
    }

    public function employmentDetails()
    {
        return $this->hasMany(ClientEmploymentDetails::class, 'client_id');
    }

    public function examData()
    {
        return $this->hasMany(ClientEnglishProficiencyTest::class, 'client_id');
    }

    public function maratialStatus()
    {
        return $this->belongsTo(MaritalStatus::class, 'maratial_status', 'id');
    }

    public function getCity()
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }
    public function getState()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }
    public function getCountry()
    {
        return $this->belongsTo(Country::class, 'country', 'id');
    }

    public function getDemoCoaching()
    {
        return $this->hasMany(ClientCoachingDemoDetails::class, 'client_id');
    }

    public function documentChecklists()
    {
        return $this->hasMany(ClientDocumentCheckList::class, 'client_id');
    }

}
