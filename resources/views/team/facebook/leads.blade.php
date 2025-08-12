@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration', 'url' => route('facebook.dashboard')],
        ['title' => 'Leads']
    ];
@endphp

<x-team.layout.app title="Facebook Integration - Leads" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Facebook Leads</h1>
                        <p class="text-gray-600">View and manage leads from Facebook Lead Ad campaigns</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('facebook.lead-forms') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                        Manage Forms
                    </a>
                    <a href="{{ route('facebook.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processed" {{ request('status') === 'processed' ? 'selected' : '' }}>Processed</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            Apply Filters
                        </button>
                        <a href="{{ route('facebook.leads') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            @if(count($leads) > 0)
                <!-- No Leads -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Leads Found</h3>
                    <p class="text-gray-600 mb-6">No leads match your current filters or no leads have been received yet.</p>
                    <div class="flex gap-3 justify-center">
                        <a href="{{ route('facebook.lead-forms') }}" class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                            Check Lead Forms
                        </a>
                        <a href="{{ route('facebook.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-6 rounded-lg transition-colors">
                            Go to Dashboard
                        </a>
                    </div>
                </div>
                                </div>
                @else
                    <!-- No Leads -->
                <!-- Leads Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left py-4 px-6 font-medium text-gray-900">Lead ID</th>
                                    <th class="text-left py-4 px-6 font-medium text-gray-900">Name</th>
                                    <th class="text-left py-4 px-6 font-medium text-gray-900">Email</th>
                                    <th class="text-left py-4 px-6 font-medium text-gray-900">Phone</th>
                                    <th class="text-left py-4 px-6 font-medium text-gray-900">Form</th>
                                    <th class="text-left py-4 px-6 font-medium text-gray-900">Page</th>
                                    <th class="text-left py-4 px-6 font-medium text-gray-900">Status</th>
                                    <th class="text-left py-4 px-6 font-medium text-gray-900">Created</th>
                                    <th class="text-left py-4 px-6 font-medium text-gray-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($leads as $lead)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-4 px-6">
                                            <a href="{{ route('facebook.leads.show', $lead) }}" class="text-purple-600 hover:text-purple-800 font-medium">
                                                {{ Str::limit($lead->facebook_lead_id, 15) }}
                                            </a>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="font-medium text-gray-900">{{ $lead->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="text-gray-900">{{ $lead->email ?? 'N/A' }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="text-gray-900">{{ $lead->phone ?? 'N/A' }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="text-gray-900">{{ $lead->facebookLeadForm->form_name ?? 'Unknown' }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="text-gray-900">{{ $lead->facebookLeadForm->facebookPage->page_name ?? 'Unknown' }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                                {{ $lead->status === 'processed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $lead->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $lead->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($lead->status) }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="text-sm text-gray-900">{{ $lead->facebook_created_time->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $lead->facebook_created_time->format('H:i') }}</div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('facebook.leads.show', $lead) }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                                    View
                                                </a>
                                                @if($lead->status === 'failed')
                                                    <form action="{{ route('facebook.leads.retry', $lead) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                                                            Retry
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($lead->status === 'pending')
                                                    <form action="{{ route('facebook.leads.mark-processed', $lead) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                            Mark Processed
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(method_exists($leads, 'hasPages') && $leads->hasPages())
                        <div class="border-t border-gray-200 px-6 py-4">
                            {{ $leads->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

                <!-- Summary Stats -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $leads->total() }}</p>
                                <p class="text-sm text-gray-600">Total Leads</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $leads->where('status', 'processed')->count() }}</p>
                                <p class="text-sm text-gray-600">Processed</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $leads->where('status', 'pending')->count() }}</p>
                                <p class="text-sm text-gray-600">Pending</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.348 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $leads->where('status', 'failed')->count() }}</p>
                                <p class="text-sm text-gray-600">Failed</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>
</x-team.layout.app>
