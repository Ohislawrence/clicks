@extends('layouts.front')

@php
    $ogImage    = $course->thumbnail ? url(Storage::url($course->thumbnail)) : asset('images/clicksintel-frontpage.PNG');
    $ogTitle    = $course->title . ' — Free Course on ' . config('app.name');
    $ogDesc     = Str::limit($course->description, 160);
    $metaKeys   = implode(', ', array_filter([$course->category, ucfirst($course->level) . ' course', 'affiliate marketing', 'free online course', 'performance marketing', config('app.name')]));
    $canonUrl   = route('learning.show', $course->slug);
    $schemaAud  = match($course->audience) {
        'affiliate'  => 'Affiliates',
        'advertiser' => 'Advertisers',
        default      => 'Affiliates and Advertisers',
    };
@endphp

@section('title', $course->title . ' — Free Course — ' . config('app.name'))
@section('meta_description', $ogDesc)
@section('meta_keywords', $metaKeys)
@section('canonical', $canonUrl)

@section('og_type', 'website')
@section('og_url', $canonUrl)
@section('og_title', $ogTitle)
@section('og_description', $ogDesc)
@section('og_image', $ogImage)

@section('twitter_url', $canonUrl)
@section('twitter_title', $ogTitle)
@section('twitter_description', $ogDesc)
@section('twitter_image', $ogImage)

@push('structured_data')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Course",
  "name": {{ Js::from($course->title) }},
  "description": {{ Js::from(Str::limit($course->description, 300)) }},
  "url": "{{ $canonUrl }}",
  "image": "{{ $ogImage }}",
  "isAccessibleForFree": true,
  "educationalLevel": "{{ ucfirst($course->level) }}",
  "audience": {
    "@@type": "Audience",
    "audienceType": "{{ $schemaAud }}"
  },
  "provider": {
    "@@type": "Organization",
    "name": {{ Js::from(config('app.name')) }},
    "url": "{{ url('/') }}"
  },
  "hasCourseInstance": {
    "@@type": "CourseInstance",
    "courseMode": "online",
    "courseWorkload": "PT{{ $course->duration_minutes ?? 0 }}M"
  }
}
</script>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════════════════════
     COURSE HERO
═══════════════════════════════════════════════════════════════════ --}}
<section class="section pt-24 pb-16" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #fff;">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">

            {{-- Left: Info --}}
            <div class="lg:col-span-3">
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-sm mb-6" style="color:#94a3b8;">
                    <a href="{{ route('learning.index') }}" style="color:#94a3b8;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#94a3b8'">Learning Center</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span style="color:#e2e8f0;">{{ $course->title }}</span>
                </nav>

                @if($course->category)
                <p class="eyebrow mb-3" style="color:#34d399;">{{ $course->category }}</p>
                @endif

                <h1 style="font-family:var(--fd);font-size:clamp(28px,4vw,50px);font-weight:800;line-height:1.15;color:#fff;margin-bottom:20px;">
                    {{ $course->title }}
                </h1>

                <p style="font-size:17px;color:#cbd5e1;line-height:1.75;margin-bottom:28px;max-width:560px;">
                    {{ $course->description }}
                </p>

                {{-- Badges --}}
                <div class="flex flex-wrap gap-3 mb-8">
                    <span style="background:rgba(52,211,153,0.15);color:#34d399;padding:6px 16px;border-radius:99px;font-size:12px;font-weight:700;border:1px solid rgba(52,211,153,0.3);">
                        FREE
                    </span>
                    <span class="capitalize" style="background:rgba(255,255,255,0.1);color:#e2e8f0;padding:6px 16px;border-radius:99px;font-size:12px;font-weight:600;border:1px solid rgba(255,255,255,0.15);">
                        {{ $course->level }}
                    </span>
                    @php
                        $aud = match($course->audience){ 'affiliate'=>'For Affiliates','advertiser'=>'For Advertisers',default=>'For Everyone' };
                    @endphp
                    <span style="background:rgba(255,255,255,0.1);color:#e2e8f0;padding:6px 16px;border-radius:99px;font-size:12px;font-weight:600;border:1px solid rgba(255,255,255,0.15);">
                        {{ $aud }}
                    </span>
                </div>

                {{-- Stats row --}}
                <div class="flex flex-wrap gap-8 text-sm" style="color:#94a3b8;">
                    <div>
                        <p style="color:#fff;font-size:22px;font-weight:700;">{{ number_format($course->enrollment_count) }}</p>
                        <p>students enrolled</p>
                    </div>
                    <div>
                        <p style="color:#fff;font-size:22px;font-weight:700;">{{ $lessons->count() }}</p>
                        <p>lessons</p>
                    </div>
                    <div>
                        <p style="color:#fff;font-size:22px;font-weight:700;">{{ $course->duration_minutes }}</p>
                        <p>minutes total</p>
                    </div>
                </div>
            </div>

            {{-- Right: Card / CTA --}}
            <div class="lg:col-span-2">
                <div style="background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.35);">
                    {{-- Thumbnail --}}
                    <div style="height:200px;background:#eef2ff;overflow:hidden;">
                        @if($course->thumbnail)
                            <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center" style="background:linear-gradient(135deg,#eef2ff,#e0e7ff);">
                                <svg class="w-20 h-20" style="color:#c7d2fe;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- CTA --}}
                    <div style="padding:28px;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                            <span style="font-size:22px;font-weight:800;color:#059669;">FREE</span>
                            <span style="font-size:12px;color:#94a3b8;">No credit card needed</span>
                        </div>

                        @auth
                            @if($isEnrolled)
                                <a href="{{ route('lms.show', $course->slug) }}"
                                   class="block text-center w-full py-3 rounded-xl text-sm font-semibold text-white mb-3 transition-colors"
                                   style="background:#059669;"
                                   onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                                    Continue Learning →
                                </a>
                                <p class="text-center text-xs" style="color:#10b981;">✓ You are enrolled in this course</p>
                            @else
                                <form method="POST" action="{{ route('lms.enroll', $course->slug) }}">
                                    @csrf
                                    <button type="submit"
                                            class="block text-center w-full py-3 rounded-xl text-sm font-semibold text-white mb-3 transition-colors cursor-pointer"
                                            style="background:#059669;border:none;"
                                            onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                                        Enroll for Free →
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('register') }}"
                               class="block text-center w-full py-3 rounded-xl text-sm font-semibold text-white mb-3 transition-colors"
                               style="background:#059669;"
                               onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                                Sign Up Free to Enroll →
                            </a>
                            <p class="text-center text-xs" style="color:#94a3b8;">
                                Already have an account?
                                <a href="{{ route('login') }}" style="color:#059669;font-weight:600;">Log in</a>
                            </p>
                        @endauth

                        <ul class="mt-6 space-y-2 text-xs" style="color:#64748b;">
                            <li class="flex items-center gap-2"><svg class="w-4 h-4" style="color:#059669;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> {{ $lessons->count() }} lessons, {{ $course->duration_minutes }} minutes</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4" style="color:#059669;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Learn at your own pace</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4" style="color:#059669;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Certificate on completion</li>
                            <li class="flex items-center gap-2"><svg class="w-4 h-4" style="color:#059669;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 100% free — always</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════════
     WHAT YOU'LL LEARN
═══════════════════════════════════════════════════════════════════ --}}
@if($course->what_you_learn && count($course->what_you_learn))
<section class="section py-14" style="background:#f8fafc;border-bottom:1px solid var(--wire);">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <h2 style="font-family:var(--fd);font-size:26px;font-weight:700;color:var(--ink);margin-bottom:28px;">What you'll learn</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($course->what_you_learn as $point)
            <div class="flex items-start gap-3" style="background:#fff;padding:16px 20px;border-radius:12px;border:1px solid var(--wire);">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span style="font-size:14px;color:var(--ink);line-height:1.5;">{{ $point }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════════════
     COURSE CONTENT / LESSONS
═══════════════════════════════════════════════════════════════════ --}}
<section class="section py-16" style="background:#fff;">
    <div class="max-w-4xl mx-auto px-6 lg:px-10">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
            <h2 style="font-family:var(--fd);font-size:26px;font-weight:700;color:var(--ink);">Course content</h2>
            <span style="font-size:14px;color:var(--stone);">{{ $lessons->count() }} lessons · {{ $course->duration_minutes }} min total</span>
        </div>

        <div style="border:1px solid var(--wire);border-radius:16px;overflow:hidden;">
            @forelse($lessons as $index => $lesson)
            <div style="padding:18px 24px;display:flex;align-items:center;gap:16px;{{ !$loop->last ? 'border-bottom:1px solid var(--wire);' : '' }}{{ $lesson->is_free_preview ? 'background:#f0fdf4;' : '' }}">
                {{-- Number --}}
                <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px;font-weight:700;{{ $lesson->is_free_preview ? 'background:#dcfce7;color:#059669;' : 'background:#f1f5f9;color:var(--stone);' }}">
                    {{ $index + 1 }}
                </div>

                {{-- Info --}}
                <div style="flex:1;">
                    <p style="font-size:14px;font-weight:600;color:var(--ink);line-height:1.4;">
                        {{ $lesson->title }}
                    </p>
                    @if($lesson->duration_minutes)
                    <p style="font-size:12px;color:var(--stone);margin-top:3px;">{{ $lesson->duration_minutes }} min</p>
                    @endif
                </div>

                {{-- Lock / Preview badge --}}
                @if($lesson->is_free_preview)
                    <span style="font-size:11px;font-weight:700;color:#059669;background:rgba(5,150,105,0.1);padding:3px 10px;border-radius:20px;flex-shrink:0;">
                        Preview
                    </span>
                @else
                    <svg class="w-4 h-4 flex-shrink-0" style="color:#94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                @endif
            </div>
            @empty
            <div style="padding:40px;text-align:center;color:var(--stone);">
                Lessons coming soon.
            </div>
            @endforelse
        </div>

        {{-- Enroll CTA below lessons --}}
        <div style="margin-top:32px;text-align:center;">
            @auth
                @if($isEnrolled)
                    <a href="{{ route('lms.show', $course->slug) }}"
                       class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-semibold text-white transition-colors"
                       style="background:#059669;" onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                        Continue Learning →
                    </a>
                @else
                    <form method="POST" action="{{ route('lms.enroll', $course->slug) }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-semibold text-white transition-colors cursor-pointer"
                                style="background:#059669;border:none;" onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                            Enroll Now — It's Free
                        </button>
                    </form>
                @endif
            @else
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 rounded-xl font-semibold text-white transition-colors"
                   style="background:#059669;" onmouseover="this.style.background='#047857'" onmouseout="this.style.background='#059669'">
                    Sign Up Free to Enroll →
                </a>
                <p style="font-size:13px;color:var(--stone);margin-top:12px;">
                    Already have an account? <a href="{{ route('login') }}" style="color:#059669;font-weight:600;">Log in</a>
                </p>
            @endauth
        </div>
    </div>
</section>

@endsection
