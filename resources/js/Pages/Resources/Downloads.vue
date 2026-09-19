<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
</script>

<template>
    <PublicLayout>
        <section class="bg-[#061A33] pt-28 pb-20 text-white">
            <div class="container-heritage">
                <p class="mb-4 text-sm font-semibold uppercase tracking-[0.22em] text-[#F5B82E]">Resources</p>
                <h1 class="font-display text-4xl font-semibold leading-tight md:text-6xl">Downloads</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/75">
                    Access corporate information, investment materials and other resources from Heritage Capital Markets.
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage">
                <div class="grid gap-6 md:grid-cols-3">
                    <article v-for="item in [
                        { title: 'Corporate Profile', text: 'Company information and an overview of Heritage Capital Markets.', action: 'Request Profile' },
                        { title: 'Investment Solutions', text: 'An overview of our investment and capital market services.', action: 'View Solutions' },
                        { title: 'Research & Reports', text: 'Market research and published reports available through our Research Centre.', action: 'Visit Research Centre' },
                    ]" :key="item.title" class="border border-slate-200 p-8 transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="mb-8 flex h-12 w-12 items-center justify-center bg-[#F7F8FA] text-[#061A33]">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M6 3h9l3 3v15H6z"/>
                                <path d="M14 3v4h4M9 12h6M9 16h6"/>
                            </svg>
                        </div>
                        <h2 class="font-display text-2xl font-semibold text-[#061A33]">{{ item.title }}</h2>
                        <p class="mt-3 leading-7 text-slate-600">{{ item.text }}</p>
                        <a
                            :href="item.title === 'Investment Solutions' ? '/investment-solutions' : item.title === 'Research & Reports' ? '/research-centre' : '/contact-us'"
                            class="mt-7 inline-flex font-semibold text-[#061A33] hover:text-[#9A6B00]"
                        >
                            {{ item.action }} <span class="ml-2">→</span>
                        </a>
                    </article>
                </div>

                <div class="mt-14 border-l-4 border-[#F5B82E] bg-[#F7F8FA] p-6 md:p-8">
                    <p class="font-semibold text-[#061A33]">Document library</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        The downloadable document library can be connected to the Media Library during the CMS implementation phase.
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
