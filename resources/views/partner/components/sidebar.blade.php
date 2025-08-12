<!-- Partner Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 dark:bg-gray-900 lg:relative lg:z-auto transform transition-transform duration-300 ease-in-out lg:translate-x-0"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     @click.away="sidebarOpen = false">
    
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-6 bg-gray-900 dark:bg-gray-950 border-b border-gray-700 dark:border-gray-600">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-teal-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                </svg>
            </div>
            <span class="ml-3 text-white font-bold text-lg">Partner Portal</span>
        </div>
        <!-- Mobile close button -->
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Organization Info -->
    <div class="px-4 py-4 border-b border-gray-700 dark:border-gray-600">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-r from-green-400 to-teal-500 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">{{ substr(auth()->user()->name, 0, 2) }}</span>
            </div>
            <div>
                <h3 class="text-white font-medium text-sm">{{ auth()->user()->name }}</h3>
                <p class="text-gray-400 dark:text-gray-500 text-xs">Partner</p>
            </div>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">        <!-- Dashboard -->
        <a href="{{ route('partner.dashboard') }}" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 
                  {{ request()->routeIs('partner.dashboard') 
                      ? 'bg-gray-700 dark:bg-gray-800 text-white border-r-2 border-teal-500' 
                      : 'text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0"></path>
            </svg>
            Dashboard
        </a>        <!-- Student Management Section Header -->
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Student Management
            </h3>
        </div>

        <!-- Student Applications -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="flex-1">Applications</span>
            <span class="bg-gray-600 dark:bg-gray-700 text-gray-300 dark:text-gray-400 text-xs px-2 py-0.5 rounded-full">0</span>
        </a>

        <!-- My Students -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <span class="flex-1">My Students</span>
            <span class="bg-gray-600 dark:bg-gray-700 text-gray-300 dark:text-gray-400 text-xs px-2 py-0.5 rounded-full">0</span>
        </a>

        <!-- Progress Tracking -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Progress Tracking
        </a>

        <!-- Resources Section Header -->
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Resources
            </h3>
        </div>

        <!-- Documents -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Documents
        </a>        <!-- Communication -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            Communication
        </a>

        <!-- Settings Section Header -->
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Settings
            </h3>
        </div>

        <!-- Profile Settings -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Profile Settings
        </a>
    </nav>

    <!-- Sidebar Footer -->
    <div class="border-t border-gray-700 dark:border-gray-600 p-4">
        <!-- Logout -->
        <form method="POST" action="{{ route('partner.logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full px-3 py-2 text-sm font-medium text-gray-300 dark:text-gray-400 rounded-md hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white transition-all duration-200">
                <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>
