<template>
    <div class="min-h-screen" :style="layoutStyles">
        <!-- Header -->
        <StoreHeader :store="store" :cart-count="cartCount" @toggle-cart="$emit('toggleCart')" />

        <!-- Main Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <StoreFooter :store="store" />

        <!-- WhatsApp Floating Button -->
        <a
            v-if="whatsappHref"
            :href="whatsappHref"
            target="_blank"
            rel="noopener noreferrer"
            class="fixed bottom-6 right-6 z-50 flex items-center justify-center w-14 h-14 rounded-full shadow-lg transition-transform hover:scale-110 focus:outline-none"
            style="background-color: #25D366;"
            aria-label="Chat on WhatsApp"
        >
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.083.533 4.044 1.472 5.754L0 24l6.414-1.433A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.652-.49-5.184-1.349l-.371-.22-3.851.86.876-3.751-.242-.387A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
            </svg>
        </a>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import StoreHeader from './StoreHeader.vue';
import StoreFooter from './StoreFooter.vue';

const props = defineProps({
    store: {
        type: Object,
        required: true,
    },
    cartCount: {
        type: Number,
        default: 0,
    },
});

defineEmits(['toggleCart']);

// Apply theme customization
const layoutStyles = computed(() => {
    const customization = props.store.theme_customization || {};
    const colors = customization.colors || {};

    return {
        backgroundColor: colors.background || '#FFFFFF',
        color: colors.text || '#1F2937',
        fontFamily: customization.typography?.fontFamily || 'Inter, sans-serif',
    };
});

// Build WhatsApp link from store's whatsapp_number
const whatsappHref = computed(() => {
    const raw = props.store.whatsapp_number;
    if (!raw) return null;
    const digits = raw.replace(/\D/g, '');
    if (!digits) return null;
    return `https://wa.me/${digits}`;
});
</script>
