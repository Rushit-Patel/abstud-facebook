@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Role Management', 'url' => route('team.settings.roles.index')],
    ['title' => 'Add Role']
];
@endphp

<x-team.layout.app title="Add Role" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Add New Role
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Create a new role with permissions
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.roles.index', ['guard' => $guard]) }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-left"></i>
                        Back to Roles
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('team.settings.roles.store') }}" class="kt-card">
                @csrf
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Role Information</h3>
                </div>
                <div class="kt-card-content">
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 py-5">
                        <!-- Guard Selection -->
                        <div class="col-span-1">
                            <x-team.forms.select
                                name="guard_name"
                                label="Guard Type"
                                required
                                :options="$guards"
                                :selected="old('guard_name', $guard)"
                                placeholder="Select guard type" />
                            <div class="text-sm text-secondary-foreground mt-1">
                                Choose the user type this role applies to
                            </div>
                        </div>
                        
                        <!-- Role Name -->
                        <div class="col-span-1">
                            <x-team.forms.input
                                name="name" 
                                label="Role Name" 
                                type="text" 
                                :required="true"
                                placeholder="Enter role name (e.g., Content Manager)" 
                                :value="old('name')" />
                        </div>
                    </div>
                    <x-team.forms.alert type="info">
                        Permissions
                    </x-team.forms.alert>
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 pt-5">
                        <!-- Permissions -->
                        @foreach($permissions as $category => $categoryPermissions)
                            <div class="col-span-1">
                                <div class="kt-card mb-4">
                                    <div class="kt-card-header py-3">
                                        <h4 class="kt-card-title text-sm">
                                            {{ ucfirst(str_replace('_', ' ', $category)) }}
                                        </h4>
                                        <div class="flex gap-2">
                                            <x-team.forms.checkbox
                                                name="permissions_all[]"
                                                id="category_{{ $category }}" 
                                                label=""
                                                value="{{ $category }}" 
                                                class="permission-category"
                                                wrapperClass="flex flex-row"
                                            />
                                        </div>
                                    </div>
                                    <div class="kt-card-content">
                                        <div class="flex flex-col gap-2">
                                            @foreach($categoryPermissions as $permission)
                                                <div class="">
                                                    @php
                                                        $isChecked = in_array($permission->id, old('permissions', []));
                                                    @endphp
                                                    <x-team.forms.checkbox
                                                        name="permissions[]"
                                                        id="permission_{{ $permission->id }}" 
                                                        label="{{ str_replace('_', ' ', $permission->name) }}"
                                                        value="{{ $permission->id }}" 
                                                        class="permission-checkbox  permission-{{ $category }}"
                                                        wrapperClass="flex flex-row"
                                                        :checked="$isChecked"
                                                    />
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        
                        @error('permissions')
                            <div class="kt-form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="kt-card-footer">
                    <div class="flex justify-end gap-2.5">
                        <a href="{{ route('team.settings.roles.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <x-team.forms.button type="submit">
                            <i class="ki-filled ki-check"></i>
                            Create Role
                        </x-team.forms.button>
                    </div>
                </div>
            </form>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        $(document).on('change', '.permission-category', function() {
            if($(this).prop('checked')) {
                $('.permission-' + $(this).val()).prop('checked', true);
            } else {
                $('.permission-' + $(this).val()).prop('checked', false);
            }    
        });
    </script>
    @endpush
</x-team.layout.app>
