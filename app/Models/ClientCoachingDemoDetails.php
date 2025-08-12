<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientCoachingDemoDetails extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_id',
            'client_lead_id',
            'coaching_id',
            'batch_id',
            'demo_date',
            'assign_owner',
            'added_by',
            'status',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];


    public function clientLead()
    {
        return $this->belongsTo(ClientLead::class);
    }

    public function clientLeadDetails()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id');
    }

    public function getDemoAssignOwner()
    {
        return $this->hasOne(User::class, 'id','assign_owner');
    }

    public function getDemoBatch()
    {
        return $this->hasOne(Batch::class, 'id','batch_id');
    }

    public function getDemoCoaching()
    {
        return $this->hasOne(Coaching::class, 'id','coaching_id');
    }

  // Status Constants
    public const STATUS_PENDING = 0;
    public const STATUS_ATTENDED = 1;
    public const STATUS_CANCELLED = 2;

    // Status Labels
    public static array $statusLabels = [
        self::STATUS_PENDING => 'Demo Pending',
        self::STATUS_ATTENDED => 'Demo Attended',
        self::STATUS_CANCELLED => 'Demo Cancelled',
    ];

    // Status Colors
    public static array $statusColors = [
        self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
        self::STATUS_ATTENDED => 'bg-green-100 text-green-700',
        self::STATUS_CANCELLED => 'bg-red-100 text-red-700',
    ];

    // Accessor for status label
    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? 'Unknown';
    }

    // Accessor for status color
    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? 'bg-gray-100 text-gray-700';
    }
}
