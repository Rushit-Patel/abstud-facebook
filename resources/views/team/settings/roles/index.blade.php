@php
$breadcrumbs = [
    ['title' => 'Home', 'url' => route('team.dashboard')],
    ['title' => 'Settings', 'url' => route('team.settings.index')],
    ['title' => 'Role Management']
];
@endphp

<x-team.layout.app title="Role Management" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
                <div class="flex flex-col justify-center gap-2">
                    <h1 class="text-xl font-medium leading-none text-mono">
                        Role Management
                    </h1>
                    <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
                        Manage user roles and permissions
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('team.settings.roles.create', ['guard' => $guard]) }}" class="kt-btn kt-btn-primary">
                        <i class="ki-filled ki-plus"></i>
                        Add Role
                    </a>
                </div>
            </div>

            <!-- Guard Tabs -->
            <div class="kt-card mb-5">
                <div class="kt-card-content p-4">
                    <div class="flex gap-2">
                        @foreach($guards as $guardKey => $guardName)
                            <a href="{{ route('team.settings.roles.index', ['guard' => $guardKey]) }}" 
                               class="kt-btn {{ $guard === $guardKey ? 'kt-btn-primary' : 'kt-btn-secondary' }}">
                                {{ $guardName }}
                                @if($guardKey === 'web')
                                    <i class="ki-filled ki-shield-check"></i>
                                @elseif($guardKey === 'student')
                                    <i class="ki-filled ki-user"></i>
                                @elseif($guardKey === 'partner')
                                    <i class="ki-filled ki-handshake"></i>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="kt-card mb-5">
                <div class="kt-card-content">
                    <form method="GET" class="flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-64">
                            <x-team.forms.input 
                                name="search" 
                                placeholder="Search roles..." 
                                :value="request('search')" 
                                label="" />
                        </div>
                        <div class="flex gap-2">
                            <x-team.forms.button type="submit">
                                <i class="ki-filled ki-magnifier"></i>
                                Search
                            </x-team.forms.button>
                            @if(request()->hasAny(['search']))
                                <a href="{{ route('team.settings.roles.index', ['guard' => $guard]) }}" class="kt-btn kt-btn-secondary">
                                    <i class="ki-filled ki-cross"></i>
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Roles Table -->
            <div class="kt-card">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">Roles ({{ $roles->total() }})</h3>
                </div>
                <div class="kt-card-content p-0">
                    @if($roles->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="kt-table">
                                <thead>
                                    <tr>
                                        <th class="kt-table-th">Role Name</th>
                                        <th class="kt-table-th text-center">Users</th>
                                        <th class="kt-table-th text-center">Permissions</th>
                                        <th class="kt-table-th text-center">Created</th>
                                        <th class="kt-table-th text-center w-24">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        <tr>
                                            <td class="kt-table-td">
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center justify-center w-8 h-8 rounded-full 
                                                                {{ $role->name === 'Super Admin' ? 'bg-danger-light text-danger' : 
                                                                   ($role->name === 'Admin' ? 'bg-warning-light text-warning' : 
                                                                   ($role->name === 'Manager' ? 'bg-info-light text-info' : 'bg-secondary-light text-secondary')) }}">
                                                        <i class="ki-filled ki-shield text-sm"></i>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('team.settings.roles.show', $role) }}" 
                                                           class="font-medium text-primary hover:text-primary-active">
                                                            {{ $role->name }}
                                                        </a>
                                                        <div class="text-sm text-secondary-foreground">
                                                            {{ $role->guard_name }} guard
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="kt-table-td text-center">
                                                <span class="kt-badge kt-badge-outline kt-badge-secondary">
                                                    {{ $role->users_count }}
                                                </span>
                                            </td>
                                            <td class="kt-table-td text-center">
                                                <span class="kt-badge kt-badge-outline kt-badge-primary">
                                                    {{ $role->permissions_count }}
                                                </span>
                                            </td>
                                            <td class="kt-table-td text-center text-sm text-secondary-foreground">
                                                {{ $role->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="kt-table-td text-center">
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('team.settings.roles.show', $role) }}" 
                                                       class="kt-btn kt-btn-sm kt-btn-secondary kt-btn-icon"
                                                       title="View">
                                                        <i class="ki-filled ki-eye"></i>
                                                    </a>
                                                    <a href="{{ route('team.settings.roles.edit', $role) }}" 
                                                       class="kt-btn kt-btn-sm kt-btn-primary kt-btn-icon"
                                                       title="Edit">
                                                        <i class="ki-filled ki-pencil"></i>
                                                    </a>
                                                    @if(!in_array($role->name, ['Super Admin', 'Admin', 'Manager', 'Staff']))
                                                        <button onclick="confirmDelete('{{ $role->name }}', '{{ route('team.settings.roles.destroy', $role) }}')" 
                                                                class="kt-btn kt-btn-sm kt-btn-danger kt-btn-icon"
                                                                title="Delete">
                                                            <i class="ki-filled ki-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($roles->hasPages())
                            <div class="kt-card-footer">
                                {{ $roles->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-10">
                            <i class="ki-filled ki-shield-cross text-4xl text-muted-foreground mb-4"></i>
                            <h3 class="text-lg font-medium mb-2">No Roles Found</h3>
                            <p class="text-secondary-foreground mb-4">
                                @if(request('search'))
                                    No roles match your search criteria.
                                @else
                                    Get started by creating your first role.
                                @endif
                            </p>
                            @if(request('search'))
                                <a href="{{ route('team.settings.roles.index') }}" class="kt-btn kt-btn-secondary">
                                    <i class="ki-filled ki-cross"></i>
                                    Clear Search
                                </a>
                            @else
                                <a href="{{ route('team.settings.roles.create') }}" class="kt-btn kt-btn-primary">
                                    <i class="ki-filled ki-plus"></i>
                                    Create First Role
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    @push('scripts')
    <script>
        function confirmDelete(roleName, deleteUrl) {
            if (confirm(`Are you sure you want to delete the role "${roleName}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = deleteUrl;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endpush
</x-team.layout.app>
