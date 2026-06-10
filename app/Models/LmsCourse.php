<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LmsCourse extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'what_you_learn',
        'thumbnail',
        'category',
        'audience',
        'level',
        'duration_minutes',
        'is_published',
        'is_featured',
        'order',
        'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'duration_minutes' => 'integer',
        'order' => 'integer',
        'what_you_learn' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(LmsLesson::class)->orderBy('order');
    }

    public function publishedLessons(): HasMany
    {
        return $this->hasMany(LmsLesson::class)->where('is_published', true)->orderBy('order');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(LmsEnrollment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForAudience($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('audience', 'both')->orWhere('audience', $role);
        });
    }

    public function getEnrollmentCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function getLessonCountAttribute(): int
    {
        return $this->publishedLessons()->count();
    }

    public function recalculateDuration(): void
    {
        $total = $this->lessons()->sum('duration_minutes');
        $this->update(['duration_minutes' => $total]);
    }
}
