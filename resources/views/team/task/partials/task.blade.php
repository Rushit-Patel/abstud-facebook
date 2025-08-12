<div class="flex flex-col gap-1">
    <div class="flex items-start justify-between gap-2">
        <a href="{{ route('team.task.show', $id) }}" 
           class="text-sm font-medium text-primary hover:text-primary-dark transition-colors line-clamp-2">
            {{ $title }}
        </a>
    </div>
    
    @if($description)
        <p class="text-xs text-muted-foreground line-clamp-2 mb-1">
            {{ Str::limit($description, 80) }}
        </p>
    @endif
    
    <div class="flex items-center gap-2 text-xs text-muted-foreground">
        @if($category)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700">
                {{ $category }}
            </span>
        @endif
        
        <span class="text-xs">
            {{ $created_at->format('M d, Y') }}
        </span>
    </div>
</div>
