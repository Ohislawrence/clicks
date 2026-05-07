<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    password: '',
});

const passwordInput = ref(null);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => {
            form.reset();

            passwordInput.value.focus();
        },
    });
};
</script>

<template>
    <Head title="Secure Area" />

    <AuthLayout title="Confirm Password">
        <div class="mb-6 text-sm text-neutral-400">
            This is a secure area of the application. Please confirm your password before continuing.
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <label for="password" class="block text-sm font-medium text-neutral-300">
                    Password
                </label>
                <div class="mt-2">
                    <input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                        placeholder="••••••••"
                    autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full justify-center rounded-lg bg-emerald-500 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="form.processing">Confirming...</span>
                    <span v-else>Confirm</span>
                </button>
            </div>
        </form>
    </AuthLayout>
</template>
