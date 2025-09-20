<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    // Explicit fillable fields
    protected $fillable = [
        'board_id',
        'status_id',
        'created_by',
        'title',
        'slug',
        'description',
        'priority',
        'due_date',
        'archived',
        'order',
        'completed_at',
        'tags',
        'attachments',
    ];

    protected $casts = [
        'due_date'   => 'date',
        'archived'   => 'boolean',
        'tags'       => 'array',
        'attachments' => 'array',
        'completed_at' => 'datetime',
    ];

    protected static function booted()
    {
        // Auto-generate slug before creating
        static::creating(function ($task) {
            if (empty($task->slug)) {
                $task->slug = self::generateSlug($task->title);
            }
        });

        // Auto-generate slug before updating if title changed
        static::updating(function ($task) {
            if ($task->isDirty('title')) {
                $task->slug = self::generateSlug($task->title);
            }
        });
    }


    // ---------------- Relationships ----------------

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'task_tag')->withTimestamps();
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class);
    }

    // ---------------- Scopes ----------------

    public function scopeActive($query)
    {
        return $query->where('archived', false);
    }

    // ---------------- Helpers ----------------

    /**
     * Fill task data safely including slug
     */
    public function fillData(array $data): self
    {
        if (!isset($data['slug'])) {
            $data['slug'] = self::generateSlug($data['title']);
        }
        $this->fill($data);
        return $this;
    }

    /**
     * Generate a unique slug based on the task title
     */
    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if the task is completed
     */
    public function isCompleted(): bool
    {
        return !is_null($this->completed_at);
    }
}
