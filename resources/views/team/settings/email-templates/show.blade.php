@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Email Templates', 'url' => route('team.settings.email-templates.index')],
    ['title' => 'View Template']
];
@endphp

<x-team.layout.app title="Email Template Preview" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Email Template Preview
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        {{ $emailTemplate->name }}
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.email-templates.edit', $emailTemplate->id) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-pencil"></i>
                        Edit Template
                    </a>
                    <a href="{{ route('team.settings.email-templates.index') }}" class="kt-btn kt-btn-light">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Template Info -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-7.5 mb-5">
                <div class="col-span-1 lg:col-span-1">
                    <x-team.card title="Template Information">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                                <p class="text-sm text-gray-900">{{ $emailTemplate->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                <p class="text-sm text-gray-900">{{ $emailTemplate->subject }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                <p class="text-sm text-gray-900">{{ $emailTemplate->category ?? 'General' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <span class="kt-badge {{ $emailTemplate->is_active ? 'kt-badge-success' : 'kt-badge-secondary' }}">
                                    {{ $emailTemplate->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            @if($emailTemplate->is_system)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">System Template</label>
                                    <div class="flex items-center">
                                        <i class="ki-filled ki-lock text-warning mr-2"></i>
                                        <span class="text-sm text-warning">This is a system template</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-team.card>
                </div>
                <div class="col-span-1 lg:col-span-2">
                    <x-team.card title="Subject: {{ $emailTemplate->subject }}" class="mb-5">
                        <iframe 
                            id="emailIframe-{{ $emailTemplate->id }}" 
                            class="email-iframe" 
                            src="{{ route('team.settings.email-templates.preview', $emailTemplate->id) }}" 
                            sandbox="allow-same-origin allow-scripts"
                            style="width: 100%; height: 600px; border: none; transition: opacity 0.3s;">
                        </iframe>
                    </x-team.card>
                </div>
            </div>
        </div>
    </x-slot>
</x-team.layout.app>