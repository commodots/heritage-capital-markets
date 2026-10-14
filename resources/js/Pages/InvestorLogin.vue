<script setup>
import { Head } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const portalUrl = 'https://heritagecapital.xcloudintegra.com/LoginInnovationbedai.aspx?ReturnUrl=%2f'
</script>

<template>
    <Head title="Investor Login" />

    <PublicLayout>
        <section class="relative overflow-hidden bg-heritage-navy text-white">
            <div class="absolute inset-0 bg-linear-to-br from-heritage-navy via-[#0A2848] to-[#102F4F]"></div>

            <div class="relative mx-auto max-w-7xl px-6 py-16 md:py-20">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-heritage-gold">
                    Investor Services
                </p>

                <h1 class="mt-4 font-display text-4xl font-semibold md:text-5xl">
                    Investor Portal
                </h1>

                <p class="mt-5 max-w-2xl text-base leading-7 text-white/75">
                    Access your investment account through the Heritage Capital Markets investor portal to view your account and available transaction services.
                </p>
            </div>
        </section>

        <section class="bg-heritage-offwhite px-6 py-16 md:py-24">
            <div class="mx-auto grid max-w-6xl items-center gap-12 lg:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[#9A6B00]">
                        Secure Account Access
                    </p>

                    <h2 class="mt-4 font-display text-3xl font-semibold leading-tight text-heritage-navy md:text-4xl">
                        Your investments, within reach.
                    </h2>

                    <p class="mt-5 max-w-lg text-sm leading-7 text-slate-600">
                        Sign in through our investor portal using the credentials provided for your account. Additional identity verification may be required.
                    </p>

                    <div class="mt-8 space-y-4">
                        <div class="flex gap-3">
                            <span class="text-[#9A6B00]" aria-hidden="true">✓</span>
                            <p class="text-sm text-slate-700">Access the existing investor portal.</p>
                        </div>

                        <div class="flex gap-3">
                            <span class="text-[#9A6B00]" aria-hidden="true">✓</span>
                            <p class="text-sm text-slate-700">
                                Continue through the provider's authentication and verification steps.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border border-slate-200 bg-white p-7 shadow-sm md:p-10">
                    <div class="mb-7 border-b border-slate-200 pb-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#9A6B00]">
                            Heritage Capital Markets
                        </p>

                        <h2 class="mt-3 text-2xl font-semibold text-heritage-navy">
                            Sign in to your account
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Access your account through the Heritage Capital Markets investor portal.
                        </p>
                    </div>

                    <div class="space-y-5">
                        <label for="investor-username" class="block">
                            <span class="text-sm font-semibold text-heritage-navy">Username</span>
                            <input
                                id="investor-username"
                                type="text"
                                autocomplete="off"
                                placeholder="Enter your username"
                                class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm text-heritage-navy outline-none focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/30"
                            >
                        </label>

                        <label for="investor-password" class="block">
                            <span class="text-sm font-semibold text-heritage-navy">Password</span>
                            <input
                                id="investor-password"
                                type="password"
                                autocomplete="off"
                                placeholder="Enter your password"
                                class="mt-2 w-full border border-slate-300 px-4 py-3 text-sm text-heritage-navy outline-none focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/30"
                            >
                        </label>
                    </div>

                    <a
                        :href="portalUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-5 flex w-full items-center justify-center bg-heritage-gold px-6 py-4 text-sm font-bold text-heritage-navy transition hover:bg-[#E6A91D] focus:outline-none focus:ring-2 focus:ring-heritage-navy focus:ring-offset-2"
                    >
                        Continue to Investor Portal
                        <span class="ml-2" aria-hidden="true">↗</span>
                    </a>

                    <p class="mt-5 text-center text-xs leading-5 text-slate-500">
                        You will continue to the external portal to complete sign-in.
                    </p>

                    <div class="mt-7 border-t border-slate-200 pt-5">
                        <p class="text-sm text-slate-600">
                            Need assistance accessing your account?
                        </p>

                        <a
                            href="/contact-us"
                            class="mt-2 inline-block text-sm font-semibold text-[#9A6B00] hover:underline"
                        >
                            Contact Heritage Capital Markets
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
