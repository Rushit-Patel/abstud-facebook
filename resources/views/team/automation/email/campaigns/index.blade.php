@php
$breadcrumbs = [
    ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
    ['title' => 'Automation', 'url' => route('team.automation.index')],
    ['title' => 'Email Campaigns']
];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/dataTables.css') }}">
@endpush

<x-team.layout.app title="Email Campaigns" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Email Campaigns
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Manage automated email campaigns and sequences
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.automation.email.campaigns.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Create Campaign
                    </a>
                </div>
            </div>

            <x-team.card title="Email Campaigns List" headerClass="">
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
            function duplicateCampaign(campaignId) {
                if (confirm('Are you sure you want to duplicate this email campaign?')) {
                    // Create a form and submit it
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/team/automation/email-campaigns/' + campaignId + '/duplicate';
                    
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
            
            function toggleCampaignStatus(campaignId) {
                if (confirm('Are you sure you want to toggle the campaign status?')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/team/automation/email-campaigns/' + campaignId + '/toggle-status';
                    
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
            
            function deleteCampaign(campaignId, campaignName) {
                if (confirm('Are you sure you want to delete the campaign "' + campaignName + '"? This action cannot be undone.')) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/team/automation/email-campaigns/' + campaignId;
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Add method override
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        </script>
    @endpush
</x-team.layout.app>
