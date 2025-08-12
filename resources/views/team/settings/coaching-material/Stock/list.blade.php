@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Coaching Material Stock Management']
];
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/dataTables.css') }}">
@endpush

<x-team.layout.app title="Coaching Material Stock Management" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Coaching Material Stock Management
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Manage coaching-material and their status configurations
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.coaching-material.stock', $id) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Add Coaching Material Stock
                    </a>
                </div>
            </div>

            <x-team.card title="Coaching Material Stock List" headerClass="">
                <div class="grid lg:grid-cols-1 gap-y-5 lg:gap-7.5 items-stretch pb-5">
                <div class="lg:col-span-1">

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">#</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Stock Date</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Stock</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Branch</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Remarks</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-600">Added By</th>
                                    @if(auth()->user()->can('coaching-material:edit') || auth()->user()->can('coaching-material:delete'))
                                        <th class="px-4 py-2 text-left font-medium text-gray-600">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($getStock as $index => $stock)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-800">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 text-gray-800">{{ $stock->stock_date ? date('d M Y', strtotime($stock->stock_date)) : '-' }}</td>
                                        <td class="px-4 py-2 text-gray-800">{{ $stock->stock ?? '-' }}</td>
                                        <td class="px-4 py-2 text-gray-800">{{ $stock?->getBranch?->branch_name ?? '-' }}</td>
                                        <td class="px-4 py-2 text-gray-800 max-w-[200px] truncate whitespace-nowrap overflow-hidden" title="{{ $stock->remarks }}">
                                            {{ $stock->remarks ?? '-' }}
                                        </td>

                                        <td class="px-4 py-2 text-gray-800">{{ $stock->getAddedBy->name ?? '-' }}</td>

                                        @if(auth()->user()->can('coaching-material:edit') || auth()->user()->can('coaching-material:delete'))
                                            <td class="px-4 py-2 text-gray-800">
                                                @haspermission('coaching-material:edit')
                                                    <a href="{{ route('team.settings.coaching-material.stock.edit', [$id, $stock->id]) }}" class="kt-btn kt-btn-sm kt-btn-primary">
                                                        <i class="ki-filled ki-pencil"></i>
                                                    </a>
                                                @endhaspermission
                                                @haspermission('coaching-material:delete')
                                                    <button type="delete" class="kt-btn-sm kt-btn-destructive" data-kt-modal-toggle="#stock_delete_modal" data-form_action="{{route('team.settings.coaching-material.stock.destroy', $stock->id)}}">
                                                        <i class="ki-filled ki-trash text-md"></i>
                                                    </button>
                                                @endhaspermission
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    </div>
                </div>
            </x-team.card>
        </div>

    </x-slot>

    @push('scripts')

    @endpush
</x-team.layout.app>

<x-team.modals.delete-modal
    id="stock_delete_modal"
    title="Delete Stock"
    formId="deleteCountryForm"
    message="Are you sure you want to delete this Stock? This action cannot be undone."
/>
