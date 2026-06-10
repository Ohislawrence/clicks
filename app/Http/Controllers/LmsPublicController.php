<?php

namespace App\Http\Controllers;

use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsLessonProgress;
use Illuminate\Http\Request;

class LmsPublicController extends Controller
{
    /**
     * Public course catalogue — no auth required.
     */
    public function index(Request $request)
    {
        $query = LmsCourse::published()
            ->withCount(['publishedLessons as lesson_count', 'enrollments as enrollment_count'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('order');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level') && in_array($request->level, ['beginner', 'intermediate', 'advanced'])) {
            $query->where('level', $request->level);
        }

        if ($request->filled('audience') && in_array($request->audience, ['affiliate', 'advertiser', 'both'])) {
            $query->where(function ($q) use ($request) {
                $q->where('audience', 'both')->orWhere('audience', $request->audience);
            });
        }

        $courses = $query->get();

        return view('front.learning.index', [
            'courses'  => $courses,
            'filters'  => $request->only(['q', 'level', 'audience']),
        ]);
    }

    /**
     * Public course preview — shows detail before enrolling.
     */
    public function show(string $slug)
    {
        $course = LmsCourse::published()
            ->where('slug', $slug)
            ->withCount(['publishedLessons as lesson_count', 'enrollments as enrollment_count'])
            ->firstOrFail();

        $lessons = $course->publishedLessons()->get();

        // If authenticated, show enrollment status
        $isEnrolled  = false;
        $progress    = 0;
        $completed   = false;

        if (auth()->check()) {
            $enrollment = LmsEnrollment::where('user_id', auth()->id())
                ->where('lms_course_id', $course->id)
                ->first();

            if ($enrollment) {
                $isEnrolled = true;
                $progress   = $enrollment->progress_percentage;
                $completed  = (bool) $enrollment->completed_at;
            }
        }

        return view('front.learning.show', compact('course', 'lessons', 'isEnrolled', 'progress', 'completed'));
    }
}
