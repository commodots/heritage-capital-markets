<script setup>
import { ref } from 'vue'
import heritageLogo from '@/assets/logo/heritage-logo.png'

const mobileOpen = ref(false)

const toggleMobile = () => {
    mobileOpen.value = !mobileOpen.value
}

const closeMobile = () => {
    mobileOpen.value = false
}
</script>

<template>
    <header class="sticky top-0 z-50 bg-heritage-navy text-white shadow-lg">

        <div class="mx-auto flex h-[74px] max-w-[1440px] items-center px-4 sm:px-6 lg:px-8">

            <!-- Logo -->
            <a href="/" class="flex w-[205px] shrink-0 items-center" @click="closeMobile">
                <div class="flex items-center">
                    <img :src="heritageLogo" alt="Heritage Capital Markets Limited" class="h-10 w-auto max-w-full object-contain">

                  
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden flex-1 items-center justify-center gap-3 xl:gap-5 lg:flex">

                <!-- Home -->
                <a
                    href="/"
                    class="relative flex h-[74px] items-center whitespace-nowrap px-1 text-sm font-medium transition hover:text-heritage-gold"
                >
                    Home
                    <span class="absolute bottom-0 left-0 h-[3px] w-full bg-heritage-gold"></span>
                </a>

                <!-- About -->
                <a
                    href="/about-us"
                    class="flex h-[74px] items-center whitespace-nowrap px-1 text-sm font-medium transition hover:text-heritage-gold"
                >
                    About Us
                </a>

                <!-- Investment Solutions -->
                <div class="group relative">

                    <button
                        class="flex h-[74px] items-center gap-1 whitespace-nowrap px-1 text-sm font-medium hover:text-heritage-gold"
                        type="button"
                    >
                        Investment Solutions

                        <svg
                            class="h-3.5 w-3.5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>

                    <div
                        class="invisible absolute left-0 top-full w-64 translate-y-2 rounded-b-md bg-white py-3 text-heritage-text opacity-0 shadow-xl transition-all group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"
                    >

                        <a
                            href="/investment-solutions"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            All Investment Solutions
                        </a>

                        <a
                            href="/investment-solutions/portfolio-management"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Portfolio Management
                        </a>

                        <a
                            href="/investment-solutions/investment-advisory"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Investment Advisory
                        </a>

                        <a
                            href="/investment-solutions/brokerage-dealing"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Brokerage & Dealing
                        </a>

                        <a
                            href="/investment-solutions/fixed-income-investments"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Fixed Income Investments
                        </a>

                        <a
                            href="/investment-solutions/alternative-investments"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Alternative Investments
                        </a>

                        <a
                            href="/investment-solutions/financial-planning"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Financial Planning
                        </a>

                    </div>
                </div>

                <!-- Research Centre -->
                <div class="group relative">

                    <button
                        class="flex h-[74px] items-center gap-1 whitespace-nowrap px-1 text-sm font-medium hover:text-heritage-gold"
                        type="button"
                    >
                        Research Centre

                        <svg
                            class="h-3.5 w-3.5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 0l-4.25-4.51a.75.75 0 01-.02-1.06z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>

                    <div
                        class="invisible absolute left-0 top-full w-56 translate-y-2 rounded-b-md bg-white py-3 text-heritage-text opacity-0 shadow-xl transition-all group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"
                    >

                        <a
                            href="/research-centre"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Research Centre
                        </a>

                        <!-- Keep these ready for the Research batch -->
                        <a
                            href="/research-centre/"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Market Overview
                        </a>

                        <a
                            href="/research-centre/"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Research Reports
                        </a>

                    </div>
                </div>

                <!-- Resources -->
                <div class="group relative">

                    <button
                        class="flex h-[74px] items-center gap-1 whitespace-nowrap px-1 text-sm font-medium hover:text-heritage-gold"
                        type="button"
                    >
                        Resources

                        <svg
                            class="h-3.5 w-3.5"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.51a.75.75 0 01-1.08 1.04l-4.25-4.51a.75.75 0 01-1.08 1.04l-4.25-4.51a.75.75 0 01-.02-1.06z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>

                    <div
                        class="invisible absolute left-0 top-full w-52 translate-y-2 rounded-b-md bg-white py-3 text-heritage-text opacity-0 shadow-xl transition-all group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"
                    >

                        <!-- Planned Resources page -->
                        <a
                            href="/resources-news"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            News & Insights
                        </a>

                        <a
                            href="/resources/downloads"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            Downloads
                        </a>

                        <a
                            href="/resources/faqs"
                            class="block px-5 py-2.5 text-sm hover:bg-gray-50 hover:text-heritage-gold"
                        >
                            FAQs
                        </a>

                    </div>
                </div>

                <!-- Contact -->
                <a
                    href="/contact-us"
                    class="flex h-[74px] items-center whitespace-nowrap px-1 text-sm font-medium transition hover:text-heritage-gold"
                >
                    Contact Us
                </a>

            </nav>

            <!-- Right Actions -->
            <div class="ml-3 hidden shrink-0 items-center gap-2 lg:flex">

                <!-- Search -->
                <button
                    class="flex h-10 w-10 items-center justify-center rounded-full hover:bg-white/10"
                    aria-label="Search"
                    type="button"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                    >
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-4-4"></path>
                    </svg>
                </button>

                <!-- Open Account -->
                <a
                    href="/open-account"
                    class="whitespace-nowrap rounded border border-heritage-gold px-4 py-2.5 text-sm font-semibold transition hover:bg-heritage-gold hover:text-heritage-navy"
                >
                    Open an Account
                </a>

                <!-- Investor Login -->
                <a
                    href="/investor-login"
                    class="flex items-center gap-2 whitespace-nowrap rounded bg-heritage-gold px-4 py-2.5 text-sm font-semibold text-heritage-navy transition hover:bg-heritage-gold-light"
                >
                    <svg
                        class="h-4 w-4"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        viewBox="0 0 24 24"
                    >
                        <circle cx="12" cy="8" r="3.5"></circle>
                        <path d="M5 20c.8-3.2 3.2-5 7-5s6.2 1.8 7 5"></path>
                    </svg>

                    Investor Login
                </a>

            </div>

            <!-- Mobile Button -->
            <button
                @click="toggleMobile"
                class="flex h-10 w-10 items-center justify-center rounded border border-white/20 lg:hidden"
                aria-label="Open menu"
                type="button"
            >
                <svg
                    v-if="!mobileOpen"
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>

                <svg
                    v-else
                    class="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-width="2" d="M6 6l12 12M6 18L18 6"/>
                </svg>
            </button>

        </div>

        <!-- Mobile Navigation -->
        <div
            v-if="mobileOpen"
            class="border-t border-white/10 bg-heritage-deep px-5 py-5 lg:hidden"
        >

            <div class="space-y-1">

                <a
                    href="/"
                    @click="closeMobile"
                    class="block rounded px-3 py-3 hover:bg-white/5"
                >
                    Home
                </a>

                <a
                    href="/about-us"
                    @click="closeMobile"
                    class="block rounded px-3 py-3 hover:bg-white/5"
                >
                    About Us
                </a>

                <!-- Investment Solutions -->
                <div class="border-t border-white/10 pt-2">

                    <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-heritage-gold">
                        Investment Solutions
                    </div>

                    <a
                        href="/investment-solutions"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        All Investment Solutions
                    </a>

                    <a
                        href="/investment-solutions/portfolio-management"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Portfolio Management
                    </a>

                    <a
                        href="/investment-solutions/investment-advisory"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Investment Advisory
                    </a>

                    <a
                        href="/investment-solutions/brokerage-dealing"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Brokerage & Dealing
                    </a>

                    <a
                        href="/investment-solutions/fixed-income-investments"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Fixed Income Investments
                    </a>

                    <a
                        href="/investment-solutions/alternative-investments"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Alternative Investments
                    </a>

                    <a
                        href="/investment-solutions/financial-planning"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Financial Planning
                    </a>

                </div>

                <!-- Research Centre -->
                <div class="border-t border-white/10 pt-2">

                    <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-heritage-gold">
                        Research Centre
                    </div>

                    <a
                        href="/research-centre"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Research Centre
                    </a>

                    <a
                        href="/research-centre"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Market Overview
                    </a>

                    <a
                        href="/research-centre"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Research Reports
                    </a>

                </div>

                <!-- Resources -->
                <div class="border-t border-white/10 pt-2">

                    <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-heritage-gold">
                        Resources
                    </div>

                    <a
                        href="/resources-news"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        News & Insights
                    </a>

                    <a
                        href="/resources/downloads"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        Downloads
                    </a>

                    <a
                        href="/resources/faqs"
                        @click="closeMobile"
                        class="block rounded px-3 py-2.5 hover:bg-white/5"
                    >
                        FAQs
                    </a>

                </div>

                <!-- Contact -->
                <a
                    href="/contact-us"
                    @click="closeMobile"
                    class="block rounded px-3 py-3 hover:bg-white/5"
                >
                    Contact Us
                </a>

                <!-- Actions -->
                <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2">

                    <a
                        href="/open-account"
                        @click="closeMobile"
                        class="rounded border border-heritage-gold px-4 py-3 text-center font-semibold"
                    >
                        Open an Account
                    </a>

                    <a
                        href="/investor-login"
                        @click="closeMobile"
                        class="rounded bg-heritage-gold px-4 py-3 text-center font-semibold text-heritage-navy"
                    >
                        Investor Login
                    </a>

                </div>

            </div>
        </div>

    </header>
</template>