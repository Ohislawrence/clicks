<script setup>
import { nextTick, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputError from '@/Components/InputError.vue';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <Head title="Two-factor Confirmation" />

    <AuthLayout title="Two-Factor Authentication">
        <div class="mb-6 text-sm text-slate-500">
            <template v-if="! recovery">
                Please confirm access to your account by entering the authentication code provided by your authenticator application.
            </template>

            <template v-else>
                Please confirm access to your account by entering one of your emergency recovery codes.
            </template>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div v-if="! recovery">
                <label for="code" class="block text-sm font-medium text-slate-700">
                    Authentication Code
                </label>
                <div class="mt-2">
                    <input
                        id="code"
                        ref="codeInput"
                        v-model="form.code"
                        type="text"
                        inputmode="numeric"
                        autofocus
                        autocomplete="one-time-code"
                        class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6 text-center tracking-widest"
                        placeholder="000000"
                    />
                    <InputError class="mt-2" :message="form.errors.code" />
                </div>
            </div>

            <div v-else>
                <label for="recovery_code" class="block text-sm font-medium text-slate-700">
                    Recovery Code
                </label>
                <div class="mt-2">
                    <input
                        id="recovery_code"
                        ref="recoveryCodeInput"
                        v-model="form.recovery_code"
                        type="text"
                        autocomplete="one-time-code"
                        class="block w-full rounded-lg border-0 bg-white px-4 py-3 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-900 sm:text-sm sm:leading-6"
                        placeholder="Enter recovery code"
                    />
                    <InputError class="mt-2" :message="form.errors.recovery_code" />
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full justify-center rounded-lg bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-900 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                    <span v-if="form.processing">Verifying...</span>
                    <span v-else>Log in</span>
                </button>
            </div>

            <div class="text-center">
                <button
                    type="button"
                    class="text-sm text-slate-700 hover:text-slate-900 transition-colors"
                    @click.prevent="toggleRecovery"
                >
                    <template v-if="! recovery">
                        Use a recovery code
                    </template>
                    <template v-else>
                        Use an authentication code
                    </template>
                </button>
            </div>
        </form>
    </AuthLayout>
</template>
