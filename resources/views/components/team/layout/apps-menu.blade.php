{{-- Apps Menu Component --}}
@props(['appData' => []])
<div data-kt-dropdown="true" data-kt-dropdown-offset="10px, 10px" data-kt-dropdown-offset-rtl="-10px, 10px"
     data-kt-dropdown-placement="bottom-end" data-kt-dropdown-placement-rtl="bottom-start">
     <button
          class="kt-btn kt-btn-ghost kt-btn-icon size-9 rounded-full hover:bg-primary/10 hover:[&amp;_i]:text-primary kt-dropdown-open:bg-primary/10 kt-dropdown-open:[&amp;_i]:text-primary"
          data-kt-dropdown-toggle="true">
          <i class="ki-filled ki-element-11 text-lg"></i>
     </button>
     <div class="kt-dropdown-menu p-0 w-screen max-w-[320px]" data-kt-dropdown-menu="true">
          <div
               class="flex items-center justify-between gap-2.5 text-xs text-secondary-foreground font-medium px-5 py-3 border-b border-b-border">
               <span>
                    Quick Access
               </span>
          </div>
          <div class="flex flex-col kt-scrollable-y-auto max-h-[400px] divide-y divide-border p-4">
               <div class="lg:col-span-1">
                    <div class="grid grid-cols-2 gap-2 lg:gap-4 h-full items-stretch">
                         <style>
                              .channel-stats-bg {
                                   background-image: url('/default/images/2600x1600/bg-3.png');
                              }

                              .dark .channel-stats-bg {
                                   background-image: url('/default/images/2600x1600/bg-3-dark.png');
                              }
                         </style>
                         <div class="kt-card flex-col justify-between gap-4 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat channel-stats-bg ">
                              <a href="{{ route('team.lead.create') }}" class="flex flex-col h-full gap-4 hover:bg-accent/60">
                                   <i class="ki-filled ki-user-edit w-7 mt-4 ms-5 text-2xl text-primary"></i>
                                   <div class="flex flex-col gap-1 pb-4 px-5">
                                        <span class="text-1xl hover:underline font-semibold text-mono">
                                             Add Lead
                                        </span>
                                   </div>
                              </a>
                         </div>
                         <div class="kt-card flex-col justify-between gap-4 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat channel-stats-bg">
                              <a href="{{ route('team.todo.index') }}" class="flex flex-col h-full gap-4 hover:bg-accent/60">
                                   <i class="ki-filled ki-message-question w-7 mt-4 ms-5 text-2xl text-primary"></i>
                                   <div class="flex flex-col gap-1 pb-4 px-5">
                                        <span class="text-1xl font-semibold text-mono hover:underline">
                                             Todo
                                        </span>
                                   </div>
                              </a>
                         </div>
                         <div class="kt-card flex-col justify-between gap-4 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat channel-stats-bg">
                              <a href="{{ route('team.task.index') }}" class="flex flex-col h-full gap-4 hover:bg-accent/60">
                                   <i class="ki-filled ki-watch w-7 mt-4 ms-5 text-2xl text-primary"></i>
                                   <div class="flex flex-col gap-1 pb-4 px-5">
                                        <span class="text-1xl font-semibold text-mono hover:underline">
                                             Task
                                        </span>
                                   </div>
                              </a>
                         </div>
                         <div class="kt-card flex-col justify-between gap-4 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat channel-stats-bg">
                              <a href="javascript:void(0)" class="flex flex-col h-full gap-4 hover:bg-accent/60">
                                   <i class="ki-filled ki-calendar w-7 mt-4 ms-5 text-2xl text-primary"></i>
                                   <div class="flex flex-col gap-1 pb-4 px-5">
                                        <span class="text-1xl font-semibold text-mono hover:underline">
                                             Calendar
                                        </span>
                                   </div>
                              </a>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>
