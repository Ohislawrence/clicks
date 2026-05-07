@extends('layouts.front')

@section('title', 'Blog - ' . config('app.name'))
@section('meta_description', 'Read the latest articles, tips, and insights about affiliate marketing, performance marketing, and CPA offers.')
@section('meta_keywords', 'affiliate marketing blog, CPA tips, performance marketing insights, affiliate news')

@push('styles')
<style>
    .blog-card {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 20px;
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .blog-card:hover {
        border-color: #3a3a3a;
        transform: translateY(-4px);
    }

    .blog-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .blog-image-placeholder {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #1a1a1a 0%, #0f0f0f 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .category-sidebar {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 16px;
    }

    .sidebar-cta {
        background: linear-gradient(135deg, #171717 0%, #1a1a1a 100%);
        border: 1px solid #2c2c2c;
        border-radius: 16px;
    }

    .category-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0;
        color: #a3a3a3;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }
    .category-link:hover {
        color: #10b981;
    }

    .category-count {
        background: #1f1f1f;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        color: #737373;
    }

    .featured-badge {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        font-size: 0.65rem;
        font-weight: 500;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
    }

    .pagination-nav {
        margin-top: 2rem;
    }
    .pagination-nav .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }
    .pagination-nav .page-item .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.5rem;
        height: 2.5rem;
        padding: 0 0.75rem;
        background: #171717;
        border: 1px solid #2c2c2c;
        border-radius: 12px;
        color: #a3a3a3;
        transition: all 0.2s ease;
    }
    .pagination-nav .page-item.active .page-link {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }
    .pagination-nav .page-item.disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .pagination-nav .page-item:not(.disabled):not(.active) .page-link:hover {
        border-color: #404040;
        background: #1f1f1f;
        color: white;
    }

    .breadcrumb-link {
        color: #a3a3a3;
        transition: color 0.2s ease;
    }
    .breadcrumb-link:hover {
        color: #10b981;
    }
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="bg-neutral-950 border-b border-neutral-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-24 text-center">
        <div class="max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-neutral-900 border border-neutral-800 rounded-full px-4 py-1.5 mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Latest Insights</span>
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight text-white mb-5">
                Our Blog
            </h1>
            <p class="text-lg md:text-xl text-neutral-400 leading-relaxed">
                Stay updated with the latest insights, tips, and news in affiliate marketing
            </p>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-12 bg-neutral-950">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Blog Posts -->
            <div class="lg:col-span-3">
                @if($posts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($posts as $post)
                            <article class="blog-card group">
                                @if($post->featured_image)
                                    <img src="{{ Storage::url($post->featured_image) }}"
                                         alt="{{ $post->title }}"
                                         class="blog-image w-full h-48 object-cover">
                                @else
                                    <div class="blog-image-placeholder">
                                        <svg class="w-12 h-12 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    </div>
                                @endif

                                <div class="p-5">
                                    <div class="flex items-center flex-wrap gap-2 text-xs text-neutral-500 mb-3">
                                        <a href="{{ route('blog.category', $post->category->slug) }}"
                                           class="featured-badge hover:bg-emerald-500/20 transition-colors">
                                            {{ $post->category->name }}
                                        </a>
                                        <time>{{ $post->published_at->format('M d, Y') }}</time>
                                        <span class="w-1 h-1 rounded-full bg-neutral-600"></span>
                                        <span>{{ $post->reading_time }} min read</span>
                                    </div>

                                    <h2 class="text-xl font-bold text-white mb-2 group-hover:text-emerald-400 transition-colors">
                                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                    </h2>

                                    <p class="text-neutral-400 text-sm leading-relaxed mb-4">
                                        {{ Str::limit($post->excerpt, 120) }}
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('blog.show', $post->slug) }}"
                                           class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 text-sm font-medium transition-colors">
                                            Read More
                                            <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg>
                                        </a>
                                        <span class="text-xs text-neutral-500">{{ number_format($post->views) }} views</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-nav mt-10">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="blog-card p-12 text-center">
                        <svg class="w-16 h-16 text-neutral-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p class="text-neutral-400 text-lg">No blog posts yet. Check back soon!</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <aside class="lg:col-span-1 space-y-6">
                <!-- Search Widget -->
                <form action="{{ route('blog.index') }}" method="GET" class="category-sidebar p-4">
                    <div class="relative">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search articles..."
                               class="w-full px-4 py-2.5 bg-neutral-900 border border-neutral-700 rounded-xl text-white placeholder-neutral-500 focus:border-emerald-500 focus:outline-none transition-colors">
                        <button type="submit" class="absolute right-3 top-2.5">
                            <svg class="w-5 h-5 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Categories Widget -->
                <div class="category-sidebar p-5">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-neutral-800">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        <h3 class="text-base font-semibold text-white">Categories</h3>
                    </div>

                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('blog.index') }}" class="category-link {{ request()->routeIs('blog.index') && !request('category') ? 'active' : '' }}">
                                <span>All Posts</span>
                                <span class="category-count">{{ $posts->total() }}</span>
                            </a>
                        </li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('blog.category', $category->slug) }}"
                                   class="category-link {{ request()->routeIs('blog.category') && request()->segment(2) === $category->slug ? 'active' : '' }}">
                                    <span>{{ $category->name }}</span>
                                    <span class="category-count">{{ $category->published_posts_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Popular Posts Widget -->
                @if(isset($popularPosts) && $popularPosts->count() > 0)
                    <div class="category-sidebar p-5">
                        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-neutral-800">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <h3 class="text-base font-semibold text-white">Popular Posts</h3>
                        </div>
                        <ul class="space-y-3">
                            @foreach($popularPosts as $popular)
                                <li>
                                    <a href="{{ route('blog.show', $popular->slug) }}"
                                       class="block text-sm text-neutral-400 hover:text-emerald-400 transition-colors leading-relaxed">
                                        {{ Str::limit($popular->title, 60) }}
                                    </a>
                                    <div class="flex items-center gap-2 text-xs text-neutral-600 mt-1">
                                        <time>{{ $popular->published_at->format('M d, Y') }}</time>
                                        <span class="w-1 h-1 rounded-full bg-neutral-700"></span>
                                        <span>{{ number_format($popular->views) }} views</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Newsletter Widget -->
                <div class="category-sidebar p-5">
                    <div class="flex items-center gap-2 mb-4 pb-3 border-b border-neutral-800">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <h3 class="text-base font-semibold text-white">Newsletter</h3>
                    </div>
                    <p class="text-sm text-neutral-400 mb-4">
                        Get the latest articles delivered to your inbox weekly.
                    </p>
                </div>

                <!-- CTA Box -->
                <div class="sidebar-cta p-5 text-center">
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">Ready to Get Started?</h3>
                    <p class="text-sm text-neutral-400 mb-5">
                        Join thousands of marketers earning with {{ config('app.name') }}
                    </p>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center w-full gap-2 bg-emerald-500 hover:bg-emerald-600 text-white py-2.5 rounded-xl font-semibold transition-all duration-200">
                        Sign Up Free
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</section>

@endsection
