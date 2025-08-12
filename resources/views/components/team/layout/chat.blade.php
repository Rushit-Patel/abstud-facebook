{{-- Chat Component --}}
@props(['appData' => []])

<!-- Chat -->
<button
     class="kt-btn kt-btn-ghost kt-btn-icon size-9 rounded-full hover:bg-primary/10 hover:[&amp;_i]:text-primary"
     data-kt-drawer-toggle="#chat_drawer">
     <i class="ki-filled ki-messages text-lg">
     </i>
</button>

<!--Chat Drawer-->
<div class="hidden kt-drawer kt-drawer-end card flex-col max-w-[90%] w-[450px] top-5 bottom-5 end-5 rounded-xl border border-border"
     data-kt-drawer="true" data-kt-drawer-container="body" id="chat_drawer">
     <div>
          <div class="flex items-center justify-between gap-2.5 text-sm text-mono font-semibold px-5 py-3.5">
               Chat
               <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-drawer-dismiss="true">
                    <i class="ki-filled ki-cross"></i>
               </button>
          </div>
          <div class="border-b border-b-border"></div>
          <div class="border-b border-border py-2.5">
               <div class="flex items-center justify-between flex-wrap gap-2 px-5">
                    <div class="flex items-center flex-wrap gap-2">
                         <div class="flex items-center justify-center shrink-0 rounded-full bg-accent/60 border border-border size-11">
                              <img alt="" class="size-7"
                                   src="https://keenthemes.com/static/metronic/tailwind/dist/assets/media/brand-logos/gitlab.svg" />
                         </div>
                         <div class="flex flex-col">
                              <a class="text-sm font-semibold text-mono hover:text-primary" href="#">
                                   HR Team
                              </a>
                              <span class="text-xs font-medium italic text-muted-foreground">
                                   Jessy is typing..
                              </span>
                         </div>
                    </div>
               </div>
          </div>
     </div>
     <!-- Chat content placeholder -->
     <div class="flex-1 p-5 text-center text-muted-foreground">
          <p>Chat functionality coming soon...</p>
     </div>
</div>
