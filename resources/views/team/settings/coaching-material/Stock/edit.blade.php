@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Coaching Material Stock', 'url' => route('team.settings.coaching-material.index')],
    ['title' => 'Edit Coaching Material Stock']
];
@endphp

<x-team.layout.app title="Edit Coaching Material Stock" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Coaching Material Stock
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Edit coaching-material-stock to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.coaching-material.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Coaching Material Stock Information" headerClass="">
                <form action="{{ route('team.settings.coaching-material.stock.update' ,[$material ,$editStock->id]) }}" method="POST" class="form">
                    @csrf
                    @method('PUT')

                    <x-team.card title="Coaching Information">
                        @php
                            $gridCols = Auth::user()->hasPermissionTo('coaching-material:show-all') ? 'lg:grid-cols-3' : 'lg:grid-cols-2';
                        @endphp

                        <div class="grid grid-cols-1 {{ $gridCols }} gap-5 py-5">

                            {{-- Branch Field --}}
                            @if(Auth::user()->hasPermissionTo('coaching-material:show-all'))
                                <div>
                                    <x-team.forms.select
                                        name="branch"
                                        label="Branch"
                                        :options="$branch"
                                        :selected="null"
                                        :selected="old('branch', $editStock?->branch_id)"
                                        placeholder="Select branch"
                                        required
                                        searchable="true"
                                    />
                                </div>
                            @else
                                <input type="hidden" name="branch" value="{{ Auth::user()->branch_id }}">
                            @endif

                            {{-- First Name --}}
                            <div>
                                <x-team.forms.datepicker
                                        label="Stock Date"
                                        name="stock_date"
                                        id="stock_date"
                                        placeholder="Select stock date"
                                        required="true"
                                        dateFormat="Y-m-d"
                                        :value="old('stock_date', \Carbon\Carbon::parse($editStock->stock_date)->format('d-m-Y'))"
                                        class="w-full flatpickr" />
                            </div>

                            <div>
                                <x-team.forms.input name="stock" label="Stock" type="number"
                                        placeholder="Enter stock" :value="old('stock', $editStock?->stock)" required />
                            </div>

                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-1 gap-5 py-5">
                            <div class="col-span-1">
                                <div class="grid gap-5">
                                    <x-team.forms.textarea
                                        id="remarks"
                                        name="remarks"
                                        label="Remarks"
                                        :value="old('remarks', $editStock?->remarks)"
                                        placeholder="Enter Remarks"
                                        />
                                </div>
                            </div>
                        </div>
                    </x-team.card>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.coaching-material.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Update Coaching Material Stock
                        </button>
                    </div>
                </form>
            </x-team.card>

        </div>
    </x-slot>

    @push('scripts')
        <script>
            // Form validation and enhancement
            $(document).ready(function() {
                // Add any additional form enhancements here

                // Focus on name field
                $('#name').focus();

                // Form submission handling
                $('form').on('submit', function() {
                    // Disable submit button to prevent double submission
                    $(this).find('button[type="submit"]').prop('disabled', true);
                });
            });
        </script>
    @endpush
</x-team.layout.app>
