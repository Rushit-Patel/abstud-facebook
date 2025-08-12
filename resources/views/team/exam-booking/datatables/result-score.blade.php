<div class="grid grid-cols-1 items-center gap-2">
    @if(!empty($results_data) && !empty($result_date))
        <span class="font-medium overflow-hidden text-ellipsis whitespace-nowrap max-w-[200px]">
            <i class="ki-filled ki-phone"></i>
            <span class="copy-text cursor-pointer hover:underline transition">
                Result Date - {{ $result_date }}
            </span>
        </span>
        @foreach($results_data as $result)
            <span class="font-medium overflow-hidden text-ellipsis whitespace-nowrap max-w-[200px]">
                <i class="ki-filled ki-phone"></i>
                <span class="copy-text cursor-pointer hover:underline transition">
                    {{ $result }}
                </span>
            </span>
        @endforeach
    @else
        <span class="text-gray-500">No Result Available</span>
    @endif
</div>
