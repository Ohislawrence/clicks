<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsQuizQuestion extends Model
{
    protected $fillable = [
        'lms_quiz_id',
        'question',
        'options',
        'correct_option',
        'explanation',
        'order',
    ];

    protected $casts = [
        'options'        => 'array',
        'correct_option' => 'integer',
        'order'          => 'integer',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LmsQuiz::class, 'lms_quiz_id');
    }
}
