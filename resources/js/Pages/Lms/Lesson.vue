<template>
    <AppLayout :title="lesson.title">
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('lms.show', course.slug)" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <p class="text-xs text-indigo-600 font-medium">{{ course.title }}</p>
                    <h2 class="text-xl font-bold text-gray-900">{{ lesson.title }}</h2>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                    <!-- Main Content -->
                    <div class="lg:col-span-3 space-y-6">

                        <!-- Video Player -->
                        <div v-if="lesson.video_url" class="aspect-video rounded-xl overflow-hidden bg-black shadow-lg">
                            <iframe
                                :src="lesson.video_url"
                                class="w-full h-full"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen
                            ></iframe>
                        </div>

                        <!-- Text Content -->
                        <div v-if="lesson.content" class="bg-white rounded-xl shadow-sm p-8">
                            <div class="prose max-w-none text-gray-700 leading-relaxed" v-html="lesson.content"></div>
                        </div>

                        <!-- Navigation + Mark Complete -->
                        <div class="flex items-center justify-between gap-4">
                            <Link
                                v-if="prevLesson && !prevLesson.is_locked"
                                :href="route('lms.lesson', [course.slug, prevLesson.slug])"
                                class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Previous
                            </Link>
                            <div v-else></div>

                            <div class="flex items-center gap-3">
                                <div v-if="isCompleted" class="flex items-center gap-2 text-green-600 text-sm font-medium">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    Completed
                                </div>
                                <Link
                                    v-if="isEnrolled && !isCompleted"
                                    :href="route('lms.complete', [course.slug, lesson.slug])"
                                    method="post"
                                    as="button"
                                    class="px-5 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors"
                                >
                                    Mark as Complete
                                </Link>
                            </div>

                            <Link
                                v-if="nextLesson && !nextLesson.is_locked"
                                :href="route('lms.lesson', [course.slug, nextLesson.slug])"
                                class="flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors"
                            >
                                Next
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                            <div v-else></div>
                        </div>
                    </div>

                    <!-- Sidebar: Lesson List -->
                    <div class="lg:col-span-1">
                        <!-- Progress -->
                        <div v-if="isEnrolled" class="bg-white rounded-xl shadow-sm p-4 mb-4">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Course Progress</span><span>{{ progress }}%</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full">
                                <div class="h-2 bg-indigo-500 rounded-full transition-all" :style="{ width: progress + '%' }"></div>
                            </div>
                        </div>

                        <!-- Lessons -->
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b bg-gray-50">
                                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Lessons</p>
                            </div>
                            <ul class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                                <li v-for="(l, idx) in allLessons" :key="l.id">
                                    <template v-if="!l.is_locked">
                                        <Link
                                            :href="route('lms.lesson', [course.slug, l.slug])"
                                            :class="[
                                                'flex items-center gap-2 px-4 py-3 hover:bg-gray-50 transition-colors text-sm',
                                                l.id === lesson.id ? 'bg-indigo-50 border-l-4 border-indigo-500' : ''
                                            ]"
                                        >
                                            <svg v-if="l.is_completed" class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span v-else class="text-xs text-gray-400 w-4 flex-shrink-0">{{ idx + 1 }}</span>
                                            <span :class="l.id === lesson.id ? 'text-indigo-700 font-medium' : 'text-gray-700'" class="flex-1 line-clamp-2 text-xs leading-snug">{{ l.title }}</span>
                                        </Link>
                                    </template>
                                    <template v-else>
                                        <div class="flex items-center gap-2 px-4 py-3 opacity-40 text-sm">
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                                            <span class="text-xs text-gray-600 flex-1 line-clamp-2 leading-snug">{{ l.title }}</span>
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
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    course: Object,
    lesson: Object,
    allLessons: Array,
    prevLesson: Object,
    nextLesson: Object,
    isEnrolled: Boolean,
    isCompleted: Boolean,
    progress: Number,
});
</script>
