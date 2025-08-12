@php
    $breadcrumbs = [
        ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
        ['title' => 'Automation', 'url' => route('team.automation.index')],
        ['title' => 'WhatsApp', 'url' => route('team.automation.whatsapp.index')],
        ['title' => 'Logs']
    ];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/dataTables.css') }}">
@endpush

<x-team.layout.app title="WhatsApp Message Logs" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        WhatsApp Message Logs
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Monitor WhatsApp message delivery status and performance
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.automation.whatsapp.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to WhatsApp Dashboard
                    </a>
                </div>
            </div>

            <x-team.card title="WhatsApp Message Logs" headerClass="">
                <div class="grid lg:grid-cols-1 gap-y-5 lg:gap-7.5 items-stretch pb-5">
                    <div class="lg:col-span-1">
                        {{ $dataTable->table() }}
                    </div>
                </div>
            </x-team.card>
        </div>
    </x-slot>
    @push('scripts')
        
        {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
        
        <script>
            function viewMessageDetails(messageId) {
                // You can implement a modal or redirect to view message details
                alert('View message details for ID: ' + messageId);
            }
            
            function retryMessage(messageId) {
                if (confirm('Are you sure you want to retry this message?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }

            function resendMessage(messageId) {
                if (confirm('Are you sure you want to resend this message?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    @endpush
</x-team.layout.app>
