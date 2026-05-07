<?php $__env->startSection('title', $post->meta_title ?: $post->title . ' - ' . config('app.name')); ?>
<?php $__env->startSection('meta_description', $post->meta_description ?: $post->excerpt); ?>
<?php $__env->startSection('meta_keywords', $post->meta_keywords ?: ''); ?>
<?php $__env->startSection('og_title', $post->title); ?>
<?php $__env->startSection('og_description', $post->excerpt); ?>
<?php $__env->startSection('og_image', $post->featured_image ? Storage::url($post->featured_image) : asset('images/og-image.jpg')); ?>
<?php $__env->startSection('twitter_title', $post->title); ?>
<?php $__env->startSection('twitter_description', $post->excerpt); ?>

<?php $__env->startPush('structured_data'); ?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "<?php echo e($post->title); ?>",
    "description": "<?php echo e($post->excerpt); ?>",
    "image": "<?php echo e($post->featured_image ? Storage::url($post->featured_image) : asset('images/og-image.jpg')); ?>",
    "author": {
        "@type": "Person",
        "name": "<?php echo e($post->author->name); ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "<?php echo e(config('app.name')); ?>",
        "logo": {
            "@type": "ImageObject",
            "url": "<?php echo e(asset('images/logo.png')); ?>"
        }
    },
    "datePublished": "<?php echo e($post->published_at->toIso8601String()); ?>",
    "dateModified": "<?php echo e($post->updated_at->toIso8601String()); ?>"
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .article-container {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 24px;
    }

    .article-content {
        color: #d4d4d4;
        line-height: 1.8;
        font-size: 1.05rem;
    }
    .article-content h2 {
        font-size: 1.75rem;
        font-weight: 700;
        color: white;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }
    .article-content h3 {
        font-size: 1.35rem;
        font-weight: 600;
        color: #e5e5e5;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .article-content p {
        margin-bottom: 1.25rem;
    }
    .article-content ul, .article-content ol {
        margin-left: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .article-content li {
        margin-bottom: 0.35rem;
    }
    .article-content a {
        color: #10b981;
        transition: color 0.2s ease;
    }
    .article-content a:hover {
        color: #34d399;
    }
    .article-content blockquote {
        border-left: 3px solid #10b981;
        background: #1f1f1f;
        padding: 1rem 1.5rem;
        margin: 1.5rem 0;
        border-radius: 12px;
        font-style: italic;
    }
    .article-content img {
        border-radius: 16px;
        margin: 1.5rem 0;
    }
    .article-content pre {
        background: #0f0f0f;
        padding: 1rem;
        border-radius: 12px;
        overflow-x: auto;
        margin: 1.5rem 0;
    }

    .related-card {
        background: #171717;
        border: 1px solid #262626;
        border-radius: 20px;
        transition: all 0.25s ease;
        overflow: hidden;
    }
    .related-card:hover {
        border-color: #3a3a3a;
        transform: translateY(-4px);
    }

    .related-image {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }

    .related-image-placeholder {
        width: 100%;
        height: 160px;
        background: linear-gradient(135deg, #1a1a1a 0%, #0f0f0f 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .share-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #1f1f1f;
        border: 1px solid #2c2c2c;
        border-radius: 10px;
        color: #a3a3a3;
        transition: all 0.2s ease;
    }
    .share-btn:hover {
        border-color: #10b981;
        color: #10b981;
        transform: translateY(-2px);
    }

    .breadcrumb-link {
        color: #a3a3a3;
        transition: color 0.2s ease;
    }
    .breadcrumb-link:hover {
        color: #10b981;
    }

    .category-badge {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
    }

    .author-section {
        background: #1a1a1a;
        border: 1px solid #2c2c2c;
        border-radius: 20px;
    }

    .table-of-contents {
        background: #1a1a1a;
        border: 1px solid #2c2c2c;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 2rem;
    }
    .table-of-contents h4 {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #10b981;
        margin-bottom: 0.75rem;
    }
    .table-of-contents ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .table-of-contents li {
        margin-bottom: 0.5rem;
    }
    .table-of-contents a {
        color: #a3a3a3;
        font-size: 0.875rem;
        transition: color 0.2s ease;
    }
    .table-of-contents a:hover {
        color: #10b981;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<!-- Article Container -->
<article class="bg-neutral-950">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-sm mb-6">
            <a href="<?php echo e(route('front.home')); ?>" class="breadcrumb-link">Home</a>
            <svg class="w-3 h-3 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="<?php echo e(route('blog.index')); ?>" class="breadcrumb-link">Blog</a>
            <svg class="w-3 h-3 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <a href="<?php echo e(route('blog.category', $post->category->slug)); ?>" class="breadcrumb-link"><?php echo e($post->category->name); ?></a>
            <svg class="w-3 h-3 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-neutral-400 truncate"><?php echo e(Str::limit($post->title, 50)); ?></span>
        </nav>

        <div class="article-container p-6 md:p-8">

            <!-- Category Badge -->
            <a href="<?php echo e(route('blog.category', $post->category->slug)); ?>"
               class="category-badge inline-flex items-center gap-1.5 mb-5 hover:bg-emerald-500/20 transition-colors">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <?php echo e($post->category->name); ?>

            </a>

            <!-- Title -->
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold tracking-tight text-white mb-5 leading-tight">
                <?php echo e($post->title); ?>

            </h1>

            <!-- Meta Information -->
            <div class="flex flex-wrap items-center gap-3 text-sm text-neutral-500 mb-8 pb-6 border-b border-neutral-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    <span><?php echo e($post->author->name); ?></span>
                </div>
                <span class="w-1 h-1 rounded-full bg-neutral-700"></span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    <time><?php echo e($post->published_at->format('F d, Y')); ?></time>
                </div>
                <span class="w-1 h-1 rounded-full bg-neutral-700"></span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?php echo e($post->reading_time); ?> min read</span>
                </div>
                <span class="w-1 h-1 rounded-full bg-neutral-700"></span>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span><?php echo e(number_format($post->views)); ?> views</span>
                </div>
            </div>

            <!-- Featured Image -->
            <?php if($post->featured_image): ?>
                <div class="mb-8 -mx-6 md:-mx-8">
                    <img src="<?php echo e(Storage::url($post->featured_image)); ?>"
                         alt="<?php echo e($post->title); ?>"
                         class="w-full h-auto object-cover rounded-t-2xl">
                </div>
            <?php else: ?>
                <div class="mb-8 -mx-6 md:-mx-8 bg-gradient-to-r from-emerald-900/30 to-neutral-900 h-64 flex items-center justify-center rounded-t-2xl">
                    <svg class="w-20 h-20 text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
            <?php endif; ?>

            <!-- Table of Contents (if has headings) -->
            <?php
                $hasHeadings = preg_match('/<h2[^>]*>.*?<\/h2>/i', $post->content);
            ?>
            <?php if($hasHeadings): ?>
                <div class="table-of-contents">
                    <h4>Table of Contents</h4>
                    <ul>
                        <?php
                            preg_match_all('/<h2[^>]*id="([^"]+)"[^>]*>(.*?)<\/h2>/i', $post->content, $matches);
                            for ($i = 0; $i < count($matches[0]); $i++) {
                                echo '<li><a href="#' . $matches[1][$i] . '">' . strip_tags($matches[2][$i]) . '</a></li>';
                            }
                        ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Article Content -->
            <div class="article-content">
                <?php echo $post->content; ?>

            </div>

            <!-- Author Section -->
            <div class="author-section p-5 mt-8 flex items-start gap-4">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white mb-1">Written by <?php echo e($post->author->name); ?></h4>
                    <p class="text-sm text-neutral-400">
                        Performance marketing expert with years of experience in the affiliate industry.
                    </p>
                </div>
            </div>

            <!-- Share Section -->
            <div class="mt-8 pt-6 border-t border-neutral-800">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <p class="text-sm text-neutral-500 mb-0">Share this article:</p>
                    <div class="flex gap-2">
                        <a href="https://twitter.com/intent/tweet?url=<?php echo e(urlencode(route('blog.show', $post->slug))); ?>&text=<?php echo e(urlencode($post->title)); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="share-btn"
                           aria-label="Share on Twitter">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                            </svg>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo e(urlencode(route('blog.show', $post->slug))); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="share-btn"
                           aria-label="Share on Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo e(urlencode(route('blog.show', $post->slug))); ?>&title=<?php echo e(urlencode($post->title)); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="share-btn"
                           aria-label="Share on LinkedIn">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                        <button onclick="navigator.clipboard.writeText('<?php echo e(route('blog.show', $post->slug)); ?>')"
                                class="share-btn"
                                aria-label="Copy link">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<!-- Related Posts Section -->
<?php if($relatedPosts->count() > 0): ?>
    <section class="bg-neutral-900 border-t border-neutral-800 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-8">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
                <h2 class="text-2xl md:text-3xl font-bold text-white">Related Articles</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php $__currentLoopData = $relatedPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $relatedPost): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="related-card group">
                        <?php if($relatedPost->featured_image): ?>
                            <img src="<?php echo e(Storage::url($relatedPost->featured_image)); ?>"
                                 alt="<?php echo e($relatedPost->title); ?>"
                                 class="related-image w-full h-40 object-cover">
                        <?php else: ?>
                            <div class="related-image-placeholder">
                                <svg class="w-8 h-8 text-neutral-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                                </svg>
                            </div>
                        <?php endif; ?>

                        <div class="p-4">
                            <div class="text-xs text-neutral-500 mb-2">
                                <time><?php echo e($relatedPost->published_at->format('M d, Y')); ?></time>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2 group-hover:text-emerald-400 transition-colors">
                                <a href="<?php echo e(route('blog.show', $relatedPost->slug)); ?>"><?php echo e($relatedPost->title); ?></a>
                            </h3>
                            <p class="text-neutral-400 text-sm leading-relaxed mb-3">
                                <?php echo e(Str::limit($relatedPost->excerpt, 80)); ?>

                            </p>
                            <a href="<?php echo e(route('blog.show', $relatedPost->slug)); ?>"
                               class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 text-sm font-medium transition-colors">
                                Read More
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Smooth scroll for table of contents links
    document.querySelectorAll('.table-of-contents a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // Copy link functionality
    const copyButton = document.querySelector('.share-btn[aria-label="Copy link"]');
    if (copyButton) {
        copyButton.addEventListener('click', async function() {
            try {
                await navigator.clipboard.writeText(window.location.href);
                const originalHtml = this.innerHTML;
                this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>';
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                }, 2000);
            } catch (err) {
                console.error('Failed to copy: ', err);
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.front', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\dealsintel\resources\views/front/blog/show.blade.php ENDPATH**/ ?>