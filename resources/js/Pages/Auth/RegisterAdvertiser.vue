<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

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

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Advertiser Registration</h2>
            <p class="mt-1 text-sm text-gray-600">Create offers and work with affiliates to grow your business</p>
        </div>

        <form @submit.prevent="submit">
            <!-- Personal Information -->
            <div class="space-y-4">
                <div>
                    <InputLabel for="name" value="Your Full Name *" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autofocus
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div>
                    <InputLabel for="email" value="Email Address *" />
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="mt-1 block w-full"
                        required
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="password" value="Password *" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password *" />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            required
                            autocomplete="new-password"
                        />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>
                </div>

                <div>
                    <InputLabel for="phone" value="Phone Number" />
                    <TextInput
                        id="phone"
                        v-model="form.phone"
                        type="tel"
                        class="mt-1 block w-full"
                        placeholder="+1 234 567 8900"
                    />
                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>
            </div>

            <!-- Company Information -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h3>

                <div class="space-y-4">
                    <div>
                        <InputLabel for="company_name" value="Company Name *" />
                        <TextInput
                            id="company_name"
                            v-model="form.company_name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            placeholder="Your Company LLC"
                        />
                        <InputError class="mt-2" :message="form.errors.company_name" />
                    </div>

                    <div>
                        <InputLabel for="website" value="Company Website" />
                        <TextInput
                            id="website"
                            v-model="form.website"
                            type="url"
                            class="mt-1 block w-full"
                            placeholder="https://yourcompany.com"
                        />
                        <InputError class="mt-2" :message="form.errors.website" />
                    </div>

                    <div>
                        <InputLabel for="country" value="Country *" />
                        <TextInput
                            id="country"
                            v-model="form.country"
                            type="text"
                            class="mt-1 block w-full"
                            required
                            placeholder="e.g., United States"
                        />
                        <InputError class="mt-2" :message="form.errors.country" />
                    </div>

                    <div>
                        <InputLabel for="bio" value="About Your Business" />
                        <textarea
                            id="bio"
                            v-model="form.bio"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                            rows="4"
                            placeholder="Tell us about your business, products/services, and what you're looking to promote..."
                        ></textarea>
                        <p class="mt-1 text-xs text-gray-500">This will help affiliates understand your brand and offerings</p>
                        <InputError class="mt-2" :message="form.errors.bio" />
                    </div>
                </div>
            </div>

            <!-- Information Notice -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">What happens next?</h3>
                        <div class="mt-2 text-sm text-blue-700">
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
            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-6">
                <InputLabel for="terms">
                    <div class="flex items-center">
                        <Checkbox id="terms" v-model:checked="form.terms" name="terms" required />

                        <div class="ms-2">
                            I agree to the <a target="_blank" :href="route('terms.show')" class="underline text-sm text-indigo-600 hover:text-indigo-900">Terms of Service</a> and <a target="_blank" :href="route('policy.show')" class="underline text-sm text-indigo-600 hover:text-indigo-900">Privacy Policy</a>
                        </div>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </InputLabel>
            </div>

            <div class="flex items-center justify-between mt-6">
                <Link :href="route('register')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                    ← Back to account type
                </Link>

                <div class="flex items-center space-x-4">
                    <Link :href="route('login')" class="text-sm text-gray-600 hover:text-gray-900 underline">
                        Already registered?
                    </Link>

                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create Advertiser Account
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </AuthenticationCard>
</template>
