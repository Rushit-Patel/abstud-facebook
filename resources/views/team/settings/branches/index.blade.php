@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Branch Management']
];
@endphp

<x-team.layout.app title="Branch Management" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Branch Management
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Manage branch locations and configurations
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.branches.create') }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Add Branch
                    </a>
                </div>
            </div>

            <div class="grid lg:grid-cols-4 gap-5 mb-7.5">
                <div class="kt-card">
                    <div class="kt-card-content flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-secondary-foreground">Total Branches</h3>
                            <p class="text-2xl font-bold">{{ $branches->total() }}</p>
                        </div>
                        <div class="kt-badge kt-badge-primary kt-badge-lg">
                            <i class="ki-filled ki-geolocation text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="kt-card">
                    <div class="kt-card-content flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-secondary-foreground">Active Branches</h3>
                            <p class="text-2xl font-bold text-success">{{ $branches->where('is_active', true)->count() }}</p>
                        </div>
                        <div class="kt-badge kt-badge-success kt-badge-lg">
                            <i class="ki-filled ki-check text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="kt-card">
                    <div class="kt-card-content flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-secondary-foreground">Inactive Branches</h3>
                            <p class="text-2xl font-bold text-danger">{{ $branches->where('is_active', false)->count() }}</p>
                        </div>
                        <div class="kt-badge kt-badge-danger kt-badge-lg">
                            <i class="ki-filled ki-cross text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="kt-card">
                    <div class="kt-card-content flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-medium text-secondary-foreground">Main Branch</h3>
                            <p class="text-2xl font-bold text-info">{{ $branches->where('is_main_branch', true)->count() }}</p>
                        </div>
                        <div class="kt-badge kt-badge-info kt-badge-lg">
                            <i class="ki-filled ki-star text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Branches</h3>
                </div>
                <div class="kt-card-content">
                    @if($branches->isEmpty())
                        <div class="text-center py-10">
                            <i class="ki-filled ki-geolocation text-4xl text-muted-foreground mb-4"></i>
                            <h3 class="text-lg font-medium mb-2">No Branches Found</h3>
                            <p class="text-secondary-foreground mb-4">Start by adding your first branch location.</p>
                            <a href="{{ route('team.settings.branches.create') }}" class="kt-btn kt-btn-primary">
                                <i class="ki-filled ki-plus"></i>
                                Add First Branch
                            </a>
                        </div>
                    @else
                        <div class="kt-table-wrapper">
                            <table class="kt-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Address</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($branches as $branch)
                                        <tr>
                                            <td>{{ $branch->branch_code }}</td>
                                            <td>{{ $branch->branch_name }}</td>
                                            <td>{{ $branch->address ?: 'Not set' }}</td>
                                            <td>{{ $branch->phone ?: 'Not set' }}</td>
                                            <td>{{ $branch->email ?: 'Not set' }}</td>
                                            <td>
                                                @if($branch->is_active)
                                                    <span class="kt-badge kt-badge-success">Active</span>
                                                @else
                                                    <span class="kt-badge kt-badge-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="flex gap-2">
                                                    <a href="{{ route('team.settings.branches.show', $branch) }}" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost" title="View Details">
                                                        <i class="ki-filled ki-eye"></i>
                                                    </a>

                                                    <a href="{{ route('team.settings.branches.edit', $branch) }}" class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost" title="Edit Branch">
                                                        <i class="ki-filled ki-notepad-edit"></i>
                                                    </a>

                                                    <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-ghost" type="delete" data-kt-modal-toggle="#delete_modal" data-form_action="{{route('team.settings.branches.destroy', $branch->id) }}" title="Delete Branch">
                                                        <i class="ki-filled ki-trash text-1xl"></i>
                                                    </button>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($branches->hasPages())
                            <div class="mt-4">
                                {{ $branches->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Branch-specific functionality can go here
            // Global toast handling is now managed in the app layout
            
            // Example of using global toast utilities:
            // window.showToast.success('Custom success message');
            // window.showToast.error('Custom error message');
        });
    </script>
    <x-team.modals.delete-modal
        id="delete_modal"
        title="Delete Branch"
        formId="deleteCountryForm"
        message="Are you sure you want to delete this branch? This action cannot be undone."
    />

    @endpush
</x-team.layout.app>
