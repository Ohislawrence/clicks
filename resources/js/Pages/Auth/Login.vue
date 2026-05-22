<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <AuthLayout title="Welcome back">
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
                <label for="password" class="block text-sm font-medium text-slate-700">
                    Password
                </label>
                <div class="mt-2">
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" class="rounded border-slate-300 bg-white text-slate-900 focus:ring-slate-900 focus:ring-offset-white" />
                    <span class="ms-2 text-sm text-slate-500">Remember me</span>
                </label>

                <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm font-medium text-slate-700 hover:text-slate-900 transition-colors">
                    Forgot password?
                </Link>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="form.processing">Signing in...</span>
                    <span v-else>Sign in</span>
                </button>
            </div>
        </form>

        <template #footer>
            <p class="text-center text-sm text-slate-500">
                Don't have an account?
                <Link :href="route('register')" class="font-semibold text-slate-900 hover:text-slate-700 transition-colors">
                    Sign up
                </Link>
            </p>
        </template>
    </AuthLayout>
</template>
