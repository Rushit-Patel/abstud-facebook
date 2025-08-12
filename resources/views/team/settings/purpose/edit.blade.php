@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Purpose', 'url' => route('team.settings.purpose.index')],
    ['title' => 'Edit Purpose']
];
@endphp

<x-team.layout.app title="Edit Purpose" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Edit Purpose: {{ $purpose->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Update purpose information
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
                <form action="{{ route('team.settings.purpose.update', $purpose) }}" method="POST" enctype="multipart/form-data" class="form">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5">
                        <div class="col-span-1">
                        <x-team.card title="Basic Information">
                            <div class="grid gap-5">
                                <x-team.forms.input
                                    name="name"
                                    label="Purpose Name"
                                    type="text"
                                    placeholder="Enter purpose name"
                                    :value="old('name', $purpose->name)"
                                    required />

                                <!-- Purpose Image -->
                                <div class="flex flex-col gap-1">
                                    <label for="image" class="kt-form-label font-normal text-mono">
                                        Purpose Image
                                    </label>
                                    <div class="grid grid-cols-4 items-center gap-4">
                                        <div class="col-span-1">
                                            @if($purpose->image)
                                                <div class="w-16 h-16 rounded-lg overflow-hidden border border-input">
                                                    <img src="{{ asset('storage/' . $purpose->image) }}"
                                                        alt="{{ $purpose->name }}"
                                                        class="w-full h-full object-cover"
                                                        id="current-image">
                                                </div>
                                            @else
                                                <div class="size-16 rounded-lg border-2 border-dashed border-input flex items-center justify-center bg-background" id="image-preview">
                                                    <i class="ki-filled ki-picture text-xl text-muted-foreground"></i>
                                                </div>
                                            @endif
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
                                                @if($purpose->image)
                                                    <p class="text-xs text-blue-600 mt-1">
                                                        Current image will be replaced if you select a new one
                                                    </p>
                                                @endif
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
                                    placeholder="Enter priority "
                                    :value="old('priority', $purpose->priority)"
                                    required />

                                <div class="flex flex-col gap-1 mt-4">
                                    <label class="kt-form-label font-normal text-mono">Status</label>
                                    <label class="kt-label">
                                        <input class="kt-checkbox kt-checkbox-sm"
                                            name="status"
                                            type="checkbox"
                                            value="1"
                                            {{ old('status', $purpose->status) ? 'checked' : '' }}
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

                    <!-- Additional Information -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 py-5 border-t border-gray-200">
                        <div class="flex flex-col gap-2.5">
                            <label class="form-label">Created At</label>
                            <div class="text-sm text-gray-700">
                                {{ $purpose->created_at->format('M d, Y \a\t h:i A') }}
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            <label class="form-label">Last Updated</label>
                            <div class="text-sm text-gray-700">
                                {{ $purpose->updated_at->format('M d, Y \a\t h:i A') }}
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
                            Update Purpose
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
                    const currentImage = $('#current-image');
                    const imagePreview = $('#image-preview');

                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (currentImage.length) {
                                currentImage.attr('src', e.target.result);
                            } else if (imagePreview.length) {
                                imagePreview.html(`
                                    <img src="${e.target.result}"
                                         alt="Preview"
                                         class="w-full h-full object-cover rounded-lg"
                                         id="current-image">
                                `);
                            }
                        };
                        reader.readAsDataURL(file);
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
