<script setup>
import { computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    solutions: {
        type: Array,
        default: () => [],
    },
})

const solutionCards = computed(() => {
    return [...props.solutions]
        .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
        .slice(0, 6)
})

/*
|--------------------------------------------------------------------------
| Public Asset URL
|--------------------------------------------------------------------------
| Images stored in Laravel's /public directory should not be imported
| through Vite. They are referenced as normal browser URLs.
|--------------------------------------------------------------------------
*/
function assetUrl(path) {
    if (!path) return ''

    return path.startsWith('/')
        ? path
        : `/${path}`
}

const benefits = [
    {
        title: 'Client-Centric Approach',
        text: 'Your goals are our priority.',
        icon: 'target',
    },
    {
        title: 'Risk Managed',
        text: 'Disciplined investment process.',
        icon: 'shield',
    },
    {
        title: 'Data Driven',
        text: 'Insights for smarter decisions.',
        icon: 'chart',
    },
    {
        title: 'Trusted Partner',
        text: "Decades of trust in Nigeria's capital markets.",
        icon: 'handshake',
    },
]

const journey = [
    {
        number: '01',
        title: 'Understand',
        text: 'We learn about your goals and risk appetite.',
    },
    {
        number: '02',
        title: 'Design',
        text: 'We create a tailored investment strategy.',
    },
    {
        number: '03',
        title: 'Implement',
        text: 'We execute with discipline and precision.',
    },
    {
        number: '04',
        title: 'Grow',
        text: 'We monitor and adjust to keep you on track.',
    },
]
</script>

<template>
    <PublicLayout>

        <!-- =====================================================
             HERO
        ====================================================== -->
        <section
            class="relative min-h-[570px] overflow-hidden bg-heritage-deep text-white lg:min-h-[650px]"
        >

            <!-- Background -->
            <div class="absolute inset-0">

                <img
					:src="assetUrl('images/hero/slide-02-market-insights.webp')"
					alt=""
					class="h-full w-full object-cover"
				/>

                <div
                    class="absolute inset-0 bg-gradient-to-r from-[#031426] via-[#031426]/90 to-[#031426]/25"
                ></div>

                <div
                    class="absolute inset-0 bg-gradient-to-t from-[#031426]/70 via-transparent to-transparent"
                ></div>

            </div>


            <!-- Decorative rings -->
            <div
                class="absolute -left-32 top-32 h-72 w-72 rounded-full border border-white/10"
            ></div>

            <div
                class="absolute -left-20 top-44 h-56 w-56 rounded-full border border-white/10"
            ></div>


            <div
                class="relative z-10 mx-auto max-w-[1400px] px-6 py-8 lg:px-12 lg:py-10"
            >

                <!-- Breadcrumb -->
                <div
                    class="mb-14 flex items-center gap-2 text-xs text-white/70"
                >
                    <a
                        href="/"
                        class="transition hover:text-heritage-gold"
                    >
                        Home
                    </a>

                    <span>›</span>

                    <span class="text-white">
                        Investment Solutions
                    </span>
                </div>


                <div class="max-w-2xl">

                    <div
                        class="text-[10px] font-semibold uppercase tracking-[0.25em] text-heritage-gold"
                    >
                        Investment Solutions
                    </div>


                    <h1
                        class="mt-3 font-display text-4xl leading-[1.08] text-white sm:text-5xl lg:text-6xl"
                    >
                        Tailored Solutions
                        <br />
                        for Your
                        <span class="text-heritage-gold">
                            Financial Goals.
                        </span>
                    </h1>


                    <p
                        class="mt-5 max-w-xl text-base leading-7 text-white/85 lg:text-lg"
                    >
                        Access a comprehensive range of investment solutions
                        designed to help you grow, protect and manage your
                        wealth through every stage of your financial journey.
                    </p>


                    <div
                        class="mt-8 flex flex-col gap-3 sm:flex-row"
                    >

                        <a
                            href="/open-account"
                            class="inline-flex items-center justify-center gap-3 rounded bg-heritage-gold px-6 py-3 text-sm font-semibold text-heritage-deep transition hover:bg-heritage-gold-light"
                        >
                            Open an Account
                            <span>→</span>
                        </a>


                        <a
                            href="/contact-us"
                            class="inline-flex items-center justify-center gap-3 rounded border border-heritage-gold px-6 py-3 text-sm font-semibold text-white transition hover:bg-heritage-gold/10"
                        >
                            Talk to an Expert
                            <span>⌕</span>
                        </a>

                    </div>

                </div>


                <!-- Right vertical words -->
                <div
                    class="absolute right-8 top-24 hidden border-l border-heritage-gold/70 pl-5 lg:block"
                >

                    <div
                        class="space-y-3 text-[10px] font-semibold uppercase tracking-[0.28em] text-white/80"
                    >
                        <div>Insights</div>
                        <div>Expertise</div>
                        <div>Opportunity</div>
                        <div>Real Results</div>
                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             BENEFITS STRIP
        ====================================================== -->
        <section class="border-b border-heritage-border bg-white">

            <div
                class="mx-auto grid max-w-[1400px] md:grid-cols-2 lg:grid-cols-4"
            >

                <div
                    v-for="(item, index) in benefits"
                    :key="item.title"
                    class="flex items-center gap-4 px-6 py-7 lg:px-8"
                    :class="
                        index < benefits.length - 1
                            ? 'border-b border-heritage-border lg:border-b-0 lg:border-r'
                            : ''
                    "
                >

                    <!-- Target -->
                    <div
                        v-if="item.icon === 'target'"
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-heritage-offwhite text-2xl text-heritage-gold"
                    >
                        ◎
                    </div>

                    <!-- Shield -->
                    <div
                        v-else-if="item.icon === 'shield'"
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-heritage-offwhite text-xl text-heritage-gold"
                    >
                        ◇
                    </div>

                    <!-- Chart -->
                    <div
                        v-else-if="item.icon === 'chart'"
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-heritage-offwhite text-xl text-heritage-gold"
                    >
                        ▥
                    </div>

                    <!-- Handshake -->
                    <div
                        v-else
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-heritage-offwhite text-xl text-heritage-gold"
                    >
                        ♢
                    </div>


                    <div>

                        <h3
                            class="font-display text-base text-heritage-navy"
                        >
                            {{ item.title }}
                        </h3>

                        <p
                            class="mt-1 text-xs text-heritage-muted"
                        >
                            {{ item.text }}
                        </p>

                    </div>

                </div>

            </div>

        </section>


        <!-- =====================================================
             SOLUTIONS
        ====================================================== -->
        <section class="bg-white">

            <div
                class="mx-auto max-w-[1400px] px-6 py-14 lg:px-12 lg:py-16"
            >

                <div
                    class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
                >

                    <div>

                        <div
                            class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold"
                        >
                            Our Solutions
                        </div>

                        <h2
                            class="mt-2 font-display text-3xl leading-tight text-heritage-navy sm:text-4xl"
                        >
                            Comprehensive Investment Solutions
                        </h2>

                    </div>


                    <p
                        class="max-w-md text-sm leading-6 text-heritage-muted"
                    >
                        From traditional investments to alternative assets,
                        our solutions are designed to meet the needs of
                        individual, corporate and institutional clients.
                    </p>

                </div>


                <!-- Cards -->
                <div
                    class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
                >

                    <article
                        v-for="solution in solutionCards"
                        :key="solution.id"
                        class="group flex min-h-[170px] overflow-hidden rounded-lg border border-heritage-border bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg"
                    >

                        <div
                            class="w-[34%] shrink-0 overflow-hidden bg-heritage-offwhite"
                        >

                            <img
                                v-if="solution.featured_image"
                                :src="assetUrl(solution.featured_image)"
                                :alt="solution.title"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                loading="lazy"
                            />

                            <div
                                v-else
                                class="flex h-full items-center justify-center bg-heritage-navy text-heritage-gold"
                            >
                                <span class="font-display text-xl">
                                    Heritage
                                </span>
                            </div>

                        </div>


                        <div class="flex flex-1 flex-col p-5">

                            <div
                                class="mb-3 text-xl text-heritage-gold"
                            >
                                ◇
                            </div>

                            <h3
                                class="font-display text-lg leading-tight text-heritage-navy"
                            >
                                {{ solution.title }}
                            </h3>

                            <p
                                class="mt-2 line-clamp-3 text-xs leading-5 text-heritage-muted"
                            >
                                {{ solution.short_description }}
                            </p>


                            <a
                                :href="
                                    solution.button_url ||
                                    `/${solution.slug}`
                                "
                                class="mt-auto pt-4 text-xs font-semibold text-heritage-gold transition hover:text-heritage-navy"
                            >
                                {{ solution.button_label || 'Learn More' }}
                                <span class="ml-1">→</span>
                            </a>

                        </div>

                    </article>

                </div>

            </div>

        </section>


        <!-- =====================================================
             INVESTMENT JOURNEY
        ====================================================== -->
        <section class="bg-[#F7F5EF]">

            <div
                class="mx-auto max-w-[1400px] px-6 py-8 lg:px-12"
            >

                <div
                    class="grid gap-8 lg:grid-cols-[260px_1fr]"
                >

                    <!-- Intro -->
                    <div>

                        <div
                            class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold"
                        >
                            Your Investment Journey
                        </div>

                        <h2
                            class="mt-2 font-display text-3xl leading-tight text-heritage-navy"
                        >
                            A Clear Path
                            <br />
                            to Your Goals
                        </h2>

                    </div>


                    <!-- Steps -->
                    <div
                        class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4"
                    >

                        <div
                            v-for="(step, index) in journey"
                            :key="step.number"
                            class="relative"
                        >

                            <!-- Connecting line -->
                            <div
                                v-if="index < journey.length - 1"
                                class="absolute left-12 right-[-1.5rem] top-5 hidden h-px bg-heritage-navy/30 lg:block"
                            ></div>


                            <div
                                class="relative z-10 flex h-10 w-10 items-center justify-center rounded-full bg-heritage-gold text-xs font-bold text-heritage-deep"
                            >
                                {{ step.number }}
                            </div>


                            <h3
                                class="mt-4 font-display text-lg text-heritage-navy"
                            >
                                {{ step.title }}
                            </h3>

                            <p
                                class="mt-1 max-w-[180px] text-xs leading-5 text-heritage-muted"
                            >
                                {{ step.text }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>


      

    </PublicLayout>
</template>