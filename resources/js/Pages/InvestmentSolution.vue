<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    solution: { type: Object, required: true },
    relatedSolutions: { type: Array, default: () => [] },
})

function assetUrl(path) {
    if (!path) return ''
    return path.startsWith('/') ? path : `/${path}`
}

const solutionImage = computed(() => assetUrl(props.solution.featured_image))
const pageTitle = computed(() => `${props.solution.title} | Heritage Capital Markets`)

const benefits = computed(() => {
    const map = {
        'portfolio-management': [
            ['Personalised Strategy', 'Investment strategies aligned with your objectives, risk appetite and investment horizon.'],
            ['Professional Oversight', 'Ongoing professional management and monitoring of your portfolio.'],
            ['Risk Discipline', 'A structured approach to diversification, risk management and portfolio review.'],
        ],
        'investment-advisory': [
            ['Independent Insight', 'Research-supported perspectives to help you evaluate investment opportunities.'],
            ['Goal Focused', 'Advice structured around your financial objectives and investment priorities.'],
            ['Market Intelligence', 'Professional interpretation of relevant market developments and opportunities.'],
        ],
        'brokerage-dealing': [
            ['Market Access', 'Professional access to relevant capital market instruments.'],
            ['Efficient Execution', 'Disciplined execution of transactions based on client instructions and objectives.'],
            ['Professional Support', 'A responsive dealing team supporting your market transactions.'],
        ],
        'fixed-income-investments': [
            ['Income Opportunities', 'Access to suitable fixed income opportunities based on prevailing market conditions.'],
            ['Investment Choice', 'Consideration of different tenures and fixed income instruments.'],
            ['Risk Awareness', 'Clear consideration of tenure, liquidity, yield and relevant risks.'],
        ],
        'alternative-investments': [
            ['Diversification', 'Explore investment opportunities beyond traditional asset classes.'],
            ['Opportunity Access', 'Identify alternative opportunities suitable for your objectives.'],
            ['Professional Assessment', 'Structured consideration of suitability, risk and investment horizon.'],
        ],
        'financial-planning': [
            ['Clear Objectives', 'Bring your financial priorities and longer-term goals into focus.'],
            ['Structured Planning', 'Develop a practical framework for managing wealth and investments.'],
            ['Long-Term Perspective', 'Build a plan designed around changing needs and future priorities.'],
        ],
    }

    const items = map[props.solution.slug] ?? [
        ['Tailored Approach', 'Solutions designed around your financial objectives and circumstances.'],
        ['Professional Expertise', 'Experienced professionals providing informed investment guidance.'],
        ['Ongoing Support', 'Continued attention to your investment priorities and changing needs.'],
    ]

    return items.map(([title, text]) => ({ title, text }))
})

const journey = [
    { number: '01', title: 'Understand', text: 'We understand your objectives, circumstances and risk appetite.' },
    { number: '02', title: 'Recommend', text: 'We develop an approach aligned with your investment priorities.' },
    { number: '03', title: 'Implement', text: 'We put the agreed strategy or transaction into action.' },
    { number: '04', title: 'Review', text: 'We maintain an ongoing focus on your investment objectives.' },
]
</script>

<template>
    <Head :title="pageTitle" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-heritage-deep text-white">
            <div class="absolute inset-0">
                <img v-if="solutionImage" :src="solutionImage" :alt="solution.title"
                     class="h-full w-full object-cover" />
                <div class="absolute inset-0 bg-gradient-to-r from-[#031426] via-[#031426]/90 to-[#031426]/45"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-[#031426]/80 via-transparent to-transparent"></div>
            </div>

            <div class="relative z-10 mx-auto max-w-[1400px] px-6 py-14 lg:px-12 lg:py-20">
                <div class="mb-10 flex flex-wrap items-center gap-2 text-xs text-white/70">
                    <Link href="/" class="hover:text-heritage-gold">Home</Link>
                    <span>›</span>
                    <Link href="/investment-solutions" class="hover:text-heritage-gold">Investment Solutions</Link>
                    <span>›</span>
                    <span class="text-white">{{ solution.title }}</span>
                </div>

                <div class="max-w-3xl">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.25em] text-heritage-gold">
                        Investment Solutions
                    </div>
                    <h1 class="mt-4 font-display text-4xl leading-[1.08] text-white sm:text-5xl lg:text-6xl">
                        {{ solution.title }}
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-7 text-white/85 lg:text-lg">
                        {{ solution.short_description }}
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <Link href="/contact-us"
                              class="inline-flex items-center justify-center gap-3 rounded bg-heritage-gold px-6 py-3 text-sm font-semibold text-heritage-deep hover:bg-heritage-gold-light">
                            Talk to an Expert <span>→</span>
                        </Link>
                        <Link href="/investment-solutions"
                              class="inline-flex items-center justify-center rounded border border-heritage-gold px-6 py-3 text-sm font-semibold text-white hover:bg-heritage-gold/10">
                            All Solutions
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto max-w-[1200px] px-6 py-14 lg:px-12 lg:py-20">
                <div class="grid gap-10 lg:grid-cols-[1.15fr_.85fr]">
                    <div>
                        <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold">Overview</div>
                        <h2 class="mt-3 font-display text-3xl leading-tight text-heritage-navy sm:text-4xl">
                            A solution designed around your objectives
                        </h2>
                        <div class="mt-6 max-w-3xl text-base leading-8 text-heritage-muted" v-html="solution.description"></div>
                    </div>

                    <div class="rounded-xl bg-heritage-offwhite p-7 lg:p-8">
                        <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold">Why Heritage</div>
                        <h3 class="mt-3 font-display text-2xl text-heritage-navy">Professional insight. Client focus.</h3>
                        <p class="mt-4 text-sm leading-7 text-heritage-muted">
                            Our approach combines professional expertise, market insight and a clear understanding of client objectives.
                        </p>
                        <Link href="/contact-us" class="mt-6 inline-flex text-sm font-semibold text-heritage-navy hover:text-heritage-gold">
                            Speak with our team →
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-[#F7F5EF]">
            <div class="mx-auto max-w-[1200px] px-6 py-14 lg:px-12 lg:py-18">
                <div class="max-w-2xl">
                    <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold">What You Can Expect</div>
                    <h2 class="mt-3 font-display text-3xl text-heritage-navy sm:text-4xl">
                        Built around your financial priorities
                    </h2>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <article v-for="(benefit, index) in benefits" :key="benefit.title"
                             class="rounded-lg border border-heritage-border bg-white p-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-heritage-navy text-sm font-semibold text-heritage-gold">
                            0{{ index + 1 }}
                        </div>
                        <h3 class="mt-5 font-display text-xl text-heritage-navy">{{ benefit.title }}</h3>
                        <p class="mt-3 text-sm leading-6 text-heritage-muted">{{ benefit.text }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="bg-white">
            <div class="mx-auto max-w-[1200px] px-6 py-14 lg:px-12 lg:py-18">
                <div class="grid gap-10 lg:grid-cols-[280px_1fr]">
                    <div>
                        <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold">Our Approach</div>
                        <h2 class="mt-3 font-display text-3xl leading-tight text-heritage-navy sm:text-4xl">
                            A clear path from strategy to action
                        </h2>
                    </div>

                    <div class="grid gap-7 sm:grid-cols-2 lg:grid-cols-4">
                        <div v-for="(step, index) in journey" :key="step.number" class="relative">
                            <div v-if="index < journey.length - 1"
                                 class="absolute left-10 right-[-1.5rem] top-5 hidden h-px bg-heritage-navy/20 lg:block"></div>
                            <div class="relative z-10 flex h-10 w-10 items-center justify-center rounded-full bg-heritage-gold text-xs font-bold text-heritage-deep">
                                {{ step.number }}
                            </div>
                            <h3 class="mt-4 font-display text-lg text-heritage-navy">{{ step.title }}</h3>
                            <p class="mt-2 text-xs leading-5 text-heritage-muted">{{ step.text }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="relatedSolutions.length" class="bg-heritage-offwhite">
            <div class="mx-auto max-w-[1200px] px-6 py-14 lg:px-12">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold">Explore More</div>
                        <h2 class="mt-2 font-display text-3xl text-heritage-navy">Other Investment Solutions</h2>
                    </div>
                    <Link href="/investment-solutions" class="text-sm font-semibold text-heritage-navy hover:text-heritage-gold">
                        View All Solutions →
                    </Link>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <Link v-for="item in relatedSolutions" :key="item.id"
                          :href="item.button_url || `/${item.slug}`"
                          class="group overflow-hidden rounded-lg border border-heritage-border bg-white transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="h-36 overflow-hidden bg-heritage-navy">
                            <img v-if="item.featured_image" :src="assetUrl(item.featured_image)"
                                 :alt="item.title"
                                 class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                 loading="lazy" />
                        </div>
                        <div class="p-5">
                            <h3 class="font-display text-lg text-heritage-navy">{{ item.title }}</h3>
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-heritage-muted">
                                {{ item.short_description }}
                            </p>
                            <div class="mt-4 text-xs font-semibold text-heritage-gold">Learn More →</div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
