<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

defineProps({
    page: {
        type: Object,
        required: true,
    },
})

function assetUrl(path) {
    if (!path) return ''

    if (path.startsWith('/')) return path

    return path.startsWith('pages/') ? `/storage/${path}` : `/${path}`
}
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="page.meta_title || page.title"
            :description="page.meta_description || page.excerpt || ''"
            :canonical-path="`/${page.slug}`"
        />

        <section class="bg-heritage-deep py-20 text-white md:py-24">
            <div class="container-heritage">
                <p v-if="page.eyebrow" class="text-xs font-semibold uppercase tracking-[0.22em] text-heritage-gold">
                    {{ page.eyebrow }}
                </p>
                <h1 class="mt-4 font-display text-4xl font-semibold md:text-6xl">
                    {{ page.title }}
                </h1>
                <p v-if="page.excerpt" class="mt-6 max-w-3xl text-base leading-7 text-white/75">
                    {{ page.excerpt }}
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-20">
            <div class="container-heritage max-w-4xl">
                <img
                    v-if="page.featured_image"
                    :src="assetUrl(page.featured_image)"
                    :alt="page.title"
                    class="mb-10 max-h-120 w-full rounded object-cover"
                >
                <article
                    v-if="page.content"
                    class="leading-7 text-heritage-muted [&_h2]:mb-4 [&_h2]:mt-8 [&_h2]:font-display [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-heritage-navy [&_p]:mb-5"
                    v-html="page.content"
                />
            </div>
        </section>
    </PublicLayout>
</template>
