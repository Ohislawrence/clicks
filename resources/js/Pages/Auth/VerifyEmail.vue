<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <Head title="Email Verification" />

    <AuthLayout title="Verify your email">
        <div class="mb-6 text-sm text-neutral-400">
            Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we'll gladly send you another.
        </div>

        <div v-if="verificationLinkSent" class="mb-6 rounded-lg bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 text-sm text-emerald-400">
            A new verification link has been sent to your email address.
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full justify-center rounded-lg bg-emerald-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="form.processing">Sending...</span>
                    <span v-else>Resend Verification Email</span>
                </button>
            </div>

            <div class="flex items-center justify-between text-sm">
                <Link
                    :href="route('profile.show')"
                    class="font-medium text-neutral-400 hover:text-white transition-colors"
                >
                    Edit Profile
                </Link>

            <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="font-medium text-red-400 hover:text-red-300 transition-colors"
                >
                    Log Out
                </Link>
            </div>
        </form>
    </AuthLayout>
</template>
