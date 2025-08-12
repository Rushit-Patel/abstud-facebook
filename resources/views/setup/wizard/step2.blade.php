@extends('setup.wizard.layout', ['step' => 2])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700">
        <!-- Company Info Display -->
        <div class="px-8 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $company->company_name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $company->city }}, {{ $company->state }}, {{ $company->country }}</p>
                </div>
                <a href="{{ route('setup.company') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Previous Step
                </a>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-y-12 lg:grid-cols-2 lg:divide-x lg:divide-[#DFE3E6]">
            <!-- Left Column - Instructions -->
            <div class="px-8 py-12">
                <div class="space-y-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Main Branch</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Set up your main branch location. This will be your primary business location and can be used for inventory management, employee assignment, and financial reporting.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Branch name and identification code
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Physical address and contact details
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Branch manager assignment
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Timezone and operating hours
                            </li>
                        </ul>
                    </div>

                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-green-900 dark:text-green-100">Multi-Branch Ready</h4>
                                <p class="text-sm text-green-700 dark:text-green-200 mt-1">
                                    You can add more branches later from the admin panel. This main branch will serve as your headquarters.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Branch Code</h4>
                                <p class="text-sm text-blue-700 dark:text-blue-200 mt-1">
                                    Use a short, memorable code (3-10 characters) that will appear in reports and transactions.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Form -->
            <div class="px-8 py-12">
                <form method="POST" action="{{ route('setup.branch.store') }}" class="space-y-6">
                    @csrf

                    <!-- Branch Name -->
                    <x-team.forms.input 
                        name="branch_name" 
                        label="Branch Name" 
                        type="text"
                        :value="old('branch_name', $company->company_name . ' - Main Branch')"
                        placeholder="Main Branch"
                        required="true"
                    />

                    <!-- Branch Code -->
                    <x-team.forms.input 
                        name="branch_code" 
                        label="Branch Code" 
                        type="text"
                        :value="old('branch_code', 'MAIN')"
                        placeholder="MAIN"
                        required="true"
                    />

                    <!-- Branch Address -->
                    <div>
                        <label for="branch_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Branch Address <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="branch_address" 
                            id="branch_address" 
                            rows="3" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-colors"
                            placeholder="Enter branch address"
                        >{{ old('branch_address', $company->company_address) }}</textarea>
                        @error('branch_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-team.forms.input 
                            name="branch_phone" 
                            label="Phone Number" 
                            type="tel"
                            :value="old('branch_phone', $company->phone ?? '')"
                            placeholder="+1 (555) 123-4567"
                        />

                        <x-team.forms.input 
                            name="branch_email" 
                            label="Email Address" 
                            type="email"
                            :value="old('branch_email', $company->email ?? '')"
                            placeholder="branch@yourcompany.com"
                        />
                    </div>

                    <!-- Location -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <x-team.forms.select 
                            name="branch_country_id" 
                            label="Country" 
                            :options="$countries"
                            :selected="old('branch_country_id')"
                            placeholder="Select Country"
                            required="true"
                            searchable="true"
                        />

                        <x-team.forms.select 
                            name="branch_state_id" 
                            label="State/Province" 
                            :options="[]"
                            :selected="old('branch_state_id')"
                            placeholder="Select State"
                            required="true"
                            searchable="true"
                        />

                        <x-team.forms.select 
                            name="branch_city_id" 
                            label="City" 
                            :options="[]"
                            :selected="old('branch_city_id')"
                            placeholder="Select City"
                            required="true"
                            searchable="true"
                        />
                    </div>
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a 
                            href="{{ route('setup.company') }}"
                            class="px-6 py-3 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Previous
                        </a>
                        
                        <button 
                            type="submit"
                            class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                        >
                            Continue to Admin Setup
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
    // Auto-uppercase branch code
    document.getElementById('branch_code').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });

    // Location dependency handling
    document.addEventListener('DOMContentLoaded', function() {
        const countrySelect = document.getElementById('branch_country_id');
        const stateSelect = document.getElementById('branch_state_id');
        const citySelect = document.getElementById('branch_city_id');

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
