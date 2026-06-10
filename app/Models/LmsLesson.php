<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LmsLesson extends Model
{
    protected $fillable = [
        'lms_course_id',
        'title',
        'slug',
        'content',
        'video_url',
        'duration_minutes',
        'order',
        'is_published',
        'is_free_preview',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_free_preview' => 'boolean',
        'duration_minutes' => 'integer',
        'order' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });

        static::saved(function ($lesson) {
            $lesson->course?->recalculateDuration();
        });

        static::deleted(function ($lesson) {
            $lesson->course?->recalculateDuration();
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(LmsLessonProgress::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function isCompletedBy(int $userId): bool
    {
        return $this->progress()->where('user_id', $userId)->whereNotNull('completed_at')->exists();
    }
}
