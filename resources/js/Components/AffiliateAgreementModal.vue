<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-gray-900/70 backdrop-blur-sm"></div>

                <!-- Modal -->
                <Transition
                    enter-active-class="transition ease-out duration-300"
                    enter-from-class="opacity-0 scale-95 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                    leave-active-class="transition ease-in duration-200"
                    leave-from-class="opacity-100 scale-100 translate-y-0"
                    leave-to-class="opacity-0 scale-95 translate-y-4"
                >
                    <div v-if="show" class="relative w-full max-w-lg max-h-[calc(100vh-3rem)] bg-white rounded-2xl shadow-2xl overflow-y-auto">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-white">Affiliate Traffic Agreement</h2>
                                    <p class="text-blue-200 text-sm">Please review and agree before continuing</p>
                                </div>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="px-6 py-5">
                            <p class="text-sm text-gray-600 leading-relaxed mb-5">
                                We are committed to delivering authentic traffic from genuine users to our advertisers.
                                To ensure this, we have implemented over <strong class="text-gray-800">20 advanced fraud detection measures</strong>
                                that help us identify and eliminate fraudulent traffic, preventing dishonest users from receiving payments.
                                These safeguards are designed to reward only those who generate legitimate traffic.
                                To continue using the platform, please review and agree to our terms:
                            </p>

                            <!-- Agreement items -->
                            <ul class="space-y-3 mb-6">
                                <li
                                    v-for="(item, index) in agreementItems"
                                    :key="index"
                                    class="flex items-start gap-3 p-3 rounded-xl bg-gray-50 border border-gray-100"
                                >
                                    <div class="mt-0.5 w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                        <svg class="w-3 h-3 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-gray-700 leading-snug" v-html="item"></span>
                                </li>
                            </ul>

                            <!-- Checkbox -->
                            <label class="flex items-start gap-3 cursor-pointer select-none group mb-5">
                                <div class="relative mt-0.5 shrink-0">
                                    <input
                                        type="checkbox"
                                        v-model="agreed"
                                        class="sr-only peer"
                                    />
                                    <div class="w-5 h-5 rounded border-2 border-gray-300 peer-checked:border-blue-600 peer-checked:bg-blue-600 transition-colors flex items-center justify-center">
                                        <svg v-if="agreed" class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-600 group-hover:text-gray-800 transition-colors">
                                    I have read and agree to all of the above terms. I understand that violation of these terms
                                    may result in account suspension or permanent ban.
                                </span>
                            </label>

                            <!-- Action button -->
                            <button
                                @click="submit"
                                :disabled="!agreed || loading"
                                class="w-full py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2"
                                :class="agreed && !loading
                                    ? 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm shadow-blue-200'
                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                            >
                                <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                {{ loading ? 'Saving...' : 'I Agree — Continue to Dashboard' }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const agreed = ref(false);
const loading = ref(false);

const agreementItems = [
    'I agree to only send <strong>real traffic</strong> to offers on this platform.',
    'I will <strong>NOT</strong> attempt to complete my own offers, as this results in an <strong>immediate ban</strong>.',
    'I am aware that I <strong>cannot give people cash</strong> to complete offers on this platform.',
    'I am aware the platform can easily identify fraudulent traffic using <strong>over 20 types of data points</strong>.',
    'I agree to the platform\'s <strong>terms of service</strong> and <strong>privacy policy</strong>.',
];

const submit = () => {
    if (!agreed.value || loading.value) return;

    loading.value = true;

    router.post(route('affiliate.agree-to-terms'), {}, {
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>
