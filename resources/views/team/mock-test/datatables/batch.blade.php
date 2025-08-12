<div class="flex flex-wrap items-center gap-2 max-w-full overflow-hidden">
    @forelse($batches as $batch)
        <span class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-200 rounded truncate max-w-[150px]" title="{{ $batch->name }} ({{ $batch->time }})">
            {{ $batch->name }} ({{ $batch->time }})
        </span>
    @empty
        <span class="text-sm text-gray-500">Unknown</span>
    @endforelse
</div>
