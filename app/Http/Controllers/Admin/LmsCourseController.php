<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
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

        LmsCourse::create($validated);

        return redirect()->route('admin.lms.courses.index')
            ->with('success', 'Course created successfully.');
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
            'order'         => 'integer|min:0',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('lms/thumbnails', 'public');
        }

        $course->update($validated);

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
        $course->update(['is_published' => ! $course->is_published]);

        return back()->with('success', 'Course status updated.');
    }
}
