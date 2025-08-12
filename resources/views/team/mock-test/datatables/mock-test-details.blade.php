<div class="grid grid-cols-1 items-center gap-2">
    <span class="font-medium">
        <i class="ki-filled ki-exit-right-corner text-primary"></i>
        <span class=" cursor-pointer hover:underline transition">{{ $name }}</span>
    </span>
    <span class="font-medium">
        <i class="ki-filled ki-phone"></i>
        <span class="copy-text cursor-pointer hover:underline transition">{{ $date_time }}</span>
    </span>
    <span class="font-medium truncate">
    <i class="ki-filled ki-messages"></i>
    <span class="copy-text cursor-pointer hover:underline transition
        {{ $status == 1 ? 'text-green-600' : 'text-red-600' }}">
        {{ $status == 1 ? 'Publish' : 'Unpublish' }}
    </span>
</span>

</div>
