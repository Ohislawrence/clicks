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
    phone: '',
    company_name: '',
    website: '',
    country: '',
    bio: '',
    terms: false,
    role: 'advertiser',
});

const submit = () => {
    form.post(route('register.advertiser.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register as Advertiser" />

    <AuthLayout title="Advertiser Registration">
        <p class="text-center text-neutral-400 mb-8 text-sm">
            Create offers and work with affiliates to grow your business
        </p>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Personal Information -->
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-300">
                        Your Full Name *
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
                        Email Address *
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-neutral-300">
                            Password *
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
                            Confirm Password *
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
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-neutral-300">
                        Phone Number
                    </label>
                    <div class="mt-2">
                        <input
                            id="phone"
                            v-model="form.phone"
                            type="tel"
                            class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                            placeholder="+1 234 567 8900"
                        />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            <div class="pt-6 border-t border-neutral-700">
                <h3 class="text-base font-semibold text-white mb-4">Company Information</h3>

                <div class="space-y-4">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-neutral-300">
                            Company Name *
                        </label>
                        <div class="mt-2">
                            <input
                                id="company_name"
                                v-model="form.company_name"
                                type="text"
                                required
                                class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                                placeholder="Your Company LLC"
                            />
                            <InputError class="mt-2" :message="form.errors.company_name" />
                        </div>
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-medium text-neutral-300">
                            Company Website
                        </label>
                        <div class="mt-2">
                            <input
                                id="website"
                                v-model="form.website"
                                type="url"
                                class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                                placeholder="https://yourcompany.com"
                            />
                            <InputError class="mt-2" :message="form.errors.website" />
                        </div>
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-neutral-300">
                            Country *
                        </label>
                        <div class="mt-2">
                            <input
                                id="country"
                                v-model="form.country"
                                type="text"
                                required
                                class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                                placeholder="e.g., United States"
                            />
                            <InputError class="mt-2" :message="form.errors.country" />
                        </div>
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-neutral-300">
                            About Your Business
                        </label>
                        <div class="mt-2">
                            <textarea
                                id="bio"
                                v-model="form.bio"
                                rows="4"
                                class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6 resize-none"
                                placeholder="Tell us about your business, products/services, and what you're looking to promote..."
                            ></textarea>
                            <p class="mt-1 text-xs text-neutral-500">This will help affiliates understand your brand and offerings</p>
                            <InputError class="mt-2" :message="form.errors.bio" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Notice -->
            <div class="p-4 bg-blue-500/10 border border-blue-500/30 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-400">What happens next?</h3>
                        <div class="mt-2 text-sm text-neutral-400">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Your account will be reviewed by our team</li>
                                <li>You'll receive an email once approved (usually within 24-48 hours)</li>
                                <li>Once approved, you can create offers and start working with affiliates</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
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

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <Link :href="route('register')" class="text-sm text-neutral-400 hover:text-white transition-colors">
                    ← Back to account type
                </Link>

                <div class="flex items-center gap-4">
                    <Link :href="route('login')" class="text-sm text-neutral-400 hover:text-white transition-colors">
                        Already registered?
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex justify-center rounded-lg bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <span v-if="form.processing">Creating account...</span>
                        <span v-else>Create Advertiser Account</span>
                    </button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
