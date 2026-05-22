<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Forgot Password" />

    <AuthLayout title="Forgot password?">
        <div class="mb-6 text-sm text-slate-500">
            No problem. Just let us know your email address and we'll email you a password reset link.
        </div>

        <div v-if="status" class="mb-4 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">
                    Email address
                </label>
                <div class="mt-2">
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6"
                        placeholder="you@example.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="form.processing">Sending...</span>
                    <span v-else>Email Password Reset Link</span>
                </button>
            </div>
        </form>

        <template #footer>
            <p class="text-center text-sm text-slate-500">
                <Link :href="route('login')" class="font-semibold text-slate-900 hover:text-slate-700 transition-colors">
                    Back to login
                </Link>
            </p>
        </template>
    </AuthLayout>
</template>
