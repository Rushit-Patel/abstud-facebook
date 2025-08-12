@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Demo', 'url' => route('team.demo.pending')],
    ['title' => 'Edit Demo']
];
@endphp
<x-team.layout.app title="Edit Demo" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Demo
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Edit demo to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.lead.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>
            <x-team.card title="Demo Information" headerClass="">
                <form action="{{ route('team.demo.update',$demoData->id) }}" method="POST" class="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <x-team.lead.demo-details
                        :getDemoData="$demoData"
                        :coaching="$coaching"
                        :user="$assignOwner"
                    />


                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.lead.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Demo
                        </button>
                    </div>
                </form>
            </x-team.card>
        </div>
    </x-slot>

@push('scripts')
    @include('team.lead.lead-js')
@endpush
</x-team.layout.app>
