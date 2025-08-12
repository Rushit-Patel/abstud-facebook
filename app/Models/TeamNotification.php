<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_type_id',
        'title',
        'message',
        'link',
        'data',
        'is_seen',
        'seen_at',
        'user_id',
        'created_by'
    ];

    protected $casts = [
        'data' => 'array',
        'is_seen' => 'boolean',
        'seen_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function notificationType()
    {
        return $this->belongsTo(TeamNotificationType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeUnseen($query)
    {
        return $query->where('is_seen', false);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Methods
    public function markAsSeen()
    {
        $this->update([
            'is_seen' => true,
            'seen_at' => now()
        ]);
    }
}