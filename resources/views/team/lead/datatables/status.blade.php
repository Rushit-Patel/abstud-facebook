<div class="flex flex-col gap-2">
    <div>
        <span class="inline-flex items-center truncate px-2 py-1 rounded text-xs font-medium {{ $badgeClass }}">
            {{ $status }}
            @if($subStatus)
                - {{ $subStatus }}
            @endif
        </span>
    </div>
    <div>
        <span class="font-medium">
            <i class="ki-filled ki-user"></i>
            @if(isset($assigned) && $assigned!="")
            <span class="cursor-pointer hover:underline transition">{{ $assigned }}</span>
            @else
                <span class="cursor-pointer hover:underline transition text-destructive assignOwner" data-id="{{ $id }}">No Assigned</span>
            @endif
        </span>
    </div>
</div>
