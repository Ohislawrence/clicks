<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LmsQuizController extends Controller
{
    /**
     * Show the quiz editor for a course.
     */
    public function edit(LmsCourse $course)
    {
        $quiz = $course->quiz()->with('questions')->first();

        return Inertia::render('Admin/Lms/Quiz/Edit', [
            'course' => $course,
            'quiz'   => $quiz,
        ]);
    }

    /**
     * Create or update the quiz + all questions in one request.
     */
    public function update(Request $request, LmsCourse $course)
    {
        $validated = $request->validate([
            'title'                          => 'required|string|max:255',
            'description'                    => 'nullable|string|max:2000',
            'time_limit_minutes'             => 'nullable|integer|min:1|max:300',
            'questions'                      => 'required|array|min:1',
            'questions.*.id'                 => 'nullable|integer',
            'questions.*.question'           => 'required|string|max:1000',
            'questions.*.options'            => 'required|array|min:2|max:6',
            'questions.*.options.*'          => 'required|string|max:500',
            'questions.*.correct_option'     => 'required|integer|min:0',
            'questions.*.explanation'        => 'nullable|string|max:2000',
        ]);

        // Validate correct_option is within options bounds per question
        foreach ($validated['questions'] as $i => $q) {
            if ($q['correct_option'] >= count($q['options'])) {
                return back()->withErrors([
                    "questions.{$i}.correct_option" => 'Correct option index is out of range.',
                ])->withInput();
            }
        }

        // Upsert the quiz
        $quiz = $course->quiz()->updateOrCreate(
            ['lms_course_id' => $course->id],
            [
                'title'               => $validated['title'],
                'description'         => $validated['description'] ?? null,
                'time_limit_minutes'  => $validated['time_limit_minutes'] ?? null,
            ]
        );

        // Sync questions: keep existing by ID, delete removed ones, add new
        $incomingIds = collect($validated['questions'])->pluck('id')->filter()->all();
        $quiz->questions()->whereNotIn('id', $incomingIds)->delete();

        foreach ($validated['questions'] as $order => $q) {
            $data = [
                'question'       => $q['question'],
                'options'        => $q['options'],
                'correct_option' => $q['correct_option'],
                'explanation'    => $q['explanation'] ?? null,
                'order'          => $order,
            ];

            if (! empty($q['id'])) {
                $quiz->questions()->where('id', $q['id'])->update($data);
            } else {
                $quiz->questions()->create($data);
            }
        }

        return redirect()->route('admin.lms.courses.quiz.edit', $course)
            ->with('success', 'Quiz saved successfully.');
    }

    /**
     * Remove the quiz (and all questions/attempts) from a course.
     */
    public function destroy(LmsCourse $course)
    {
        $course->quiz()->delete();

        return redirect()->route('admin.lms.courses.lessons.index', $course)
            ->with('success', 'Quiz removed.');
    }
}
