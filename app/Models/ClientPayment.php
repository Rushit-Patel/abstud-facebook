<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientPayment extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_id',
            'client_lead_id',
            'invoice_id',
            'amount',
            'payment_mode',
            'remarks',
            'added_by',
            'created_by',
            'payment_receipt',
            'gst',
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
    public function CreatedByOwner()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function getInvoice()
    {
        return $this->belongsTo(ClientInvoice::class, 'invoice_id', 'id');
    }

    public function getPaymentMode()
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode', 'id');
    }

}
