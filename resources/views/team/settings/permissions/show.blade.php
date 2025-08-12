@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Permission Management', 'url' => route('team.settings.permissions.index')],
    ['title' => $permission->name]
];

// Extract module and action from permission name (module:action format)
$parts = explode(':', $permission->name);
$module = $parts[0] ?? 'general';
$action = $parts[1] ?? 'unknown';

$moduleOptions = [
    'branch' => 'Branch Management',
    'user' => 'User Management',
    'lead' => 'Lead Management',
    'follow-up' => 'Follow-up Management',
    'master-module' => 'Master Data',
    'student' => 'Student Management',
    'partner' => 'Partner Management',
    'dashboard' => 'Dashboard Access',
    'company' => 'Company Settings',
    'roles' => 'Role Management',
    'permissions' => 'Permission Management',
];
@endphp

<x-team.layout.app title="Permission Details" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        {{ $permission->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        <span class="kt-badge kt-badge-outline">{{ $moduleOptions[$module] ?? ucfirst(str_replace('-', ' ', $module)) }}</span>
                        <span class="kt-badge kt-badge-outline">{{ ucfirst(str_replace(['_', '-'], ' ', $action)) }}</span>
                        <span class="kt-badge kt-badge-outline">{{ $permission->guard_name }} guard</span>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.permissions.edit', $permission) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-edit"></i>
                        Edit Permission
                    </a>
                    <a href="{{ route('team.settings.permissions.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Permissions
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Permission Details -->
                <div class="lg:col-span-2">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Permission Information</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="space-y-6">
                                <!-- Basic Info -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="kt-form-label text-mono">Permission Name</label>
                                        <div class="kt-input bg-muted">{{ $permission->name }}</div>
                                    </div>
                                    <div>
                                        <label class="kt-form-label text-mono">Guard</label>
                                        <div class="kt-input bg-muted">{{ $permission->guard_name }}</div>
                                    </div>
                                    <div>
                                        <label class="kt-form-label text-mono">Module</label>
                                        <div class="kt-input bg-muted">
                                            <span class="kt-badge kt-badge-outline">
                                                {{ $moduleOptions[$module] ?? ucfirst(str_replace('-', ' ', $module)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="kt-form-label text-mono">Action</label>
                                        <div class="kt-input bg-muted">
                                            <span class="kt-badge kt-badge-outline">
                                                {{ ucfirst(str_replace(['_', '-'], ' ', $action)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="kt-form-label text-mono">Created</label>
                                        <div class="kt-input bg-muted">{{ $permission->created_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>

                                <!-- Display Name -->
                                @if($permission->display_name)
                                    <div>
                                        <label class="kt-form-label text-mono">Display Name</label>
                                        <div class="kt-input bg-muted">{{ $permission->display_name }}</div>
                                    </div>
                                @endif

                                <!-- Description -->
                                @if($permission->description)
                                    <div>
                                        <label class="kt-form-label text-mono">Description</label>
                                        <div class="kt-input bg-muted min-h-20">{{ $permission->description }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Roles -->
                    <div class="kt-card mt-6">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Assigned Roles ({{ $permission->roles->count() }})</h3>
                        </div>
                        <div class="kt-card-content">
                            @if($permission->roles->count() > 0)
                                <div class="space-y-3">
                                    @foreach($permission->roles as $role)
                                        <div class="flex items-center justify-between p-4 border border-border rounded-lg">
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center justify-center w-10 h-10 rounded-full 
                                                            {{ $role->name === 'Super Admin' ? 'bg-danger-light text-danger' : 
                                                               ($role->name === 'Admin' ? 'bg-warning-light text-warning' : 
                                                               ($role->name === 'Manager' ? 'bg-info-light text-info' : 'bg-secondary-light text-secondary')) }}">
                                                    <i class="ki-filled ki-shield text-lg"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-mono">{{ $role->name }}</div>
                                                    <div class="text-sm text-secondary-foreground">
                                                        {{ $role->users->count() }} users • {{ $role->permissions->count() }} permissions
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex gap-2">
                                                <a href="{{ route('team.settings.roles.show', $role) }}" 
                                                   class="kt-btn kt-btn-sm kt-btn-outline">
                                                    <i class="ki-filled ki-eye"></i>
                                                    View Role
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-12">
                                    <i class="ki-filled ki-information-2 text-6xl text-secondary-foreground mb-4"></i>
                                    <h4 class="text-lg font-medium mb-2">No Roles Assigned</h4>
                                    <p class="text-secondary-foreground mb-4">This permission is not assigned to any roles yet.</p>
                                    <a href="{{ route('team.settings.roles.index') }}" class="kt-btn kt-btn-primary">
                                        <i class="ki-filled ki-shield"></i>
                                        Manage Roles
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Statistics -->
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Statistics</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-secondary-foreground">Total Roles</span>
                                    <span class="kt-badge kt-badge-outline kt-badge-primary">
                                        {{ $permission->roles_count ?? $permission->roles->count() }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-secondary-foreground">Total Users</span>
                                    <span class="kt-badge kt-badge-outline kt-badge-secondary">
                                        {{ $permission->users_count ?? $permission->roles->sum(function($role) { return $role->users->count(); }) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-secondary-foreground">Guard Type</span>
                                    <span class="kt-badge kt-badge-outline">{{ $permission->guard_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Quick Actions</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="space-y-3">
                                <a href="{{ route('team.settings.permissions.edit', $permission) }}" 
                                   class="w-full kt-btn kt-btn-outline">
                                    <i class="ki-filled ki-edit"></i>
                                    Edit Permission
                                </a>
                                <a href="{{ route('team.settings.roles.index') }}" 
                                   class="w-full kt-btn kt-btn-outline">
                                    <i class="ki-filled ki-shield"></i>
                                    Manage Roles
                                </a>
                                <a href="{{ route('team.settings.permissions.create') }}" 
                                   class="w-full kt-btn kt-btn-outline">
                                    <i class="ki-filled ki-plus"></i>
                                    Create New Permission
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    @if(!in_array($permission->name, ['manage_users', 'view_users', 'manage_roles', 'view_roles', 'manage_permissions', 'view_dashboard']))
                        <div class="kt-card border-destructive">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title text-destructive">Danger Zone</h3>
                            </div>
                            <div class="kt-card-content">
                                <p class="text-sm text-secondary-foreground mb-4">
                                    Once you delete this permission, it will be removed from all roles and cannot be recovered.
                                </p>
                                <form action="{{ route('team.settings.permissions.destroy', $permission) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this permission? This action cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full kt-btn kt-btn-destructive">
                                        <i class="ki-filled ki-trash"></i>
                                        Delete Permission
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>
</x-team.layout.app>
