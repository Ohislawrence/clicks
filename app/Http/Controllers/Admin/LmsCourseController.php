<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
use App\Models\User;
use App\Notifications\NewCoursePublishedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LmsCourseController extends Controller
{
    public function index()
    {
        $courses = LmsCourse::withCount(['lessons', 'enrollments'])
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Lms/Courses/Index', [
            'courses' => $courses,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Lms/Courses/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:lms_courses',
            'description'   => 'required|string',
            'what_you_learn'=> 'nullable|array',
            'what_you_learn.*' => 'string|max:255',
            'thumbnail'     => 'nullable|image|max:2048',
            'category'      => 'nullable|string|max:100',
            'audience'      => 'required|in:affiliate,advertiser,both',
            'level'         => 'required|in:beginner,intermediate,advanced',
            'is_published'  => 'boolean',
            'is_featured'   => 'boolean',
            'pass_score'    => 'integer|min:0|max:100',
            'order'         => 'integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('storage/lms/thumbnails'), $filename);

            $validated['thumbnail'] = 'lms/thumbnails/' . $filename;
        }

        $validated['created_by'] = auth()->id();

        // Courses cannot be published at creation — no lessons exist yet.
        $validated['is_published'] = false;

        LmsCourse::create($validated);

        return redirect()->route('admin.lms.courses.index')
            ->with('success', 'Course created. Add lessons before publishing.');
    }

    public function edit(LmsCourse $course)
    {
        return Inertia::render('Admin/Lms/Courses/Edit', [
            'course' => $course->load('lessons'),
        ]);
    }

    public function update(Request $request, LmsCourse $course)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:lms_courses,slug,' . $course->id,
            'description'   => 'required|string',
            'what_you_learn'=> 'nullable|array',
            'what_you_learn.*' => 'string|max:255',
            'thumbnail'     => 'nullable|image|max:2048',
            'category'      => 'nullable|string|max:100',
            'audience'      => 'required|in:affiliate,advertiser,both',
            'level'         => 'required|in:beginner,intermediate,advanced',
            'is_published'  => 'boolean',
            'is_featured'   => 'boolean',
            'pass_score'    => 'integer|min:0|max:100',
            'order'         => 'integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $wasPublished    = $course->is_published;
        $wantsPublished  = (bool) ($validated['is_published'] ?? false);

        if (! $wasPublished && $wantsPublished) {
            $publishedLessonCount = $course->lessons()->where('is_published', true)->count();
            if ($publishedLessonCount === 0) {
                return back()
                    ->withErrors(['is_published' => 'Add and publish at least one lesson before publishing the course.'])
                    ->withInput();
            }
        }

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('lms/thumbnails', 'public');
        }

        $course->update($validated);

        if (! $wasPublished && $course->fresh()->is_published) {
            $this->notifyUsers($course);
        }

        return redirect()->route('admin.lms.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    public function destroy(LmsCourse $course)
    {
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('admin.lms.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    public function toggle(LmsCourse $course)
    {
        $publishing = ! $course->is_published;

        if ($publishing) {
            $publishedLessonCount = $course->lessons()->where('is_published', true)->count();
            if ($publishedLessonCount === 0) {
                return back()->with('error', 'Cannot publish a course with no published lessons. Add at least one lesson first.');
            }
        }

        $course->update(['is_published' => $publishing]);

        if ($publishing) {
            $this->notifyUsers($course);
        }

        return back()->with('success', $publishing ? 'Course published and users notified.' : 'Course unpublished.');
    }

    private function notifyUsers(LmsCourse $course): void
    {
        $roles = match ($course->audience) {
            'affiliate'  => ['affiliate'],
            'advertiser' => ['advertiser'],
            default      => ['affiliate', 'advertiser'],
        };

        User::role($roles)
            ->where('is_active', true)
            ->chunk(100, function ($users) use ($course) {
                foreach ($users as $user) {
                    $user->notify(new NewCoursePublishedNotification($course));
                }
            });
    }
}
