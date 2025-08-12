@extends('setup.wizard.layout', ['step' => 1])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-y-12 lg:grid-cols-2 lg:divide-x lg:divide-[#DFE3E6]">
            <!-- Left Column - Instructions -->
            <div class="px-8 py-12">
                <div class="space-y-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Company Setup</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Start by setting up your company's basic information. This will be used throughout your ERP system for invoices, reports, and branding.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Company name and basic details
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Company logo and favicon
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Business address and contact information
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Website and social media links
                            </li>
                        </ul>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Quick Tip</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-200 mt-1">
                                    Upload a high-quality logo (PNG/SVG recommended) for best results across your system. The favicon will appear in browser tabs.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Form -->
            <div class="px-8 py-12">
                <form method="POST" action="{{ route('setup.company.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Company Name -->
                    <x-team.forms.input 
                        name="company_name" 
                        label="Company Name" 
                        type="text"
                        :value="old('company_name')"
                        placeholder="Enter your company name"
                        required="true"
                    />

                    <!-- Website URL -->
                    <x-team.forms.input 
                        name="website_url" 
                        label="Website URL" 
                        type="url"
                        :value="old('website_url')"
                        placeholder="https://www.yourcompany.com"
                        required="true"
                    />

                    <!-- Logo Upload -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Company Logo
                            </label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <input type="file" name="company_logo" id="company_logo" accept="image/*" class="hidden">
                                <label for="company_logo" class="cursor-pointer">
                                    <span class="mt-2 block text-sm font-medium text-gray-900 dark:text-gray-100">Upload Logo</span>
                                    <span class="mt-1 block text-xs text-gray-500">PNG, JPG, SVG up to 2MB</span>
                                </label>
                            </div>
                            @error('company_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="company_favicon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Favicon
                            </label>
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <input type="file" name="company_favicon" id="company_favicon" accept="image/*" class="hidden">
                                <label for="company_favicon" class="cursor-pointer">
                                    <span class="mt-2 block text-sm font-medium text-gray-900 dark:text-gray-100">Upload Favicon</span>
                                    <span class="mt-1 block text-xs text-gray-500">ICO, PNG up to 1MB</span>
                                </label>
                            </div>
                            @error('company_favicon')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="company_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Company Address <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="company_address" 
                            id="company_address" 
                            rows="3" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                            placeholder="Enter your complete business address"
                        >{{ old('company_address') }}</textarea>
                        @error('company_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <x-team.forms.select 
                                name="country_id" 
                                label="Country" 
                                :options="$countries"
                                :selected="old('country_id')"
                                placeholder="Select Country"
                                required="true"
                                searchable="true"
                            />
                        </div>

                        <div>
                            <x-team.forms.select 
                                name="state_id" 
                                label="State/Province" 
                                :options="[]"
                                :selected="old('state_id')"
                                placeholder="Select State"
                                required="true"
                                searchable="true"
                            />
                        </div>

                        <div>
                            <x-team.forms.select 
                                name="city_id" 
                                label="City" 
                                :options="[]"
                                :selected="old('city_id')"
                                placeholder="Select City"
                                required="true"
                                searchable="true"
                            />
                        </div>
                    </div>
                    <!-- Navigation Buttons -->
                    <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button 
                            type="submit"
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            Continue to Branch Setup
                            <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // File upload preview functionality
    document.getElementById('company_logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const label = e.target.closest('div').querySelector('label span:first-child');
            label.textContent = file.name;
        }
    });

    document.getElementById('company_favicon').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const label = e.target.closest('div').querySelector('label span:first-child');
            label.textContent = file.name;
        }
    });

    // Location dependency handling
    document.addEventListener('DOMContentLoaded', function() {
        const countrySelect = document.getElementById('country_id');
        const stateSelect = document.getElementById('state_id');
        const citySelect = document.getElementById('city_id');

        // Country change handler
        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            
            // Reset state and city
            stateSelect.innerHTML = '<option value="">Select State</option>';
            citySelect.innerHTML = '<option value="">Select City</option>';
            
            if (countryId) {
                fetch(`{{ route('setup.states', ['country' => ':countryId']) }}`.replace(':countryId', countryId))
                    .then(response => response.json())
                    .then(states => {
                        states.forEach(state => {
                            const option = document.createElement('option');
                            option.value = state.id;
                            option.textContent = state.name;
                            stateSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading states:', error));
            }
        });

        // State change handler
        stateSelect.addEventListener('change', function() {
            const stateId = this.value;
            
            // Reset city
            citySelect.innerHTML = '<option value="">Select City</option>';
            
            if (stateId) {
                fetch(`{{ route('setup.cities', ['state' => ':stateId']) }}`.replace(':stateId', stateId))
                    .then(response => response.json())
                    .then(cities => {
                        cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            citySelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading cities:', error));
            }
        });
    });
</script>
@endpush
@endsection
