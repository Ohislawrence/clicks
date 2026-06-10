<template>
    <AppLayout :title="isEdit ? 'Edit Lesson' : 'New Lesson'">
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('admin.lms.courses.lessons.index', course.id)" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Edit Lesson' : 'New Lesson' }}</h2>
                    <p class="text-sm text-gray-600">{{ course.title }}</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- ── AI Generate Panel ───────────────────────────────────── -->
                <div class="bg-gradient-to-r from-violet-50 to-indigo-50 border border-indigo-200 rounded-xl p-6 mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900">Generate with AI</h3>
                            <p class="text-xs text-gray-500">Deepseek will write full lesson content based on the title and optional outline</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Lesson title to generate for</label>
                            <input
                                v-model="aiTitle"
                                type="text"
                                placeholder="Uses the lesson title above if left blank"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Outline / key points (optional)</label>
                            <textarea
                                v-model="aiOutline"
                                rows="3"
                                placeholder="e.g. What is X, Why it matters, Step-by-step guide, Common mistakes, Action items"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ></textarea>
                        </div>

                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                @click="generateContent"
                                :disabled="aiGenerating"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                            >
                                <svg v-if="aiGenerating" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                {{ aiGenerating ? 'Generating…' : 'Generate lesson content' }}
                            </button>
                            <span v-if="aiError" class="text-xs text-red-600">{{ aiError }}</span>
                            <span v-if="aiSuccess" class="text-xs text-green-600">✓ Content inserted into editor</span>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit" class="bg-white rounded-xl shadow-sm p-8 space-y-6">

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Title *</label>
                        <input v-model="form.title" type="text" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
                    </div>

                    <!-- Video URL -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Video URL (YouTube / Vimeo embed)</label>
                        <input v-model="form.video_url" type="url" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://www.youtube.com/embed/..." />
                        <p class="mt-1 text-xs text-gray-500">Use the embed URL (youtube.com/embed/... or player.vimeo.com/video/...)</p>
                        <p v-if="errors.video_url" class="mt-1 text-sm text-red-600">{{ errors.video_url }}</p>
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lesson Content</label>
                        <div class="mb-16">
                            <QuillEditor
                                ref="quillRef"
                                v-model:content="form.content"
                                theme="snow"
                                toolbar="full"
                                contentType="html"
                                :style="{ height: '400px' }"
                                class="bg-white"
                            />
                        </div>
                    </div>

                    <!-- Duration + Order -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                            <input v-model.number="form.duration_minutes" type="number" min="0" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                            <input v-model.number="form.order" type="number" min="0" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="flex flex-wrap gap-8">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input v-model="form.is_published" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5" />
                            <span class="text-sm font-medium text-gray-700">Published</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input v-model="form.is_free_preview" type="checkbox" class="rounded border-gray-300 text-amber-500 focus:ring-amber-400 w-5 h-5" />
                            <span class="text-sm font-medium text-gray-700">Free Preview (visible without enrollment)</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-end gap-4 pt-4 border-t">
                        <Link :href="route('admin.lms.courses.lessons.index', course.id)" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </Link>
                        <button type="submit" :disabled="submitting" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                            {{ submitting ? 'Saving...' : (isEdit ? 'Update Lesson' : 'Add Lesson') }}
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
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
    course: Object,
    lesson: Object,
});

const isEdit = computed(() => !! props.lesson);
const errors = computed(() => usePage().props.errors || {});
const submitting = ref(false);
const quillRef = ref(null);

const form = ref({
    title:            props.lesson?.title ?? '',
    video_url:        props.lesson?.video_url ?? '',
    content:          props.lesson?.content ?? '',
    duration_minutes: props.lesson?.duration_minutes ?? 5,
    order:            props.lesson?.order ?? 0,
    is_published:     props.lesson?.is_published ?? false,
    is_free_preview:  props.lesson?.is_free_preview ?? false,
});

// ── AI generation state ────────────────────────────────────────────────────
const aiTitle      = ref('');
const aiOutline    = ref('');
const aiGenerating = ref(false);
const aiError      = ref('');
const aiSuccess    = ref(false);

const generateContent = async () => {
    aiError.value   = '';
    aiSuccess.value = false;
    aiGenerating.value = true;

    const titleToUse = aiTitle.value.trim() || form.value.title.trim();

    if (!titleToUse) {
        aiError.value = 'Enter a lesson title first.';
        aiGenerating.value = false;
        return;
    }

    try {
        const response = await fetch(
            route('admin.lms.courses.lessons.generate-content', props.course.id),
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({
                    lesson_title: titleToUse,
                    outline: aiOutline.value.trim(),
                }),
            }
        );

        const data = await response.json();

        if (!response.ok) {
            aiError.value = data.error ?? 'Generation failed. Please try again.';
            return;
        }

        // Insert into Quill editor and sync form
        form.value.content = data.content;
        aiSuccess.value = true;

        // Auto-clear the success message after 3 seconds
        setTimeout(() => { aiSuccess.value = false; }, 3000);

    } catch (e) {
        aiError.value = 'Network error. Please try again.';
    } finally {
        aiGenerating.value = false;
    }
};

// ── Save form ──────────────────────────────────────────────────────────────
const submit = () => {
    submitting.value = true;

    const routeName = isEdit.value
        ? route('admin.lms.courses.lessons.update', [props.course.id, props.lesson.id])
        : route('admin.lms.courses.lessons.store', props.course.id);

    const method = isEdit.value ? 'put' : 'post';

    router[method](routeName, form.value, {
        onFinish: () => { submitting.value = false; },
    });
};
</script>
