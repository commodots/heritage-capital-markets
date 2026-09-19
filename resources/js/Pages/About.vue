<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';

import buildingImage from '@/assets/about/heritage-building.jpg';
import receptionImage from '@/assets/about/heritage-reception.jpg';
import teamImage from '@/assets/about/heritage-team.jpg';
import journeyImage from '@/assets/about/heritage-journey.jpg';

defineProps({
    page: {
        type: Object,
        default: () => ({}),
    },
});

const credibilityItems = [
    {
        title: 'Established Presence',
        text: 'Decades of experience in Nigeria’s capital markets.',
        icon: 'crown',
    },
    {
        title: 'Client Focused',
        text: 'Serving individuals, corporates and institutional clients.',
        icon: 'users',
    },
    {
        title: 'Investment Expertise',
        text: 'Comprehensive solutions across multiple asset classes.',
        icon: 'chart',
    },
    {
        title: 'SEC Regulated',
        text: 'Licensed and regulated by the Securities and Exchange Commission.',
        icon: 'shield',
    },
];

const stats = [
    {
        value: '30+',
        label: 'Years of',
        label2: 'Experience',
        icon: 'badge',
    },
    {
        value: '100+',
        label: 'Clients',
        label2: 'Served',
        icon: 'users',
    },
    {
        value: '₦850B+',
        label: 'Assets Under',
        label2: 'Management',
        icon: 'chart',
    },
    {
        value: '120+',
        label: 'Dedicated',
        label2: 'Professionals',
        icon: 'users',
    },
];

const values = [
    'Integrity',
    'Excellence',
    'Client Focus',
    'Innovation',
    'Teamwork',
];

const milestones = [
    {
        year: '1990s',
        title: 'Our foundation',
        text: 'Our foundation and early growth',
    },
    {
        year: '2000s',
        title: 'Expanded our',
        text: 'Expanded our product offerings',
    },
    {
        year: '2010s',
        title: 'Strengthened',
        text: 'Strengthened market presence',
    },
    {
        year: '2020s',
        title: 'Greater innovation',
        text: 'Greater innovation and digital transformation',
    },
    {
        year: 'Today',
        title: 'A stronger future',
        text: 'A stronger future for our clients',
    },
];
</script>

<template>
    <PublicLayout>

        <!-- =========================================================
             HERO
        ========================================================== -->
        <section class="relative min-h-[560px] overflow-hidden bg-heritage-navy lg:min-h-[575px]">

            <!-- Building -->
            <img
                :src="buildingImage"
                alt="Heritage Capital Markets"
                class="absolute inset-0 h-full w-full object-cover object-center"
            />

            <!-- Dark brand overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-heritage-deep via-heritage-navy/90 to-heritage-navy/20"></div>

            <!-- Subtle decorative lines -->
            <div class="hero-lines absolute -left-32 top-20 h-[400px] w-[400px] rounded-full border border-heritage-gold/10"></div>
            <div class="hero-lines absolute -left-24 top-28 h-[350px] w-[350px] rounded-full border border-heritage-gold/10"></div>
            <div class="hero-lines absolute -left-16 top-36 h-[300px] w-[300px] rounded-full border border-heritage-gold/10"></div>

            <div class="container-heritage relative z-10">

                <!-- Breadcrumb -->
                <div class="pt-5 text-xs text-white/65">
                    <a href="/" class="transition hover:text-heritage-gold">
                        Home
                    </a>

                    <span class="mx-2 text-white/30">›</span>

                    <span class="text-white/85">
                        About Us
                    </span>
                </div>

                <div class="grid min-h-[485px] items-center lg:grid-cols-[1fr_420px]">

                    <!-- Hero copy -->
                    <div class="max-w-[600px] pb-16 pt-12 lg:pb-20">

                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-heritage-gold">
                            Our Heritage. Your Advantage.
                        </p>

                        <h1 class="mt-3 font-display text-[42px] font-medium leading-[1.02] text-white sm:text-[52px] lg:text-[58px]">
                            A Legacy of Trust.
                            <span class="block text-heritage-gold">
                                A Future of Possibilities.
                            </span>
                        </h1>

                        <p class="mt-5 max-w-[510px] text-sm leading-6 text-white/90 sm:text-[15px]">
                            For over three decades, Heritage Capital Markets Limited
                            has been a trusted partner in Nigeria’s capital markets,
                            delivering innovative investment solutions and exceptional
                            value to our clients.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3">

                            <a
                                href="#our-story"
                                class="btn-gold"
                            >
                                Our Story
                                <span>→</span>
                            </a>

                            <a
                                href="/contact-us"
                                class="inline-flex items-center gap-2 rounded-[4px] border border-heritage-gold px-5 py-3 text-sm font-semibold text-white transition hover:bg-heritage-gold/10"
                            >
                                Talk to an Expert
                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                >
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.8 19.8 0 0 1 3.08 5.18 2 2 0 0 1 5.06 3h3a2 2 0 0 1 2 1.72c.12.9.33 1.78.62 2.63a2 2 0 0 1-.45 2.11L9 10.73a16 16 0 0 0 4.27 4.27l1.27-1.27a2 2 0 0 1 2.11-.45c.85.29 1.73.5 2.63.62A2 2 0 0 1 22 16.92Z" />
                                </svg>
                            </a>

                        </div>
                    </div>

                    <!-- Hero side labels -->
                    <div class="hidden self-start justify-self-end pt-12 text-right lg:block">
                        <div class="space-y-2 text-[10px] font-semibold uppercase tracking-[0.25em] text-heritage-navy">
                            <div>People</div>
                            <div>Expertise</div>
                            <div>Integrity</div>
                            <div>Impact</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Location tag -->
            <div class="absolute bottom-20 right-6 hidden rounded-md bg-heritage-deep/90 px-4 py-3 text-xs text-white shadow-lg lg:block">
                <div class="flex items-center gap-3">
                    <svg
                        class="h-6 w-6 text-white"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.6"
                    >
                        <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" />
                        <circle cx="12" cy="10" r="2.5" />
                    </svg>

                    <div>
                        <div class="font-medium">The Heritage Place</div>
                        <div class="text-white/70">Lagos, Nigeria</div>
                    </div>
                </div>
            </div>

        </section>


        <!-- =========================================================
             CREDIBILITY STRIP
        ========================================================== -->
        <section class="relative z-20 -mt-10 px-4">
            <div class="mx-auto max-w-[1140px]">

                <div class="grid overflow-hidden rounded-md border border-white/20 bg-heritage-navy shadow-xl md:grid-cols-2 lg:grid-cols-4">

                    <article
                        v-for="(item, index) in credibilityItems"
                        :key="item.title"
                        class="relative flex min-h-[90px] items-center gap-4 px-5 py-5"
                    >

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center text-heritage-gold">
                            <!-- Crown -->
                            <svg
                                v-if="item.icon === 'crown'"
                                class="h-9 w-9"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.4"
                            >
                                <path d="m3 7 4 4 5-7 5 7 4-4-2 12H5L3 7Z" />
                                <path d="M5 19h14" />
                            </svg>

                            <!-- Users -->
                            <svg
                                v-else-if="item.icon === 'users'"
                                class="h-9 w-9"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.4"
                            >
                                <circle cx="9" cy="8" r="3" />
                                <path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6" />
                                <circle cx="17" cy="9" r="2" />
                                <path d="M16 14c2.8.4 5 2.8 5 6" />
                            </svg>

                            <!-- Chart -->
                            <svg
                                v-else-if="item.icon === 'chart'"
                                class="h-9 w-9"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path d="M4 20V10" />
                                <path d="M10 20V5" />
                                <path d="M16 20V2" />
                                <path d="M22 20H2" />
                            </svg>

                            <!-- Shield -->
                            <svg
                                v-else
                                class="h-9 w-9"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path d="M12 3 20 6v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3Z" />
                                <path d="m9 12 2 2 4-4" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-display text-sm font-semibold text-white">
                                {{ item.title }}
                            </h3>

                            <p class="mt-1 text-[11px] leading-4 text-white/65">
                                {{ item.text }}
                            </p>
                        </div>

                        <div
                            v-if="index < credibilityItems.length - 1"
                            class="absolute bottom-4 right-0 top-4 hidden w-px bg-white/20 lg:block"
                        ></div>

                    </article>

                </div>
            </div>
        </section>


        <!-- =========================================================
             ABOUT HERITAGE
        ========================================================== -->
        <section
            id="our-story"
            class="bg-white py-20 lg:py-24"
        >
            <div class="container-heritage">

                <div class="grid items-center gap-10 lg:grid-cols-[.72fr_1.28fr]">

                    <!-- Text -->
                    <div class="relative z-10">

                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-heritage-gold">
                            About Heritage
                        </p>

                        <h2 class="mt-3 font-display text-4xl font-semibold leading-[1.05] text-heritage-navy sm:text-5xl">
                            More Than a Firm.
                            <span class="block">
                                A Lasting Impact.
                            </span>
                        </h2>

                        <div class="mt-5 space-y-4 text-sm leading-6 text-heritage-muted">
                            <p>
                                Founded with a clear purpose, Heritage Capital Markets Limited
                                has consistently focused on providing reliable, innovative and
                                client-centric investment solutions. We combine deep market
                                knowledge with global best practices to help our clients grow,
                                protect and manage their wealth across all market conditions.
                            </p>

                            <p>
                                Our commitment to integrity, professionalism and excellence
                                continues to drive our reputation as a leading capital markets
                                firm in Nigeria.
                            </p>
                        </div>

                        <a
                            href="#our-story"
                            class="btn-gold mt-6"
                        >
                            Learn More About Our Story
                            <span>→</span>
                        </a>

                        <!-- Quote -->
                        <div class="mt-8 flex gap-4">
                            <div class="font-display text-6xl leading-none text-heritage-gold">
                                “
                            </div>

                            <div>
                                <p class="font-display text-sm italic leading-6 text-heritage-navy">
                                    Our success is built on the trust of our clients,
                                    our people and the enduring values that guide us.
                                </p>

                                <div class="mt-4 flex items-center gap-3">
                                    <span class="h-px w-5 bg-heritage-gold"></span>
                                    <span class="text-[11px] font-medium text-heritage-navy">
                                        Management Team
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>


                    <!-- Image + Stats -->
                    <div class="relative">

                        <div class="overflow-hidden rounded-md">
                            <img
                                :src="receptionImage"
                                alt="The Heritage Place reception"
                                class="h-[430px] w-full object-cover object-center transition duration-700 hover:scale-[1.02]"
                            />
                        </div>

                        <div class="absolute -bottom-7 left-6 right-6 rounded-md border border-heritage-border bg-white px-5 py-5 shadow-xl lg:left-8 lg:right-8">

                            <div class="mb-3 flex items-center gap-2">
                                <h3 class="font-display text-lg font-semibold text-heritage-navy">
                                    At a Glance
                                </h3>

                                <span class="mt-2 h-px w-5 bg-heritage-gold"></span>
                            </div>

                            <div class="grid grid-cols-2 lg:grid-cols-4">

                                <div
                                    v-for="(item, index) in stats"
                                    :key="item.value"
                                    class="flex gap-3 px-3 py-2 first:pl-0 lg:border-r lg:border-heritage-border lg:last:border-r-0"
                                >

                                    <div class="hidden text-heritage-gold sm:block">

                                        <svg
                                            v-if="item.icon === 'badge'"
                                            class="h-7 w-7"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <circle cx="12" cy="9" r="5" />
                                            <path d="m9 13-1 8 4-2 4 2-1-8" />
                                            <path d="m10 9 1.3 1.3L14 7.5" />
                                        </svg>

                                        <svg
                                            v-else-if="item.icon === 'users'"
                                            class="h-7 w-7"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <circle cx="9" cy="8" r="3" />
                                            <path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6" />
                                            <circle cx="17" cy="9" r="2" />
                                        </svg>

                                        <svg
                                            v-else
                                            class="h-7 w-7"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <path d="M4 20V10" />
                                            <path d="M10 20V5" />
                                            <path d="M16 20V2" />
                                            <path d="M22 20H2" />
                                        </svg>

                                    </div>

                                    <div>
                                        <div class="font-display text-xl font-semibold text-heritage-navy">
                                            {{ item.value }}
                                        </div>

                                        <div class="text-[10px] leading-4 text-heritage-muted">
                                            {{ item.label }}<br />
                                            {{ item.label2 }}
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Image label -->
                        <div class="absolute bottom-0 right-0 hidden rounded-tl-md bg-heritage-deep/90 px-4 py-3 text-[10px] text-white sm:block">
                            A Professional Environment<br />
                            <span class="text-white/65">
                                Designed for Your Success
                            </span>
                        </div>

                    </div>

                </div>

            </div>
        </section>


        <!-- =========================================================
             MISSION / VISION / VALUES
        ========================================================== -->
        <section class="bg-[#f8f9fb] py-14 lg:py-16">
            <div class="container-heritage">

                <div class="grid lg:grid-cols-3">

                    <!-- Mission -->
                    <article class="flex gap-5 px-2 py-5 lg:border-r lg:border-heritage-border lg:pr-8">

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white shadow-sm">
                            <svg
                                class="h-8 w-8 text-heritage-gold"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <circle cx="12" cy="12" r="8" />
                                <circle cx="12" cy="12" r="4" />
                                <path d="m12 4 2-2M12 20l2 2M4 12H2M22 12h-2" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-display text-lg font-semibold text-heritage-navy">
                                Our Mission
                            </h3>

                            <p class="mt-2 text-xs leading-5 text-heritage-muted">
                                To create sustainable value for our clients through
                                innovative investment solutions, expert guidance and
                                unwavering commitment to excellence.
                            </p>
                        </div>

                    </article>


                    <!-- Vision -->
                    <article class="flex gap-5 px-2 py-5 lg:border-r lg:border-heritage-border lg:px-8">

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white shadow-sm">
                            <svg
                                class="h-8 w-8 text-heritage-gold"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
                                <circle cx="12" cy="12" r="2.5" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-display text-lg font-semibold text-heritage-navy">
                                Our Vision
                            </h3>

                            <p class="mt-2 text-xs leading-5 text-heritage-muted">
                                To be the most trusted and respected investment firm
                                in Africa, recognized for our integrity, expertise and
                                client-focused solutions.
                            </p>
                        </div>

                    </article>


                    <!-- Values -->
                    <article class="flex gap-5 px-2 py-5 lg:pl-8">

                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-white shadow-sm">
                            <svg
                                class="h-8 w-8 text-heritage-gold"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.4"
                            >
                                <path d="m12 2 4 4 6 1-4 5 1 7-7-3-7 3 1-7-4-5 6-1 4-4Z" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="font-display text-lg font-semibold text-heritage-navy">
                                Our Core Values
                            </h3>

                            <ul class="mt-2 space-y-1 text-xs text-heritage-muted">
                                <li
                                    v-for="value in values"
                                    :key="value"
                                    class="flex items-center gap-2"
                                >
                                    <span class="text-heritage-gold">✓</span>
                                    {{ value }}
                                </li>
                            </ul>
                        </div>

                    </article>

                </div>

            </div>
        </section>


        <!-- =========================================================
             JOURNEY
        ========================================================== -->
        <section class="relative overflow-hidden py-16 lg:py-20">

            <img
                :src="journeyImage"
                alt=""
                class="absolute inset-0 h-full w-full object-cover object-center"
            />

            <div class="absolute inset-0 bg-heritage-deep/90"></div>

            <div class="container-heritage relative z-10">

                <div class="grid gap-10 lg:grid-cols-[230px_1fr]">

                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-heritage-gold">
                            Our Journey
                        </p>

                        <h2 class="mt-2 font-display text-3xl text-white">
                            Key Milestones
                        </h2>

                        <p class="mt-3 text-xs leading-5 text-white/65">
                            A journey of growth, innovation and impact in Nigeria’s
                            capital markets.
                        </p>

                        <a
                            href="#our-story"
                            class="btn-gold mt-5"
                        >
                            Explore Our History
                            <span>→</span>
                        </a>
                    </div>


                    <!-- Timeline -->
                    <div class="relative pt-8">

                        <div class="absolute left-0 right-0 top-[52px] hidden h-px bg-white/60 lg:block"></div>

                        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-5">

                            <article
                                v-for="item in milestones"
                                :key="item.year"
                                class="relative"
                            >

                                <div class="relative z-10 mb-4 flex items-center lg:block">

                                    <span class="block h-3 w-3 rounded-full bg-heritage-gold ring-4 ring-heritage-gold/20"></span>

                                    <span class="ml-3 h-px flex-1 bg-white/20 lg:hidden"></span>

                                </div>

                                <div class="font-display text-xl text-white">
                                    {{ item.year }}
                                </div>

                                <p class="mt-2 text-[11px] font-medium text-white/85">
                                    {{ item.title }}
                                </p>

                                <p class="mt-1 text-[10px] leading-4 text-white/55">
                                    {{ item.text }}
                                </p>

                            </article>

                        </div>

                    </div>

                </div>

            </div>
        </section>


        <!-- =========================================================
             PEOPLE
        ========================================================== -->
        <section class="bg-white py-16 lg:py-20">
            <div class="container-heritage">

                <div class="grid items-center gap-8 lg:grid-cols-[.55fr_1fr_.48fr]">

                    <!-- Text -->
                    <div>

                        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-heritage-gold">
                            Our People
                        </p>

                        <h2 class="mt-2 font-display text-3xl font-semibold leading-tight text-heritage-navy">
                            Driven by Expertise.
                            <span class="block">
                                Focused on You.
                            </span>
                        </h2>

                        <p class="mt-4 text-xs leading-5 text-heritage-muted">
                            Our team of experienced professionals brings deep market
                            knowledge, analytical rigor and a client-focused mindset
                            to every engagement.
                        </p>

                        <a
                            href="#"
                            class="btn-gold mt-5"
                        >
                            Meet Our Team
                            <span>→</span>
                        </a>

                    </div>


                    <!-- Team image -->
                    <div class="relative overflow-hidden rounded-md">
                        <img
                            :src="teamImage"
                            alt="Heritage Capital Markets team"
                            class="h-[250px] w-full object-cover"
                        />

                        <div class="absolute bottom-0 left-0 right-0 bg-heritage-deep/80 px-4 py-3 text-[10px] text-white">
                            Collaboration. Insight. Better Outcomes.
                        </div>
                    </div>


                    <!-- Careers -->
                    <div class="relative min-h-[250px] overflow-hidden rounded-md bg-heritage-gold">

                        <div class="absolute inset-0 opacity-10">
                            <img
                                :src="receptionImage"
                                alt=""
                                class="h-full w-full object-cover"
                            />
                        </div>

                        <div class="relative flex h-full flex-col justify-between p-7">

                            <h3 class="max-w-[180px] font-display text-2xl font-semibold leading-tight text-heritage-deep">
                                Join a Team
                                <span class="block">
                                    That Makes
                                </span>
                                <span class="block">
                                    a Difference.
                                </span>
                            </h3>

                            <a
                                href="#"
                                class="inline-flex w-fit items-center gap-2 rounded-[4px] bg-heritage-deep px-5 py-3 text-xs font-semibold text-white transition hover:bg-heritage-navy"
                            >
                                View Careers
                                <span>→</span>
                            </a>

                        </div>

                    </div>

                </div>

            </div>
        </section>


       

    </PublicLayout>
</template>

<style scoped>
.hero-lines {
    pointer-events: none;
}

@media (max-width: 767px) {
    .hero-lines {
        display: none;
    }
}
</style>