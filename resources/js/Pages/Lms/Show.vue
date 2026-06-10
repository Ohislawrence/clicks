<template>
    <AppLayout :title="course.title">
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('lms.index')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ course.title }}</h2>
                    <p class="text-sm text-gray-600 capitalize">{{ course.level }} · {{ course.duration_minutes }} min total</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>
                <div v-if="$page.props.flash?.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.error }}
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left: Course Details -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Thumbnail -->
                        <div class="rounded-xl overflow-hidden bg-indigo-50 aspect-video">
                            <img
                                v-if="course.thumbnail"
                                :src="`/storage/${course.thumbnail}`"
                                class="w-full h-full object-cover"
                                alt=""
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-24 h-24 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">About This Course</h3>
                            <p class="text-gray-600 leading-relaxed">{{ course.description }}</p>
                        </div>

                        <!-- What You'll Learn -->
                        <div v-if="course.what_you_learn?.length" class="bg-white rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">What You'll Learn</h3>
                            <ul class="space-y-2">
                                <li v-for="(point, idx) in course.what_you_learn" :key="idx" class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-gray-700 text-sm">{{ point }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right: Enroll card + Lessons -->
                    <div class="space-y-6">

                        <!-- Enroll / Progress Card -->
                        <div class="bg-white rounded-xl shadow-sm p-6 sticky top-6">
                            <!-- Completed -->
                            <div v-if="completed" class="text-center mb-4">
                                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <p class="font-semibold text-green-700">Course Completed!</p>
                            </div>

                            <!-- Progress bar for enrolled -->
                            <div v-if="isEnrolled && !completed" class="mb-4">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Progress</span><span>{{ progress }}%</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full">
                                    <div class="h-2 bg-indigo-500 rounded-full transition-all" :style="{ width: progress + '%' }"></div>
                                </div>
                            </div>

                            <!-- CTA -->
                            <div v-if="! isEnrolled">
                                <Link
                                    :href="route('lms.enroll', course.slug)"
                                    method="post"
                                    as="button"
                                    class="w-full py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors text-center block"
                                >
                                    Enroll — It's Free
                                </Link>
                                <p class="text-center text-xs text-gray-400 mt-2">Free access. No credit card needed.</p>
                            </div>
                            <div v-else>
                                <Link
                                    v-if="firstUncompletedLesson"
                                    :href="route('lms.lesson', [course.slug, firstUncompletedLesson.slug])"
                                    class="w-full py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors text-center block"
                                >
                                    {{ progress === 0 ? 'Start Learning' : 'Continue Learning' }}
                                </Link>
                            </div>

                            <!-- Meta -->
                            <div class="mt-5 space-y-2 text-sm text-gray-600 border-t pt-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253" />
                                    </svg>
                                    {{ lessons.length }} lessons
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ course.duration_minutes }} minutes total
                                </div>
                                <div class="flex items-center gap-2 capitalize">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    {{ course.level }} level
                                </div>
                            </div>
                        </div>

                        <!-- Lesson List -->
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b">
                                <h3 class="font-semibold text-gray-900">Course Content</h3>
                            </div>
                            <ul class="divide-y divide-gray-100">
                                <li v-for="(lesson, idx) in lessons" :key="lesson.id">
                                    <template v-if="! lesson.is_locked">
                                        <Link
                                            :href="route('lms.lesson', [course.slug, lesson.slug])"
                                            class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-colors"
                                        >
                                            <span class="text-xs text-gray-400 w-5">{{ idx + 1 }}</span>
                                            <svg v-if="lesson.is_completed" class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <svg v-else-if="lesson.video_url" class="w-4 h-4 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                            <svg v-else class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6" /></svg>
                                            <span class="text-sm text-gray-800 flex-1">{{ lesson.title }}</span>
                                            <span v-if="lesson.is_free_preview && !isEnrolled" class="text-xs text-amber-600">Free</span>
                                            <span class="text-xs text-gray-400">{{ lesson.duration_minutes }}m</span>
                                        </Link>
                                    </template>
                                    <template v-else>
                                        <div class="flex items-center gap-3 px-5 py-3 opacity-50">
                                            <span class="text-xs text-gray-400 w-5">{{ idx + 1 }}</span>
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                            <span class="text-sm text-gray-600 flex-1">{{ lesson.title }}</span>
                                            <span class="text-xs text-gray-400">{{ lesson.duration_minutes }}m</span>
                                        </div>
                                    </template>
                                </li>

                                <!-- Final Quiz row -->
                                <li v-if="quiz">
                                    <template v-if="isEnrolled">
                                        <Link
                                            :href="route('lms.quiz', course.slug)"
                                            class="flex items-center gap-3 px-5 py-3 hover:bg-indigo-50 transition-colors border-t-2 border-indigo-100"
                                        >
                                            <span class="text-xs text-gray-400 w-5">✦</span>
                                            <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-sm text-indigo-700 font-medium flex-1">{{ quiz.title }}</span>
                                            <span v-if="quizPassed" class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">Passed</span>
                                            <span v-else class="text-xs text-indigo-500">Final Quiz</span>
                                        </Link>
                                    </template>
                                    <template v-else>
                                        <div class="flex items-center gap-3 px-5 py-3 opacity-50 border-t-2 border-indigo-100">
                                            <span class="text-xs text-gray-400 w-5">✦</span>
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            <span class="text-sm text-gray-600 flex-1">{{ quiz.title }}</span>
                                            <span class="text-xs text-gray-400">Enroll to unlock</span>
                                        </div>
                                    </template>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    course:     Object,
    lessons:    Array,
    isEnrolled: Boolean,
    progress:   Number,
    completed:  Boolean,
    quiz:       Object,   // null if no quiz exists
    quizPassed: Boolean,
});

const firstUncompletedLesson = computed(() =>
    props.lessons.find(l => ! l.is_completed && ! l.is_locked)
    ?? props.lessons.find(l => ! l.is_locked)
);
</script>
