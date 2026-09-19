<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

/*
|--------------------------------------------------------------------------
| Homepage Images
|--------------------------------------------------------------------------
| These remain Vite-managed until the Media Library is connected.
|--------------------------------------------------------------------------
*/
import coinsTreeImage from '@/assets/hero/slide-01-coins-tree.webp'
import insightsImage from '@/assets/hero/slide-02-market-insights.webp'
import receptionImage from '@/assets/hero/slide-03-heritage-reception.webp'
import impactImage from '@/assets/hero/slide-03-heritage-reception.png'
import futureImage from '@/assets/hero/slide-04-financial-future.webp'

const page = usePage()

/*
|--------------------------------------------------------------------------
| Laravel / Database Homepage Data
|--------------------------------------------------------------------------
*/
const heroSlides = computed(() => page.props.heroSlides ?? [])
const solutions = computed(() => page.props.solutions ?? [])
const markets = computed(() => page.props.markets ?? [])
const settings = computed(() => page.props.settings ?? {})

/*
|--------------------------------------------------------------------------
| Homepage Settings Helper
|--------------------------------------------------------------------------
*/
const setting = (key, fallback = '') => {
    return settings.value[key] ?? fallback
}

/*
|--------------------------------------------------------------------------
| Asset Helper
|--------------------------------------------------------------------------
*/
const assetUrl = (path) => {
    if (!path) return ''

    return path.startsWith('/')
        ? path
        : `/${path}`
}

/*
|--------------------------------------------------------------------------
| Hero Carousel
|--------------------------------------------------------------------------
*/
const currentSlide = ref(0)
let timer = null

const slide = computed(() => {
    return heroSlides.value[currentSlide.value] ?? {}
})

function nextSlide() {
    if (!heroSlides.value.length) return

    currentSlide.value =
        (currentSlide.value + 1) % heroSlides.value.length
}

function previousSlide() {
    if (!heroSlides.value.length) return

    currentSlide.value =
        (currentSlide.value - 1 + heroSlides.value.length) %
        heroSlides.value.length
}

function goToSlide(index) {
    if (!heroSlides.value.length) return

    currentSlide.value = index
}

function startCarousel() {
    stopCarousel()

    if (heroSlides.value.length > 1) {
        timer = setInterval(nextSlide, 7000)
    }
}

function stopCarousel() {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}

onMounted(startCarousel)
onBeforeUnmount(stopCarousel)

/*
|--------------------------------------------------------------------------
| Why Heritage Benefits
|--------------------------------------------------------------------------
| These are intentionally retained as structured frontend content for now.
| They can later be moved into a dedicated homepage_benefits table.
|--------------------------------------------------------------------------
*/
const benefits = [
    {
        title: 'Proven Experience',
        text: 'Decades of experience in Nigeria’s capital markets and a deep understanding of evolving market conditions.',
        icon: 'star',
    },
    {
        title: 'Client Focused',
        text: 'We put your objectives, preferences and long-term financial goals at the centre of every engagement.',
        icon: 'users',
    },
    {
        title: 'Market Expertise',
        text: 'Research-driven insights and market intelligence support informed investment decisions.',
        icon: 'chart',
    },
    {
        title: 'Trust & Transparency',
        text: 'Clear communication, disciplined processes and a strong commitment to professional standards.',
        icon: 'shield',
    },
]

/*
|--------------------------------------------------------------------------
| Research / Insights
|--------------------------------------------------------------------------
| Static presentation content for now.
|--------------------------------------------------------------------------
*/
const researchItems = [
    {
        category: 'Market Insights',
        title: 'Understanding the Markets. Identifying the Opportunities.',
        text: 'Access timely market perspectives and informed analysis.',
        image: insightsImage,
        href: '/research-centre',
    },
    {
        category: 'Special Report',
        title: 'Research That Goes Deeper.',
        text: 'Explore analysis and perspectives across key market and investment areas.',
        image: coinsTreeImage,
        href: '/research-centre/reports',
    },
    {
        category: 'Market Perspective',
        title: 'Stay Ahead of the Conversation.',
        text: 'Discover news, commentary and insights from our research team.',
        image: futureImage,
        href: '/resources-news',
    },
]
</script>

<template>
    <PublicLayout>

        <!-- =========================================================
             HERO
        ========================================================== -->
        <section
            class="relative min-h-[610px] overflow-hidden bg-heritage-deep text-white lg:min-h-[680px]"
            @mouseenter="stopCarousel"
            @mouseleave="startCarousel"
        >

            <!-- Background -->
            <Transition
                enter-active-class="transition-opacity duration-700"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="absolute inset-0 transition-opacity duration-700"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    :key="currentSlide"
                    class="absolute inset-0 bg-cover bg-center"
                    :style="{
                        backgroundImage: `url(${assetUrl(slide.image)})`
                    }"
                ></div>
            </Transition>

            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-gradient-to-r from-heritage-deep/95 via-heritage-deep/70 to-heritage-deep/20"
            ></div>

            <!-- Content -->
            <div
                class="relative z-10 mx-auto flex min-h-[610px] max-w-[1400px] items-center px-6 py-20 lg:min-h-[680px] lg:px-12"
            >
                <div class="max-w-2xl pl-10 sm:pl-14 lg:pl-20 xl:pl-24">

                    <!-- Eyebrow -->
                    <div
                        v-if="slide.eyebrow"
                        class="mb-5 text-[10px] font-semibold uppercase tracking-[0.28em] text-heritage-gold sm:text-xs"
                    >
                        {{ slide.eyebrow }}
                    </div>

                    <!-- Title -->
                    <h1
                        class="max-w-2xl font-display text-4xl leading-[1.05] sm:text-5xl lg:text-6xl xl:text-7xl"
                    >
                        {{ slide.title }}
                    </h1>

                    <!-- Description -->
                    <p
                        v-if="slide.description"
                        class="mt-6 max-w-xl text-sm leading-7 text-white/75 sm:text-base"
                    >
                        {{ slide.description }}
                    </p>

                    <!-- Buttons -->
                    <div
                        v-if="slide.primary_label || slide.secondary_label"
                        class="mt-8 flex flex-wrap gap-3"
                    >
                        <a
                            v-if="slide.primary_label"
                            :href="slide.primary_url || '#'"
                            class="btn-gold"
                        >
                            {{ slide.primary_label }}
                            <span aria-hidden="true">→</span>
                        </a>

                        <a
                            v-if="slide.secondary_label"
                            :href="slide.secondary_url || '#'"
                            class="btn-outline"
                        >
                            {{ slide.secondary_label }}
                        </a>
                    </div>

                    <!-- Location -->
                    <div
                        v-if="slide.location_label || slide.location_text"
                        class="mt-12 border-l border-heritage-gold pl-4"
                    >
                        <div
                            v-if="slide.location_label"
                            class="text-[10px] font-semibold uppercase tracking-[0.2em] text-heritage-gold"
                        >
                            {{ slide.location_label }}
                        </div>

                        <div
                            v-if="slide.location_text"
                            class="mt-1 text-xs text-white/60"
                        >
                            {{ slide.location_text }}
                        </div>
                    </div>

                </div>
            </div>

            <!-- Previous -->
            <button
                v-if="heroSlides.length > 1"
                type="button"
                aria-label="Previous slide"
                @click="previousSlide"
                class="absolute left-4 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/20 text-white backdrop-blur-sm transition hover:border-heritage-gold hover:text-heritage-gold sm:left-6"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
            </button>

            <!-- Next -->
            <button
                v-if="heroSlides.length > 1"
                type="button"
                aria-label="Next slide"
                @click="nextSlide"
                class="absolute right-4 top-1/2 z-20 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/20 bg-black/20 text-white backdrop-blur-sm transition hover:border-heritage-gold hover:text-heritage-gold sm:right-6"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.7"
                        d="M9 5l7 7-7 7"
                    />
                </svg>
            </button>

            <!-- Indicators -->
            <div
                v-if="heroSlides.length > 1"
                class="absolute bottom-7 left-1/2 z-20 flex -translate-x-1/2 items-center gap-2"
            >
                <button
                    v-for="(_, index) in heroSlides"
                    :key="index"
                    type="button"
                    :aria-label="`Go to slide ${index + 1}`"
                    @click="goToSlide(index)"
                    class="h-1.5 rounded-full transition-all duration-300"
                    :class="
                        currentSlide === index
                            ? 'w-9 bg-heritage-gold'
                            : 'w-4 bg-white/40 hover:bg-white/70'
                    "
                ></button>
            </div>

        </section>


        <!-- =========================================================
             CREDIBILITY STRIP
        ========================================================== -->
        <section class="bg-heritage-navy text-white">

            <div class="mx-auto max-w-[1400px] px-6 lg:px-12">

                <div
                    class="grid divide-y divide-white/10 sm:grid-cols-2 sm:divide-x sm:divide-y-0 lg:grid-cols-4"
                >

                    <!-- Experience -->
                    <div class="flex items-center gap-4 px-4 py-5 sm:px-6">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-heritage-gold/50 text-heritage-gold"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.6"
                                    d="M12 6v6l4 2"
                                />
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="8"
                                    stroke-width="1.6"
                                />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-display text-base font-semibold lg:text-lg">
                                30+ Years
                            </h3>

                            <p class="mt-0.5 text-xs text-white/60">
                                Market Experience
                            </p>
                        </div>

                    </div>


                    <!-- Client Focus -->
                    <div class="flex items-center gap-4 px-4 py-5 sm:px-6">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-heritage-gold/50 text-heritage-gold"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.6"
                                    d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                                />
                                <circle
                                    cx="9"
                                    cy="7"
                                    r="4"
                                    stroke-width="1.6"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.6"
                                    d="M19 8v6M22 11h-6"
                                />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-display text-base font-semibold lg:text-lg">
                                Client Focused
                            </h3>

                            <p class="mt-0.5 text-xs text-white/60">
                                Solutions Built Around You
                            </p>
                        </div>

                    </div>


                    <!-- SEC -->
                    <div class="flex items-center gap-4 px-4 py-5 sm:px-6">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-heritage-gold/50 text-heritage-gold"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.6"
                                    d="M12 3l7 3v5c0 4.5-2.9 8.3-7 10-4.1-1.7-7-5.5-7-10V6l7-3Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.6"
                                    d="m9 12 2 2 4-4"
                                />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-display text-base font-semibold lg:text-lg">
                                SEC Regulated
                            </h3>

                            <p class="mt-0.5 text-xs text-white/60">
                                Professional Standards
                            </p>
                        </div>

                    </div>


                    <!-- Research -->
                    <div class="flex items-center gap-4 px-4 py-5 sm:px-6">

                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-heritage-gold/50 text-heritage-gold"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.6"
                                    d="M4 19V5M4 19h16M7 15l3-4 3 2 5-6"
                                />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-display text-base font-semibold lg:text-lg">
                                Research Driven
                            </h3>

                            <p class="mt-0.5 text-xs text-white/60">
                                Insights That Matter
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             MARKET OVERVIEW
        ========================================================== -->
        <section class="border-b border-heritage-border bg-white">

            <div class="mx-auto max-w-[1400px] px-6 py-6 lg:px-12">

                <div
                    class="grid gap-5 lg:grid-cols-[210px_1fr] lg:items-center"
                >

                    <div>

                        <div
                            class="text-[10px] font-semibold uppercase tracking-[0.2em] text-heritage-gold"
                        >
                            Market Overview
                        </div>

                        <h2
                            class="mt-1 font-display text-2xl text-heritage-navy"
                        >
                            Today's Markets
                        </h2>

                    </div>


                    <div class="grid grid-cols-2 gap-y-5 md:grid-cols-5">

                        <div
                            v-for="market in markets"
                            :key="market.id ?? market.name"
                            class="border-l border-heritage-border pl-4"
                        >

                            <div class="text-[11px] text-heritage-muted">
                                {{ market.name }}
                            </div>

                            <div
                                class="mt-1 text-sm font-semibold text-heritage-navy"
                            >
                                {{ market.value }}
                            </div>

                            <div
                                class="text-[10px] font-semibold"
                                :class="
                                    market.is_positive
                                        ? 'text-green-600'
                                        : 'text-red-500'
                                "
                            >
                                <span class="mr-1">
                                    {{ market.is_positive ? '▲' : '▼' }}
                                </span>

                                {{ market.is_positive ? '+' : '-' }}{{ Number(market.change_percent).toFixed(2) }}%
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             INVESTMENT SOLUTIONS
        ========================================================== -->
        <section class="bg-heritage-offwhite">

            <div class="mx-auto max-w-[1400px] px-6 py-14 lg:px-12">

                <div class="grid gap-8 lg:grid-cols-[270px_1fr]">

                    <!-- Intro -->
                    <div>

                        <div
                            class="text-[10px] font-semibold uppercase tracking-[0.2em] text-heritage-gold"
                        >
                            Invest With Confidence
                        </div>

                        <h2
                            class="mt-3 font-display text-4xl leading-tight text-heritage-navy"
                        >
                            Solutions for Today.<br />
                            A Stronger Tomorrow.
                        </h2>

                        <p
                            class="mt-4 text-sm leading-6 text-heritage-muted"
                        >
                            From wealth management to capital markets advisory,
                            we provide tailored solutions to help you grow,
                            protect and manage your wealth.
                        </p>

                        <a
                            href="/investment-solutions"
                            class="mt-6 inline-flex items-center gap-2 rounded bg-heritage-gold px-5 py-2.5 text-sm font-semibold text-heritage-deep transition hover:bg-heritage-gold-light"
                        >
                            Discover Our Solutions
                            <span>→</span>
                        </a>

                    </div>


                    <!-- Cards -->
                    <div class="grid gap-4 md:grid-cols-3">

                        <article
                            v-for="item in solutions"
                            :key="item.id"
                            class="overflow-hidden rounded-lg border border-heritage-border bg-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg"
                        >

                            <img
                                v-if="item.featured_image"
                                :src="assetUrl(item.featured_image)"
                                :alt="item.title"
                                class="h-44 w-full object-cover"
                                loading="lazy"
                            />

                            <div
                                v-else
                                class="flex h-44 items-center justify-center bg-heritage-navy text-heritage-gold"
                            >
                                <span class="font-display text-3xl">
                                    Heritage
                                </span>
                            </div>


                            <div class="p-5">

                                <h3
                                    class="font-display text-xl text-heritage-navy"
                                >
                                    {{ item.title }}
                                </h3>

                                <p
                                    class="mt-2 text-sm leading-6 text-heritage-muted"
                                >
                                    {{ item.short_description }}
                                </p>

                                <a
                                    :href="
                                        item.button_url ||
                                        (item.slug
                                            ? `/${item.slug}`
                                            : '/investment-solutions')
                                    "
                                    class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-heritage-gold"
                                >
                                    {{ item.button_label || 'Learn More' }}
                                    <span>→</span>
                                </a>

                            </div>

                        </article>

                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             WHY HERITAGE
        ========================================================== -->
        <section class="bg-white">

            <div class="mx-auto max-w-[1400px] px-6 py-14 lg:px-12">

                <!-- Section Header -->
                <div class="max-w-2xl">

                    <div
                        class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold"
                    >
                        {{ setting('homepage_why_eyebrow', 'Why Heritage') }}
                    </div>

                    <h2
                        class="mt-2 font-display text-4xl leading-tight text-heritage-navy sm:text-5xl"
                    >
                        {{ setting(
                            'homepage_why_title',
                            'Experience You Can Trust. Insight You Can Act On.'
                        ) }}
                    </h2>

                    <p
                        class="mt-4 text-sm leading-6 text-heritage-muted"
                    >
                        {{ setting(
                            'homepage_why_description',
                            'Our approach combines market knowledge, disciplined execution and a long-term commitment to our clients.'
                        ) }}
                    </p>

                </div>


                <!-- Benefits -->
                <div
                    class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4"
                >

                    <div
                        v-for="benefit in benefits"
                        :key="benefit.title"
                        class="rounded-lg border border-heritage-border bg-white p-6 transition duration-300 hover:-translate-y-1 hover:border-heritage-gold/60 hover:shadow-lg"
                    >

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-full bg-heritage-offwhite text-heritage-gold"
                        >

                            <!-- Star -->
                            <svg
                                v-if="benefit.icon === 'star'"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"
                                />
                            </svg>

                            <!-- Users -->
                            <svg
                                v-else-if="benefit.icon === 'users'"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"
                                />

                                <circle
                                    cx="9"
                                    cy="7"
                                    r="4"
                                    stroke-width="1.5"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"
                                />
                            </svg>

                            <!-- Chart -->
                            <svg
                                v-else-if="benefit.icon === 'chart'"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M4 19V5M4 19h16M7 15l3-4 3 2 5-6"
                                />
                            </svg>

                            <!-- Shield -->
                            <svg
                                v-else
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M12 3l7 3v5c0 4.5-2.9 8.3-7 10-4.1-1.7-7-5.5-7-10V6l7-3Z"
                                />

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="m9 12 2 2 4-4"
                                />
                            </svg>

                        </div>


                        <h3
                            class="mt-4 font-display text-xl text-heritage-navy"
                        >
                            {{ benefit.title }}
                        </h3>

                        <p
                            class="mt-2 text-sm leading-6 text-heritage-muted"
                        >
                            {{ benefit.text }}
                        </p>

                    </div>

                </div>


                <!-- Metrics -->
                <div
                    class="mt-8 grid grid-cols-2 border-y border-heritage-border md:grid-cols-4"
                >

                    <!-- Stat 1 -->
                    <div
                        class="border-b border-heritage-border px-5 py-5 text-center md:border-b-0 md:border-r"
                    >
                        <div
                            class="font-display text-3xl text-heritage-navy"
                        >
                            {{ setting('homepage_stat_1_value', '30+') }}
                        </div>

                        <div
                            class="mt-1 text-[10px] uppercase tracking-wider text-heritage-muted"
                        >
                            {{ setting(
                                'homepage_stat_1_label',
                                'Years Experience'
                            ) }}
                        </div>
                    </div>


                    <!-- Stat 2 -->
                    <div
                        class="border-b border-heritage-border px-5 py-5 text-center md:border-b-0 md:border-r"
                    >
                        <div
                            class="font-display text-3xl text-heritage-navy"
                        >
                            {{ setting('homepage_stat_2_value', '100+') }}
                        </div>

                        <div
                            class="mt-1 text-[10px] uppercase tracking-wider text-heritage-muted"
                        >
                            {{ setting(
                                'homepage_stat_2_label',
                                'Clients Served'
                            ) }}
                        </div>
                    </div>


                    <!-- Stat 3 -->
                    <div
                        class="border-b border-heritage-border px-5 py-5 text-center md:border-b-0 md:border-r"
                    >
                        <div
                            class="font-display text-3xl text-heritage-navy"
                        >
                            {{ setting('homepage_stat_3_value', '₦850B+') }}
                        </div>

                        <div
                            class="mt-1 text-[10px] uppercase tracking-wider text-heritage-muted"
                        >
                            {{ setting(
                                'homepage_stat_3_label',
                                'Assets Under Management'
                            ) }}
                        </div>
                    </div>


                    <!-- Stat 4 -->
                    <div
                        class="px-5 py-5 text-center"
                    >
                        <div
                            class="font-display text-3xl text-heritage-navy"
                        >
                            {{ setting('homepage_stat_4_value', '120+') }}
                        </div>

                        <div
                            class="mt-1 text-[10px] uppercase tracking-wider text-heritage-muted"
                        >
                            {{ setting(
                                'homepage_stat_4_label',
                                'Dedicated Professionals'
                            ) }}
                        </div>
                    </div>

                </div>

            </div>

        </section>


        <!-- =========================================================
             RESEARCH & INSIGHTS
        ========================================================== -->
        <section class="bg-heritage-offwhite">

            <div class="mx-auto max-w-[1400px] px-6 py-14 lg:px-12">

                <!-- Header -->
                <div
                    class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between"
                >

                    <div>

                        <div
                            class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold"
                        >
                            Research & Insights
                        </div>

                        <h2
                            class="mt-2 font-display text-4xl leading-tight text-heritage-navy sm:text-5xl"
                        >
                            Knowledge That Moves
                            <br />
                            With the Market.
                        </h2>

                    </div>

                    <a
                        href="/research-centre"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-heritage-navy transition hover:text-heritage-gold"
                    >
                        Explore Research

                        <span class="text-heritage-gold">
                            →
                        </span>
                    </a>

                </div>


                <!-- Research Layout -->
                <div
                    class="mt-10 grid gap-5 lg:grid-cols-[1.5fr_1fr]"
                >

                    <!-- Featured -->
                    <article
                        class="group overflow-hidden rounded-xl bg-heritage-navy text-white"
                    >

                        <div class="relative h-[390px] overflow-hidden">

                            <img
                                :src="researchItems[0].image"
                                :alt="researchItems[0].title"
                                class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                            />

                            <div
                                class="absolute inset-0 bg-gradient-to-t from-heritage-deep via-heritage-deep/40 to-transparent"
                            ></div>

                            <div
                                class="absolute inset-x-0 bottom-0 p-7 lg:p-9"
                            >

                                <div
                                    class="text-[10px] font-semibold uppercase tracking-[0.2em] text-heritage-gold"
                                >
                                    {{ researchItems[0].category }}
                                </div>

                                <h3
                                    class="mt-2 max-w-xl font-display text-3xl leading-tight sm:text-4xl"
                                >
                                    {{ researchItems[0].title }}
                                </h3>

                                <p
                                    class="mt-3 max-w-xl text-sm leading-6 text-white/70"
                                >
                                    {{ researchItems[0].text }}
                                </p>

                                <a
                                    :href="researchItems[0].href"
                                    class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-heritage-gold"
                                >
                                    Read More
                                    <span>→</span>
                                </a>

                            </div>

                        </div>

                    </article>


                    <!-- Side Cards -->
                    <div class="grid gap-5">

                        <article
                            v-for="item in researchItems.slice(1)"
                            :key="item.title"
                            class="group grid overflow-hidden rounded-xl border border-heritage-border bg-white sm:grid-cols-[170px_1fr] lg:grid-cols-1"
                        >

                            <div
                                class="h-48 overflow-hidden sm:h-full lg:h-40"
                            >

                                <img
                                    :src="item.image"
                                    :alt="item.title"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    loading="lazy"
                                />

                            </div>


                            <div class="p-5">

                                <div
                                    class="text-[10px] font-semibold uppercase tracking-[0.18em] text-heritage-gold"
                                >
                                    {{ item.category }}
                                </div>

                                <h3
                                    class="mt-2 font-display text-xl leading-tight text-heritage-navy"
                                >
                                    {{ item.title }}
                                </h3>

                                <p
                                    class="mt-2 text-sm leading-6 text-heritage-muted"
                                >
                                    {{ item.text }}
                                </p>

                                <a
                                    :href="item.href"
                                    class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-heritage-navy transition hover:text-heritage-gold"
                                >
                                    Explore

                                    <span class="text-heritage-gold">
                                        →
                                    </span>
                                </a>

                            </div>

                        </article>

                    </div>

                </div>


                <!-- Research Links -->
                <div class="mt-8 grid gap-3 md:grid-cols-3">

                    <a
                        href="/research-centre/market-overview"
                        class="flex items-center justify-between rounded border border-heritage-border bg-white px-5 py-4 text-sm font-medium text-heritage-navy transition hover:border-heritage-gold"
                    >
                        <span>
                            Today's Market
                        </span>

                        <span class="text-heritage-gold">
                            →
                        </span>
                    </a>


                    <a
                        href="/research-centre/reports"
                        class="flex items-center justify-between rounded border border-heritage-border bg-white px-5 py-4 text-sm font-medium text-heritage-navy transition hover:border-heritage-gold"
                    >
                        <span>
                            Reports & Analysis
                        </span>

                        <span class="text-heritage-gold">
                            →
                        </span>
                    </a>


                    <a
                        href="/resources-news"
                        class="flex items-center justify-between rounded border border-heritage-border bg-white px-5 py-4 text-sm font-medium text-heritage-navy transition hover:border-heritage-gold"
                    >
                        <span>
                            News & Insights
                        </span>

                        <span class="text-heritage-gold">
                            →
                        </span>
                    </a>

                </div>

            </div>

        </section>


        <!-- =========================================================
             ABOUT HERITAGE
        ========================================================== -->
        <section class="bg-white">

            <div
                class="mx-auto max-w-[1400px] px-6 py-14 lg:px-12"
            >

                <div
                    class="grid items-center gap-10 lg:grid-cols-[1fr_0.95fr]"
                >

                    <!-- Text -->
                    <div>

                        <div
                            class="text-[10px] font-semibold uppercase tracking-[0.22em] text-heritage-gold"
                        >
                            {{ setting(
                                'homepage_about_eyebrow',
                                'About Heritage'
                            ) }}
                        </div>

                        <h2
                            class="mt-2 font-display text-4xl leading-tight text-heritage-navy sm:text-5xl"
                        >
                            {{ setting(
                                'homepage_about_title',
                                'More Than a Firm. A Lasting Impact.'
                            ) }}
                        </h2>

                        <p
                            class="mt-4 max-w-xl text-sm leading-6 text-heritage-muted"
                        >
                            {{ setting(
                                'homepage_about_text_1',
                                'Heritage Capital Markets provides investment and capital markets solutions designed around the evolving needs of our clients.'
                            ) }}
                        </p>

                        <p
                            class="mt-3 max-w-xl text-sm leading-6 text-heritage-muted"
                        >
                            {{ setting(
                                'homepage_about_text_2',
                                'Our approach combines experience, market knowledge, disciplined execution and long-term relationships to help our clients navigate opportunities with confidence.'
                            ) }}
                        </p>

                        <a
                            href="/about-us"
                            class="mt-6 inline-flex items-center gap-2 rounded bg-heritage-navy px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-heritage-deep"
                        >
                            {{ setting(
                                'homepage_about_button',
                                'Discover Heritage'
                            ) }}

                            <span>→</span>
                        </a>

                    </div>


                    <!-- Image -->
                    <div class="relative overflow-hidden rounded-xl">

                        <img
                            :src="impactImage"
                            alt="Heritage Capital Markets team"
                            class="h-[430px] w-full object-cover"
                            loading="lazy"
                        />

                        <div
                            class="absolute bottom-0 left-0 h-1 w-28 bg-heritage-gold"
                        ></div>

                    </div>

                </div>

            </div>

        </section>

    </PublicLayout>
</template>