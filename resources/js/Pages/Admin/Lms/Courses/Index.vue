<template>
    <AppLayout title="LMS Courses">
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">LMS Courses</h2>
                    <p class="mt-1 text-sm text-gray-600">Manage learning courses for affiliates and advertisers</p>
                </div>
                <Link
                    :href="route('admin.lms.courses.create')"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    New Course
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Flash Message -->
                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Stats Row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <p class="text-sm text-gray-600">Total Courses</p>
                        <p class="text-3xl font-bold text-indigo-600">{{ courses.total }}</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <p class="text-sm text-gray-600">Published</p>
                        <p class="text-3xl font-bold text-green-600">
                            {{ courses.data.filter(c => c.is_published).length }}
                        </p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <p class="text-sm text-gray-600">Total Enrollments</p>
                        <p class="text-3xl font-bold text-blue-600">
                            {{ courses.data.reduce((sum, c) => sum + c.enrollments_count, 0) }}
                        </p>
                    </div>
                </div>

                <!-- Courses Table -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Audience</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lessons</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Enrolled</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="course in courses.data" :key="course.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img
                                                v-if="course.thumbnail"
                                                :src="`/storage/${course.thumbnail}`"
                                                class="h-12 w-16 rounded object-cover"
                                                alt=""
                                            />
                                            <div v-else class="h-12 w-16 bg-indigo-100 rounded flex items-center justify-center">
                                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ course.title }}</p>
                                                <p class="text-xs text-gray-500">{{ course.category || 'Uncategorized' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="audienceBadge(course.audience)" class="px-2 py-1 text-xs font-medium rounded-full capitalize">
                                            {{ course.audience }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-700 capitalize">{{ course.level }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ course.lessons_count }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ course.enrollments_count }}</td>
                                    <td class="px-6 py-4">
                                        <span :class="course.is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ course.is_published ? 'Published' : 'Draft' }}
                                        </span>
                                        <span v-if="course.is_featured" class="ml-1 px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            Featured
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <Link
                                                :href="route('admin.lms.courses.lessons.index', course.id)"
                                                class="text-xs text-blue-600 hover:underline"
                                            >
                                                Lessons
                                            </Link>
                                            <Link
                                                :href="route('admin.lms.courses.edit', course.id)"
                                                class="text-xs text-indigo-600 hover:underline"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="toggleCourse(course)"
                                                class="text-xs text-yellow-600 hover:underline"
                                            >
                                                {{ course.is_published ? 'Unpublish' : 'Publish' }}
                                            </button>
                                            <button
                                                @click="deleteCourse(course)"
                                                class="text-xs text-red-600 hover:underline"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="courses.data.length === 0">
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                        No courses yet. Create your first course!
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="courses.links.length > 3" class="mt-6 flex justify-center gap-1">
                    <Link
                        v-for="(link, i) in courses.links"
                        :key="i"
                        :href="link.url"
                        v-html="link.label"
                        :class="['px-3 py-2 text-sm border rounded', link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50']"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    courses: Object,
});

const audienceBadge = (audience) => {
    const map = {
        affiliate: 'bg-purple-100 text-purple-800',
        advertiser: 'bg-blue-100 text-blue-800',
        both: 'bg-green-100 text-green-800',
    };
    return map[audience] || 'bg-gray-100 text-gray-700';
};

const toggleCourse = (course) => {
    router.patch(route('admin.lms.courses.toggle', course.id));
};

const deleteCourse = (course) => {
    if (confirm(`Delete "${course.title}"? This will also remove all lessons.`)) {
        router.delete(route('admin.lms.courses.destroy', course.id));
    }
};
</script>
