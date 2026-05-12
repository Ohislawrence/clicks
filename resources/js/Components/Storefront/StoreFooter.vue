<template>
    <!-- ============================================================
         MARKETPLACE FOOTER - Dark 3-column footer
         ============================================================ -->
    <footer v-if="themeSlug === 'marketplace'" :style="{ backgroundColor: colors.footer_bg || '#1C1C2E', color: '#e5e7eb' }">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <!-- Brand Column -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <img v-if="store.logo" :src="'/storage/' + store.logo" :alt="store.name"
                            class="h-10 w-10 object-contain rounded-lg" />
                        <div v-else class="h-10 w-10 rounded-lg flex items-center justify-center font-bold text-lg" :style="logoStyles">
                            {{ store.name.charAt(0).toUpperCase() }}
                        </div>
                        <span class="font-bold text-xl text-white">{{ store.name }}</span>
                    </div>
                    <p class="text-sm opacity-70 leading-relaxed">
                        {{ footerConfig.tagline || truncateDescription(store.description) || 'Your trusted online store.' }}
                    </p>
                    <!-- Social Icons -->
                    <div class="flex gap-3">
                        <a v-if="footerConfig.facebook" :href="footerConfig.facebook" target="_blank"
                            class="p-2 rounded-full bg-white/10 hover:bg-white/20 transition" title="Facebook">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                            </svg>
                        </a>
                        <a v-if="footerConfig.instagram" :href="footerConfig.instagram" target="_blank"
                            class="p-2 rounded-full bg-white/10 hover:bg-white/20 transition" title="Instagram">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" fill="none" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/>
                                <circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
                            </svg>
                        </a>
                        <a v-if="footerConfig.twitter" :href="footerConfig.twitter" target="_blank"
                            class="p-2 rounded-full bg-white/10 hover:bg-white/20 transition" title="Twitter / X">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                        <a v-if="store.whatsapp_number"
                            :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')" target="_blank"
                            class="p-2 rounded-full bg-white/10 hover:bg-white/20 transition text-green-400" title="WhatsApp">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Contact Column -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm uppercase tracking-widest mb-4">Contact Us</h4>
                    <a v-if="store.email" :href="'mailto:' + store.email" class="flex items-center gap-2 text-sm opacity-70 hover:opacity-100 transition">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ store.email }}
                    </a>
                    <a v-if="store.phone" :href="'tel:' + store.phone" class="flex items-center gap-2 text-sm opacity-70 hover:opacity-100 transition">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ store.phone }}
                    </a>
                    <p v-if="store.address" class="flex items-start gap-2 text-sm opacity-70">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ store.address }}
                    </p>
                </div>

                <!-- Trust Column -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm uppercase tracking-widest mb-4">Why Shop With Us</h4>
                    <div class="flex items-center gap-2 text-sm opacity-70">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Secure & Safe Checkout
                    </div>
                    <div class="flex items-center gap-2 text-sm opacity-70">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Quality Products
                    </div>
                    <div class="flex items-center gap-2 text-sm opacity-70">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Fast Delivery Available
                    </div>
                    <div class="flex items-center gap-2 text-sm opacity-70">
                        <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Customer Support
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-white/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-sm opacity-50">
                <p>&copy; {{ currentYear }} {{ store.name }}. All rights reserved.</p>
                <p>Powered by ClicksIntel Store Builder</p>
            </div>
        </div>
    </footer>

    <!-- ============================================================
         BOUTIQUE FOOTER - Elegant centered minimal footer
         ============================================================ -->
    <footer v-else-if="themeSlug === 'boutique'" class="mt-16 border-t"
        :style="{ backgroundColor: colors.footer_bg || colors.background || '#fafafa', borderColor: primaryColor + '20' }">
        <div class="container mx-auto px-6 py-12 text-center">
            <!-- Logo + Name -->
            <div class="flex flex-col items-center gap-3 mb-6">
                <img v-if="store.logo" :src="'/storage/' + store.logo" :alt="store.name"
                    class="h-12 w-12 object-contain rounded-full" />
                <div v-else class="h-12 w-12 rounded-full flex items-center justify-center font-bold text-xl" :style="logoStyles">
                    {{ store.name.charAt(0).toUpperCase() }}
                </div>
                <h3 class="font-serif font-bold text-xl tracking-[0.15em] uppercase" :style="{ color: colors.text || '#1F2937' }">{{ store.name }}</h3>
                <p class="text-sm opacity-60 max-w-sm" :style="{ color: colors.text || '#4B5563' }">
                    {{ footerConfig.tagline || truncateDescription(store.description) }}
                </p>
            </div>

            <!-- Social Icons -->
            <div class="flex justify-center gap-4 mb-8">
                <a v-if="footerConfig.facebook" :href="footerConfig.facebook" target="_blank"
                    class="p-2.5 rounded-full border transition hover:scale-110"
                    :style="{ borderColor: primaryColor + '40', color: primaryColor }">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                </a>
                <a v-if="footerConfig.instagram" :href="footerConfig.instagram" target="_blank"
                    class="p-2.5 rounded-full border transition hover:scale-110"
                    :style="{ borderColor: primaryColor + '40', color: primaryColor }">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
                    </svg>
                </a>
                <a v-if="footerConfig.twitter" :href="footerConfig.twitter" target="_blank"
                    class="p-2.5 rounded-full border transition hover:scale-110"
                    :style="{ borderColor: primaryColor + '40', color: primaryColor }">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a v-if="store.whatsapp_number" :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')" target="_blank"
                    class="p-2.5 rounded-full border transition hover:scale-110 text-green-600" :style="{ borderColor: '#22c55e40' }">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </a>
            </div>

            <!-- Contact row -->
            <div class="flex flex-wrap justify-center gap-6 text-sm opacity-60 mb-8" :style="{ color: colors.text || '#4B5563' }">
                <a v-if="store.email" :href="'mailto:' + store.email" class="hover:opacity-100 transition">{{ store.email }}</a>
                <a v-if="store.phone" :href="'tel:' + store.phone" class="hover:opacity-100 transition">{{ store.phone }}</a>
                <span v-if="store.address">{{ store.address }}</span>
            </div>

            <!-- Gradient rule -->
            <div class="h-px w-32 mx-auto mb-6" :style="{ background: `linear-gradient(to right, transparent, ${primaryColor}, transparent)` }"></div>
            <p class="text-xs opacity-40" :style="{ color: colors.text || '#9CA3AF' }">&copy; {{ currentYear }} {{ store.name }}. Powered by ClicksIntel.</p>
        </div>
    </footer>

    <!-- ============================================================
         MINIMAL / DEFAULT FOOTER - Clean simple footer
         ============================================================ -->
    <footer v-else class="border-t mt-12" :style="{ backgroundColor: colors.footer_bg || colors.background || '#fff', color: colors.text || '#1F2937', borderColor: primaryColor + '20' }">
        <div class="container mx-auto px-4 py-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- About -->
                <div class="space-y-3">
                    <h3 class="font-semibold text-lg">{{ store.name }}</h3>
                    <p class="text-sm opacity-70 max-w-md leading-relaxed">
                        {{ footerConfig.tagline || truncateDescription(store.description) }}
                    </p>
                    <!-- Social -->
                    <div class="flex gap-2 pt-1">
                        <a v-if="footerConfig.facebook" :href="footerConfig.facebook" target="_blank"
                            class="p-2 rounded-lg hover:bg-black/5 transition" :style="{ color: primaryColor }">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                        </a>
                        <a v-if="footerConfig.instagram" :href="footerConfig.instagram" target="_blank"
                            class="p-2 rounded-lg hover:bg-black/5 transition" :style="{ color: primaryColor }">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
                            </svg>
                        </a>
                        <a v-if="footerConfig.twitter" :href="footerConfig.twitter" target="_blank"
                            class="p-2 rounded-lg hover:bg-black/5 transition" :style="{ color: primaryColor }">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <a v-if="store.whatsapp_number" :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')" target="_blank"
                            class="p-2 rounded-lg hover:bg-black/5 transition text-green-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Contact -->
                <div class="space-y-3">
                    <h4 class="font-semibold">Contact Us</h4>
                    <div class="space-y-2 text-sm">
                        <a v-if="store.email" :href="'mailto:' + store.email" class="flex items-center gap-2 opacity-70 hover:opacity-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ store.email }}
                        </a>
                        <a v-if="store.phone" :href="'tel:' + store.phone" class="flex items-center gap-2 opacity-70 hover:opacity-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ store.phone }}
                        </a>
                        <a v-if="store.whatsapp_number" :href="'https://wa.me/' + store.whatsapp_number.replace(/[^0-9]/g, '')" target="_blank" class="flex items-center gap-2 opacity-70 hover:opacity-100 transition">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            WhatsApp Us
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t text-center text-sm opacity-50" :style="{ borderColor: primaryColor + '20' }">
                <p>&copy; {{ currentYear }} {{ store.name }}. All rights reserved. &middot; Powered by ClicksIntel</p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    store: { type: Object, required: true },
});

const themeSlug = computed(() => props.store.theme?.slug || 'minimal');
const customization = computed(() => props.store.theme_customization || {});
const colors = computed(() => customization.value.colors || {});
const primaryColor = computed(() => colors.value.primary || '#3B82F6');
const secondaryColor = computed(() => colors.value.secondary || primaryColor.value);
const footerConfig = computed(() => customization.value.footer || {});
const currentYear = new Date().getFullYear();

const logoStyles = computed(() => ({
    background: `linear-gradient(135deg, ${primaryColor.value}, ${secondaryColor.value})`,
    color: '#fff',
}));

const truncateDescription = (text) => {
    if (!text) return '';
    const stripped = text.replace(/<[^>]*>/g, '');
    return stripped.length > 150 ? stripped.substring(0, 150) + '...' : stripped;
};
</script>
