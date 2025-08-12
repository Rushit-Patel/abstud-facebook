<div class="grid grid-cols-1 items-center gap-2">
    <span class="font-medium">
        <a href="{{ route('team.client.show', $client_id) }}" class="flex items-center gap-1">
            <i class="ki-filled ki-exit-right-corner text-primary"></i>
            <span class=" cursor-pointer hover:underline transition">{{ $name }}</span>
        </a>
        
    </span>
    <span class="font-medium">
        <i class="ki-filled ki-phone"></i>
        <span class="copy-text cursor-pointer hover:underline transition">{{ $mobile_no }}</span>
    </span>
    <span class="font-medium truncate">
        <i class="ki-filled ki-messages"></i>
        <span class="copy-text cursor-pointer hover:underline transition">{{ $email_id }}</span>
    </span>
</div>