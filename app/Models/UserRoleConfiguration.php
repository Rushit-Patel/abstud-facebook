<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRoleConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'permission_type',
        'configuration_data'
    ];

    protected $casts = [
        'configuration_data' => 'array'
    ];

    /**
     * Get the user that owns the configuration
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role that owns the configuration
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }

    /**
     * Scope to get configurations by permission type
     */
    public function scopeByPermissionType($query, $permissionType)
    {
        return $query->where('permission_type', $permissionType);
    }

    /**
     * Scope to get configurations by user and permission type
     */
    public function scopeForUserAndPermission($query, $userId, $permissionType)
    {
        return $query->where('user_id', $userId)
                    ->where('permission_type', $permissionType);
    }

    /**
     * Get human readable permission type label
     */
    public function getPermissionTypeLabelAttribute()
    {
        return match($this->permission_type) {
            'show-all' => 'Branch Access',
            'country' => 'Country Access',  
            'purpose' => 'Purpose Access',
            'coaching' => 'Coaching Access',
            default => ucfirst(str_replace('-', ' ', $this->permission_type))
        };
    }

    /**
     * Helper method to add configuration for a user-role combination
     */
    public static function addConfiguration($userId, $roleId, $permissionType, $configurationData)
    {
        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'role_id' => $roleId,
                'permission_type' => $permissionType
            ],
            [
                'configuration_data' => $configurationData
            ]
        );
    }
}
