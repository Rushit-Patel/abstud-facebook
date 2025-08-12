@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Settings', 'url' => route('team.settings.index')],
        ['title' => 'Company Settings', 'url' => route('team.settings.company.index')],
        ['title' => 'Edit Company Settings']
    ];
@endphp
<x-team.layout.app title="Edit Company Settings" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Page Header -->
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Company Settings
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update your company information and system settings
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.company.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-black-left"></i>
                        Back to Settings
                    </a>
                </div>
            </div>
            <div class="flex grow gap-5 lg:gap-7.5">
                <!-- Sidebar Navigation -->
                <div class="hidden lg:block w-[230px] shrink-0">
                    <div class="w-[230px]" data-kt-sticky="true" data-kt-sticky-animation="true"
                        data-kt-sticky-class="fixed z-4 left-auto top-[calc(var(--header-height)+1rem)]"
                        data-kt-sticky-name="scrollspy" data-kt-sticky-offset="200" data-kt-sticky-target="body"
                        data-kt-sticky-initialized="true">
                        <div class="flex flex-col grow relative before:absolute before:left-[11px] before:top-0 before:bottom-0 before:border-l before:border-border"
                            data-kt-scrollspy="true" data-kt-scrollspy-offset="110px" data-kt-scrollspy-target="body"
                            data-kt-scrollspy-initialized="true">
                            
                            <!-- Company Information -->
                            <a class="flex items-center rounded-lg pl-2.5 pr-2.5 py-2.5 gap-1.5 border border-transparent text-sm text-foreground hover:text-primary hover:font-medium kt-scrollspy-active:bg-secondary-active kt-scrollspy-active:text-primary kt-scrollspy-active:font-medium hover:rounded-lg"
                                data-kt-scrollspy-anchor="true" href="#company_information">
                                <span class="flex w-1.5 relative before:absolute before:top-0 before:size-1.5 before:rounded-full before:-translate-x-2/4 before:-translate-y-2/4 kt-scrollspy-active:before:bg-primary"></span>
                                Company Information
                            </a>
                            
                            <!-- Location Details -->
                            <a class="flex items-center rounded-lg pl-2.5 pr-2.5 py-2.5 gap-1.5 border border-transparent text-sm text-foreground hover:text-primary hover:font-medium kt-scrollspy-active:bg-secondary-active kt-scrollspy-active:text-primary kt-scrollspy-active:font-medium hover:rounded-lg"
                                data-kt-scrollspy-anchor="true" href="#location_details">
                                <span class="flex w-1.5 relative before:absolute before:top-0 before:size-1.5 before:rounded-full before:-translate-x-2/4 before:-translate-y-2/4 kt-scrollspy-active:before:bg-primary"></span>
                                Location Details
                            </a>
                            
                            <!-- Company Assets -->
                            <a class="flex items-center rounded-lg pl-2.5 pr-2.5 py-2.5 gap-1.5 border border-transparent text-sm text-foreground hover:text-primary hover:font-medium kt-scrollspy-active:bg-secondary-active kt-scrollspy-active:text-primary kt-scrollspy-active:font-medium hover:rounded-lg"
                                data-kt-scrollspy-anchor="true" href="#company_assets">
                                <span class="flex w-1.5 relative before:absolute before:top-0 before:size-1.5 before:rounded-full before:-translate-x-2/4 before:-translate-y-2/4 kt-scrollspy-active:before:bg-primary"></span>
                                Company Assets
                            </a>
                            
                            <!-- Setup Status -->
                            <a class="flex items-center rounded-lg pl-2.5 pr-2.5 py-2.5 gap-1.5 border border-transparent text-sm text-foreground hover:text-primary hover:font-medium kt-scrollspy-active:bg-secondary-active kt-scrollspy-active:text-primary kt-scrollspy-active:font-medium hover:rounded-lg"
                                data-kt-scrollspy-anchor="true" href="#setup_status">
                                <span class="flex w-1.5 relative before:absolute before:top-0 before:size-1.5 before:rounded-full before:-translate-x-2/4 before:-translate-y-2/4 kt-scrollspy-active:before:bg-primary"></span>
                                Setup Status
                            </a>
                            <a class="flex items-center rounded-lg pl-2.5 pr-2.5 py-2.5 gap-1.5 border border-transparent text-sm text-foreground hover:text-primary hover:font-medium kt-scrollspy-active:bg-secondary-active kt-scrollspy-active:text-primary kt-scrollspy-active:font-medium hover:rounded-lg"
                                data-kt-scrollspy-anchor="true" href="#whatsapp_integration">
                                <span class="flex w-1.5 relative before:absolute before:top-0 before:size-1.5 before:rounded-full before:-translate-x-2/4 before:-translate-y-2/4 kt-scrollspy-active:before:bg-primary"></span>
                                WhatsApp Integration
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex flex-col grow gap-5 lg:gap-7.5">
                    <form action="{{ route('team.settings.company.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col items-stretch grow gap-5 lg:gap-7.5 mb-5">
                        @csrf
                        @method('PUT')
                        <!-- Company Settings Form -->
                        <!-- Company Information Section -->
                        <x-team.card title="Company Information" id="company_information">
                            <div class="grid gap-5">
                                <!-- Company Name -->
                                <x-team.forms.input name="company_name" label="Company Name" type="text"
                                    :required="true" placeholder="Enter company name" 
                                    :value="old('company_name', $company->company_name ?? '')" />

                                <!-- Email -->
                                <x-team.forms.input name="email" label="Company Email" type="email"
                                    placeholder="Enter company email address" 
                                    :value="old('email', $company->email ?? '')" />

                                <!-- Phone -->
                                <x-team.forms.input name="phone" label="Phone Number" type="tel"
                                    placeholder="Enter phone number" 
                                    :value="old('phone', $company->phone ?? '')" />

                                <!-- Website URL -->
                                <x-team.forms.input name="website_url" label="Website URL" type="url"
                                    placeholder="https://example.com" 
                                    :value="old('website_url', $company->website_url ?? '')" />

                                <!-- Company Address -->
                                <x-team.forms.textarea name="company_address" label="Company Address"
                                    placeholder="Enter complete company address"
                                    :value="old('company_address', $company->company_address ?? '')" />
                            </div>
                        </x-team.card>

                        <!-- Location Details Section -->
                        <x-team.card title="Location Details" id="location_details">
                            <div class="grid gap-5">
                                <!-- Country -->
                                <x-team.forms.select name="country_id" label="Country" :options="$countries ?? []"
                                    :selected="old('country_id', $company->country_id ?? '')"
                                    placeholder="Select Country" :required="true" searchable="true" />

                                <!-- State -->
                                <x-team.forms.select name="state_id" label="State/Province" :options="$states ?? []"
                                    :selected="old('state_id', $company->state_id ?? '')" 
                                    placeholder="Select State" :required="true" searchable="true" />

                                <!-- City -->
                                <x-team.forms.select name="city_id" label="City" :options="$cities ?? []"
                                    :selected="old('city_id', $company->city_id ?? '')" 
                                    placeholder="Select City" :required="true" searchable="true" />

                                <!-- Postal Code -->
                                <x-team.forms.input name="postal_code" label="Postal Code" type="text"
                                    placeholder="Enter postal/zip code" 
                                    :value="old('postal_code', $company->postal_code ?? '')" />
                            </div>
                        </x-team.card>

                        <!-- Company Assets Section -->
                        <x-team.card title="Company Assets" id="company_assets">
                            <div class="grid gap-5">
                                <!-- Company Logo -->
                                <div class="flex flex-col gap-1">
                                    <label for="company_logo" class="kt-form-label font-normal text-mono">
                                        Company Logo
                                    </label>
                                    <div class="grid grid-cols-4 items-center gap-4">
                                        <div class="col-span-1">
                                            @if($company && $company->company_logo)
                                                <div class="w-40 h-16 rounded-lg overflow-hidden border border-input">
                                                    <img src="{{ Storage::url($company->company_logo) }}"
                                                        alt="{{ $company->company_name }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                            @else
                                                <div class="w-16 h-16 rounded-lg border-2 border-dashed border-input flex items-center justify-center bg-background">
                                                    <i class="ki-filled ki-picture text-xl text-muted-foreground"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-span-3">
                                            <input type="file" id="company_logo" name="company_logo" 
                                                    accept="image/*" class="kt-input" />
                                            <p class="text-xs text-muted-foreground mt-1">
                                                Upload company logo (PNG, JPG, GIF). Recommended size: 150x150px
                                            </p>
                                        </div>
                                    </div>
                                    @error('company_logo')
                                        <span class="text-destructive text-sm mt-1">
                                            {{ $errors->first('company_logo') }}
                                        </span>
                                    @enderror
                                </div>
                                
                                <!-- Company Favicon -->
                                <div class="flex flex-col gap-1">
                                    <label for="company_favicon" class="kt-form-label font-normal text-mono">
                                        Favicon
                                    </label>
                                    <div class="grid grid-cols-4 items-center gap-4">
                                        <div class="col-span-1">
                                            @if($company && $company->company_favicon)
                                                <div class="w-8 h-8 rounded overflow-hidden border border-input">
                                                    <img src="{{ Storage::url($company->company_favicon) }}"
                                                        alt="Favicon" class="w-full h-full object-cover">
                                                </div>
                                            @else
                                                <div class="w-8 h-8 rounded border-2 border-dashed border-input flex items-center justify-center bg-background">
                                                    <i class="ki-filled ki-picture text-xs text-muted-foreground"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-span-3">
                                            <input type="file" id="company_favicon" name="company_favicon" 
                                                    accept="image/*" class="kt-input" />
                                            <p class="text-xs text-muted-foreground mt-1">
                                                Upload favicon (ICO, PNG). Recommended size: 32x32px
                                            </p>
                                        </div>
                                    </div>
                                    @error('company_favicon')
                                        <span class="text-destructive text-sm mt-1">
                                            {{ $errors->first('company_favicon') }}
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </x-team.card>

                        <!-- Setup Status Section -->
                        <x-team.card title="Setup Status" id="setup_status">
                            <div class="flex items-center gap-3">
                                <x-team.forms.checkbox name="is_setup_completed"
                                    label="Mark company setup as completed" 
                                    :checked="old('is_setup_completed', $company && $company->is_setup_completed)" />
                                <div class="text-sm text-muted-foreground">
                                    Check this when all required company information has been configured
                                </div>
                            </div>
                        </x-team.card>

                        <!-- Form Actions -->
                        <div class="flex justify-end pt-2.5 gap-2.5">
                            <a href="{{ route('team.settings.company.index') }}" class="kt-btn kt-btn-secondary">
                                Cancel
                            </a>
                            <div>
                                <x-team.forms.button type="submit">
                                    <i class="ki-filled ki-check"></i>
                                    Update Company Settings
                                </x-team.forms.button>
                            </div>
                        </div>
                    </form>
                    <form action="{{ route('team.settings.company.whatsapp-update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col items-stretch grow gap-5 lg:gap-7.5 mb-5">
                        @csrf
                        @method('POST')
                        <!-- Company Settings Form -->
                        <!-- WhatsApp Integration Section -->
                        <x-team.card title="WhatsApp Integration" id="whatsapp_integration">
                            <div class="mb-5">
                                <h3 class="text-base font-medium text-mono">
                                    API Provider
                                </h3>
                                <span class="text-sm text-secondary-foreground">
                                    Select Your API Provider for WhatsApp Integration
                                </span>
                            </div>
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-7.5 mb-5">
                                @foreach($whatsappProviders as $provider)
                                <div>
                                    <label class="flex items-end border bg-no-repeat bg-contain border-input rounded-xl has-checked:border-green-500 has-checked:border-3 has-checked:[&amp;_.checked]:flex h-[70px] mb-0.5" style="background-image: url('/default/images/whatsapp/{{ $provider->slug }}.svg');background-position: center;background-size: {{ $provider->slug === 'interakt' ? '86% 75%' : '80% 111%' }};">
                                        <input {{ $activeWhatsappProvider && $activeWhatsappProvider->id === $provider->id ? 'checked' : '' }} class="appearance-none whatsapp_provider" name="whatsapp_provider" type="radio" value="{{ $provider->slug }}">
                                        <span class="checked {{ $activeWhatsappProvider && $activeWhatsappProvider->id === $provider->id ? '' : 'hidden' }}">
                                            <i class="ki-solid ki-check-circle ml-5 mb-5 text-xl text-green-500 leading-none"></i>
                                        </span>
                                    </label>
                                    <span class="text-sm font-medium text-mono">
                                        {{ $provider->name }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                            <div class="border-b border-border"></div>
                            
                            <!-- Dynamic Configuration Forms -->
                            @foreach($whatsappProviders as $provider)
                            <div class="provider-config {{ $activeWhatsappProvider && $activeWhatsappProvider->id === $provider->id ? '' : 'hidden' }}" data-provider="{{ $provider->slug }}">
                                <div class="grid gap-7 mt-5">
                                    <div class="text-base font-semibold text-mono">
                                        Configure {{ $provider->name }} Authentication
                                    </div>
                                    
                                    @if($provider->slug === 'interakt')
                                        <div class="w-full">
                                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                                <label class="kt-form-label max-w-56">
                                                    API Key
                                                </label>
                                                <input class="kt-input" type="text" name="interakt_secret_key" 
                                                       placeholder="Enter Interakt API Key" 
                                                       value="{{ $provider->getConfigValue('api_key') ?: '' }}">
                                            </div>
                                        </div>
                                    @elseif($provider->slug === 'gupshup')
                                        <div class="w-full">
                                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                                <label class="kt-form-label max-w-56">
                                                    API Key
                                                </label>
                                                <input class="kt-input" type="text" name="gupshup_apikey" 
                                                       placeholder="Enter Gupshup API Key"
                                                       value="{{ $provider->getConfigValue('apikey') ?: '' }}">
                                            </div>
                                        </div>
                                        <div class="w-full">
                                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                                <label class="kt-form-label max-w-56">
                                                    Channel
                                                </label>
                                                <input class="kt-input" type="text" name="gupshup_channel" 
                                                       placeholder="whatsapp" 
                                                       value="{{ $provider->getConfigValue('channel') ?: 'whatsapp' }}">
                                            </div>
                                        </div>
                                    @elseif($provider->slug === 'gallabox')
                                        <div class="w-full">
                                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                                <label class="kt-form-label max-w-56">
                                                    API Key
                                                </label>
                                                <input class="kt-input" type="text" name="gallabox_api_key" 
                                                       placeholder="Enter Gallabox API Key"
                                                       value="{{ $provider->getConfigValue('api_key') ?: '' }}">
                                            </div>
                                        </div>
                                        <div class="w-full">
                                            <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                                                <label class="kt-form-label max-w-56">
                                                    Workspace ID
                                                </label>
                                                <input class="kt-input" type="text" name="gallabox_workspace_id" 
                                                       placeholder="Enter Workspace ID"
                                                       value="{{ $provider->getConfigValue('workspace_id') ?: '' }}">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                            
                            <div class="flex justify-end mt-5">
                                <button type="submit" class="kt-btn kt-btn-primary">
                                    Save Changes
                                </button>
                            </div>
                        </x-team.card>
                    </form>
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
        <script src="{{ asset('assets/js/team/location-ajax.js') }}"></script>
        <script>
            $(document).ready(function () {
                // Initialize Location AJAX for country/state/city dropdowns
                LocationAjax.init({
                    countrySelector: '#country_id',
                    stateSelector: '#state_id',
                    citySelector: '#city_id',
                    statesRoute: '{{ route("team.settings.company.states", ":countryId") }}'.replace(':countryId', ''),
                    citiesRoute: '{{ route("team.settings.company.cities", ":stateId") }}'.replace(':stateId', '')
                });

                // If editing existing company with location data, set the values
                @if($company && $company->country_id)
                    // Set initial values for edit mode
                    setTimeout(function () {
                        LocationAjax.setSelectedValues({
                            country_id: '{{ $company->country_id }}',
                            state_id: '{{ $company->state_id }}',
                            city_id: '{{ $company->city_id }}'
                        });
                    }, 100);
                @endif

                // WhatsApp provider selection handling
                $('input[name="whatsapp_provider"]').on('change', function() {
                    const selectedProvider = $(this).val();
                    
                    // Hide all provider configs
                    $('.provider-config').addClass('hidden');
                    
                    // Show selected provider config
                    $(`.provider-config[data-provider="${selectedProvider}"]`).removeClass('hidden');
                    
                    // Update checked state visuals
                    $('.whatsapp_provider').each(function() {
                        const $radio = $(this);
                        const $checked = $radio.siblings('.checked');
                        
                        if ($radio.prop('checked')) {
                            $checked.removeClass('hidden');
                            $radio.closest('label').addClass('border-green-500 border-3');
                        } else {
                            $checked.addClass('hidden');
                            $radio.closest('label').removeClass('border-green-500 border-3');
                        }
                    });
                });

                // Initialize WhatsApp provider selection on page load
                @if($whatsappProviders->where('is_active', 1)->first())
                    // Select the currently active provider
                    const activeProviderId = '{{ $whatsappProviders->where('is_active', 1)->first()->id }}';
                    $(`input[name="whatsapp_provider"][value="${activeProviderId}"]`).prop('checked', true).trigger('change');
                @else
                    // If no active provider, select the first one by default
                    const firstProvider = $('input[name="whatsapp_provider"]:first');
                    if (firstProvider.length) {
                        firstProvider.prop('checked', true).trigger('change');
                    }
                @endif
            });
        </script>
    @endpush
</x-team.layout.app>