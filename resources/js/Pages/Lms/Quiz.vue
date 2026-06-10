<template>
    <AppLayout :title="`Quiz — ${course.title}`">
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('lms.show', course.slug)" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ quiz.title }}</h2>
                    <p class="text-sm text-gray-600">{{ course.title }}</p>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

                <!-- Flash / result banner -->
                <div v-if="$page.props.flash?.success"
                    :class="latestAttempt?.passed ? 'bg-green-50 border-green-300 text-green-800' : 'bg-amber-50 border-amber-300 text-amber-800'"
                    class="border rounded-xl px-5 py-4 font-medium text-sm">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Quiz info card -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex flex-wrap gap-6 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ quiz.questions_count }} questions
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pass score: {{ passScore }}%
                        </div>
                        <div v-if="quiz.time_limit_minutes" class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ quiz.time_limit_minutes }} min limit
                        </div>
                        <div v-if="bestAttempt" class="flex items-center gap-2">
                            <svg class="w-4 h-4" :class="bestAttempt.passed ? 'text-green-500' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            Best: {{ bestAttempt.score }}%
                            <span v-if="bestAttempt.passed" class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">Passed</span>
                        </div>
                    </div>

                    <p v-if="quiz.description" class="mt-4 text-sm text-gray-600 leading-relaxed border-t pt-4">{{ quiz.description }}</p>
                </div>

                <!-- Already passed: review answers -->
                <div v-if="alreadyPassed && latestAttempt" class="space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-xl p-5 text-center">
                        <div class="text-4xl font-extrabold text-green-700 mb-1">{{ latestAttempt.score }}%</div>
                        <p class="text-green-800 font-semibold">You passed this quiz!</p>
                        <p class="text-sm text-green-600 mt-1">Great job — this course is marked complete in your profile.</p>
                    </div>

                    <!-- Answer review -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b">
                            <h3 class="font-semibold text-gray-900">Answer Review</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            <div v-for="(q, qi) in questions" :key="q.id" class="p-6">
                                <div class="flex items-start gap-3 mb-3">
                                    <span class="text-xs font-bold text-gray-400 mt-1 flex-shrink-0">{{ qi + 1 }}</span>
                                    <p class="font-medium text-gray-900 text-sm">{{ q.question }}</p>
                                </div>
                                <div class="ml-5 space-y-2">
                                    <div
                                        v-for="(opt, oi) in q.options"
                                        :key="oi"
                                        class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm"
                                        :class="{
                                            'bg-green-50 border border-green-200 text-green-800': oi === q.correct_option,
                                            'bg-red-50 border border-red-200 text-red-700': getAttemptAnswer(q.id)?.selected === oi && oi !== q.correct_option,
                                            'text-gray-600': oi !== q.correct_option && getAttemptAnswer(q.id)?.selected !== oi,
                                        }"
                                    >
                                        <svg v-if="oi === q.correct_option" class="w-4 h-4 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <svg v-else-if="getAttemptAnswer(q.id)?.selected === oi" class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        <span v-else class="w-4 h-4 flex-shrink-0"></span>
                                        {{ opt }}
                                    </div>
                                </div>
                                <p v-if="q.explanation" class="ml-5 mt-3 text-xs text-gray-500 bg-gray-50 rounded-lg px-3 py-2">
                                    <span class="font-semibold">Explanation:</span> {{ q.explanation }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <Link :href="route('lms.show', course.slug)" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors text-sm">
                            ← Back to Course
                        </Link>
                    </div>
                </div>

                <!-- Quiz form (not yet passed OR first attempt) -->
                <form v-else @submit.prevent="submitQuiz" class="space-y-4">

                    <!-- Previous failed attempt score -->
                    <div v-if="latestAttempt && !latestAttempt.passed" class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                        <p class="font-semibold text-red-700">Last attempt: {{ latestAttempt.score }}% — try again!</p>
                        <p class="text-sm text-red-500 mt-1">You need {{ passScore }}% to pass. Review the questions carefully.</p>
                    </div>

                    <!-- Questions -->
                    <div
                        v-for="(q, qi) in questions"
                        :key="q.id"
                        class="bg-white rounded-xl shadow-sm p-6"
                    >
                        <div class="flex items-start gap-3 mb-4">
                            <span class="flex-shrink-0 w-7 h-7 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-bold">{{ qi + 1 }}</span>
                            <p class="font-medium text-gray-900">{{ q.question }}</p>
                        </div>
                        <div class="space-y-2 ml-10">
                            <label
                                v-for="(opt, oi) in q.options"
                                :key="oi"
                                class="flex items-center gap-3 p-3 rounded-lg cursor-pointer border transition-colors"
                                :class="answers[q.id] === oi
                                    ? 'border-indigo-400 bg-indigo-50'
                                    : 'border-gray-200 hover:border-indigo-200 hover:bg-gray-50'"
                            >
                                <input
                                    type="radio"
                                    :name="`q-${q.id}`"
                                    :value="oi"
                                    v-model="answers[q.id]"
                                    class="text-indigo-600 focus:ring-indigo-500"
                                />
                                <span class="text-sm text-gray-800">{{ opt }}</span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-between items-center pt-2">
                        <p class="text-sm text-gray-500">Answer all {{ questions.length }} questions before submitting.</p>
                        <button
                            type="submit"
                            :disabled="submitting || !allAnswered"
                            class="px-8 py-3 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 disabled:opacity-50 transition-colors text-sm"
                        >
                            {{ submitting ? 'Submitting...' : 'Submit Quiz' }}
                        </button>
                    </div>
                    <p v-if="!allAnswered && attempted" class="text-sm text-red-500 text-right">Please answer all questions before submitting.</p>
                </form>

            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    course:        Object,
    quiz:          Object,
    questions:     Array,
    passScore:     Number,
    bestAttempt:   Object,
    latestAttempt: Object,
    alreadyPassed: Boolean,
});

// answers: { [questionId]: selectedOptionIndex }
const answers    = ref({});
const submitting = ref(false);
const attempted  = ref(false);

const allAnswered = computed(() =>
    props.questions.every(q => answers.value[q.id] !== undefined)
);

const getAttemptAnswer = (questionId) => {
    if (! props.latestAttempt?.answers) return null;
    return props.latestAttempt.answers.find(a => a.question_id === questionId) ?? null;
};

const submitQuiz = () => {
    attempted.value = true;
    if (! allAnswered.value) return;

    submitting.value = true;
    router.post(route('lms.quiz.submit', props.course.slug), { answers: answers.value }, {
        onFinish: () => { submitting.value = false; },
    });
};
</script>
