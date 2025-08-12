@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Permission Management', 'url' => route('team.settings.permissions.index')],
    ['title' => 'Edit Permission']
];
@endphp

<x-team.layout.app title="Edit Permission" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Permission
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update permission details
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.permissions.show', $permission) }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-eye"></i>
                        View Details
                    </a>
                    <a href="{{ route('team.settings.permissions.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to Permissions
                    </a>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Permission Details</h3>
                </div>
                <div class="kt-card-content">
                    <form action="{{ route('team.settings.permissions.update', $permission) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Module -->
                            <div>
                                <x-team.forms.select
                                    name="module"
                                    label="Module"
                                    required
                                    :options="$modules"
                                    :selected="old('module', $currentModule)"
                                    placeholder="Select module" />
                            </div>
                            
                            <!-- Action -->
                            <div>
                                <x-team.forms.select
                                    name="action"
                                    label="Action"
                                    required
                                    :options="$actions"
                                    :selected="old('action', $currentAction)"
                                    placeholder="Select action" />
                            </div>
                            
                            <!-- Permission Name (Auto-generated preview) -->
                            <div>
                                <label class="kt-form-label text-mono">Permission Name (Preview)</label>
                                <div class="kt-input bg-muted" id="permission-preview">
                                    {{ $permission->name }}
                                </div>
                                <div class="text-sm text-secondary-foreground mt-1">
                                    Format: module:action
                                </div>
                            </div>
                            
                            <!-- Guard -->
                            <div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="kt-form-label text-mono">Guard</label>
                                    <div class="kt-input bg-muted">{{ $permission->guard_name }}</div>
                                </div>
                            </div>
                            
                            <!-- Created Date -->
                            <div>
                                <div class="flex flex-col gap-1.5">
                                    <label class="kt-form-label text-mono">Created</label>
                                    <div class="kt-input bg-muted">{{ $permission->created_at->format('M d, Y H:i') }}</div>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="md:col-span-2">
                                <div class="flex flex-col gap-1.5">
                                    <label for="description" class="kt-form-label text-mono">
                                        Description (Optional)
                                    </label>
                                    <textarea 
                                        class="kt-input" 
                                        id="description" 
                                        name="description" 
                                        rows="3"
                                        placeholder="Describe what this permission allows users to do">{{ old('description', $permission->description ?? '') }}</textarea>
                                    @error('description')
                                        <span class="text-destructive text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t border-border">
                            <a href="{{ route('team.settings.permissions.index') }}" class="kt-btn kt-btn-secondary">
                                Cancel
                            </a>
                            <x-team.forms.button type="submit">
                                <i class="ki-filled ki-save"></i>
                                Update Permission
                            </x-team.forms.button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assignment Information -->
            <div class="kt-card mt-5">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Permission Usage</h3>
                </div>
                <div class="kt-card-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Assigned Roles -->
                        <div>
                            <h4 class="font-medium text-mono mb-3">Assigned to Roles</h4>
                            @if($permission->roles->count() > 0)
                                <div class="space-y-2">
                                    @foreach($permission->roles as $role)
                                        <div class="flex items-center justify-between p-3 border border-border rounded-lg">
                                            <div class="flex items-center gap-3">
                                                <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                                            {{ $role->name === 'Super Admin' ? 'bg-danger-light text-danger' : 
                                                               ($role->name === 'Admin' ? 'bg-warning-light text-warning' : 
                                                               ($role->name === 'Manager' ? 'bg-info-light text-info' : 'bg-secondary-light text-secondary')) }}">
                                                    <i class="ki-filled ki-shield text-sm"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium">{{ $role->name }}</div>
                                                    <div class="text-sm text-secondary-foreground">{{ $role->users_count ?? 0 }} users</div>
                                                </div>
                                            </div>
                                            <a href="{{ route('team.settings.roles.show', $role) }}" 
                                               class="kt-btn kt-btn-sm kt-btn-outline">
                                                View Role
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="ki-filled ki-information-2 text-4xl text-secondary-foreground mb-3"></i>
                                    <p class="text-secondary-foreground">This permission is not assigned to any roles yet.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Statistics -->
                        <div>
                            <h4 class="font-medium text-mono mb-3">Statistics</h4>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 border border-border rounded-lg">
                                    <span class="text-secondary-foreground">Total Roles</span>
                                    <span class="kt-badge kt-badge-outline kt-badge-primary">{{ $permission->roles_count ?? $permission->roles->count() }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-border rounded-lg">
                                    <span class="text-secondary-foreground">Total Users (via roles)</span>
                                    <span class="kt-badge kt-badge-outline kt-badge-secondary">{{ $permission->users_count ?? $permission->roles->sum('users_count') }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 border border-border rounded-lg">
                                    <span class="text-secondary-foreground">Guard</span>
                                    <span class="kt-badge kt-badge-outline">{{ $permission->guard_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const moduleSelect = document.querySelector('select[name="module"]');
            const actionSelect = document.querySelector('select[name="action"]');
            const preview = document.getElementById('permission-preview');

            function updatePreview() {
                const module = moduleSelect.value;
                const action = actionSelect.value;
                
                if (module && action) {
                    preview.textContent = `${module}:${action}`;
                } else {
                    preview.textContent = '{{ $permission->name }}';
                }
            }

            moduleSelect.addEventListener('change', updatePreview);
            actionSelect.addEventListener('change', updatePreview);
        });
    </script>
    @endpush
</x-team.layout.app>
