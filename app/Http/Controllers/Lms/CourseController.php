<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsLesson;
use App\Models\LmsLessonProgress;
use App\Models\LmsQuizAttempt;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    /**
     * Browse all published courses visible to the current user's role.
     */
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first(); // affiliate or advertiser

        $courses = LmsCourse::published()
            ->forAudience($role)
            ->withCount(['publishedLessons as lesson_count', 'enrollments as enrollment_count'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('order')
            ->get()
            ->map(function ($course) use ($user) {
                $enrollment = LmsEnrollment::where('user_id', $user->id)
                    ->where('lms_course_id', $course->id)
                    ->first();

                $course->is_enrolled = (bool) $enrollment;
                $course->progress    = $enrollment ? $enrollment->progress_percentage : 0;
                $course->completed   = (bool) ($enrollment?->completed_at);
                return $course;
            });

        return Inertia::render('Lms/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show a single course overview and lesson list.
     */
    public function show(LmsCourse $course)
    {
        abort_if(! $course->is_published, 404);

        $user = auth()->user();

        $enrollment = LmsEnrollment::where('user_id', $user->id)
            ->where('lms_course_id', $course->id)
            ->first();

        $completedLessonIds = LmsLessonProgress::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->pluck('lms_lesson_id')
            ->toArray();

        $lessons = $course->publishedLessons()
            ->get()
            ->map(function ($lesson) use ($enrollment, $completedLessonIds) {
                $lesson->is_locked    = ! $enrollment && ! $lesson->is_free_preview;
                $lesson->is_completed = in_array($lesson->id, $completedLessonIds);
                return $lesson;
            });

        $quiz = $course->quiz;
        $quizPassed = false;

        if ($quiz && $enrollment) {
            $quizPassed = LmsQuizAttempt::where('lms_quiz_id', $quiz->id)
                ->where('user_id', $user->id)
                ->where('passed', true)
                ->exists();
        }

        return Inertia::render('Lms/Show', [
            'course'      => $course,
            'lessons'     => $lessons,
            'isEnrolled'  => (bool) $enrollment,
            'progress'    => $enrollment ? $enrollment->progress_percentage : 0,
            'completed'   => (bool) ($enrollment?->completed_at),
            'quiz'        => $quiz ? ['id' => $quiz->id, 'title' => $quiz->title] : null,
            'quizPassed'  => $quizPassed,
        ]);
    }

    /**
     * Enroll the authenticated user in a course.
     */
    public function enroll(LmsCourse $course)
    {
        abort_if(! $course->is_published, 404);

        LmsEnrollment::firstOrCreate(
            ['user_id' => auth()->id(), 'lms_course_id' => $course->id],
            ['enrolled_at' => now()]
        );

        $firstLesson = $course->publishedLessons()->orderBy('order')->first();

        if ($firstLesson) {
            return redirect()->route('lms.lesson', [$course->slug, $firstLesson->slug])
                ->with('success', 'Enrolled successfully! Start learning.');
        }

        return back()->with('success', 'Enrolled successfully!');
    }

    /**
     * View a single lesson.
     */
    public function lesson(LmsCourse $course, LmsLesson $lesson)
    {
        //abort_if(! $course->is_published || ! $lesson->is_published, 404);
        abort_if($lesson->lms_course_id !== $course->id, 404);

        $user = auth()->user();

        $enrollment = LmsEnrollment::where('user_id', $user->id)
            ->where('lms_course_id', $course->id)
            ->first();

        // Gate: must be enrolled or it must be a free preview
        if (! $enrollment && ! $lesson->is_free_preview) {
            return redirect()->route('lms.show', $course->slug)
                ->with('error', 'Please enroll in this course to access the lesson.');
        }

        $completedLessonIds = LmsLessonProgress::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->pluck('lms_lesson_id')
            ->toArray();

        $allLessons = $course->publishedLessons()
            ->get()
            ->map(function ($l) use ($enrollment, $completedLessonIds) {
                $l->is_locked    = ! $enrollment && ! $l->is_free_preview;
                $l->is_completed = in_array($l->id, $completedLessonIds);
                return $l;
            });

        $currentIndex = $allLessons->search(fn ($l) => $l->id === $lesson->id);
        $prevLesson   = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson   = $currentIndex < $allLessons->count() - 1 ? $allLessons[$currentIndex + 1] : null;

        return Inertia::render('Lms/Lesson', [
            'course'     => $course,
            'lesson'     => $lesson,
            'allLessons' => $allLessons,
            'prevLesson' => $prevLesson,
            'nextLesson' => $nextLesson,
            'isEnrolled' => (bool) $enrollment,
            'isCompleted'=> in_array($lesson->id, $completedLessonIds),
            'progress'   => $enrollment ? $enrollment->progress_percentage : 0,
        ]);
    }

    /**
     * Mark a lesson as completed.
     */
    public function complete(Request $request, LmsCourse $course, LmsLesson $lesson)
    {
        abort_if($lesson->lms_course_id !== $course->id, 404);

        $userId = auth()->id();

        $enrollment = LmsEnrollment::where('user_id', $userId)
            ->where('lms_course_id', $course->id)
            ->firstOrFail();

        LmsLessonProgress::updateOrCreate(
            ['user_id' => $userId, 'lms_lesson_id' => $lesson->id],
            ['completed_at' => now()]
        );

        $enrollment->checkAndMarkCompleted();

        return back()->with('success', 'Lesson marked as complete!');
    }
}
