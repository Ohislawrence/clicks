<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <AuthLayout title="Reset your password">
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
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-700">
                    New Password
                </label>
                <div class="mt-2">
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700">
                    Confirm Password
                </label>
                <div class="mt-2">
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="form.processing">Resetting...</span>
                    <span v-else>Reset Password</span>
                </button>
            </div>
        </form>
    </AuthLayout>
</template>
