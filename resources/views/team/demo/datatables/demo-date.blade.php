<div class="flex items-center gap-2 mb-2">
    <span class="font-medium text-sm {{ $is_overdue ? 'text-red-600' : 'text-gray-900' }}">
        {{ $demo_date }}
    </span>
    @if($is_overdue)
        <span class="kt-badge kt-badge-sm bg-red-100 text-red-800">
            Overdue
        </span>
    @else
        <span class="kt-badge kt-badge-sm bg-green-100 text-green-800">
            Today
        </span>
    @endif
</div>

<div class="grid grid-cols-1 items-center gap-2">
    <span class="font-medium text-sm">
        <span >{{$coaching}}</span>
    </span>
    <span class="font-medium text-sm">
        <span >{{ $batch->name .' - '.$batch->time}}</span>
    </span>
</div>

