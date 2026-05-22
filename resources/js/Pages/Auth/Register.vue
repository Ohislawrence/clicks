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
                <label for="name" class="block text-sm font-medium text-slate-700">
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
                        class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6"
                        placeholder="John Doe"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
            </div>

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

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature">
                <label class="flex items-start">
                    <Checkbox id="terms" v-model:checked="form.terms" name="terms" required class="mt-0.5 rounded border-slate-300 bg-white text-slate-900 focus:ring-slate-900 focus:ring-offset-white" />
                    <span class="ms-2 text-sm text-slate-500">
                        I agree to the
                        <a target="_blank" :href="route('terms.show')" class="text-slate-700 underline hover:text-slate-900 transition-colors">Terms of Service</a>
                        and
                        <a target="_blank" :href="route('policy.show')" class="text-slate-700 underline hover:text-slate-900 transition-colors">Privacy Policy</a>
                    </span>
                </label>
                <InputError class="mt-2" :message="form.errors.terms" />
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="form.processing">Creating account...</span>
                    <span v-else>Register</span>
                </button>
            </div>
        </form>

        <template #footer>
            <p class="text-center text-sm text-slate-500">
                Already registered?
                <Link :href="route('login')" class="font-semibold text-slate-900 hover:text-slate-700 transition-colors">
                    Sign in
                </Link>
            </p>
        </template>
    </AuthLayout>
</template>
