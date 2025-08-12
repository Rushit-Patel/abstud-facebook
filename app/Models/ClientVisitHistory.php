<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class ClientVisitHistory extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "client_id",
        "client_lead_id",
        "branch_id",
        "assign_to",
        "token_no",
        "date",
        "status",
        "invited_at",
        "received_at",
        "completed_at",
        "remarks"
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function getClient()
    {
        return $this->belongsTo(ClientDetails::class, 'client_id');
    }

    public function getClientLead()
    {
        return $this->belongsTo(ClientLead::class, 'client_lead_id');
    }

    public function getBranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function getAssignTo()
    {
        return $this->belongsTo(User::class, 'assign_to');
    }

    public static function generateTokenNo()
    {
        $today = date('Y-m-d');

        $lastVisit = self::whereDate('created_at', $today)
            ->whereNotNull('token_no')
            ->orderByDesc('token_no')
            ->first();

        if ($lastVisit && is_numeric($lastVisit->token_no)) {
            $nextToken = (int) $lastVisit->token_no + 1;
        } else {
            $nextToken = 1001;
        }

        return $nextToken;
    }
}
