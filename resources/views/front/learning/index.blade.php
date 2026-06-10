@extends('layouts.front')

@section('title', 'Free Learning Center — ' . config('app.name'))
@section('meta_description', 'Free courses for affiliates and advertisers on ' . config('app.name') . '. Learn affiliate marketing, e-commerce, and digital sales — at your own pace.')
@section('meta_keywords', 'free affiliate marketing course, performance marketing training, CPA network training, affiliate income Nigeria, ' . config('app.name') . ' learning center')
@section('canonical', route('learning.index'))

@section('og_type', 'website')
@section('og_url', route('learning.index'))
@section('og_title', 'Free Learning Center — ' . config('app.name'))
@section('og_description', 'Free courses for affiliates and advertisers. Learn affiliate marketing, e-commerce, and digital sales — at your own pace.')
@section('og_image', asset('images/clicksintel-frontpage.PNG'))

@section('twitter_url', route('learning.index'))
@section('twitter_title', 'Free Learning Center — ' . config('app.name'))
@section('twitter_description', 'Free courses for affiliates and advertisers. Learn affiliate marketing, e-commerce, and digital sales — at your own pace.')
@section('twitter_image', asset('images/clicksintel-frontpage.PNG'))

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════════════════════════ --}}
<section class="section pt-24 pb-16" style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border-bottom: 1px solid var(--wire);">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold mb-6"
                 style="background: rgba(5,150,105,0.08); color: #059669; border: 1px solid rgba(5,150,105,0.2);">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                100% Free — No credit card needed
            </div>
            <h1 class="display mb-5" style="font-size: clamp(32px, 5vw, 60px); color: var(--ink); line-height: 1.1; font-weight: 900;">
                Learn. Grow. <span style="color: #059669;">Earn more.</span>
            </h1>
            <p class="mb-8" style="font-size: 18px; color: var(--ash); line-height: 1.75; max-width: 580px;">
                Free courses built specifically for Nigerian affiliates and advertisers. Master performance marketing, affiliate strategies, and digital sales — and start earning more on {{ config('app.name') }}.
            </p>
            <div class="flex flex-wrap gap-6 text-sm" style="color: var(--stone);">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                    </svg>
                    <span><strong style="color:var(--ink);">{{ $courses->count() }}</strong> courses available</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Learn at your own pace</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Certificate on completion</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     SEARCH + FILTERS
═══════════════════════════════════════════════════════════════════ --}}
<section class="section py-8" style="background: #fff; border-bottom: 1px solid var(--wire); position: sticky; top: 80px; z-index: 40;">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <form method="GET" action="{{ route('learning.index') }}" class="flex flex-wrap gap-3 items-center">
            {{-- Search --}}
            <div class="flex-1 min-w-56 relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:var(--stone);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    type="text"
                    name="q"
                    value="{{ $filters['q'] ?? '' }}"
                    placeholder="Search courses..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-lg border text-sm"
                    style="border-color: var(--wire); color: var(--ink); outline: none;"
                    onfocus="this.style.borderColor='#059669'" onblur="this.style.borderColor='var(--wire)'"
                />
            </div>

            {{-- Level --}}
            <select name="level" class="py-2.5 px-4 rounded-lg border text-sm" style="border-color: var(--wire); color: var(--ink);"
                    onchange="this.form.submit()">
                <option value="">All Levels</option>
                <option value="beginner" {{ ($filters['level'] ?? '') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                <option value="intermediate" {{ ($filters['level'] ?? '') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                <option value="advanced" {{ ($filters['level'] ?? '') === 'advanced' ? 'selected' : '' }}>Advanced</option>
            </select>

            {{-- Audience --}}
            <select name="audience" class="py-2.5 px-4 rounded-lg border text-sm" style="border-color: var(--wire); color: var(--ink);"
                    onchange="this.form.submit()">
                <option value="">All Audiences</option>
                <option value="affiliate" {{ ($filters['audience'] ?? '') === 'affiliate' ? 'selected' : '' }}>For Affiliates</option>
                <option value="advertiser" {{ ($filters['audience'] ?? '') === 'advertiser' ? 'selected' : '' }}>For Advertisers</option>
            </select>

            <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-white transition-colors"
                    style="background: #0f172a;">
                Search
            </button>

            @if(array_filter($filters))
            <a href="{{ route('learning.index') }}" class="text-sm font-medium" style="color: var(--stone);">
                Clear filters
            </a>
            @endif
        </form>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     COURSES GRID
═══════════════════════════════════════════════════════════════════ --}}
<section class="section py-16" style="background: #f8fafc;">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">

        @if($courses->isEmpty())
        <div class="text-center py-20">
            <svg class="w-16 h-16 mx-auto mb-4" style="color: #e2e8f0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
            </svg>
            <p style="font-size: 18px; font-weight: 600; color: var(--ink);">No courses found</p>
            <p style="color: var(--stone); margin-top: 6px;">Try adjusting your search or filters.</p>
            <a href="{{ route('learning.index') }}" class="inline-block mt-6 px-5 py-2.5 rounded-lg text-sm font-semibold text-white" style="background:#059669;">
                View all courses
            </a>
        </div>
        @else
        <p class="mb-8 text-sm" style="color: var(--stone);">Showing {{ $courses->count() }} course{{ $courses->count() !== 1 ? 's' : '' }}</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
            <article class="card-lift" style="background:#fff;border:1px solid var(--wire);border-radius:20px;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .2s;">

                {{-- Thumbnail --}}
                <a href="{{ route('learning.show', $course->slug) }}" class="block relative" style="height:200px;background:#eef2ff;overflow:hidden;">
                    @if($course->thumbnail)
                        <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-20 h-20" style="color:#c7d2fe;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Free badge --}}
                    <div class="absolute top-3 left-3">
                        <span style="background:rgba(5,150,105,0.9);color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;letter-spacing:0.05em;">FREE</span>
                    </div>

                    {{-- Featured badge --}}
                    @if($course->is_featured)
                    <div class="absolute top-3 right-3">
                        <span style="background:rgba(245,158,11,0.9);color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;">⭐ Featured</span>
                    </div>
                    @endif
                </a>

                {{-- Body --}}
                <div style="padding:24px;flex:1;display:flex;flex-direction:column;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;flex-wrap:wrap;">
                        @if($course->category)
                        <span style="font-size:11px;font-weight:600;color:#059669;">{{ $course->category }}</span>
                        <span style="color:var(--wire);">·</span>
                        @endif
                        <span class="capitalize" style="font-size:11px;color:var(--stone);">{{ $course->level }}</span>
                        <span style="color:var(--wire);">·</span>
                        @php
                            $audience = match($course->audience) {
                                'affiliate' => 'Affiliates',
                                'advertiser' => 'Advertisers',
                                default => 'Everyone',
                            };
                        @endphp
                        <span style="font-size:11px;color:var(--stone);">{{ $audience }}</span>
                    </div>

                    <a href="{{ route('learning.show', $course->slug) }}"
                       style="font-family:var(--fd);font-size:17px;font-weight:700;color:var(--ink);line-height:1.35;margin-bottom:10px;display:block;text-decoration:none;"
                       onmouseover="this.style.color='#059669'" onmouseout="this.style.color='var(--ink)'">
                        {{ $course->title }}
                    </a>

                    <p style="font-size:13px;color:var(--ash);line-height:1.7;margin-bottom:16px;flex:1;">
                        {{ Str::limit($course->description, 100) }}
                    </p>

                    {{-- Meta --}}
                    <div style="display:flex;align-items:center;gap:16px;font-size:12px;color:var(--stone);margin-bottom:18px;padding-top:16px;border-top:1px solid var(--wire);">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13"/></svg>
                            {{ $course->lesson_count }} lessons
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $course->duration_minutes }} min
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                            {{ number_format($course->enrollment_count) }} enrolled
                        </span>
                    </div>

                    <a href="{{ route('learning.show', $course->slug) }}"
                       class="block text-center py-2.5 rounded-lg text-sm font-semibold transition-colors"
                       style="background:#059669;color:#fff;"
                       onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                        View Course →
                    </a>
                </div>
            </article>
            @endforeach
        </div>
        @endif
    </div>
</section>

@endsection
