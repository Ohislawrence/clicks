<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthLayout from '@/Layouts/AuthLayout.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    bio: '',
    country: '',
    traffic_sources: [
        { type: 'instagram', name: '', url: '', followers: '' }
    ],
    terms: false,
    role: 'affiliate',
});

const trafficSourceTypes = [
    { value: 'instagram', label: 'Instagram', icon: '📷' },
    { value: 'tiktok', label: 'TikTok', icon: '🎵' },
    { value: 'youtube', label: 'YouTube', icon: '▶️' },
    { value: 'twitter', label: 'Twitter/X', icon: '🐦' },
    { value: 'facebook', label: 'Facebook', icon: '👥' },
    { value: 'website', label: 'Website', icon: '🌐' },
    { value: 'blog', label: 'Blog', icon: '📝' },
    { value: 'other', label: 'Other', icon: '🔗' },
];

const addTrafficSource = () => {
    form.traffic_sources.push({ type: 'instagram', name: '', url: '', followers: '' });
};

const removeTrafficSource = (index) => {
    if (form.traffic_sources.length > 1) {
        form.traffic_sources.splice(index, 1);
    }
};

const submit = () => {
    form.post(route('register.affiliate.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Register as Affiliate" />

    <AuthLayout title="Affiliate Registration">
        <p class="text-center text-neutral-400 mb-8 text-sm">
            Join our network and start earning commissions
        </p>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Basic Information -->
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-300">
                        Full Name *
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
                        Tell us about yourself
                    </label>
                    <div class="mt-2">
                        <textarea
                            id="bio"
                            v-model="form.bio"
                            rows="3"
                            class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6 resize-none"
                            placeholder="Brief description of your experience and niche..."
                        ></textarea>
                        <InputError class="mt-2" :message="form.errors.bio" />
                    </div>
                </div>
            </div>

            <!-- Traffic Sources Section -->
            <div class="pt-6 border-t border-neutral-700">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-base font-semibold text-white">Your Traffic Sources *</h3>
                        <p class="text-sm text-neutral-400">Add your social media accounts, websites, or blogs</p>
                    </div>
                    <button
                        type="button"
                        @click="addTrafficSource"
                        class="inline-flex items-center px-3 py-2 border border-emerald-600 text-sm font-medium rounded-lg text-emerald-400 bg-emerald-500/10 hover:bg-emerald-500/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors"
                    >
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Source
                    </button>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="(source, index) in form.traffic_sources"
                        :key="index"
                        class="p-4 bg-neutral-800/30 rounded-lg border border-neutral-700 relative"
                    >
                        <!-- Remove Button -->
                        <button
                            v-if="form.traffic_sources.length > 1"
                            type="button"
                            @click="removeTrafficSource(index)"
                            class="absolute top-2 right-2 text-red-400 hover:text-red-300 transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="space-y-3">
                            <div>
                                <label :for="`source_type_${index}`" class="block text-sm font-medium text-neutral-300">
                                    Platform Type
                                </label>
                                <div class="mt-2">
                                    <select
                                        :id="`source_type_${index}`"
                                        v-model="source.type"
                                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                                    >
                                        <option v-for="type in trafficSourceTypes" :key="type.value" :value="type.value">
                                            {{ type.icon }} {{ type.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label :for="`source_name_${index}`" class="block text-sm font-medium text-neutral-300">
                                    {{ source.type === 'website' || source.type === 'blog' ? 'Website/Blog Name' : 'Account Name' }}
                                </label>
                                <div class="mt-2">
                                    <input
                                        :id="`source_name_${index}`"
                                        v-model="source.name"
                                        type="text"
                                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                                        :placeholder="source.type === 'website' ? 'My Awesome Blog' : '@yourusername'"
                                    />
                                    <InputError class="mt-2" :message="form.errors[`traffic_sources.${index}.name`]" />
                                </div>
                            </div>

                            <div>
                                <label :for="`source_url_${index}`" class="block text-sm font-medium text-neutral-300">
                                    URL/Link
                                </label>
                                <div class="mt-2">
                                    <input
                                        :id="`source_url_${index}`"
                                        v-model="source.url"
                                        type="url"
                                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                                        :placeholder="source.type === 'instagram' ? 'https://instagram.com/yourusername' : 'https://yourwebsite.com'"
                                    />
                                    <InputError class="mt-2" :message="form.errors[`traffic_sources.${index}.url`]" />
                                </div>
                            </div>

                            <div>
                                <label :for="`source_followers_${index}`" class="block text-sm font-medium text-neutral-300">
                                    {{ source.type === 'website' || source.type === 'blog' ? 'Monthly Visitors (optional)' : 'Followers/Subscribers (optional)' }}
                                </label>
                                <div class="mt-2">
                                    <input
                                        :id="`source_followers_${index}`"
                                        v-model="source.followers"
                                        type="number"
                                        class="block w-full rounded-lg border-0 bg-neutral-800/50 px-4 py-3 text-white shadow-sm ring-1 ring-inset ring-neutral-700 placeholder:text-neutral-500 focus:ring-2 focus:ring-inset focus:ring-emerald-500 sm:text-sm sm:leading-6"
                                        placeholder="e.g., 10000"
                                    />
                                    <InputError class="mt-2" :message="form.errors[`traffic_sources.${index}.followers`]" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <InputError class="mt-2" :message="form.errors.traffic_sources" />
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
                        <span v-else>Create Affiliate Account</span>
                    </button>
                </div>
            </div>
        </form>
    </AuthLayout>
</template>
