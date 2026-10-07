<script setup>
import { Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

defineProps({
    articles: {
        type: Array,
        default: () => [],
    },
    upcomingEvents: {
        type: Array,
        default: () => [],
    },
})

function assetUrl(path) {
    if (!path) return '/images/hero/slide-02-market-insights.webp'
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('/')) {
        return path
    }
    return path.startsWith('events/') ? `/storage/${path}` : `/${path}`
}

function formatDate(value) {
    if (!value) return ''
    return new Date(value).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}

function formatEventDate(value) {
    if (!value) return ''
    return new Date(value).toLocaleString('en-GB', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}
</script>

<template>
    <PublicLayout>
        <SeoHead
            title="Resources & News"
            description="Explore market perspectives, company updates, investment insights, and upcoming events from Heritage Capital Markets."
            canonical-path="/resources-news"
        />
        <section class="bg-heritage-deep text-white">
            <div class="mx-auto max-w-350 px-6 py-24 lg:px-12">
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
            <div class="mx-auto max-w-350 px-6 py-20 lg:px-12">
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

         <section v-if="upcomingEvents.length" class="bg-white">
            <div class="mx-auto max-w-350 px-6 py-16 lg:px-12">
                <div class="mb-8 max-w-2xl">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.28em] text-heritage-gold">
                        Save the date
                    </div>
                    <h2 class="mt-3 font-display text-3xl text-heritage-navy sm:text-4xl">
                        Upcoming Events
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-heritage-muted">
                        Join us for upcoming events, conversations and market insights.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="event in upcomingEvents"
                        :key="event.id"
                        class="overflow-hidden rounded-xl border border-heritage-border bg-heritage-offwhite"
                    >
                        <img
                            v-if="event.featured_image"
                            :src="assetUrl(event.featured_image)"
                            :alt="event.title"
                            class="h-52 w-full object-cover"
                        >
                        <div class="p-6">
                            <time
                                v-if="event.event_date"
                                :datetime="event.event_date"
                                class="text-xs font-semibold uppercase tracking-wide text-heritage-gold"
                            >
                                {{ formatEventDate(event.event_date) }}
                            </time>
                            <h3 class="mt-3 font-display text-2xl leading-tight text-heritage-navy">
                                {{ event.title }}
                            </h3>
                            <p v-if="event.location" class="mt-2 text-sm font-medium text-slate-600">
                                {{ event.location }}
                            </p>
                            <p v-if="event.excerpt" class="mt-3 text-sm leading-6 text-heritage-muted">
                                {{ event.excerpt }}
                            </p>
                        </div>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
