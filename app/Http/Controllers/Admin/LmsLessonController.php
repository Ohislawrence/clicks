<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
use App\Models\LmsLesson;
use App\Services\DeepseekService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LmsLessonController extends Controller
{
    public function index(LmsCourse $course)
    {
        return Inertia::render('Admin/Lms/Lessons/Index', [
            'course'  => $course,
            'lessons' => $course->lessons()->orderBy('order')->get(),
        ]);
    }

    public function create(LmsCourse $course)
    {
        return Inertia::render('Admin/Lms/Lessons/Create', [
            'course' => $course,
        ]);
    }

    public function store(Request $request, LmsCourse $course)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'slug'            => 'nullable|string|max:255',
            'content'         => 'nullable|string',
            'video_url'       => 'nullable|url|max:500',
            'duration_minutes'=> 'required|integer|min:0',
            'order'           => 'integer|min:0',
            'is_published'    => 'boolean',
            'is_free_preview' => 'boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Ensure slug is unique within course
        $slug = $validated['slug'];
        $counter = 1;
        while (LmsLesson::where('lms_course_id', $course->id)->where('slug', $slug)->exists()) {
            $slug = $validated['slug'] . '-' . $counter++;
        }
        $validated['slug'] = $slug;

        $course->lessons()->create($validated);

        return redirect()->route('admin.lms.courses.lessons.index', $course)
            ->with('success', 'Lesson created successfully.');
    }

    public function edit(LmsCourse $course, LmsLesson $lesson)
    {
        return Inertia::render('Admin/Lms/Lessons/Edit', [
            'course' => $course,
            'lesson' => $lesson,
        ]);
    }

    public function update(Request $request, LmsCourse $course, LmsLesson $lesson)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'content'         => 'nullable|string',
            'video_url'       => 'nullable|url|max:500',
            'duration_minutes'=> 'required|integer|min:0',
            'order'           => 'integer|min:0',
            'is_published'    => 'boolean',
            'is_free_preview' => 'boolean',
        ]);

        $lesson->update($validated);

        return redirect()->route('admin.lms.courses.lessons.index', $course)
            ->with('success', 'Lesson updated successfully.');
    }

    public function destroy(LmsCourse $course, LmsLesson $lesson)
    {
        $lesson->delete();

        return back()->with('success', 'Lesson deleted successfully.');
    }

    public function reorder(Request $request, LmsCourse $course)
    {
        $request->validate([
            'lessons'   => 'required|array',
            'lessons.*' => 'integer|exists:lms_lessons,id',
        ]);

        foreach ($request->lessons as $order => $id) {
            LmsLesson::where('id', $id)
                ->where('lms_course_id', $course->id)
                ->update(['order' => $order]);
        }

        return back()->with('success', 'Lessons reordered.');
    }

    /**
     * Generate lesson content using Deepseek AI.
     */
    public function generateContent(Request $request, LmsCourse $course)
    {
        $request->validate([
            'lesson_title' => 'required|string|max:255',
            'outline'      => 'nullable|string|max:2000',
        ]);

        $deepseek = app(DeepseekService::class);

        $result = $deepseek->generateLesson(
            courseTitle: $course->title,
            lessonTitle: $request->lesson_title,
            audience:    $course->audience,
            level:       $course->level,
            outline:     $request->outline ?? '',
        );

        if (! $result['success']) {
            return response()->json([
                'error' => $result['message'] ?? 'AI generation failed. Please try again.',
            ], 500);
        }

        return response()->json([
            'content' => $result['content'],
        ]);
    }
}
