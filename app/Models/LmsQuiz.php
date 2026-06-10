<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsQuiz extends Model
{
    protected $fillable = [
        'lms_course_id',
        'title',
        'description',
        'time_limit_minutes',
    ];

    protected $casts = [
        'time_limit_minutes' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(LmsCourse::class, 'lms_course_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LmsQuizQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(LmsQuizAttempt::class);
    }
}
