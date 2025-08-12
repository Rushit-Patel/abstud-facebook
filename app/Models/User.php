<?php

namespace App\Models;

use App\Traits\GuardHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, GuardHelpers;

    protected $guard_name = 'web';

    // Use integer primary key and username for authentication
    protected $primaryKey = 'id'; // Use integer ID as primary key
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name', 'email', 'username', 'password', 'base_password', 'phone', 'profile_photo', 'is_active', 'branch_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     * Keep this as username for login, but use ID for sessions
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Get the unique identifier for the user.
     * This will return the username for authentication
     */
    public function getAuthIdentifier()
    {
        return $this->username;
    }

    /**
     * Get the primary key for sessions (integer ID)
     */
    public function getKey()
    {
        return $this->getAttribute($this->getKeyName());
    }

    // Rest of your methods...
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the role configurations for this user
     */
    public function roleConfigurations()
    {
        return $this->hasMany(UserRoleConfiguration::class);
    }

    /**
     * Get configuration for a specific role and permission type
     */
    public function getRoleConfiguration($roleId, $permissionType)
    {
        $config = $this->roleConfigurations()
            ->where('role_id', $roleId)
            ->where('permission_type', $permissionType)
            ->first();

        return $config ? $config->configuration_data : [];
    }

    /**
     * Get all branch IDs user has access to based on role configurations
     */
    public function getAccessibleBranchIds()
    {
        $branchIds = [];

        // Get from role configurations
        $configs = $this->roleConfigurations()
            ->where('permission_type', 'show-all')
            ->get();

        foreach ($configs as $config) {
            $branchIds = array_merge($branchIds, $config->configuration_data ?? []);
        }

        // Add user's primary branch
        if ($this->branch_id) {
            $branchIds[] = $this->branch_id;
        }

        return array_unique($branchIds);
    }

    /**
     * Get all country IDs user has access to based on role configurations
     */
    public function getAccessibleCountryIds()
    {
        $countryIds = [];

        $configs = $this->roleConfigurations()
            ->where('permission_type', 'country')
            ->get();

        foreach ($configs as $config) {
            $countryIds = array_merge($countryIds, $config->configuration_data ?? []);
        }

        return array_unique($countryIds);
    }

    /**
     * Get all purpose IDs user has access to based on role configurations
     */
    public function getAccessiblePurposeIds()
    {
        $purposeIds = [];

        $configs = $this->roleConfigurations()
            ->where('permission_type', 'purpose')
            ->get();

        foreach ($configs as $config) {
            $purposeIds = array_merge($purposeIds, $config->configuration_data ?? []);
        }

        return array_unique($purposeIds);
    }

    /**
     * Get all coaching IDs user has access to based on role configurations
     */
    public function getAccessibleCoachingIds()
    {
        $coachingIds = [];

        $configs = $this->roleConfigurations()
            ->where('permission_type', 'coaching')
            ->get();

        foreach ($configs as $config) {
            $coachingIds = array_merge($coachingIds, $config->configuration_data ?? []);
        }

        return array_unique($coachingIds);
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    public function getAllUserPermissions(): array
    {
        return $this->getAllGuardPermissions()->pluck('name')->toArray();
    }

    public function getRoleDisplayName(): string
    {
        $roles = $this->getGuardRoles();

        if ($roles->isEmpty()) {
            return ucfirst($this->user_type ?? 'User');
        }

        return $roles->first()->name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithRole($query, string $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role)->where('guard_name', 'web');
        });
    }


    // for task management
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function updatedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'updated_by');
    }

    public function taskAssignments(): HasMany
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function assignedTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_assignments')
                    ->withPivot([
                        'assigned_by', 'role', 'assignment_notes', 'assigned_at',
                        'accepted_at', 'completed_at', 'estimated_hours', 'logged_hours',
                        'is_active', 'notifications_enabled'
                    ])
                    ->withTimestamps()
                    ->wherePivot('is_active', true);
    }

    public function taskComments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function taskAttachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class, 'uploaded_by');
    }

    public function taskTimeLogs(): HasMany
    {
        return $this->hasMany(TaskTimeLog::class);
    }

    public function taskActivityLogs(): HasMany
    {
        return $this->hasMany(TaskActivityLog::class);
    }

    // Scopes for user task queries
    public function scopeWithActiveTasks(Builder $query): Builder
    {
        return $query->whereHas('assignedTasks', function($q) {
            $q->where('is_archived', false)
            ->whereHas('status', fn($status) => $status->where('is_completed', false));
        });
    }

    public function getActiveTasksCountAttribute(): int
    {
        return $this->assignedTasks()
                    ->where('is_archived', false)
                    ->whereHas('status', fn($q) => $q->where('is_completed', false))
                    ->count();
    }

    public function getCompletedTasksCountAttribute(): int
    {
        return $this->assignedTasks()
                    ->whereHas('status', fn($q) => $q->where('is_completed', true))
                    ->count();
    }

    public function getOverdueTasksCountAttribute(): int
    {
        return $this->assignedTasks()
                    ->where('due_date', '<', now())
                    ->whereHas('status', fn($q) => $q->where('is_completed', false))
                    ->count();
    }
}
