<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

defineProps({
    articles: {
        type: Array,
        default: () => [],
    },
})

function assetUrl(path) {
    if (!path) return '/images/hero/slide-02-market-insights.webp'
    return path.startsWith('/') ? path : `/${path}`
}

function formatDate(value) {
    if (!value) return ''
    return new Date(value).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}
</script>

<template>
    <Head title="Resources & News | Heritage Capital Markets" />

    <PublicLayout>
        <section class="bg-heritage-deep text-white">
            <div class="mx-auto max-w-[1400px] px-6 py-24 lg:px-12">
                <div class="max-w-3xl">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.28em] text-heritage-gold">
                        Resources & News
                    </div>
                    <h1 class="mt-4 font-display text-5xl leading-tight sm:text-6xl">
                        Insights, News & Perspectives.
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-7 text-white/70">
                        Explore market perspectives, company updates and insights published by Heritage Capital Markets.
                    </p>
                </div>
            </div>
        </section>

        <section class="bg-heritage-offwhite">
            <div class="mx-auto max-w-[1400px] px-6 py-20 lg:px-12">
                <div v-if="articles.length" class="grid gap-7 md:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="article in articles"
                        :key="article.id"
                        class="overflow-hidden rounded-xl border border-heritage-border bg-white transition hover:-translate-y-1 hover:shadow-lg"
                    >
                        <img
                            :src="assetUrl(article.featured_image)"
                            :alt="article.title"
                            class="h-56 w-full object-cover"
                        />

                        <div class="p-7">
                            <div class="flex items-center gap-3 text-[10px] font-semibold uppercase tracking-wider text-heritage-gold">
                                <span>{{ article.category || 'Insights' }}</span>
                                <span v-if="article.published_at" class="text-heritage-muted">
                                    {{ formatDate(article.published_at) }}
                                </span>
                            </div>

                            <h2 class="mt-3 font-display text-2xl leading-tight text-heritage-navy">
                                {{ article.title }}
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-heritage-muted">
                                {{ article.excerpt }}
                            </p>

                            <Link
                                :href="`/resources-news/${article.slug}`"
                                class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-heritage-gold"
                            >
                                Read Article <span>→</span>
                            </Link>
                        </div>
                    </article>
                </div>

                <div v-else class="rounded-xl border border-heritage-border bg-white px-8 py-16 text-center">
                    <h2 class="font-display text-3xl text-heritage-navy">No published articles yet.</h2>
                    <p class="mt-3 text-sm text-heritage-muted">
                        New insights and updates will appear here when published.
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
