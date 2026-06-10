<template>
    <AppLayout :title="`Quiz — ${course.title}`">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link :href="route('admin.lms.courses.lessons.index', course.id)" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Final Quiz</h2>
                        <p class="text-sm text-gray-600">{{ course.title }}</p>
                    </div>
                </div>
                <button
                    v-if="quiz"
                    @click="confirmDelete = true"
                    class="text-sm text-red-600 hover:underline"
                >
                    Remove Quiz
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Flash messages -->
                <div v-if="$page.props.flash?.success" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Delete confirm modal -->
                <div v-if="confirmDelete" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-xl p-6 max-w-sm w-full shadow-xl">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Remove Quiz?</h3>
                        <p class="text-sm text-gray-600 mb-6">This will delete all questions and all user attempt records. This cannot be undone.</p>
                        <div class="flex gap-3 justify-end">
                            <button @click="confirmDelete = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm hover:bg-gray-50">Cancel</button>
                            <Link
                                :href="route('admin.lms.courses.quiz.destroy', course.id)"
                                method="delete"
                                as="button"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700"
                            >
                                Delete Quiz
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Quiz settings -->
                <form @submit.prevent="save" class="space-y-6">
                    <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                        <h3 class="text-base font-semibold text-gray-900 border-b pb-3">Quiz Settings</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Title *</label>
                            <input v-model="form.title" type="text" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400">(optional)</span></label>
                            <textarea v-model="form.description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Instructions for students before they start..."></textarea>
                        </div>

                        <div class="w-48">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Time Limit (minutes) <span class="text-gray-400">(optional)</span></label>
                            <input v-model.number="form.time_limit_minutes" type="number" min="1" max="300" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. 30" />
                        </div>
                    </div>

                    <!-- Questions -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">Questions ({{ form.questions.length }})</h3>
                            <button type="button" @click="addQuestion" class="text-sm text-indigo-600 hover:underline font-medium">
                                + Add Question
                            </button>
                        </div>

                        <div v-if="form.questions.length === 0" class="p-10 text-center text-gray-400 text-sm">
                            No questions yet. Click "Add Question" to get started.
                        </div>

                        <div class="divide-y divide-gray-100">
                            <div
                                v-for="(q, qi) in form.questions"
                                :key="qi"
                                class="p-6 space-y-4"
                                :class="qi % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'"
                            >
                                <!-- Question header -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Question {{ qi + 1 }}</span>
                                    <button type="button" @click="removeQuestion(qi)" class="text-xs text-red-500 hover:text-red-700">Remove</button>
                                </div>

                                <!-- Question text -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Question *</label>
                                    <textarea v-model="q.question" rows="2" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Enter the question..."></textarea>
                                </div>

                                <!-- Options -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="text-xs font-medium text-gray-600">Answer Options * <span class="text-gray-400">(select the correct one)</span></label>
                                        <button
                                            v-if="q.options.length < 6"
                                            type="button"
                                            @click="q.options.push('')"
                                            class="text-xs text-indigo-600 hover:underline"
                                        >+ Add option</button>
                                    </div>
                                    <div class="space-y-2">
                                        <div v-for="(opt, oi) in q.options" :key="oi" class="flex items-center gap-3">
                                            <input
                                                type="radio"
                                                :name="`correct-${qi}`"
                                                :value="oi"
                                                v-model="q.correct_option"
                                                class="text-green-600 focus:ring-green-500"
                                                :title="`Mark option ${oi + 1} as correct`"
                                            />
                                            <input
                                                v-model="q.options[oi]"
                                                type="text"
                                                required
                                                class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                :placeholder="`Option ${oi + 1}...`"
                                            />
                                            <button
                                                v-if="q.options.length > 2"
                                                type="button"
                                                @click="removeOption(qi, oi)"
                                                class="text-gray-400 hover:text-red-500 flex-shrink-0"
                                                title="Remove option"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">The radio button on the left marks which option is correct.</p>
                                </div>

                                <!-- Explanation -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Explanation <span class="text-gray-400">(shown after quiz is completed)</span></label>
                                    <textarea v-model="q.explanation" rows="2" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Why is this the correct answer?"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save button -->
                    <div class="flex justify-end gap-4">
                        <Link :href="route('admin.lms.courses.lessons.index', course.id)" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm transition-colors">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="saving || form.questions.length === 0"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 text-sm transition-colors"
                        >
                            {{ saving ? 'Saving...' : 'Save Quiz' }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    course: Object,
    quiz:   Object, // null if no quiz yet
});

const confirmDelete = ref(false);
const saving        = ref(false);

const makeBlankQuestion = () => ({
    id:             null,
    question:       '',
    options:        ['', '', '', ''],
    correct_option: 0,
    explanation:    '',
});

const form = ref({
    title:               props.quiz?.title              ?? 'Final Quiz',
    description:         props.quiz?.description        ?? '',
    time_limit_minutes:  props.quiz?.time_limit_minutes ?? null,
    questions:           props.quiz?.questions?.map(q => ({
        id:             q.id,
        question:       q.question,
        options:        [...q.options],
        correct_option: q.correct_option,
        explanation:    q.explanation ?? '',
    })) ?? [],
});

const addQuestion = () => {
    form.value.questions.push(makeBlankQuestion());
};

const removeQuestion = (qi) => {
    form.value.questions.splice(qi, 1);
};

const removeOption = (qi, oi) => {
    const q = form.value.questions[qi];
    q.options.splice(oi, 1);
    // Adjust correct_option if it was pointing to the removed option or beyond
    if (q.correct_option >= q.options.length) {
        q.correct_option = q.options.length - 1;
    }
};

const save = () => {
    saving.value = true;
    router.put(route('admin.lms.courses.quiz.update', props.course.id), form.value, {
        onFinish: () => { saving.value = false; },
    });
};
</script>
