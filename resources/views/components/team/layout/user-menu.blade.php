{{-- Team User Menu Component --}}
@props([
    'user' => [],
    'appData' => []
])

@php
$userData = array_merge([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'avatar' => null,
    'initials' => 'AU',
    'role' => 'Administrator'
], $appData['user'] ?? $user);
@endphp

<!-- User -->
<div class="shrink-0" data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px" data-kt-dropdown-offset-rtl="-20px, 10px" data-kt-dropdown-placement="bottom-end" data-kt-dropdown-placement-rtl="bottom-start" data-kt-dropdown-trigger="click">
    <div class="cursor-pointer shrink-0" data-kt-dropdown-toggle="true">
        @if($userData['avatar'])
            <img alt="" class="size-9 rounded-full border-2 border-green-500 shrink-0"
                src="{{ $userData['avatar'] }}" />
        @else
            <div class="size-9 rounded-full border-2 border-green-500 shrink-0 bg-primary flex items-center justify-center text-primary-foreground font-semibold">
                {{ $userData['initials'] }}
            </div>
        @endif
    </div>
     <div class="kt-dropdown-menu w-[250px]" data-kt-dropdown-menu="true">
         <div class="flex items-center justify-between px-2.5 py-1.5 gap-1.5">
              <div class="flex items-center gap-2">
                   @if($userData['avatar'])
                        <img alt="" class="size-9 shrink-0 rounded-full border-2 border-green-500"
                             src="{{ $userData['avatar'] }}" />
                   @else
                        <div class="size-9 shrink-0 rounded-full border-2 border-green-500 bg-primary flex items-center justify-center text-primary-foreground font-semibold">
                             {{ $userData['initials'] }}
                        </div>
                   @endif
                    <div class="flex flex-col gap-1.5 max-w-[160px]"> 
                         <span class="text-sm text-foreground font-semibold leading-none truncate overflow-hidden whitespace-nowrap block">
                              {{ $userData['name'] }}
                         </span>
                         <a class="text-xs text-secondary-foreground hover:text-primary font-medium leading-none truncate overflow-hidden whitespace-nowrap block" href="#">
                              {{ $userData['email'] }}
                         </a>
                    </div>
              </div>
         </div>
          <ul class="kt-dropdown-menu-sub">
              <li>
                   <div class="kt-dropdown-menu-separator">
                   </div>
              </li>
              <li>
                   <a class="kt-dropdown-menu-link" href="{{ route('team.profile') }}">
                        <i class="ki-filled ki-profile-circle">
                        </i>
                        My Profile
                   </a>
              </li>
               {{-- <li data-kt-dropdown="true" data-kt-dropdown-placement="right-start"
                   data-kt-dropdown-trigger="hover">
                   <button class="kt-dropdown-menu-toggle py-1" data-kt-dropdown-toggle="true">
                        <span class="flex items-center gap-2">
                             <i class="ki-filled ki-icon">
                             </i>
                             Language
                        </span>
                        <span class="ms-auto kt-badge kt-badge-stroke shrink-0">
                             English
                             <img alt="" class="inline-block size-3.5 rounded-full"
                                  src="https://keenthemes.com/static/metronic/tailwind/dist/assets/media/flags/united-states.svg" />
                        </span>
                   </button>
                   <div class="kt-dropdown-menu w-[180px]" data-kt-dropdown-menu="true">
                        <ul class="kt-dropdown-menu-sub">
                             <li class="active">
                                  <a class="kt-dropdown-menu-link" href="#">
                                       <span class="flex items-center gap-2">
                                            <img alt="" class="inline-block size-4 rounded-full"
                                                 src="https://keenthemes.com/static/metronic/tailwind/dist/assets/media/flags/united-states.svg" />
                                            <span class="kt-menu-title">
                                                 English
                                            </span>
                                       </span>
                                       <i class="ki-solid ki-check-circle ms-auto text-green-500 text-base">
                                       </i>
                                  </a>
                             </li>
                             <li class="">
                                  <a class="kt-dropdown-menu-link" href="#">
                                       <span class="flex items-center gap-2">
                                            <img alt="" class="inline-block size-4 rounded-full"
                                                 src="https://keenthemes.com/static/metronic/tailwind/dist/assets/media/flags/saudi-arabia.svg" />
                                            <span class="kt-menu-title">
                                                 Arabic(Saudi)
                                            </span>
                                       </span>
                                  </a>
                             </li>
                             <li class="">
                                  <a class="kt-dropdown-menu-link" href="#">
                                       <span class="flex items-center gap-2">
                                            <img alt="" class="inline-block size-4 rounded-full"
                                                 src="https://keenthemes.com/static/metronic/tailwind/dist/assets/media/flags/spain.svg" />
                                            <span class="kt-menu-title">
                                                 Spanish
                                            </span>
                                       </span>
                                  </a>
                             </li>
                             <li class="">
                                  <a class="kt-dropdown-menu-link" href="#">
                                       <span class="flex items-center gap-2">
                                            <img alt="" class="inline-block size-4 rounded-full"
                                                 src="https://keenthemes.com/static/metronic/tailwind/dist/assets/media/flags/germany.svg" />
                                            <span class="kt-menu-title">
                                                 German
                                            </span>
                                       </span>
                                  </a>
                             </li>
                             <li class="">
                                  <a class="kt-dropdown-menu-link" href="#">
                                       <span class="flex items-center gap-2">
                                            <img alt="" class="inline-block size-4 rounded-full"
                                                 src="https://keenthemes.com/static/metronic/tailwind/dist/assets/media/flags/japan.svg" />
                                            <span class="kt-menu-title">
                                                 Japanese
                                            </span>
                                       </span>
                                  </a>
                             </li>
                        </ul>
                   </div>
              </li> --}}
         </ul>
         <div class="px-2.5 pt-1.5 mb-2.5 flex flex-col gap-3.5">
              <div class="flex items-center gap-2 justify-between">
                   <span class="flex items-center gap-2">
                        <i class="ki-filled ki-moon text-base text-muted-foreground">
                        </i>
                        <span class="font-medium text-2sm">
                             Dark Mode
                        </span>
                   </span>
                   <input class="kt-switch" data-kt-theme-switch-state="dark"
                        data-kt-theme-switch-toggle="true" name="check" type="checkbox" value="1" />
              </div>
              <form method="POST" action="{{ route('team.logout') }}" class="w-full">
                   @csrf
                   <button type="submit" class="logout_button kt-btn kt-btn-outline justify-center w-full">
                        <i class="ki-filled ki-exit text-base mr-2"></i>
                        Log out
                   </button>
              </form>
         </div>
    </div>
</div>
<!-- End of User -->
