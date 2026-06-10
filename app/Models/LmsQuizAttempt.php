<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsQuizAttempt extends Model
{
    protected $fillable = [
        'lms_quiz_id',
        'user_id',
        'answers',
        'score',
        'passed',
        'completed_at',
    ];

    protected $casts = [
        'answers'      => 'array',
        'score'        => 'integer',
        'passed'       => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LmsQuiz::class, 'lms_quiz_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
