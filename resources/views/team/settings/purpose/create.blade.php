@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Purpose', 'url' => route('team.settings.purpose.index')],
    ['title' => 'Create Purpose']
];
@endphp

<x-team.layout.app title="Create Purpose" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Create New Purpose
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Add a new purpose to the system
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.purpose.index') }}" class="kt-btn kt-btn-secondary">
                        <i class="ki-filled ki-arrow-left"></i>
                        Back to List
                    </a>
                </div>
            </div>

            <x-team.card title="Purpose Information" headerClass="">
                <form action="{{ route('team.settings.purpose.store') }}" method="POST" enctype="multipart/form-data" class="form">
                    @csrf

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">

                        <div class="col-span-1">
                            <x-team.card title="Basic Information">
                                <div class="grid gap-5">
                                    <!-- Purpose Name -->
                                    <x-team.forms.input
                                        name="name"
                                        label="Purpose Name"
                                        type="text"
                                        placeholder="Enter purpose name"
                                        :value="old('name')"
                                        required />

                                    <!-- Purpose Image -->
                                    <div class="flex flex-col gap-1">
                                        <label for="image" class="kt-form-label font-normal text-mono">
                                            Purpose Image
                                        </label>
                                        <div class="grid grid-cols-4 items-center gap-4">
                                            <div class="col-span-1">
                                                <div class="size-16 rounded-lg border-2 border-dashed border-input flex items-center justify-center bg-background">
                                                    <i class="ki-filled ki-picture text-xl text-muted-foreground"></i>
                                                </div>
                                            </div>
                                            <div class="col-span-3">
                                                <div class="flex-1">
                                                    <input
                                                        type="file"
                                                        id="image"
                                                        name="image"
                                                        accept="image/*"
                                                        class="kt-input"
                                                    />
                                                    <p class="text-xs text-muted-foreground mt-1">
                                                        Upload purpose image (PNG, JPG, GIF, SVG). Max size: 2MB
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        @error('image')
                                        <span class="text-destructive text-sm mt-1">
                                            {{ $errors->first('image') }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </x-team.card>
                        </div>

                         <div class="col-span-1">
                            <div class="grid gap-5 lg:gap-7.5">
                                <!-- Status -->
                                <x-team.card title="Status Settings">

                                <x-team.forms.input
                                        name="priority"
                                        label="Priority"
                                        type="number"
                                        placeholder="Enter priority e.g 1"
                                        :value="old('priority')"
                                        required />

                                    <div class="flex flex-col gap-1 mt-4">
                                        <label class="kt-form-label font-normal text-mono">Status</label>
                                        <label class="kt-label">
                                            <input class="kt-checkbox kt-checkbox-sm"
                                                name="status"
                                                type="checkbox"
                                                value="1"
                                                {{ old('status', true) ? 'checked' : '' }}
                                            />
                                            <span class="kt-checkbox-label">
                                                Uncheck to make this purpose inactive
                                            </span>
                                        </label>
                                    </div>
                                </x-team.card>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end gap-2.5 pt-5 border-t border-gray-200">
                        <a href="{{ route('team.settings.purpose.index') }}" class="kt-btn kt-btn-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="kt-btn kt-btn-primary">
                            <i class="ki-filled ki-check"></i>
                            Create Purpose
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
                // Focus on name field
                $('#name').focus();

                // Image preview functionality
                $('#image').on('change', function(e) {
                    const file = e.target.files[0];
                    const preview = $(this).closest('.grid').find('.size-16');

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.html(`
                                <img src="${e.target.result}"
                                     alt="Preview"
                                     class="w-full h-full object-cover rounded-lg">
                            `);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.html(`
                            <i class="ki-filled ki-picture text-xl text-muted-foreground"></i>
                        `);
                    }
                });

                // Form submission handling
                $('form').on('submit', function() {
                    // Disable submit button to prevent double submission
                    $(this).find('button[type="submit"]').prop('disabled', true);
                });
            });
        </script>
    @endpush
</x-team.layout.app>
