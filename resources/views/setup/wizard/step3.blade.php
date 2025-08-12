@extends('setup.wizard.layout', ['step' => 3])

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
                <a href="{{ route('setup.branch') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Previous Step
                </a>
            </div>
        </div>        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-y-12 lg:grid-cols-2 lg:divide-x lg:divide-[#DFE3E6]">
            <!-- Left Column - Instructions -->
            <div class="px-8 py-12">
                <div class="space-y-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Administrator Account</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Create your super administrator account to manage your AbstudERP system. This account will have full access to all features and settings.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Full system administration privileges
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Manage users, students, and partners
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Configure system settings and branches
                            </li>                        </ul>
                    </div>

                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                        <div class="flex">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-purple-900 dark:text-purple-100">Super Administrator</h4>
                                <p class="text-sm text-purple-700 dark:text-purple-200 mt-1">
                                    This account will have complete control over your ERP system. You can create additional admin accounts later.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Almost Done!</h3>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            After creating your admin account, your AbstudERP system will be ready to use. You can start managing students, partners, and your business operations.
                        </p>
                    </div>                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 18.5c-.77.833-.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-yellow-900 dark:text-yellow-100">Password Security</h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-200 mt-1">
                                    Use a strong password with at least 8 characters, including uppercase, lowercase, numbers, and symbols.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Form -->
            <div class="px-8 py-12">
                <!-- Display Validation Errors -->
                @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-red-900 dark:text-red-100">Please correct the following errors:</h4>
                            <ul class="text-sm text-red-700 dark:text-red-200 mt-2 list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('setup.admin.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- Full Name -->
                        <x-team.forms.input 
                            name="name" 
                            label="Full Name" 
                            type="text"
                            :value="old('name')"
                            placeholder="Enter your full name"
                            required="true"
                        />

                        <!-- Email Address -->
                        <x-team.forms.input 
                            name="email" 
                            label="Email Address" 
                            type="email"
                            :value="old('email')"
                            placeholder="admin@company.com"
                            required="true"
                        />

                        <!-- Phone Number -->
                        <x-team.forms.input 
                            name="phone" 
                            label="Phone Number" 
                            type="tel"
                            :value="old('phone')"
                            placeholder="+1 (555) 123-4567"
                        />
                        
                        <!-- Password Section -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Security</h3>
                            
                            <div class="space-y-4">
                                <!-- Username -->
                                <x-team.forms.input 
                                    name="username" 
                                    label="Username" 
                                    type="text"
                                    :value="old('username')"
                                    placeholder="Enter your username"
                                    required="true"
                                />
                                
                                <!-- Password -->
                                <x-team.forms.input 
                                    name="password" 
                                    label="Password" 
                                    type="password"
                                    placeholder="Enter a strong password"
                                    required="true"
                                />
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    Must contain at least 8 characters with uppercase, lowercase, numbers, and symbols
                                </div>

                                <!-- Confirm Password -->
                                <x-team.forms.input 
                                    name="password_confirmation" 
                                    label="Confirm Password" 
                                    type="password"
                                    placeholder="Confirm your password"
                                    required="true"
                                />
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('setup.branch') }}" 
                               class="px-6 py-3 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </a>

                            <button 
                                type="submit" 
                                class="px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                Complete Setup
                                <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
