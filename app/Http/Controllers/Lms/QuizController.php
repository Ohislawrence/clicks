<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsQuizAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
    /**
     * Show the quiz page. Passes questions (correct answers hidden unless already passed).
     * Also passes the user's best / most recent attempt.
     */
    public function show(LmsCourse $course)
    {
        abort_if(! $course->is_published, 404);

        $user = auth()->user();

        $enrollment = LmsEnrollment::where('user_id', $user->id)
            ->where('lms_course_id', $course->id)
            ->firstOrFail();

        $quiz = $course->quiz()->with('questions')->firstOrFail();

        $bestAttempt = LmsQuizAttempt::where('lms_quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->orderByDesc('score')
            ->first();

        $latestAttempt = LmsQuizAttempt::where('lms_quiz_id', $quiz->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        // Only reveal correct answers + explanations after the user has passed
        $alreadyPassed = (bool) ($bestAttempt?->passed);

        $questions = $quiz->questions->map(fn ($q) => [
            'id'             => $q->id,
            'question'       => $q->question,
            'options'        => $q->options,
            'correct_option' => $alreadyPassed ? $q->correct_option : null,
            'explanation'    => $alreadyPassed ? $q->explanation : null,
        ])->values();

        return Inertia::render('Lms/Quiz', [
            'course'        => $course,
            'quiz'          => [
                'id'                 => $quiz->id,
                'title'              => $quiz->title,
                'description'        => $quiz->description,
                'time_limit_minutes' => $quiz->time_limit_minutes,
                'questions_count'    => $quiz->questions->count(),
            ],
            'questions'     => $questions,
            'passScore'     => $course->pass_score,
            'bestAttempt'   => $bestAttempt,
            'latestAttempt' => $latestAttempt,
            'alreadyPassed' => $alreadyPassed,
        ]);
    }

    /**
     * Submit quiz answers, record attempt, mark course complete if passed.
     */
    public function submit(Request $request, LmsCourse $course)
    {
        abort_if(! $course->is_published, 404);

        $user = auth()->user();

        $enrollment = LmsEnrollment::where('user_id', $user->id)
            ->where('lms_course_id', $course->id)
            ->firstOrFail();

        $quiz = $course->quiz()->with('questions')->firstOrFail();

        $request->validate([
            'answers'   => 'required|array',
            'answers.*' => 'nullable|integer|min:0',
        ]);

        $total   = $quiz->questions->count();
        $correct = 0;
        $answers = [];

        foreach ($quiz->questions as $question) {
            $selected  = isset($request->answers[$question->id])
                ? (int) $request->answers[$question->id]
                : null;
            $isCorrect = $selected !== null && $selected === $question->correct_option;

            if ($isCorrect) {
                $correct++;
            }

            $answers[] = [
                'question_id' => $question->id,
                'selected'    => $selected,
                'correct'     => $question->correct_option,
                'is_correct'  => $isCorrect,
            ];
        }

        $score  = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed = $score >= $course->pass_score;

        LmsQuizAttempt::create([
            'lms_quiz_id'  => $quiz->id,
            'user_id'      => $user->id,
            'answers'      => $answers,
            'score'        => $score,
            'passed'       => $passed,
            'completed_at' => now(),
        ]);

        // Mark the enrollment as completed when the user first passes
        if ($passed && ! $enrollment->completed_at) {
            $enrollment->update(['completed_at' => now()]);
        }

        return redirect()->route('lms.quiz', $course->slug)
            ->with('success', $passed
                ? "Congratulations! You scored {$score}% and passed the quiz!"
                : "You scored {$score}%. You need {$course->pass_score}% to pass. Try again!");
    }
}
