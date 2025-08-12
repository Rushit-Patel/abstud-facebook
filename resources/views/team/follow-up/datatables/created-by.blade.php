<div class="flex items-center gap-2">
    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
        <span class="text-xs font-medium text-blue-800">
            {{ substr($created_by, 0, 2) }}
        </span>
    </div>
    <span class="text-sm font-medium text-gray-900">{{ $created_by }}</span>
</div>
