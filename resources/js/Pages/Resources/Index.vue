<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

defineProps({
    markets: {
        type: Array,
        default: () => [],
    },
    content: {
        type: Object,
        default: () => ({}),
    },
})

function formatDate(value) {
    if (!value) return 'Not provided'
    return new Date(value).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="content.resources_title"
            :description="content.resources_intro"
            canonical-path="/resources"
        />

        <section class="bg-heritage-deep py-24 text-white">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-heritage-gold">Resources</p>
                <h1 class="mt-4 font-display text-4xl font-semibold md:text-6xl">{{ content.resources_title }}</h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-white/75">
                    {{ content.resources_intro }}
                </p>
                <nav aria-label="On this page" class="mt-8 flex flex-wrap gap-4 text-sm font-semibold text-heritage-gold">
                    <a href="#market-data" class="underline underline-offset-4">Market Data</a>
                    <a href="#investor-education" class="underline underline-offset-4">Investor Education</a>
                    <a href="#glossary" class="underline underline-offset-4">Glossary</a>
                </nav>
            </div>
        </section>

        <section id="market-data" class="scroll-mt-24 bg-white py-16 md:py-20">
            <div class="container-heritage">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-heritage-gold">{{ content.resources_market_eyebrow }}</p>
                    <h2 class="mt-3 font-display text-3xl font-semibold text-heritage-navy md:text-4xl">{{ content.resources_market_title }}</h2>
                    <p class="mt-4 leading-7 text-heritage-muted">
                        {{ content.resources_market_description }}
                    </p>
                </div>

                <div v-if="markets.length" class="mt-8 overflow-x-auto rounded border border-heritage-border">
                    <table class="w-full min-w-160 text-left text-sm">
                        <caption class="sr-only">Market index values and movements</caption>
                        <thead class="bg-heritage-offwhite text-heritage-navy">
                            <tr>
                                <th scope="col" class="px-5 py-4 font-semibold">Index</th>
                                <th scope="col" class="px-5 py-4 font-semibold">Symbol</th>
                                <th scope="col" class="px-5 py-4 font-semibold">Value</th>
                                <th scope="col" class="px-5 py-4 font-semibold">Change</th>
                                <th scope="col" class="px-5 py-4 font-semibold">As of</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-heritage-border">
                            <tr v-for="market in markets" :key="market.id" class="text-heritage-navy">
                                <th scope="row" class="px-5 py-4 font-semibold">{{ market.name }}</th>
                                <td class="px-5 py-4 text-heritage-muted">{{ market.symbol || '—' }}</td>
                                <td class="px-5 py-4">{{ market.value }}</td>
                                <td class="px-5 py-4">
                                    <span :class="Number(market.change_percent) < 0 ? 'text-red-700' : 'text-green-700'">
                                        {{ market.change_value }} ({{ Number(market.change_percent) > 0 ? '+' : '' }}{{ market.change_percent }}%)
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-heritage-muted">{{ formatDate(market.as_of) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="mt-8 rounded border border-heritage-border bg-heritage-offwhite p-6 text-heritage-muted">
                    Market data is not currently available.
                </p>
            </div>
        </section>

        <section id="investor-education" class="scroll-mt-24 bg-heritage-offwhite py-16 md:py-20">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-heritage-gold">{{ content.resources_education_eyebrow }}</p>
                <h2 class="mt-3 font-display text-3xl font-semibold text-heritage-navy md:text-4xl">{{ content.resources_education_title }}</h2>
                <div class="mt-8 grid gap-5 md:grid-cols-3">
                    <article v-for="number in [1, 2, 3]" :key="number" class="rounded border border-heritage-border bg-white p-6">
                        <h3 class="font-display text-xl font-semibold text-heritage-navy">{{ content[`resources_topic_${number}_title`] }}</h3>
                        <p class="mt-3 leading-7 text-heritage-muted">{{ content[`resources_topic_${number}_body`] }}</p>
                    </article>
                </div>
                <p class="mt-6 text-sm leading-6 text-heritage-muted">
                    {{ content.resources_education_disclaimer }}
                </p>
            </div>
        </section>

        <section id="glossary" class="scroll-mt-24 bg-white py-16 md:py-20">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-heritage-gold">{{ content.resources_glossary_eyebrow }}</p>
                <h2 class="mt-3 font-display text-3xl font-semibold text-heritage-navy md:text-4xl">{{ content.resources_glossary_title }}</h2>
                <dl class="mt-8 grid gap-x-10 md:grid-cols-2">
                    <div v-for="number in [1, 2, 3, 4, 5, 6]" :key="number" class="border-b border-heritage-border py-5">
                        <dt class="font-semibold text-heritage-navy">{{ content[`resources_glossary_${number}_term`] }}</dt>
                        <dd class="mt-2 leading-6 text-heritage-muted">{{ content[`resources_glossary_${number}_definition`] }}</dd>
                    </div>
                </dl>
            </div>
        </section>
    </PublicLayout>
</template>
