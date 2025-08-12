{{-- Dynamic Sidebar Component - Example of how to make the sidebar fully data-driven --}}
@props([
    'appData' => [],
    'menuItems' => []
])

{{-- Main Sidebar Container --}}
<div class="kt-sidebar bg-background border-e border-e-border fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0 [--kt-drawer-enable:true] lg:[--kt-drawer-enable:false]"
    data-kt-drawer="true" data-kt-drawer-class="kt-drawer kt-drawer-start top-0 bottom-0" id="sidebar">
     
     {{-- Sidebar Header --}}
     <x-team.navigation.sidebar-header :appData="$appData" />
     
     {{-- Sidebar Content --}}
     <div class="kt-sidebar-content flex grow shrink-0 py-5 pe-2" id="sidebar_content">
          <div class="kt-scrollable-y-hover grow shrink-0 flex ps-2 lg:ps-5 pe-1 lg:pe-3"
               data-kt-scrollable="true" data-kt-scrollable-dependencies="#sidebar_header"
               data-kt-scrollable-height="auto" data-kt-scrollable-offset="0px"
               data-kt-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">
               
               {{-- Sidebar Menu --}}
               <div class="kt-menu flex flex-col grow gap-1" data-kt-menu="true"
                    data-kt-menu-accordion-expand-all="false" id="sidebar_menu">
                    
                    {{-- Dynamic Menu Items --}}
                    @foreach($menuItems as $menuItem)
                        @if($menuItem['type'] === 'heading')
                            <x-team.navigation.sidebar-heading :label="$menuItem['label']" />
                        @elseif($menuItem['type'] === 'item')
                            <x-team.navigation.sidebar-menu-item 
                                :icon="$menuItem['icon'] ?? ''"
                                :label="$menuItem['label']"
                                :route="$menuItem['route'] ?? ''"
                                :url="$menuItem['url'] ?? ''"
                                :active="$menuItem['active'] ?? false"
                                :hasSubmenu="isset($menuItem['children']) && count($menuItem['children']) > 0"
                                :isExpanded="$menuItem['expanded'] ?? false"
                                :badge="$menuItem['badge'] ?? null"
                                :target="$menuItem['target'] ?? '_self'">
                                
                                @if(isset($menuItem['children']) && count($menuItem['children']) > 0)
                                    @foreach($menuItem['children'] as $childItem)
                                        @if($childItem['type'] === 'group')
                                            <x-team.navigation.sidebar-menu-item 
                                                :label="$childItem['label']"
                                                :hasSubmenu="isset($childItem['children']) && count($childItem['children']) > 0"
                                                :isSubmenuItem="true">
                                                
                                                @if(isset($childItem['children']))
                                                    @php
                                                        $visibleChildren = array_slice($childItem['children'], 0, $childItem['visibleCount'] ?? count($childItem['children']));
                                                        $hiddenChildren = array_slice($childItem['children'], $childItem['visibleCount'] ?? count($childItem['children']));
                                                    @endphp
                                                    
                                                    {{-- Visible children --}}
                                                    @foreach($visibleChildren as $grandChild)
                                                        <x-team.navigation.sidebar-menu-item 
                                                            :label="$grandChild['label']"
                                                            :route="$grandChild['route'] ?? ''"
                                                            :url="$grandChild['url'] ?? ''"
                                                            :active="$grandChild['active'] ?? false"
                                                            :isSubmenuItem="true"
                                                            :badge="$grandChild['badge'] ?? null" />
                                                    @endforeach
                                                    
                                                    {{-- Show more section --}}
                                                    @if(count($hiddenChildren) > 0)
                                                        <x-team.navigation.sidebar-show-more :showText="'Show ' . count($hiddenChildren) . ' more'">
                                                            @foreach($hiddenChildren as $grandChild)
                                                                <x-team.navigation.sidebar-menu-item 
                                                                    :label="$grandChild['label']"
                                                                    :route="$grandChild['route'] ?? ''"
                                                                    :url="$grandChild['url'] ?? ''"
                                                                    :active="$grandChild['active'] ?? false"
                                                                    :isSubmenuItem="true"
                                                                    :badge="$grandChild['badge'] ?? null" />
                                                            @endforeach
                                                        </x-team.navigation.sidebar-show-more>
                                                    @endif
                                                @endif
                                            </x-team.navigation.sidebar-menu-item>
                                        @else
                                            {{-- Direct menu item --}}
                                            <x-team.navigation.sidebar-menu-item 
                                                :label="$childItem['label']"
                                                :route="$childItem['route'] ?? ''"
                                                :url="$childItem['url'] ?? ''"
                                                :active="$childItem['active'] ?? false"
                                                :isSubmenuItem="true"
                                                :badge="$childItem['badge'] ?? null" />
                                        @endif
                                    @endforeach
                                @endif
                            </x-team.navigation.sidebar-menu-item>
                        @endif
                    @endforeach
                    
                    {{-- Custom Menu Items Slot --}}
                    {{ $slot }}
               </div>
          </div>
     </div>
</div>
