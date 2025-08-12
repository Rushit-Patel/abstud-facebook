@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'view profile', 'url' => route('team.profile')],
    ['title' => 'Edit User: ' . $user->name]
];
@endphp

<x-team.layout.app title="Edit User" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit User: {{ $user->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Modify user information and permissions
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.profile') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-eye"></i>
                        View User
                    </a>
                    <a href="{{ route('team.dashboard') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-left"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <form method="POST" action="{{ route('team.profile.update', $user->id) }}" class="kt-card">
                @csrf
                @method('PUT')
                <div class="kt-card-header">
                    <h3 class="kt-card-title">User Information</h3>
                </div>
                <div class="kt-card-content">
                    <div class="grid lg:grid-cols-2 gap-5">
                        <!-- Full Name -->
                        <x-team.forms.input
                            name="name"
                            label="Full Name"
                            type="text"
                            :required="true"
                            placeholder="Enter full name"
                            :value="old('name', $user->name)" />

                        <!-- Username -->
                        <x-team.forms.input
                            name="username"
                            label="Username"
                            type="text"
                            :required="true"
                            placeholder="Enter username"
                            :value="old('username', $user->username)" />

                        <!-- Email -->
                        <x-team.forms.input
                            name="email"
                            label="Email Address"
                            type="email"
                            :required="true"
                            placeholder="Enter email address"
                            :value="old('email', $user->email)" />

                        <!-- Phone -->
                        <x-team.forms.input
                            name="phone"
                            label="Phone Number"
                            type="tel"
                            placeholder="Enter phone number"
                            :value="old('phone', $user->phone)" />

                        <!-- Branch -->
                        <x-team.forms.select
                            name="branch_id"
                            label="Branch"
                            :options="$branches"
                            :selected="old('branch_id', $user->branch_id)"
                            placeholder="Select branch"
                            :required="true"
                            :searchable="true" />

                        <!-- Password -->
                        <x-team.forms.input
                            name="password"
                            label="Password"
                            type="password"
                            :value="old('password', base64_decode($user->base_password))"
                            placeholder="Leave blank to keep current password" />

                        <!-- Confirm Password -->
                        <x-team.forms.input
                            name="password_confirmation"
                            label="Confirm Password"
                            type="password"
                            :value="old('password', base64_decode($user->base_password))"
                            placeholder="Confirm new password" />

                        <!-- Status -->
                        <div class="lg:col-span-2" hidden>
                            <x-team.forms.checkbox
                                name="is_active"
                                label="Active User - User can login and access the system"
                                :checked="old('is_active', $user->is_active)" />
                        </div>

                        <!-- Role -->
                        <div class="lg:col-span-2 mt-6" hidden>
                            <div class="kt-separator kt-separator-dashed my-5"></div>
                            <label class="kt-form-label text-mono required">
                                Roles & Permissions Configuration
                                <span class="text-destructive">*</span>
                            </label>
                            <div class="text-2sm text-gray-600 mb-4">
                                Select one or more roles and configure their specific permissions as needed.
                            </div>

                            <div class="space-y-3">
                                @foreach($roles as $role)
                                    @php
                                        $userRoleIds = old('roles', $user->roles->pluck('id')->toArray());
                                        $isRoleSelected = in_array($role->id, $userRoleIds);
                                    @endphp
                                    <div class="border border-gray-200 rounded-lg overflow-hidden role-card">
                                        <div class="p-4 bg-gray-50">
                                            <div class="flex items-center gap-3">
                                                <x-team.forms.checkbox
                                                    name="roles[]"
                                                    :value="$role->id"
                                                    :id="'role_' . $role->id"
                                                    :checked="$isRoleSelected"
                                                    class="role-checkbox"
                                                    style="inline"
                                                    :label="$role->name"
                                                    data-role-id='{{ $role->id }}' />
                                                @if($role->permissions->count() > 0)
                                                    <span class="text-2xs bg-primary-100 text-primary-700 px-2 py-1 rounded">
                                                        {{ $role->permissions->count() }} permissions
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Role-specific configurations -->
                                        <div id="role_config_{{ $role->id }}" class="role-config border-t border-gray-200 p-4 bg-white" style="display: {{ $isRoleSelected ? 'block' : 'none' }};">
                                            @php
                                                $permissions = $role->permissions->pluck('name');
                                                $hasConfigurations = false;
                                            @endphp

                                            @if($permissions->contains('lead:show-all') || $permissions->contains('followup:show-all'))
                                                @php
                                                    $hasConfigurations = true;
                                                    $branchConfig = $user->roleConfigurations->where('role_id', $role->id)->where('permission_type', 'show-all')->first();
                                                    $selectedBranches = $branchConfig
                                                        ? $branchConfig->configuration_data
                                                        : old("role_branch_configurations.{$role->id}", []);
                                                @endphp
                                                <div class="mb-4">
                                                    <x-team.forms.select
                                                        name="role_branch_configurations[{{ $role->id }}][]"
                                                        label="Branches Access"
                                                        :options="$branches"
                                                        :selected="$selectedBranches"
                                                        :multiple="true"
                                                        placeholder="Select branches..."
                                                        :searchable="true" />
                                                    <p class="text-2xs text-gray-500 mt-1">Select which branches this role can access</p>
                                                </div>
                                            @endif

                                            @if($permissions->contains('lead:country'))
                                                @php
                                                    $hasConfigurations = true;
                                                    $countryConfig = $user->roleConfigurations->where('role_id', $role->id)->where('permission_type', 'country')->first();
                                                    $selectedCountries = $countryConfig
                                                        ? $countryConfig->configuration_data
                                                        : old("role_country_configurations.{$role->id}", []);
                                                @endphp
                                                <div class="mb-4">
                                                    <x-team.forms.select
                                                        name="role_country_configurations[{{ $role->id }}][]"
                                                        label="Countries Access"
                                                        :options="$countries"
                                                        :selected="$selectedCountries"
                                                        :multiple="true"
                                                        placeholder="Select countries..."
                                                        :searchable="true" />
                                                    <p class="text-2xs text-gray-500 mt-1">Select which countries this role can manage</p>
                                                </div>
                                            @endif

                                            @if($permissions->contains('lead:purpose'))
                                                @php
                                                    $hasConfigurations = true;
                                                    $purposeConfig = $user->roleConfigurations->where('role_id', $role->id)->where('permission_type', 'purpose')->first();
                                                    $selectedPurposes = $purposeConfig
                                                        ? $purposeConfig->configuration_data
                                                        : old("role_purpose_configurations.{$role->id}", []);
                                                @endphp
                                                <div class="mb-4">
                                                    <x-team.forms.select
                                                        name="role_purpose_configurations[{{ $role->id }}][]"
                                                        label="Purposes Access"
                                                        :options="$purposes"
                                                        :selected="$selectedPurposes"
                                                        :multiple="true"
                                                        placeholder="Select purposes..."
                                                        :searchable="true" />
                                                    <p class="text-2xs text-gray-500 mt-1">Select which purposes this role can handle</p>
                                                </div>
                                            @endif

                                            @if($permissions->contains('lead:coaching'))
                                                @php
                                                    $hasConfigurations = true;
                                                    $coachingConfig = $user->roleConfigurations->where('role_id', $role->id)->where('permission_type', 'coaching')->first();
                                                    $selectedCoaching = $coachingConfig
                                                        ? $coachingConfig->configuration_data
                                                        : old("role_coaching_configurations.{$role->id}", []);
                                                @endphp
                                                <div class="mb-4">
                                                    <x-team.forms.select
                                                        name="role_coaching_configurations[{{ $role->id }}][]"
                                                        label="Coaching Access"
                                                        :options="$coaching"
                                                        :selected="$selectedCoaching"
                                                        :multiple="true"
                                                        placeholder="Select coaching..."
                                                        :searchable="true" />
                                                    <p class="text-2xs text-gray-500 mt-1">Select which coaching programs this role can manage</p>
                                                </div>
                                            @endif

                                            @if(!$hasConfigurations)
                                                <div class="text-center text-gray-500 py-2">
                                                    <i class="ki-filled ki-information-5 text-lg mb-1"></i>
                                                    <p class="text-2xs">This role doesn't require additional configuration.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if($roles->isEmpty())
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="ki-filled ki-information-5 text-3xl mb-2"></i>
                                        <p class="text-sm">No roles available. Please create roles first.</p>
                                    </div>
                                @endif
                            </div>
                            @error('roles')
                                <div class="text-danger text-sm mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="kt-card-footer">
                    <div class="flex justify-end gap-2.5">
                        <a href="{{ route('team.profile') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <x-team.forms.button type="submit">
                            <i class="ki-filled ki-check"></i>
                            Update User
                        </x-team.forms.button>
                    </div>
                </div>
            </form>
        </div>
    </x-slot>

    <x-slot name="scripts">
        <script>
            $(document).ready(function() {
                // Handle role checkbox changes using jQuery
                $('.role-checkbox').on('change', function() {
                    const roleId = $(this).data('role-id');
                    const configDiv = $('#role_config_' + roleId);
                    const roleCard = $(this).closest('.role-card');

                    if ($(this).is(':checked')) {
                        configDiv.show();
                        roleCard.addClass('border-primary shadow-sm');
                    } else {
                        configDiv.hide();
                        roleCard.removeClass('border-primary shadow-sm');
                        // Clear any select2 selections
                        configDiv.find('select').val(null).trigger('change');
                    }
                });

                // Show configurations for initially checked roles
                $('.role-checkbox:checked').trigger('change');
            });
        </script>
    </x-slot>
</x-team.layout.app>
