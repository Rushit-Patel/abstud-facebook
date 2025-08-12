@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Role Management', 'url' => route('team.settings.roles.index')],
    ['title' => $role->name]
];
@endphp

<x-team.layout.app title="Role Details" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Role: {{ $role->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        View role permissions and assigned users
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.roles.edit', $role) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-pencil"></i>
                        Edit Role
                    </a>
                    <a href="{{ route('team.settings.roles.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-left"></i>
                        Back to Roles
                    </a>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-5">
                <!-- Role Information -->
                <div class="lg:col-span-2 space-y-5">
                    <!-- Basic Information -->
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Role Information</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="text-sm font-medium text-secondary-foreground">Role Name</label>
                                    <p class="text-lg font-semibold">{{ $role->name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-secondary-foreground">Guard</label>
                                    <p class="text-base">
                                        <span class="kt-badge kt-badge-outline kt-badge-info">{{ $role->guard_name }}</span>
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-secondary-foreground">Created</label>
                                    <p class="text-base">{{ $role->created_at->format('M d, Y \a\t H:i') }}</p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-secondary-foreground">Last Updated</label>
                                    <p class="text-base">{{ $role->updated_at->format('M d, Y \a\t H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions -->
                    @if($role->permissions->isNotEmpty())
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title">Permissions ({{ $role->permissions->count() }})</h3>
                            </div>
                            <div class="kt-card-content">
                                @php
                                    $groupedPermissions = $role->permissions->groupBy(function($permission) {
                                        $parts = explode('_', $permission->name);
                                        return $parts[0] === 'manage' || $parts[0] === 'view' || $parts[0] === 'create' || $parts[0] === 'edit' || $parts[0] === 'delete' 
                                            ? $parts[1] ?? $parts[0] 
                                            : $parts[0];
                                    });
                                @endphp
                                
                                <div class="space-y-4">
                                    @foreach($groupedPermissions as $category => $permissions)
                                        <div>
                                            <h4 class="text-sm font-medium text-secondary-foreground mb-2">
                                                {{ ucfirst(str_replace('_', ' ', $category)) }}
                                            </h4>
                                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-2">
                                                @foreach($permissions as $permission)
                                                    <div class="flex items-center gap-2 p-2 bg-light rounded">
                                                        <i class="ki-filled ki-check-circle text-success text-sm"></i>
                                                        <span class="text-sm">{{ ucfirst(str_replace('_', ' ', $permission->name)) }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Users with this Role -->
                    @if($role->users->isNotEmpty())
                        <div class="kt-card">
                            <div class="kt-card-header">
                                <h3 class="kt-card-title">Users with this Role ({{ $role->users->count() }})</h3>
                            </div>
                            <div class="kt-card-content">
                                <div class="space-y-3">
                                    @foreach($role->users as $user)
                                        <div class="flex items-center justify-between p-3 bg-light rounded">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-primary-light text-primary rounded-full flex items-center justify-center">
                                                    <i class="ki-filled ki-profile-circle text-sm"></i>
                                                </div>
                                                <div>
                                                    <p class="font-medium">{{ $user->name }}</p>
                                                    <p class="text-sm text-secondary-foreground">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="kt-badge {{ $user->is_active ? 'kt-badge-success' : 'kt-badge-danger' }}">
                                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                                <a href="{{ route('team.settings.users.show', $user) }}" 
                                                   class="kt-btn kt-btn-sm kt-btn-secondary kt-btn-icon">
                                                    <i class="ki-filled ki-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-5">
                    <!-- Statistics -->
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Statistics</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Total Permissions</span>
                                    <span class="kt-badge kt-badge-primary">{{ $role->permissions->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Assigned Users</span>
                                    <span class="kt-badge kt-badge-info">{{ $role->users_count }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Guard Type</span>
                                    <span class="kt-badge kt-badge-outline kt-badge-secondary">{{ $role->guard_name }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">System Role</span>
                                    <span class="kt-badge {{ in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'Staff']) ? 'kt-badge-warning' : 'kt-badge-secondary' }}">
                                        {{ in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'Staff']) ? 'Yes' : 'No' }}
                                    </span>
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
                            <div class="space-y-2">
                                <a href="{{ route('team.settings.roles.edit', $role) }}" 
                                   class="kt-btn kt-btn-secondary kt-btn-sm w-full">
                                    <i class="ki-filled ki-pencil"></i>
                                    Edit Role
                                </a>
                                <a href="{{ route('team.settings.users.index', ['role' => $role->name]) }}" 
                                   class="kt-btn kt-btn-info kt-btn-sm w-full">
                                    <i class="ki-filled ki-people"></i>
                                    View Users
                                </a>
                                @if(!in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'Staff']))
                                    <button onclick="confirmDelete()" 
                                            class="kt-btn kt-btn-danger kt-btn-sm w-full">
                                        <i class="ki-filled ki-trash"></i>
                                        Delete Role
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    @if(!in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'Staff']))
    @push('scripts')
    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this role? This action cannot be undone and will affect all users with this role.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("team.settings.roles.destroy", $role) }}';
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endpush
    @endif
</x-team.layout.app>
