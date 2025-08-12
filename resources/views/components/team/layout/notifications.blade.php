{{-- Team Notifications Component --}}
@props([
    'data' => [],
    'appData' => [],
    'showBadge' => true
])
@php
// Use notification data from appData (TeamAppComposer) or fallback to data prop
$notificationData = $appData['notifications'] ?? $data;
// Ensure default structure
$notificationData = array_merge([
    'hasUnread' => false,
    'count' => 0,
    'items' => [],
    'total' => 0
], $notificationData);

// Separate notifications by type for different tabs
$clientNotifications = collect($notificationData['items'])->whereNotIn('type', ['team'])->take(10);
$teamNotifications = collect($notificationData['items'])->where('type', 'team')->take(10);
@endphp

<!-- Notifications -->
<button class="kt-btn kt-btn-ghost kt-btn-icon size-9 rounded-full hover:bg-primary/10 relative  hover:[&_i]:text-primary"
        data-kt-drawer-toggle="#notifications_drawer">
    
    @if($showBadge && $notificationData['hasUnread'])
        <span class="rounded-full absolute top-0 end-2 rtl:start-0 transform translate-x-full">
            <span class="kt-badge rounded-full kt-badge-outline kt-badge-success kt-badge-sm">
                {{ $notificationData['count'] }}
            </span>
            
        </span>
    @endif
    <i class="ki-filled ki-notification-status text-lg"></i>
</button>

<!--Notifications Drawer-->
<div class="hidden kt-drawer kt-drawer-end card flex-col max-w-[90%] w-[450px] top-5 bottom-5 end-5 rounded-xl border border-border"
    data-kt-drawer="true" data-kt-drawer-container="body" id="notifications_drawer">
    <div class="flex items-center justify-between gap-2.5 text-sm text-mono font-semibold px-5 py-2.5 border-b border-b-border"
        id="notifications_header">
        Notifications
        <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-drawer-dismiss="true">
            <i class="ki-filled ki-cross">
            </i>
        </button>
    </div>
    <div class="kt-tabs kt-tabs-line justify-between px-5 mb-2" data-kt-tabs="true" id="notifications_tabs">
        <div class="flex items-center gap-5">
            <button class="kt-tab-toggle py-3 active" data-kt-tab-toggle="#notifications_tab_client">
                Client 
                <span
                    class="rounded-full bg-green-500 size-[5px] absolute top-2 rtl:start-0 end-0 transform translate-y-1/2 translate-x-full">
                </span>
            </button>
            <button class="kt-tab-toggle py-3" data-kt-tab-toggle="#notifications_tab_team">
                Team
            </button>
        </div>
    </div>
    <div class="grow flex flex-col" id="notifications_tab_client">
        <div class="grow kt-scrollable-y-auto" data-kt-scrollable="true" data-kt-scrollable-dependencies="#header"
            data-kt-scrollable-max-height="auto" data-kt-scrollable-offset="150px">
            <div class="flex flex-col gap-5 pt-3 pb-4">
                @forelse($clientNotifications as $notification)
                    <div class="flex grow gap-2.5 px-5" id="notification_request_{{ $notification['id'] }}">
                        <div class="flex items-center justify-center size-8 bg-{{ $notification['color'] }}-50 rounded-full border border-{{ $notification['color'] }}-200 dark:border-{{ $notification['color'] }}-950">
                            <i class="{{ $notification['icon'] }} text-lg p-2 text-{{ $notification['color'] }}-500"></i>
                        </div>
                        <div class="flex flex-col gap-3.5 grow">
                            <div class="flex flex-col gap-1">
                                <div class="text-sm font-medium mb-px">
                                    <span class="text-secondary-foreground">
                                        {{ $notification['title'] }}
                                    </span>
                                    @if(isset($notification['data']['client_name']))
                                        <a class="hover:text-primary text-mono font-semibold" href="{{ $notification['link'] }}">
                                            {{ $notification['data']['client_name'] }}
                                        </a>
                                    @endif
                                </div>
                                <span class="flex items-center text-xs font-medium text-muted-foreground">
                                    {{ $notification['time_ago'] }}
                                    @if(isset($notification['data']['lead_source']))
                                        <span class="rounded-full size-1 bg-mono/30 mx-1.5"></span>
                                        {{ $notification['data']['lead_source'] }}
                                    @endif
                                </span>
                            </div>
                            
                            @if($notification['message'])
                                <div class="kt-card shadow-none flex items-center flex-row justify-between gap-1.5 px-2.5 py-2 rounded-lg bg-muted/70">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-mono text-xs">
                                            {{ $notification['message'] }}
                                        </span>
                                        @if(isset($notification['data']['assigned_agent']))
                                            <span class="text-muted-foreground font-medium text-xs">
                                                Agent: {{ $notification['data']['assigned_agent'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                            <div class="flex flex-wrap gap-2.5">
                                @if(!$notification['is_seen'])
                                    <button class="kt-btn kt-btn-mono kt-btn-sm" onclick="markAsRead({{ $notification['id'] }})">
                                        Mark as Read
                                    </button>
                                @endif
                                @if($notification['link'])
                                    <a href="{{ $notification['link'] }}" class="kt-btn kt-btn-outline kt-btn-sm">
                                        View
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @unless($loop->last)
                        <div class="border-b border-b-border"></div>
                    @endunless
                @empty
                    <div class="flex flex-col items-center justify-center py-10 px-5">
                        <i class="ki-filled ki-notification-off text-4xl text-muted-foreground/50 mb-4"></i>
                        <span class="text-sm text-muted-foreground">No client notifications</span>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="border-b border-b-border">
        </div>
        <div class="grid grid-cols-1 p-5 gap-2.5 justify-end" id="notifications_inbox_footer">
            <button class="kt-btn kt-btn-outline justify-center" onclick="markAllAsRead('client')">
                Mark all as read
            </button>
        </div>
    </div>
    <div class="grow flex flex-col hidden" id="notifications_tab_team">
        <div class="grow kt-scrollable-y-auto" data-kt-scrollable="true" data-kt-scrollable-dependencies="#header"
            data-kt-scrollable-max-height="auto" data-kt-scrollable-offset="150px">
            <div class="flex flex-col gap-5 pt-3 pb-4">
                @forelse($teamNotifications as $notification)
                    <div class="flex grow gap-2 px-5">
                        <div class="flex items-center justify-center size-8 bg-{{ $notification['color'] }}-50 rounded-full border border-{{ $notification['color'] }}-200 dark:border-{{ $notification['color'] }}-950">
                            <i class="{{ $notification['icon'] }} text-lg text-{{ $notification['color'] }}-500"></i>
                        </div>
                        <div class="flex flex-col gap-3 grow" id="notification_request_{{ $notification['id'] }}">
                            <div class="flex flex-col gap-1">
                                <div class="text-sm font-medium mb-px">
                                    <span class="text-secondary-foreground">
                                        {{ $notification['title'] }}
                                    </span>
                                    @if($notification['link'])
                                        <a class="hover:text-primary text-mono font-semibold" href="{{ $notification['link'] }}">
                                            View Details
                                        </a>
                                    @endif
                                </div>
                                <span class="flex items-center text-xs font-medium text-muted-foreground">
                                    {{ $notification['time_ago'] }}
                                    <span class="rounded-full size-1 bg-mono/30 mx-1.5"></span>
                                    {{ ucfirst($notification['type']) }}
                                </span>
                            </div>
                            
                            @if($notification['message'])
                                <div class="kt-card shadow-none p-2.5 rounded-lg bg-muted/70">
                                    <span class="text-xs text-secondary-foreground">
                                        {{ $notification['message'] }}
                                    </span>
                                </div>
                            @endif
                            
                            <div class="flex flex-wrap gap-2.5">
                                @if(!$notification['is_seen'])
                                    <button class="kt-btn kt-btn-mono kt-btn-sm" onclick="markAsRead({{ $notification['id'] }})">
                                        Mark as Read
                                    </button>
                                @endif
                                @if($notification['link'])
                                    <a href="{{ $notification['link'] }}" class="kt-btn kt-btn-outline kt-btn-sm">
                                        View Details
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    @unless($loop->last)
                        <div class="border-b border-b-border"></div>
                    @endunless
                @empty
                    <div class="flex flex-col items-center justify-center py-10 px-5">
                        <i class="ki-filled ki-notification-off text-4xl text-muted-foreground/50 mb-4"></i>
                        <span class="text-sm text-muted-foreground">No team notifications</span>
                    </div>
                @endforelse
            </div>
        </div>
        <div class="border-b border-b-border">
        </div>
        <div class="grid grid-cols-1 p-5 gap-2.5" id="notifications_team_footer">
            <button class="kt-btn kt-btn-outline justify-center" onclick="markAllAsRead('team')">
                Mark all as read
            </button>
        </div>
    </div>
</div>