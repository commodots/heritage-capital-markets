<script setup>
import { computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

const props = defineProps({
    pageKey: {
        type: String,
        required: true,
    },
    pagePath: {
        type: String,
        required: true,
    },
    pageTitle: {
        type: String,
        required: true,
    },
    content: {
        type: Object,
        default: () => ({}),
    },
})

const sections = computed(() => Array.from({ length: 6 }, (_, index) => {
    const number = index + 1

    return {
        number,
        title: props.content[`${props.pageKey}_section_${number}_title`],
        body: props.content[`${props.pageKey}_section_${number}_body`],
    }
}).filter(section => section.body?.trim()))
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="content[`${pageKey}_title`] || pageTitle"
            :description="content[`${pageKey}_intro`]"
            :canonical-path="pagePath"
        />

        <section class="bg-heritage-deep py-24 text-white">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-heritage-gold">Important Information</p>
                <h1 class="mt-4 font-display text-4xl font-semibold md:text-6xl">
                    {{ content[`${pageKey}_title`] || pageTitle }}
                </h1>
                <p class="mt-6 max-w-3xl whitespace-pre-line text-base leading-7 text-white/75">
                    {{ content[`${pageKey}_intro`] }}
                </p>
                <p v-if="content[`${pageKey}_updated_at`]" class="mt-5 text-sm text-white/65">
                    Last updated: {{ content[`${pageKey}_updated_at`] }}
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage max-w-4xl">
                <article
                    v-for="section in sections"
                    :key="section.number"
                    class="border-b border-heritage-border py-7 first:pt-0 last:border-b-0"
                >
                    <h2 class="font-display text-2xl font-semibold text-heritage-navy">
                        {{ section.title }}
                    </h2>
                    <p class="mt-4 whitespace-pre-line leading-7 text-heritage-muted">
                        {{ section.body }}
                    </p>
                </article>
                <p class="mt-8 text-sm text-heritage-muted">
                    Questions? <a href="/contact-us" class="font-semibold text-heritage-navy underline">Contact us</a>.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
