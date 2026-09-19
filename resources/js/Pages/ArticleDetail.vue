<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    article: {
        type: Object,
        required: true,
    },
    relatedArticles: {
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
        month: 'long',
        year: 'numeric',
    })
}
</script>

<template>
    <Head :title="`${article.title} | Heritage Capital Markets`" />

    <PublicLayout>
        <section class="bg-heritage-deep text-white">
            <div class="mx-auto max-w-[1200px] px-6 py-20 lg:px-12">
                <Link href="/resources-news" class="text-xs text-white/60 hover:text-heritage-gold">
                    ← Resources & News
                </Link>

                <div class="mt-8 max-w-4xl">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.28em] text-heritage-gold">
                        {{ article.category || 'Insights' }}
                        <span v-if="article.published_at"> · {{ formatDate(article.published_at) }}</span>
                    </div>

                    <h1 class="mt-4 font-display text-4xl leading-tight sm:text-5xl lg:text-6xl">
                        {{ article.title }}
                    </h1>

                    <p v-if="article.excerpt" class="mt-6 max-w-3xl text-base leading-7 text-white/70">
                        {{ article.excerpt }}
                    </p>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto grid max-w-[1200px] gap-12 px-6 py-16 lg:grid-cols-[1fr_320px] lg:px-12">
                <article>
                    <img
                        :src="assetUrl(article.featured_image)"
                        :alt="article.title"
                        class="mb-10 h-auto max-h-[520px] w-full rounded-xl object-cover"
                    />

                    <div
                        class="prose prose-slate max-w-none leading-8"
                        v-html="article.content"
                    ></div>
                </article>

                <aside v-if="relatedArticles.length">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold">
                        More Insights
                    </div>

                    <div class="mt-5 space-y-5">
                        <Link
                            v-for="item in relatedArticles"
                            :key="item.id"
                            :href="`/resources-news/${item.slug}`"
                            class="block border-b border-heritage-border pb-5"
                        >
                            <div class="text-xs text-heritage-muted">
                                {{ item.category || 'Insights' }}
                            </div>
                            <h3 class="mt-1 font-display text-lg leading-tight text-heritage-navy">
                                {{ item.title }}
                            </h3>
                        </Link>
                    </div>
                </aside>
            </div>
        </section>
    </PublicLayout>
</template>
