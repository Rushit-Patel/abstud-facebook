<!-- Student Sidebar -->
<div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 dark:bg-gray-900 lg:relative lg:z-auto transform transition-transform duration-300 ease-in-out lg:translate-x-0"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
     @click.away="sidebarOpen = false">
    
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between h-16 px-6 bg-gray-900 dark:bg-gray-950 border-b border-gray-700 dark:border-gray-600">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="ml-3 text-white font-bold text-lg">Student Portal</span>
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
            <div class="w-10 h-10 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">{{ substr(auth()->user()->name, 0, 2) }}</span>
            </div>
            <div>
                <h3 class="text-white font-medium text-sm">{{ auth()->user()->name }}</h3>
                <p class="text-gray-400 dark:text-gray-500 text-xs">Student</p>
            </div>
        </div>
    </div>

    <!-- Sidebar Navigation -->
    <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">        <!-- Dashboard -->
        <a href="{{ route('student.dashboard') }}" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 
                  {{ request()->routeIs('student.dashboard') 
                      ? 'bg-gray-700 dark:bg-gray-800 text-white border-r-2 border-indigo-500' 
                      : 'text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v0"></path>
            </svg>
            Dashboard
        </a>        <!-- Academics Section Header -->
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Academics
            </h3>
        </div>

        <!-- My Courses -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <span class="flex-1">My Courses</span>
            <span class="bg-gray-600 dark:bg-gray-700 text-gray-300 dark:text-gray-400 text-xs px-2 py-0.5 rounded-full">3</span>
        </a>

        <!-- Assignments -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="flex-1">Assignments</span>
            <span class="bg-yellow-600 text-white text-xs px-2 py-0.5 rounded-full">2</span>
        </a>

        <!-- Grades -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Grades
        </a>

        <!-- Schedule -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Schedule
        </a>

        <!-- Services Section Header -->
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Services
            </h3>
        </div>

        <!-- Library -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
            </svg>
            Library
        </a>

        <!-- Support -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>            </svg>
            Support
        </a>

        <!-- Financial Section Header -->
        <div class="pt-4 pb-2">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Financial
            </h3>
        </div>

        <!-- Payment History -->
        <a href="#" 
           class="group flex items-center px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-300 dark:text-gray-400 hover:bg-gray-700 dark:hover:bg-gray-800 hover:text-white">
            <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Payment History
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
        <form method="POST" action="{{ route('student.logout') }}">
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
