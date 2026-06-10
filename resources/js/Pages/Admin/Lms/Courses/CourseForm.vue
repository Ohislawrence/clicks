<template>
    <AppLayout :title="isEdit ? 'Edit Course' : 'New Course'">
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.lms.courses.index')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <h2 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Edit Course' : 'New Course' }}</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <form @submit.prevent="submit" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm p-8 space-y-6">

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Course Title *</label>
                        <input v-model="form.title" type="text" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug (auto-generated if empty)</label>
                        <input v-model="form.slug" type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="my-course-slug" />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea v-model="form.description" rows="4" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                    </div>

                    <!-- What You'll Learn -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">What You'll Learn</label>
                        <div class="space-y-2">
                            <div v-for="(item, idx) in form.what_you_learn" :key="idx" class="flex gap-2">
                                <input v-model="form.what_you_learn[idx]" type="text" class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Bullet point..." />
                                <button type="button" @click="removeLearningPoint(idx)" class="text-red-500 hover:text-red-700 px-2">✕</button>
                            </div>
                        </div>
                        <button type="button" @click="addLearningPoint" class="mt-2 text-sm text-indigo-600 hover:underline">+ Add Point</button>
                    </div>

                    <!-- Row: Category + Audience + Level -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <input v-model="form.category" type="text" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. Affiliate Marketing" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Audience *</label>
                            <select v-model="form.audience" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="both">Both</option>
                                <option value="affiliate">Affiliates Only</option>
                                <option value="advertiser">Advertisers Only</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                            <select v-model="form.level" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail</label>
                        <input type="file" @change="handleThumbnail" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                        <img v-if="thumbnailPreview" :src="thumbnailPreview" class="mt-2 h-32 rounded-lg object-cover" alt="Preview" />
                        <img v-else-if="course?.thumbnail" :src="`/storage/${course.thumbnail}`" class="mt-2 h-32 rounded-lg object-cover" alt="Current thumbnail" />
                    </div>

                    <!-- Order -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                        <input v-model.number="form.order" type="number" min="0" class="w-32 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>

                    <!-- Toggles -->
                    <div class="flex flex-wrap gap-8">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input v-model="form.is_published" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5" />
                            <span class="text-sm font-medium text-gray-700">Published</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input v-model="form.is_featured" type="checkbox" class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-400 w-5 h-5" />
                            <span class="text-sm font-medium text-gray-700">Featured</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end gap-4 pt-4 border-t">
                        <Link :href="route('admin.lms.courses.index')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="submitting" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                            {{ submitting ? 'Saving...' : (isEdit ? 'Update Course' : 'Create Course') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    course: Object,
});

const isEdit = computed(() => !! props.course);
const errors = computed(() => usePage().props.errors || {});

const form = ref({
    title:          props.course?.title ?? '',
    slug:           props.course?.slug ?? '',
    description:    props.course?.description ?? '',
    what_you_learn: props.course?.what_you_learn ?? [],
    category:       props.course?.category ?? '',
    audience:       props.course?.audience ?? 'both',
    level:          props.course?.level ?? 'beginner',
    is_published:   props.course?.is_published ?? false,
    is_featured:    props.course?.is_featured ?? false,
    order:          props.course?.order ?? 0,
    thumbnail:      null,
});

const thumbnailPreview = ref(null);
const submitting = ref(false);

const handleThumbnail = (e) => {
    const file = e.target.files[0];
    if (! file) return;
    form.value.thumbnail = file;
    thumbnailPreview.value = URL.createObjectURL(file);
};

const addLearningPoint = () => {
    form.value.what_you_learn.push('');
};

const removeLearningPoint = (idx) => {
    form.value.what_you_learn.splice(idx, 1);
};

const submit = () => {
    submitting.value = true;

    const data = new FormData();
    Object.entries(form.value).forEach(([key, val]) => {
        if (key === 'what_you_learn') {
            val.forEach((v, i) => data.append(`what_you_learn[${i}]`, v));
        } else if (val !== null && val !== undefined) {
            data.append(key, typeof val === 'boolean' ? (val ? 1 : 0) : val);
        }
    });

    const routeName = isEdit.value
        ? route('admin.lms.courses.update', props.course.id)
        : route('admin.lms.courses.store');

    if (isEdit.value) {
        data.append('_method', 'PUT');
    }

    router.post(routeName, data, {
        onFinish: () => { submitting.value = false; },
    });
};
</script>
