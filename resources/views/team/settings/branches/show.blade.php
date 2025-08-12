@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Branch Management', 'url' => route('team.settings.branches.index')],
    ['title' => $branch->branch_name]
];
@endphp

<x-team.layout.app title="Branch Details - {{ $branch->branch_name }}" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        {{ $branch->branch_name }}
                        @if($branch->is_main_branch)
                            <span class="kt-badge kt-badge-info">Main Branch</span>
                        @endif
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Branch Code: {{ $branch->branch_code }} • 
                        Status: 
                        @if($branch->is_active)
                            <span class="kt-badge kt-badge-success kt-badge-sm">Active</span>
                        @else
                            <span class="kt-badge kt-badge-danger kt-badge-sm">Inactive</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.branches.edit', $branch) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-notepad-edit"></i>
                        Edit Branch
                    </a>
                    <a href="{{ route('team.settings.branches.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-left"></i>
                        Back to Branches
                    </a>
                </div>
            </div>

            <div class="grid lg:grid-cols-3 gap-5 mb-7.5">
                <!-- Basic Information Card -->
                <div class="lg:col-span-2">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">
                                <i class="ki-filled ki-geolocation text-primary mr-2"></i>
                                Branch Information
                            </h3>
                        </div>
                        <div class="kt-card-content">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Branch Name</label>
                                        <p class="text-base font-medium">{{ $branch->branch_name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Branch Code</label>
                                        <p class="text-base font-medium">{{ $branch->branch_code }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Status</label>
                                        <p class="text-base">
                                            @if($branch->is_active)
                                                <span class="kt-badge kt-badge-success">Active</span>
                                            @else
                                                <span class="kt-badge kt-badge-danger">Inactive</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Phone Number</label>
                                        <p class="text-base">
                                            @if($branch->phone)
                                                <a href="tel:{{ $branch->phone }}" class="text-primary hover:underline">
                                                    {{ $branch->phone }}
                                                </a>
                                            @else
                                                Not provided
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Email Address</label>
                                        <p class="text-base">
                                            @if($branch->email)
                                                <a href="mailto:{{ $branch->email }}" class="text-primary hover:underline">
                                                    {{ $branch->email }}
                                                </a>
                                            @else
                                                Not provided
                                            @endif
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-secondary-foreground">Map Link</label>
                                        <p class="text-base">
                                            @if($branch->map_link)
                                                <a href="{{ $branch->map_link }}" target="_blank" class="text-primary hover:underline inline-flex items-center gap-1">
                                                    View Location <i class="ki-filled ki-directbox-default text-xs"></i>
                                                </a>
                                            @else
                                                Not provided
                                            @endif
                                        </p>
                                    </div>
                                    
                                </div>
                            </div>

                            @if($branch->address)
                                <div class="mt-6 pt-6 border-t border-border">
                                    <label class="text-sm font-medium text-secondary-foreground">Full Address</label>
                                    <p class="text-base mt-1">{{ $branch->address }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="space-y-5">
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">
                                <i class="ki-filled ki-chart-simple text-success mr-2"></i>
                                Quick Stats
                            </h3>
                        </div>
                        <div class="kt-card-content space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-secondary-foreground">Branch Type</span>
                                <span class="font-medium">
                                    @if($branch->is_main_branch)
                                        <span class="kt-badge kt-badge-info">Main Branch</span>
                                    @else
                                        <span class="kt-badge kt-badge-light">Sub Branch</span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-secondary-foreground">Created Date</span>
                                <span class="font-medium">{{ $branch->created_at?->format('M d, Y') ?: 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-secondary-foreground">Last Updated</span>
                                <span class="font-medium">{{ $branch->updated_at?->format('M d, Y') ?: 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Location Information -->
                    @if($branch->country || $branch->state || $branch->city)
                    <div class="kt-card">
                        <div class="kt-card-header">
                            <h3 class="kt-card-title">
                                <i class="ki-filled ki-map text-info mr-2"></i>
                                Location Details
                            </h3>
                        </div>
                        <div class="kt-card-content space-y-3">
                            @if($branch->country)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-secondary-foreground">Country</span>
                                    <span class="font-medium">{{ $branch->country->name ?? $branch->country }}</span>
                                </div>
                            @endif
                            @if($branch->state)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-secondary-foreground">State</span>
                                    <span class="font-medium">{{ $branch->state->name ?? $branch->state }}</span>
                                </div>
                            @endif
                            @if($branch->city)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-secondary-foreground">City</span>
                                    <span class="font-medium">{{ $branch->city->name ?? $branch->city }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Additional Details Section -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">
                        <i class="ki-filled ki-note-2 text-warning mr-2"></i>
                        Additional Information
                    </h3>
                </div>
                <div class="kt-card-content">
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label class="text-sm font-medium text-secondary-foreground">Branch Configuration</label>
                            <div class="mt-2 space-y-2">
                                <div class="flex items-center gap-2">
                                    <i class="ki-filled {{ $branch->is_active ? 'ki-check-circle text-success' : 'ki-cross-circle text-danger' }}"></i>
                                    <span class="text-sm">{{ $branch->is_active ? 'Currently Active' : 'Currently Inactive' }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="ki-filled {{ $branch->is_main_branch ? 'ki-star text-warning' : 'ki-geolocation text-muted' }}"></i>
                                    <span class="text-sm">{{ $branch->is_main_branch ? 'Main Branch' : 'Sub Branch' }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-secondary-foreground">Contact Information</label>
                            <div class="mt-2 space-y-2">
                                @if($branch->phone)
                                    <div class="flex items-center gap-2">
                                        <i class="ki-filled ki-phone text-primary"></i>
                                        <a href="tel:{{ $branch->phone }}" class="text-sm text-primary hover:underline">
                                            {{ $branch->phone }}
                                        </a>
                                    </div>
                                @endif
                                @if($branch->email)
                                    <div class="flex items-center gap-2">
                                        <i class="ki-filled ki-sms text-primary"></i>
                                        <a href="mailto:{{ $branch->email }}" class="text-sm text-primary hover:underline">
                                            {{ $branch->email }}
                                        </a>
                                    </div>
                                @endif
                                @if(!$branch->phone && !$branch->email)
                                    <p class="text-sm text-muted-foreground">No contact information available</p>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-secondary-foreground">Lead Generation Link</label>
                            <div class="mt-2 space-y-2">
                                @php
                                    $encodedBranchId = base64_encode($branch->id);
                                    $leadGenerationUrl = route('client.guest.session-branch', $encodedBranchId);
                                @endphp
                                <div class="flex items-center gap-2 p-2 bg-muted/30 rounded border">
                                    <i class="ki-filled ki-link text-info"></i>
                                    <input 
                                        type="text" 
                                        id="leadGenerationUrl" 
                                        value="{{ $leadGenerationUrl }}" 
                                        readonly 
                                        class="flex-1 bg-transparent border-0 text-xs font-mono focus:outline-none select-all"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="copyLeadGenerationUrl()" 
                                        class="kt-btn kt-btn-xs kt-btn-info"
                                        title="Copy to clipboard"
                                    >
                                        <i class="ki-filled ki-copy"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-muted-foreground">Share this link to generate leads for this branch</p>
                            </div>
                        </div>
                    </div>

                    @if($branch->address)
                        <div class="mt-6 pt-6 border-t border-border">
                            <label class="text-sm font-medium text-secondary-foreground">Complete Address</label>
                            <div class="mt-2 p-4 bg-muted/50 rounded-lg">
                                <p class="text-sm leading-relaxed">{{ $branch->address }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        function copyLeadGenerationUrl() {
            const urlField = document.getElementById('leadGenerationUrl');
            urlField.select();
            document.execCommand('copy');
            KTToast.show({
                message: "Lead generation URL copied to clipboard",
                icon: '<i class="ki-filled ki-check text-success text-xl"></i>',
                pauseOnHover: true,
                variant: "success",
            });
        }
    </script>
    @endpush
</x-team.layout.app>