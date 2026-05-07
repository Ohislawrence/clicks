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
        <div v-if="status" class="mb-4 rounded-lg bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 text-sm text-emerald-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-neutral-300">
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
                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                        placeholder="you@example.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-300">
                    Password
                </label>
                <div class="mt-2">
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" class="rounded border-neutral-700 bg-neutral-800/50 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-neutral-900" />
                    <span class="ms-2 text-sm text-neutral-400">Remember me</span>
                </label>

                <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm font-medium text-emerald-500 hover:text-emerald-400 transition-colors">
                    Forgot password?
                </Link>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full justify-center rounded-lg bg-emerald-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="form.processing">Signing in...</span>
                    <span v-else>Sign in</span>
                </button>
            </div>
        </form>

        <template #footer>
            <p class="text-center text-sm text-neutral-400">
                Don't have an account?
                <Link :href="route('register')" class="font-semibold text-emerald-500 hover:text-emerald-400 transition-colors">
                    Sign up
                </Link>
            </p>
        </template>
    </AuthLayout>
</template>
