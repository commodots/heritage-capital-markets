<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

defineProps({
    reports: {
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
    <Head title="Research Centre | Heritage Capital Markets" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-heritage-deep text-white">
            <div class="absolute inset-0 bg-[url('/images/hero/slide-02-market-insights.webp')] bg-cover bg-center opacity-35"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-heritage-deep via-heritage-deep/90 to-heritage-deep/40"></div>

            <div class="relative mx-auto max-w-[1400px] px-6 py-24 lg:px-12">
                <div class="max-w-3xl">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.28em] text-heritage-gold">
                        Research & Insights
                    </div>
                    <h1 class="mt-4 font-display text-5xl leading-tight sm:text-6xl">
                        Research Centre
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-7 text-white/75">
                        Market research, investment insights and reports from the Heritage Capital Markets team.
                    </p>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto max-w-[1400px] px-6 py-20 lg:px-12">
                <div class="grid gap-10 lg:grid-cols-[300px_1fr]">
                    <div>
                        <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold">
                            Research Centre
                        </div>
                        <h2 class="mt-3 font-display text-4xl leading-tight text-heritage-navy">
                            Market insight for informed decisions.
                        </h2>
                        <p class="mt-5 text-sm leading-7 text-heritage-muted">
                            Explore published research and reports covering relevant market and investment themes.
                        </p>
                    </div>

                    <div>
                        <div v-if="reports.length" class="grid gap-6 md:grid-cols-2">
                            <article
                                v-for="report in reports"
                                :key="report.id"
                                class="overflow-hidden rounded-xl border border-heritage-border bg-white transition hover:-translate-y-1 hover:shadow-lg"
                            >
                                <img
                                    :src="assetUrl(report.featured_image)"
                                    :alt="report.title"
                                    class="h-52 w-full object-cover"
                                />

                                <div class="p-6">
                                    <div class="text-[10px] font-semibold uppercase tracking-wider text-heritage-gold">
                                        Research Report
                                        <span v-if="report.published_at" class="text-heritage-muted">
                                            · {{ formatDate(report.published_at) }}
                                        </span>
                                    </div>

                                    <h3 class="mt-3 font-display text-2xl leading-tight text-heritage-navy">
                                        {{ report.title }}
                                    </h3>

                                    <p v-if="report.excerpt" class="mt-3 text-sm leading-6 text-heritage-muted">
                                        {{ report.excerpt }}
                                    </p>

                                    <Link
                                        :href="`/research-centre/reports/${report.id}`"
                                        class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-heritage-gold"
                                    >
                                        Read Report <span>→</span>
                                    </Link>
                                </div>
                            </article>
                        </div>

                        <div v-else class="rounded-xl border border-heritage-border bg-heritage-offwhite px-8 py-16 text-center">
                            <h3 class="font-display text-3xl text-heritage-navy">
                                Research reports will appear here.
                            </h3>
                            <p class="mt-3 text-sm text-heritage-muted">
                                Published reports can be managed from the Research Reports section of the CMS.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
