<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register" />

    <AuthLayout title="Create Account">
        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-neutral-300">
                    Full Name
                </label>
                <div class="mt-2">
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                        placeholder="John Doe"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
            </div>

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
                        autocomplete="new-password"
                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-neutral-300">
                    Confirm Password
                </label>
                <div class="mt-2">
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        autocomplete="new-password"
                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                        placeholder="••••••••"
                    />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                <label class="flex items-start">
                    <Checkbox id="terms" v-model:checked="form.terms" name="terms" required class="mt-0.5 rounded border-neutral-700 bg-neutral-800/50 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-neutral-900" />
                    <span class="ms-2 text-sm text-neutral-400">
                        I agree to the
                        <a target="_blank" :href="route('terms.show')" class="text-emerald-500 hover:text-emerald-400 transition-colors">Terms of Service</a>
                        and
                        <a target="_blank" :href="route('policy.show')" class="text-emerald-500 hover:text-emerald-400 transition-colors">Privacy Policy</a>
                    </span>
                </label>
                <InputError class="mt-2" :message="form.errors.terms" />
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full justify-center rounded-lg bg-emerald-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="form.processing">Creating account...</span>
                    <span v-else>Register</span>
                </button>
            </div>
        </form>

        <template #footer>
            <p class="text-center text-sm text-neutral-400">
                Already registered?
                <Link :href="route('login')" class="font-semibold text-emerald-500 hover:text-emerald-400 transition-colors">
                    Sign in
                </Link>
            </p>
        </template>
    </AuthLayout>
</template>
