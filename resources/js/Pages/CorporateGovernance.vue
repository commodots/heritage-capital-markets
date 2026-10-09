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
            :title="content.governance_title"
            :description="content.governance_intro"
            canonical-path="/corporate-governance"
        />

        <section class="bg-heritage-deep py-24 text-white">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-heritage-gold">{{ content.governance_eyebrow }}</p>
                <h1 class="mt-4 font-display text-4xl font-semibold md:text-6xl">{{ content.governance_title }}</h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-white/75">
                    {{ content.governance_intro }}
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage max-w-4xl">
                <h2 class="font-display text-3xl font-semibold text-heritage-navy">{{ content.governance_principles_title }}</h2>
                <p class="mt-5 leading-7 text-heritage-muted">
                    {{ content.governance_principles_intro }}
                </p>
                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <article v-for="number in [1, 2, 3]" :key="number" class="border border-heritage-border p-6">
                        <h3 class="font-display text-xl font-semibold text-heritage-navy">{{ content[`governance_principle_${number}_title`] }}</h3>
                        <p class="mt-3 leading-6 text-heritage-muted">{{ content[`governance_principle_${number}_body`] }}</p>
                    </article>
                </div>
                <p class="mt-8 text-sm leading-6 text-heritage-muted">
                    {{ content.governance_contact_text }}
                    <a href="/contact-us" class="font-semibold text-heritage-navy underline">Contact us</a>.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
