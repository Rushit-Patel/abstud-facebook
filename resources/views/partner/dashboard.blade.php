@extends('partner.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Partner Dashboard
                </h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Welcome, {{ $partner->name }}
                    </span>
                    <form action="{{ route('partner.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Partner Information
                    </h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Partner ID</h4>
                            <p class="text-gray-900 dark:text-white">{{ $partner->partner_id }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Name</h4>
                            <p class="text-gray-900 dark:text-white">{{ $partner->name }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Email</h4>
                            <p class="text-gray-900 dark:text-white">{{ $partner->email }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Company</h4>
                            <p class="text-gray-900 dark:text-white">{{ $partner->company_name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Phone</h4>
                            <p class="text-gray-900 dark:text-white">{{ $partner->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Website</h4>
                            <p class="text-gray-900 dark:text-white">
                                @if($partner->website)
                                    <a href="{{ $partner->website }}" target="_blank" class="text-blue-600 hover:text-blue-700">
                                        {{ $partner->website }}
                                    </a>
                                @else
                                    Not provided
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-purple-50 dark:bg-purple-900 border border-purple-200 dark:border-purple-700 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-purple-900 dark:text-purple-200">
                            Welcome to {{ $company->company_name ?? 'Partner Portal' }}
                        </h3>
                        <p class="text-purple-700 dark:text-purple-300">
                            Your partner portal is ready! Additional features will be available soon.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
