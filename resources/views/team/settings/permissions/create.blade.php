@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Permission Management', 'url' => route('team.settings.permissions.index')],
    ['title' => 'Create Permission']
];
@endphp

<x-team.layout.app title="Create Permission" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New Permission
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new permission to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.permissions.index', ['guard' => $guard]) }}" class="kt-btn kt-btn-secondary">
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
                    <form action="{{ route('team.settings.permissions.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Guard Selection -->
                            <div>
                                <x-team.forms.select
                                    name="guard_name"
                                    label="Guard Type"
                                    required
                                    :options="$guards"
                                    :selected="old('guard_name', $guard)"
                                    placeholder="Select guard type" />
                                <div class="text-sm text-secondary-foreground mt-1">
                                    Choose the user type this permission applies to
                                </div>
                            </div>
                            
                            <!-- Module -->
                            <div>
                                <x-team.forms.select
                                    name="module"
                                    label="Module"
                                    required
                                    :options="$modules"
                                    :selected="old('module')"
                                    placeholder="Select module" />
                            </div>
                            
                            <!-- Action -->
                            <div>
                                <x-team.forms.select
                                    name="action"
                                    label="Action"
                                    required
                                    :options="$actions"
                                    :selected="old('action')"
                                    placeholder="Select action" />
                            </div>
                            
                            <!-- Permission Name (Auto-generated preview) -->
                            <div>
                                <label class="kt-form-label text-mono">Permission Name (Preview)</label>
                                <div class="kt-input bg-muted" id="permission-preview">
                                    Select module and action
                                </div>
                                <div class="text-sm text-secondary-foreground mt-1">
                                    Format: module:action
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
                                        placeholder="Describe what this permission allows users to do">{{ old('description') }}</textarea>
                                    @error('description')
                                        <span class="text-destructive text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-6 border-t border-border">
                            <a href="{{ route('team.settings.permissions.index', ['guard' => $guard]) }}" class="kt-btn kt-btn-secondary">
                                Cancel
                            </a>
                            <x-team.forms.button type="submit">
                                <i class="ki-filled ki-plus"></i>
                                Create Permission
                            </x-team.forms.button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Permission Modules Help -->
            <div class="kt-card mt-5">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Available Modules & Actions</h3>
                </div>
                <div class="kt-card-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($modules as $key => $moduleName)
                            <div class="p-4 border border-border rounded-lg">
                                <h4 class="font-medium text-mono mb-2">{{ $moduleName }}</h4>
                                <div class="text-sm text-secondary-foreground">
                                    @switch($key)
                                        @case('branch')
                                            Branch management and operations
                                            @break
                                        @case('user')
                                            User account management
                                            @break
                                        @case('lead')
                                            Lead management and tracking
                                            @break
                                        @case('follow-up')
                                            Follow-up activities and scheduling
                                            @break
                                        @case('master-module')
                                            Master data configuration
                                            @break
                                        @case('student')
                                            Student profile and academic operations
                                            @break
                                        @case('partner')
                                            Partner management and collaboration
                                            @break
                                        @case('dashboard')
                                            Dashboard access and analytics
                                            @break
                                        @case('company')
                                            Company settings and configuration
                                            @break
                                        @case('roles')
                                            Role management
                                            @break
                                        @case('permissions')
                                            Permission management
                                            @break
                                        @default
                                            General system permissions
                                    @endswitch
                                </div>
                            </div>
                        @endforeach
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
                    preview.textContent = 'Select module and action';
                }
            }

            moduleSelect.addEventListener('change', updatePreview);
            actionSelect.addEventListener('change', updatePreview);
        });
    </script>
    @endpush
</x-team.layout.app>
