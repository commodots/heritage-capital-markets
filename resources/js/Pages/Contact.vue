<script setup>
import { computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import buildingImage from '@/assets/about/heritage-building.jpg'

const page = usePage()

const flashSuccess = computed(() => page.props.flash?.success ?? '')

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
})

function submit() {
    form.post('/contact-us', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <PublicLayout>
        <SeoHead
            title="Contact Us"
            description="Contact Heritage Capital Markets about our investment solutions, professional services, or other enquiries."
            canonical-path="/contact-us"
        />
        <section class="relative overflow-hidden bg-heritage-deep text-white">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                :style="{ backgroundImage: `url(${buildingImage})` }"
            ></div>
            <div class="absolute inset-0 bg-linear-to-r from-heritage-deep via-heritage-deep/90 to-heritage-deep/45"></div>

            <div class="relative mx-auto max-w-350 px-6 py-24 lg:px-12 lg:py-28">
                <div class="max-w-3xl">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.28em] text-heritage-gold">
                        Contact Heritage
                    </div>
                    <h1 class="mt-4 font-display text-5xl leading-tight sm:text-6xl">
                        Let’s Start a Conversation.
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-7 text-white/75">
                        Contact our team for information about our investment solutions,
                        professional services or other enquiries.
                    </p>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto grid max-w-350 gap-12 px-6 py-20 lg:grid-cols-[0.8fr_1.2fr] lg:px-12">
                <div>
                    <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold">
                        Get In Touch
                    </div>

                    <h2 class="mt-3 font-display text-4xl leading-tight text-heritage-navy">
                        How can we help?
                    </h2>

                    <p class="mt-5 max-w-lg text-sm leading-7 text-heritage-muted">
                        Send us your enquiry and a member of the Heritage team will review
                        your request and get back to you.
                    </p>

                    <div class="mt-10 space-y-5">
                        <div class="border-l-2 border-heritage-gold pl-5">
                            <div class="text-xs font-semibold uppercase tracking-wider text-heritage-gold">
                                Head Office
                            </div>
                            <p class="mt-2 text-sm leading-6 text-heritage-muted">
                                Lagos, Nigeria
                            </p>
                        </div>

                        <div class="border-l-2 border-heritage-gold pl-5">
                            <div class="text-xs font-semibold uppercase tracking-wider text-heritage-gold">
                                Enquiries
                            </div>
                            <p class="mt-2 text-sm leading-6 text-heritage-muted">
                                Use the enquiry form and our team will respond through the contact details provided.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-heritage-border bg-heritage-offwhite p-7 lg:p-10">
                    <div v-if="flashSuccess" class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                        {{ flashSuccess }}
                    </div>

                    <form class="space-y-5" @submit.prevent="submit">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="contact-name" class="text-xs font-semibold text-heritage-navy">Full Name *</label>
                                <input id="contact-name" v-model="form.name" type="text" required
                                    :aria-invalid="Boolean(form.errors.name)"
                                    :aria-describedby="form.errors.name ? 'contact-name-error' : undefined"
                                    class="mt-2 w-full rounded border border-heritage-border bg-white px-4 py-3 text-sm outline-none focus:border-heritage-gold" />
                                <div v-if="form.errors.name" id="contact-name-error" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</div>
                            </div>

                            <div>
                                <label for="contact-email" class="text-xs font-semibold text-heritage-navy">Email Address *</label>
                                <input id="contact-email" v-model="form.email" type="email" required
                                    :aria-invalid="Boolean(form.errors.email)"
                                    :aria-describedby="form.errors.email ? 'contact-email-error' : undefined"
                                    class="mt-2 w-full rounded border border-heritage-border bg-white px-4 py-3 text-sm outline-none focus:border-heritage-gold" />
                                <div v-if="form.errors.email" id="contact-email-error" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</div>
                            </div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div>
                                <label for="contact-phone" class="text-xs font-semibold text-heritage-navy">Phone</label>
                                <input id="contact-phone" v-model="form.phone" type="tel"
                                    class="mt-2 w-full rounded border border-heritage-border bg-white px-4 py-3 text-sm outline-none focus:border-heritage-gold" />
                            </div>

                            <div>
                                <label for="contact-subject" class="text-xs font-semibold text-heritage-navy">Subject *</label>
                                <input id="contact-subject" v-model="form.subject" type="text" required
                                    :aria-invalid="Boolean(form.errors.subject)"
                                    :aria-describedby="form.errors.subject ? 'contact-subject-error' : undefined"
                                    class="mt-2 w-full rounded border border-heritage-border bg-white px-4 py-3 text-sm outline-none focus:border-heritage-gold" />
                                <div v-if="form.errors.subject" id="contact-subject-error" class="mt-1 text-xs text-red-600">{{ form.errors.subject }}</div>
                            </div>
                        </div>

                        <div>
                            <label for="contact-message" class="text-xs font-semibold text-heritage-navy">Message *</label>
                            <textarea id="contact-message" v-model="form.message" rows="7" required
                                :aria-invalid="Boolean(form.errors.message)"
                                :aria-describedby="form.errors.message ? 'contact-message-error' : undefined"
                                class="mt-2 w-full resize-none rounded border border-heritage-border bg-white px-4 py-3 text-sm outline-none focus:border-heritage-gold"></textarea>
                            <div v-if="form.errors.message" id="contact-message-error" class="mt-1 text-xs text-red-600">{{ form.errors.message }}</div>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 rounded bg-heritage-gold px-7 py-3 text-sm font-semibold text-heritage-deep transition hover:bg-heritage-gold-light disabled:opacity-60"
                        >
                            {{ form.processing ? 'Sending...' : 'Send Enquiry' }}
                            <span>→</span>
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
