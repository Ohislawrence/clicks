<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
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

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Affiliate Registration</h2>
            <p class="mt-1 text-sm text-gray-600">Join our network and start earning commissions</p>
        </div>

        <form @submit.prevent="submit">
            <!-- Basic Information -->
            <div class="space-y-4">
                <div>
                    <InputLabel for="name" value="Full Name *" />
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
                    <InputLabel for="bio" value="Tell us about yourself" />
                    <textarea
                        id="bio"
                        v-model="form.bio"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                        rows="3"
                        placeholder="Brief description of your experience and niche..."
                    ></textarea>
                    <InputError class="mt-2" :message="form.errors.bio" />
                </div>
            </div>

            <!-- Traffic Sources Section -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Your Traffic Sources *</h3>
                        <p class="text-sm text-gray-600">Add your social media accounts, websites, or blogs</p>
                    </div>
                    <button
                        type="button"
                        @click="addTrafficSource"
                        class="inline-flex items-center px-3 py-2 border border-indigo-300 text-sm font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
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
                        class="p-4 bg-gray-50 rounded-lg border border-gray-200 relative"
                    >
                        <!-- Remove Button -->
                        <button
                            v-if="form.traffic_sources.length > 1"
                            type="button"
                            @click="removeTrafficSource(index)"
                            class="absolute top-2 right-2 text-red-600 hover:text-red-800"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <div class="space-y-3">
                            <div>
                                <InputLabel :for="`source_type_${index}`" value="Platform Type" />
                                <select
                                    :id="`source_type_${index}`"
                                    v-model="source.type"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                >
                                    <option v-for="type in trafficSourceTypes" :key="type.value" :value="type.value">
                                        {{ type.icon }} {{ type.label }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <InputLabel :for="`source_name_${index}`" :value="source.type === 'website' || source.type === 'blog' ? 'Website/Blog Name' : 'Account Name'" />
                                <TextInput
                                    :id="`source_name_${index}`"
                                    v-model="source.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :placeholder="source.type === 'website' ? 'My Awesome Blog' : '@yourusername'"
                                />
                                <InputError class="mt-2" :message="form.errors[`traffic_sources.${index}.name`]" />
                            </div>

                            <div>
                                <InputLabel :for="`source_url_${index}`" value="URL/Link" />
                                <TextInput
                                    :id="`source_url_${index}`"
                                    v-model="source.url"
                                    type="url"
                                    class="mt-1 block w-full"
                                    :placeholder="source.type === 'instagram' ? 'https://instagram.com/yourusername' : 'https://yourwebsite.com'"
                                />
                                <InputError class="mt-2" :message="form.errors[`traffic_sources.${index}.url`]" />
                            </div>

                            <div>
                                <InputLabel
                                    :for="`source_followers_${index}`"
                                    :value="source.type === 'website' || source.type === 'blog' ? 'Monthly Visitors (optional)' : 'Followers/Subscribers (optional)'"
                                />
                                <TextInput
                                    :id="`source_followers_${index}`"
                                    v-model="source.followers"
                                    type="number"
                                    class="mt-1 block w-full"
                                    placeholder="e.g., 10000"
                                />
                                <InputError class="mt-2" :message="form.errors[`traffic_sources.${index}.followers`]" />
                            </div>
                        </div>
                    </div>
                </div>

                <InputError class="mt-2" :message="form.errors.traffic_sources" />
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
                        Create Affiliate Account
                    </PrimaryButton>
                </div>
            </div>
        </form>
    </AuthenticationCard>
</template>
