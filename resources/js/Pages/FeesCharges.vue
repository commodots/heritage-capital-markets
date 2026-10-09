<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
})
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="content.fees_title"
            :description="content.fees_intro"
            canonical-path="/fees-charges"
        />

        <section class="bg-heritage-deep py-24 text-white">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-heritage-gold">{{ content.fees_eyebrow }}</p>
                <h1 class="mt-4 font-display text-4xl font-semibold md:text-6xl">{{ content.fees_title }}</h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-white/75">
                    {{ content.fees_intro }}
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage max-w-4xl">
                <h2 class="font-display text-3xl font-semibold text-heritage-navy">{{ content.fees_section_title }}</h2>
                <p class="mt-5 leading-7 text-heritage-muted">
                    {{ content.fees_section_body }}
                </p>
                <a href="/contact-us" class="mt-7 inline-flex items-center gap-2 rounded bg-heritage-gold px-6 py-3 font-semibold text-heritage-deep transition hover:bg-heritage-gold-light">
                    {{ content.fees_contact_button }} <span aria-hidden="true">→</span>
                </a>
            </div>
        </section>
    </PublicLayout>
</template>
