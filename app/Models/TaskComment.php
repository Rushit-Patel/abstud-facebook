<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskComment extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'parent_id',
        'content',
        'mentions',
        'is_internal',
        'is_edited',
        'edited_at',
    ];

    protected $casts = [
        'mentions' => 'array',
        'is_internal' => 'boolean',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TaskComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TaskComment::class, 'parent_id')->orderBy('created_at');
    }

    public function mentionedUsers(): HasMany
    {
        if (empty($this->mentions)) {
            return $this->hasMany(User::class)->whereRaw('1 = 0'); // Empty relation
        }

        return $this->hasMany(User::class)->whereIn('id', $this->mentions);
    }

    // Scopes
    public function scopeTopLevel(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeReplies(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal(Builder $query): Builder
    {
        return $query->where('is_internal', true);
    }

    public function scopeWithMentions(Builder $query): Builder
    {
        return $query->whereNotNull('mentions')->where('mentions', '!=', '[]');
    }

    // Accessors
    public function getIsReplyAttribute(): bool
    {
        return !is_null($this->parent_id);
    }

    public function getHasMentionsAttribute(): bool
    {
        return !empty($this->mentions);
    }

    public function getRepliesCountAttribute(): int
    {
        return $this->replies()->count();
    }

    public function getFormattedContentAttribute(): string
    {
        $content = $this->content;

        // Replace mentions with user links (you can customize this)
        if ($this->has_mentions) {
            foreach ($this->mentions as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $content = str_replace("@{$userId}", "@{$user->name}", $content);
                }
            }
        }

        return $content;
    }

    // Methods
    public function addReply(string $content, User $user, array $options = []): TaskComment
    {
        return static::create([
            'task_id' => $this->task_id,
            'parent_id' => $this->id,
            'user_id' => $user->id,
            'content' => $content,
            'mentions' => $options['mentions'] ?? [],
            'is_internal' => $options['is_internal'] ?? $this->is_internal,
        ]);
    }

    public function edit(string $content): bool
    {
        return $this->update([
            'content' => $content,
            'is_edited' => true,
            'edited_at' => now(),
        ]);
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->user_id === $user->id || $user->hasRole('admin');
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->user_id === $user->id || $user->hasRole('admin');
    }
}
