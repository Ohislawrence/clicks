<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'lms_course_id',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function getProgressPercentageAttribute(): int
    {
        $totalLessons = $this->course->publishedLessons()->count();
        if ($totalLessons === 0) {
            return 0;
        }

        $completed = LmsLessonProgress::where('user_id', $this->user_id)
            ->whereIn('lms_lesson_id', $this->course->publishedLessons()->pluck('id'))
            ->whereNotNull('completed_at')
            ->count();

        return (int) round(($completed / $totalLessons) * 100);
    }

    public function checkAndMarkCompleted(): void
    {
        if ($this->completed_at) {
            return;
        }

        $totalLessons = $this->course->publishedLessons()->count();
        if ($totalLessons === 0) {
            return;
        }

        $completed = LmsLessonProgress::where('user_id', $this->user_id)
            ->whereIn('lms_lesson_id', $this->course->publishedLessons()->pluck('id'))
            ->whereNotNull('completed_at')
            ->count();

        if ($completed >= $totalLessons) {
            $this->update(['completed_at' => now()]);
        }
    }
}
