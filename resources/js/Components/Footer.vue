<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import NewsletterSignup from '@/Components/NewsletterSignup.vue'

const page = usePage()

const siteSettings = computed(() => page.props.siteSettings || {})
const companyName = computed(
    () => siteSettings.value.company_name || 'Heritage Capital Markets Limited'
)
const phone = computed(() => siteSettings.value.phone || '')
const email = computed(() => siteSettings.value.email || '')
const address = computed(() => siteSettings.value.address || '')
const abaAddress = computed(
    () => siteSettings.value.address_aba || '45 Aba Road, Opp. First Bank,\nAba, Abia State.'
)
const footerText = computed(
    () => siteSettings.value.footer_text ||
        'Building wealth and creating value for our clients through trusted investment solutions.'
)
const phoneHref = computed(() => {
    const value = phone.value.trim()

    if (!/^\+?[\d\s().-]+$/.test(value)) {
        return ''
    }

    const digits = value.replace(/\D/g, '')

    if (digits.length < 7 || digits.length > 15) {
        return ''
    }

    return `tel:${value.startsWith('+') ? '+' : ''}${digits}`
})

function safeHttpUrl(value) {
    if (!value) {
        return ''
    }

    try {
        const url = new URL(value)

        return ['http:', 'https:'].includes(url.protocol) && !url.username && !url.password
            ? url.href
            : ''
    } catch {
        return ''
    }
}

const socialLinks = computed(() => ({
    linkedin: safeHttpUrl(siteSettings.value.linkedin),
    facebook: safeHttpUrl(siteSettings.value.facebook),
    instagram: safeHttpUrl(siteSettings.value.instagram),
}))
</script>

<template>
    <footer class="bg-heritage-deep text-white">

        <NewsletterSignup />

        <!-- CTA -->
        <section class="border-b border-white/10 bg-heritage-navy">

            <div class="mx-auto flex max-w-350 flex-col gap-6 px-5 py-8 lg:flex-row lg:items-center lg:justify-between lg:px-8">

                <div>
                    <h2 class="font-display text-2xl lg:text-3xl">
                        Let’s Build Your Financial Future
                    </h2>

                    <p class="mt-2 text-sm text-white/70">
                        Speak with our team and discover how Heritage can help you achieve your goals.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">

                    <a
                        href="/contact-us"
                        class="rounded bg-heritage-gold px-6 py-3 text-sm font-semibold text-heritage-navy hover:bg-heritage-gold-light"
                    >
                        Request a Call Back
                        <span class="ml-2">→</span>
                    </a>

                    <a
                        href="/contact-us"
                        class="rounded border border-heritage-gold px-6 py-3 text-sm font-semibold hover:bg-white/5"
                    >
                        Contact Us
                    </a>

                </div>

            </div>

        </section>

        <!-- Main Footer -->
        <div class="mx-auto max-w-350 px-5 py-12 lg:px-8">

            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-6">

                <!-- Brand -->
                <div class="lg:col-span-2">

                    <div class="flex items-center gap-3">

                        <div class="flex h-11 w-11 items-center justify-center bg-heritage-gold text-heritage-navy">
                            <span class="font-display text-2xl font-bold">H</span>
                        </div>

                        <div>
                            <div class="font-display text-xl font-semibold">
                                {{ companyName }}
                            </div>

                            <div class="text-[9px] font-semibold tracking-[0.18em] text-white/70">
                                CAPITAL MARKETS
                            </div>
                        </div>

                    </div>

                    <p class="mt-5 max-w-sm text-sm leading-6 text-white/65">
                        {{ footerText }}
                    </p>

                    <!-- Social -->
                    <div v-if="socialLinks.linkedin || socialLinks.facebook || socialLinks.instagram" class="mt-6 flex gap-3">

                        <a v-if="socialLinks.linkedin" :href="socialLinks.linkedin" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/30 text-xs hover:border-heritage-gold hover:text-heritage-gold">
                            in
                        </a>

                        <a v-if="socialLinks.facebook" :href="socialLinks.facebook" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/30 text-xs hover:border-heritage-gold hover:text-heritage-gold">
                            f
                        </a>

                        <a v-if="socialLinks.instagram" :href="socialLinks.instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="flex h-9 w-9 items-center justify-center rounded-full border border-white/30 text-xs hover:border-heritage-gold hover:text-heritage-gold">
                            ◎
                        </a>

                    </div>

                </div>

                <!-- Company -->
                <div>
                    <h3 class="mb-4 text-sm font-semibold text-heritage-gold">
                        Company
                    </h3>

                    <div class="space-y-2 text-sm text-white/70">
                        <a href="/about-us" class="block hover:text-white">About Us</a>
                        <a href="/board-management" class="block hover:text-white">Board & Management</a>
                        <a href="/board-management#careers" class="block hover:text-white">Careers</a>
                        <a href="/corporate-governance" class="block hover:text-white">Corporate Governance</a>
                    </div>
                </div>

                <!-- Solutions -->
                <div>
                    <h3 class="mb-4 text-sm font-semibold text-heritage-gold">
                        Investment Solutions
                    </h3>

                    <div class="space-y-2 text-sm text-white/70">
                        <a href="/investment-solutions" class="block hover:text-white">Our Services</a>
                        <a href="/investment-solutions" class="block hover:text-white">Products</a>
                        <a href="/fees-charges" class="block hover:text-white">Fees & Charges</a>
                    </div>
                </div>

                <!-- Research -->
                <div>
                    <h3 class="mb-4 text-sm font-semibold text-heritage-gold">
                        Research Centre
                    </h3>

                    <div class="space-y-2 text-sm text-white/70">
                        <a href="/#market-overview" class="block hover:text-white">Market Overview</a>
                        <a href="/research-centre" class="block hover:text-white">Research Reports</a>
                        <a href="/resources-news" class="block hover:text-white">News & Insights</a>
                        <a href="/resources#investor-education" class="block hover:text-white">Investor Education</a>
                    </div>
                </div>

                <!-- Resources -->
                <div>
                    <h3 class="mb-4 text-sm font-semibold text-heritage-gold">
                        Resources
                    </h3>

                    <div class="space-y-2 text-sm text-white/70">
                        <a href="/resources#market-data" class="block hover:text-white">Market Data</a>
                        <a href="/resources/downloads" class="block hover:text-white">Downloads</a>
                        <a href="/resources/faqs" class="block hover:text-white">FAQs</a>
                        <a href="/resources#glossary" class="block hover:text-white">Glossary</a>
                    </div>
                </div>

            </div>

            <!-- Offices -->
            <div class="mt-12 border-t border-white/10 pt-8">

                <div class="grid gap-8 md:grid-cols-2">

                    <div>
                        <h3 class="mb-4 text-sm font-semibold text-heritage-gold">
                            Our Offices
                        </h3>

                        <div class="space-y-4 text-sm text-white/70">

                            <div v-if="address">
                                <div class="font-semibold text-white">
                                    Head Office
                                </div>

                                <div class="whitespace-pre-line">
                                    {{ address }}
                                </div>
                            </div>

                            <div>
                                <div class="font-semibold text-white">
                                    Aba Office
                                </div>

                                <div class="whitespace-pre-line">
                                    {{ abaAddress }}
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="md:text-right">
                        <div v-if="phoneHref" class="text-sm text-white/70">
                            <a :href="phoneHref" class="hover:text-heritage-gold">{{ phone }}</a>
                        </div>
                        <div v-if="email" class="mt-2 text-sm text-white/70">
                            <a :href="`mailto:${email}`" class="hover:text-heritage-gold">{{ email }}</a>
                        </div>
                        <a
                            href="/contact-us"
                            class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-heritage-gold hover:text-heritage-gold-light"
                        >
                            View all locations
                            <span>→</span>
                        </a>
                    </div>

                </div>

            </div>

        </div>

        <!-- Copyright -->
        <div class="border-t border-white/10">

            <div class="mx-auto flex max-w-350 flex-col gap-3 px-5 py-5 text-xs text-white/55 md:flex-row md:items-center md:justify-between lg:px-8">

                <div>
                    © {{ new Date().getFullYear() }} {{ companyName }}.
                    All Rights Reserved.
                </div>

                <div class="flex flex-wrap gap-5">
                    <a href="/privacy-policy" class="hover:text-white">Privacy Policy</a>
                    <a href="/terms-of-use" class="hover:text-white">Terms of Use</a>
                    <a href="/risk-disclosure" class="hover:text-white">Risk Disclosure</a>
                </div>

                <div>
                    SEC License No. (Placeholder)
                </div>

            </div>

        </div>

    </footer>
</template>