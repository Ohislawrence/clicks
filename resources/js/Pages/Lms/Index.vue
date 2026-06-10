<template>
    <AppLayout title="Learning Center">
        <template #header>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Learning Center</h2>
                <p class="mt-1 text-sm text-gray-600">Courses to help you grow as a {{ userRole }}</p>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Hero Banner -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 mb-10 text-white">
                    <h3 class="text-3xl font-bold mb-2">Grow Your Skills</h3>
                    <p class="text-indigo-100 text-lg max-w-xl">
                        Free courses designed to help you maximise earnings and grow your business on DealsIntel.
                    </p>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-10">
                    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                        <p class="text-2xl font-bold text-indigo-600">{{ courses.length }}</p>
                        <p class="text-sm text-gray-600">Courses</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ enrolledCount }}</p>
                        <p class="text-sm text-gray-600">Enrolled</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                        <p class="text-2xl font-bold text-blue-600">{{ completedCount }}</p>
                        <p class="text-sm text-gray-600">Completed</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                        <p class="text-2xl font-bold text-amber-500">{{ totalMinutes }}</p>
                        <p class="text-sm text-gray-600">Minutes of Content</p>
                    </div>
                </div>

                <!-- Courses Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="course in courses"
                        :key="course.id"
                        class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow group"
                    >
                        <!-- Thumbnail -->
                        <div class="relative h-44 bg-indigo-50">
                            <img
                                v-if="course.thumbnail"
                                :src="`/storage/${course.thumbnail}`"
                                class="w-full h-full object-cover"
                                alt=""
                            />
                            <div v-else class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <!-- Badges -->
                            <div class="absolute top-3 left-3 flex gap-1">
                                <span v-if="course.is_featured" class="px-2 py-0.5 text-xs font-semibold bg-yellow-400 text-yellow-900 rounded-full">
                                    ⭐ Featured
                                </span>
                                <span class="px-2 py-0.5 text-xs font-semibold bg-white/80 text-gray-700 rounded-full capitalize">
                                    {{ course.level }}
                                </span>
                            </div>
                            <!-- Progress overlay for enrolled -->
                            <div v-if="course.is_enrolled" class="absolute bottom-0 left-0 right-0 h-1.5 bg-gray-200">
                                <div class="h-full bg-green-500 transition-all" :style="{ width: course.progress + '%' }"></div>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="p-5">
                            <div class="flex items-center gap-2 mb-2">
                                <span v-if="course.category" class="text-xs text-indigo-600 font-medium">{{ course.category }}</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-500">{{ course.lesson_count }} lessons</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-500">{{ course.duration_minutes }} min</span>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-2 group-hover:text-indigo-600 transition-colors">{{ course.title }}</h3>
                            <p class="text-sm text-gray-600 line-clamp-2 mb-4">{{ course.description }}</p>

                            <!-- Progress / CTA -->
                            <div v-if="course.completed" class="flex items-center gap-2 text-green-600 text-sm font-medium mb-3">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                Completed
                            </div>
                            <div v-else-if="course.is_enrolled" class="mb-3">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                    <span>Progress</span>
                                    <span>{{ course.progress }}%</span>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full">
                                    <div class="h-2 bg-indigo-500 rounded-full transition-all" :style="{ width: course.progress + '%' }"></div>
                                </div>
                            </div>

                            <Link
                                :href="route('lms.show', course.slug)"
                                :class="[
                                    'block text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors',
                                    course.completed
                                        ? 'bg-green-50 text-green-700 hover:bg-green-100'
                                        : course.is_enrolled
                                        ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                                        : 'border border-indigo-600 text-indigo-600 hover:bg-indigo-50'
                                ]"
                            >
                                {{ course.completed ? 'Review Course' : course.is_enrolled ? 'Continue Learning' : 'View Course' }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="courses.length === 0" class="text-center py-16 text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <p class="text-lg font-medium">No courses available yet.</p>
                    <p class="text-sm">Check back soon!</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    courses: Array,
});

const userRole = computed(() => {
    const roles = usePage().props.auth?.user?.roles ?? [];
    return roles[0]?.name ?? 'user';
});

const enrolledCount = computed(() => props.courses.filter(c => c.is_enrolled).length);
const completedCount = computed(() => props.courses.filter(c => c.completed).length);
const totalMinutes = computed(() => props.courses.reduce((sum, c) => sum + (c.duration_minutes || 0), 0));
</script>
