<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientInvoice extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_id',
            'client_lead_id',
            'invoice_date',
            'service_id',
            'total_amount',
            'discount',
            'payable_amount',
            'billing_company_id',
            'added_by',
            'due_date',
            'due_amount',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function AddedByOwner()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function clientLead()
    {
        return $this->belongsTo(ClientLead::class, 'client_lead_id', 'id');
    }

    public function clientLeadDetails()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id', 'id');
    }

    public function getService()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function getBillingcompany()
    {
        return $this->belongsTo(BillingCompany::class,  'billing_company_id', 'id');
    }

    public function getPayments()
    {
        return $this->hasMany(ClientPayment::class, 'invoice_id');
    }

}
