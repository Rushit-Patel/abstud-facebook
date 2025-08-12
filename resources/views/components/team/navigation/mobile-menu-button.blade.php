{{-- Team Mobile Menu Button --}}
@props([
    'ariaLabel' => 'Toggle mobile menu'
])

<button @click="toggleSidebar()" 
        class="team-mobile-menu-btn lg:hidden mr-3 p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-md transition-colors duration-200"
        aria-label="{{ $ariaLabel }}">
    <svg class="w-6 h-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>
