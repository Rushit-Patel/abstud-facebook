@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => $user->name]
];
@endphp

<x-team.layout.app title="User Details" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        User Details
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        View user information and permissions
                    </div>
                </div>
                <div class="flex items-center gap-2.5">

                    <a href="{{ route('team.profile.edit', $user) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-pencil"></i>
                        Edit User
                    </a>
                    <a href="{{ route('team.dashboard') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-5">
                <!-- User Information Card -->
                <div class="lg:col-span-2">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">User Information</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="grid md:grid-cols-2 gap-5">
                                <!-- Personal Information -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Full Name</label>
                                        <p class="text-base font-medium">{{ $user->name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Email</label>
                                        <p class="text-base">{{ $user->email }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Phone</label>
                                        <p class="text-base">{{ $user->phone ?: 'Not provided' }}</p>
                                    </div>
                                </div>

                                <!-- Work Information -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Roles & Permissions</label>
                                        <div class="space-y-2">
                                            @if($user->roles->isNotEmpty())
                                                @foreach($user->roles as $role)
                                                    <div class="flex items-center gap-2">
                                                        <span class="kt-badge kt-badge-outline kt-badge-{{ $role->name === 'Super Admin' ? 'danger' : ($role->name === 'Admin' ? 'warning' : ($role->name === 'Manager' ? 'info' : 'primary')) }}">
                                                            {{ $role->name }}
                                                        </span>
                                                        @if($role->permissions->count() > 0)
                                                            <span class="text-2xs text-gray-500">
                                                                ({{ $role->permissions->count() }} permissions)
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-secondary-foreground">No roles assigned</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Username</label>
                                        <p class="text-base">{{ $user->username }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Branch</label>
                                        <p class="text-base">{{ $user->branch->branch_name ?? 'Not assigned' }}</p>
                                        @if($user->branch)
                                            <p class="text-sm text-secondary-foreground">{{ $user->branch->branch_code }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles & Permissions Card -->
                    @if($user->roles->isNotEmpty())
                    <div class="kt-card mt-5">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Roles & Permissions Configuration</h3>
                            <div class="text-sm text-gray-600">
                                User has {{ $user->roles->count() }} role{{ $user->roles->count() > 1 ? 's' : '' }} assigned
                            </div>
                        </div>
                        <div class="kt-card-content">
                            <div class="space-y-6">
                                @foreach($user->roles as $role)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-3">
                                                <span class="kt-badge kt-badge-outline kt-badge-{{ $role->name === 'Super Admin' ? 'danger' : ($role->name === 'Admin' ? 'warning' : ($role->name === 'Manager' ? 'info' : 'primary')) }}">
                                                    {{ $role->name }}
                                                </span>
                                                @if($role->permissions->count() > 0)
                                                    <span class="text-sm text-gray-500">
                                                        {{ $role->permissions->count() }} permissions
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if($role->permissions->isNotEmpty())
                                            <!-- Permissions List -->
                                            <div class="mb-4">
                                                <h6 class="text-sm font-medium text-gray-700 mb-2">Permissions:</h6>
                                                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-2">
                                                    @foreach($role->permissions as $permission)
                                                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded">
                                                            <i class="ki-filled ki-check-circle text-success text-sm"></i>
                                                            <span class="text-sm">{{ $permission->name }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Role Configurations -->
                                            @php
                                                $roleConfigs = $user->roleConfigurations->where('role_id', $role->id);
                                                $permissions = $role->permissions->pluck('name');
                                            @endphp

                                            @if($roleConfigs->isNotEmpty())
                                                <div class="border-t border-gray-200 pt-4">
                                                    <h6 class="text-sm font-medium text-gray-700 mb-3">Role Configurations:</h6>
                                                    <div class="grid md:grid-cols-2 gap-4">
                                                        @foreach($roleConfigs as $config)
                                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                                                <div class="flex items-center gap-2 mb-2">
                                                                    <i class="ki-filled ki-setting-2 text-blue-600"></i>
                                                                    <span class="text-sm font-medium text-blue-900">
                                                                        {{ ucfirst($config->permission_type) }} Access
                                                                    </span>
                                                                </div>
                                                                <div class="space-y-1">
                                                                    @if(is_array($config->configuration_data) && count($config->configuration_data) > 0)
                                                                        @foreach($config->configuration_data as $configId)
                                                                            @php
                                                                                $configName = '';
                                                                                switch($config->permission_type) {
                                                                                    case 'show-all':
                                                                                        $branch = \App\Models\Branch::find($configId);
                                                                                        $configName = $branch ? $branch->branch_name : "Branch ID: {$configId}";
                                                                                        break;
                                                                                    case 'country':
                                                                                        $country = \App\Models\ForeignCountry::find($configId);
                                                                                        $configName = $country ? $country->name : "Country ID: {$configId}";
                                                                                        break;
                                                                                    case 'purpose':
                                                                                        $purpose = \App\Models\Purpose::find($configId);
                                                                                        $configName = $purpose ? $purpose->name : "Purpose ID: {$configId}";
                                                                                        break;
                                                                                    case 'coaching':
                                                                                        $coaching = \App\Models\Coaching::find($configId);
                                                                                        $configName = $coaching ? $coaching->name : "Coaching ID: {$configId}";
                                                                                        break;
                                                                                    default:
                                                                                        $configName = "ID: {$configId}";
                                                                                }
                                                                            @endphp
                                                                            <span class="inline-block text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                                                {{ $configName }}
                                                                            </span>
                                                                        @endforeach
                                                                    @else
                                                                        <span class="text-xs text-gray-500">No specific configuration</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @elseif($permissions->contains(function($perm) { return str_contains($perm, ':show-all') || str_contains($perm, ':country') || str_contains($perm, ':purpose') || str_contains($perm, ':coaching'); }))
                                                <div class="border-t border-gray-200 pt-4">
                                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                                        <div class="flex items-center gap-2">
                                                            <i class="ki-filled ki-information-5 text-yellow-600"></i>
                                                            <span class="text-sm font-medium text-yellow-900">Configuration Required</span>
                                                        </div>
                                                        <p class="text-xs text-yellow-700 mt-1">
                                                            This role has permissions that require configuration, but no configurations are set.
                                                        </p>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center text-gray-500 py-4">
                                                <i class="ki-filled ki-information-5 text-lg mb-1"></i>
                                                <p class="text-sm">This role has no permissions assigned.</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Quick Stats & Actions -->
                <div class="space-y-5">
                    <!-- Status Card -->
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Status</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Account Status</span>
                                    <span class="kt-badge {{ $user->is_active ? 'kt-badge-success' : 'kt-badge-danger' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium">Email Verified</span>
                                    <span class="kt-badge {{ $user->email_verified_at ? 'kt-badge-success' : 'kt-badge-warning' }}">
                                        {{ $user->email_verified_at ? 'Verified' : 'Unverified' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-secondary-foreground">Member Since</span>
                                    <p class="text-sm">{{ $user->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-secondary-foreground">Last Updated</span>
                                    <p class="text-sm">{{ $user->updated_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="kt-card" hidden>
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">Quick Actions</h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="space-y-2">
                                <a href="{{ route('team.profile.edit', $user) }}" class="kt-btn kt-btn-secondary kt-btn-sm w-full">
                                    <i class="ki-filled ki-pencil"></i>
                                    Edit User
                                </a>
                                @can('delete', $user)
                                    <button onclick="confirmDelete()" class="kt-btn kt-btn-danger kt-btn-sm w-full">
                                        <i class="ki-filled ki-trash"></i>
                                        Delete User
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    @can('delete', $user)
    @push('scripts')
    <script>
        function confirmDelete() {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("team.settings.users.destroy", $user) }}';
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
    @endcan
</x-team.layout.app>
