<script setup>
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    account_name: props.user.payment_details?.account_name || '',
    account_number: props.user.payment_details?.account_number || '',
    bank_name: props.user.payment_details?.bank_name || '',
    bank_code: props.user.payment_details?.bank_code || '',
});

const updatePaymentDetails = () => {
    form.put(route('user.payment-details.update'), {
        errorBag: 'updatePaymentDetails',
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection @submitted="updatePaymentDetails">
        <template #title>
            Payment Details
        </template>

        <template #description>
            Update your bank account information for receiving payouts. This information will be used when you request withdrawals.
        </template>

        <template #form>
            <!-- Account Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="account_name" value="Account Name" />
                <TextInput
                    id="account_name"
                    v-model="form.account_name"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="John Doe"
                />
                <InputError :message="form.errors.account_name" class="mt-2" />
                <p class="mt-1 text-xs text-gray-500">
                    Account name as it appears on your bank account
                </p>
            </div>

            <!-- Account Number -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="account_number" value="Account Number" />
                <TextInput
                    id="account_number"
                    v-model="form.account_number"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="0123456789"
                    maxlength="10"
                />
                <InputError :message="form.errors.account_number" class="mt-2" />
            </div>

            <!-- Bank Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="bank_name" value="Bank Name" />
                <TextInput
                    id="bank_name"
                    v-model="form.bank_name"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="Access Bank, GTBank, First Bank, etc."
                />
                <InputError :message="form.errors.bank_name" class="mt-2" />
            </div>

            <!-- Bank Code (Optional) -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="bank_code" value="Bank Code (Optional)" />
                <TextInput
                    id="bank_code"
                    v-model="form.bank_code"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="044, 058, 011, etc."
                />
                <InputError :message="form.errors.bank_code" class="mt-2" />
                <p class="mt-1 text-xs text-gray-500">
                    Required for Paystack/Flutterwave payouts. Optional for manual bank transfers.
                </p>
            </div>

            <!-- Info Note -->
            <div class="col-span-6 sm:col-span-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                <strong>Why we need this:</strong> Your bank account details are required to process payouts.
                                We use secure payment gateways (Paystack/Flutterwave) to send funds directly to your account.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Saved.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Save Payment Details
            </PrimaryButton>
        </template>
    </FormSection>
</template>
