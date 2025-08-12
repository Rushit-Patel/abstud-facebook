@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Role Management', 'url' => route('team.settings.roles.index')],
    ['title' => 'Edit Role: ' . $role->name]
];
@endphp

<x-team.layout.app title="Edit Role" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Role: {{ $role->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Modify role permissions and settings
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.roles.show', $role) }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-eye"></i>
                        View Role
                    </a>
                    <a href="{{ route('team.settings.roles.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-left"></i>
                        Back to Roles
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('team.settings.roles.update', $role) }}" class="kt-card">
                @csrf
                @method('PUT')
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Role Information</h3>
                </div>
                <div class="kt-card-content">
                    <div class="grid gap-5">
                        <!-- Role Name -->
                        <div class="lg:w-1/2">
                            <x-team.forms.input 
                                name="name" 
                                label="Role Name" 
                                type="text" 
                                :required="true"
                                placeholder="Enter role name" 
                                :value="old('name', $role->name)" />
                        </div>

                        <!-- Current Statistics -->
                        <div class="grid md:grid-cols-3 gap-4 p-4 bg-light rounded-lg">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">{{ $role->permissions->count() }}</div>
                                <div class="text-sm text-secondary-foreground">Assigned Permissions</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-info">{{ $role->users()->count() }}</div>
                                <div class="text-sm text-secondary-foreground">Users with this Role</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-success">{{ $role->guard_name }}</div>
                                <div class="text-sm text-secondary-foreground">Guard</div>
                            </div>
                        </div>
                        <x-team.forms.alert type="info">
                            Permissions
                        </x-team.forms.alert>
                        <!-- Permissions -->
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 pt-5">
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
                                                            $isChecked = in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray()));
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
                        </div>
                        @error('permissions')
                            <div class="kt-form-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="kt-card-footer">
                    <div class="flex justify-end gap-2.5">
                        <a href="{{ route('team.settings.roles.show', $role) }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <x-team.forms.button type="submit">
                            <i class="ki-filled ki-check"></i>
                            Update Role
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
