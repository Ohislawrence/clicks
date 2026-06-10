<template>
    <AppLayout :title="`Lessons — ${course.title}`">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.lms.courses.index')" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Lessons</h2>
                        <p class="text-sm text-gray-600">{{ course.title }}</p>
                    </div>
                </div>
                <Link
                    :href="route('admin.lms.courses.lessons.create', course.id)"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Lesson
                </Link>
                <Link
                    :href="route('admin.lms.courses.quiz.edit', course.id)"
                    class="inline-flex items-center px-4 py-2 border border-indigo-300 text-indigo-700 rounded-lg hover:bg-indigo-50 transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Manage Quiz
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Lessons List -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div v-if="lessons.length === 0" class="px-6 py-12 text-center text-gray-400">
                        No lessons yet. Add your first lesson!
                    </div>
                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="lesson in lessons" :key="lesson.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-500 w-12">{{ lesson.order + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg v-if="lesson.video_url" class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                        <svg v-else class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="font-medium text-gray-900">{{ lesson.title }}</span>
                                        <span v-if="lesson.is_free_preview" class="px-1.5 py-0.5 text-xs bg-amber-100 text-amber-700 rounded">Free Preview</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ lesson.duration_minutes }} min</td>
                                <td class="px-6 py-4">
                                    <span :class="lesson.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'" class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ lesson.is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <Link
                                            :href="route('admin.lms.courses.lessons.edit', [course.id, lesson.id])"
                                            class="text-xs text-indigo-600 hover:underline"
                                        >
                                            Edit
                                        </Link>
                                        <button @click="deleteLesson(lesson)" class="text-xs text-red-600 hover:underline">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    course: Object,
    lessons: Array,
});

const deleteLesson = (lesson) => {
    if (confirm(`Delete lesson "${lesson.title}"?`)) {
        router.delete(route('admin.lms.courses.lessons.destroy', [lesson.lms_course_id, lesson.id]));
    }
};
</script>
