<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

defineProps({
    documents: {
        type: Array,
        default: () => [],
    },
})

const accountTypes = [
    { type: 'individual', title: 'Individual Account' },
    { type: 'corporate', title: 'Corporate Account' },
    { type: 'joint', title: 'Joint Account' },
    { type: 'minor', title: 'Minor Account' },
    { type: 'estate', title: 'Estate Account' },
]
</script>

<template>
    <PublicLayout>
        <SeoHead
            title="Downloads"
            description="Access corporate information, investment materials, research reports, and other resources from Heritage Capital Markets."
            canonical-path="/resources/downloads"
        />
        <section class="bg-heritage-navy pt-28 pb-20 text-white">
            <div class="container-heritage">
                <p class="mb-4 text-sm font-semibold uppercase tracking-[0.22em] text-heritage-gold">Resources</p>
                <h1 class="font-display text-4xl font-semibold leading-tight md:text-6xl">Downloads</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/75">
                    Access corporate information, investment materials and other resources from Heritage Capital Markets.
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage">
                <div class="mb-14">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#9A6B00]">Account opening</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold text-heritage-navy">Choose an application form</h2>
                    <p class="mt-3 max-w-2xl leading-7 text-slate-600">
                        Start an online application or download a blank printable form. Submitted online applications
                        are emailed to our administrators as PDFs for review.
                    </p>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        <article v-for="account in accountTypes" :key="account.type"
                            class="border border-slate-200 p-5">
                            <span class="block font-semibold text-heritage-navy">{{ account.title }}</span>
                            <a :href="`/open-account/${account.type}`"
                                class="mt-3 inline-flex text-sm font-semibold text-[#9A6B00] hover:underline">
                                Start online <span class="ml-2">→</span>
                            </a>
                            <a :href="`/resources/downloads/account-opening/${account.type}.pdf`"
                                class="mt-2 block text-sm font-semibold text-heritage-navy hover:underline">
                                Download blank PDF
                            </a>
                        </article>
                    </div>
                </div>

                <div class="mb-14 border-t border-slate-200 pt-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#9A6B00]">Documents</p>
                    <h2 class="mt-2 font-display text-3xl font-semibold text-heritage-navy">Downloadable forms & resources</h2>
                    <div v-if="documents.length" class="mt-6 divide-y divide-slate-200 border-y border-slate-200">
                        <article v-for="document in documents" :key="document.id"
                            class="flex flex-col gap-3 py-5 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-semibold text-heritage-navy">{{ document.name }}</h3>
                                <p v-if="document.caption" class="mt-1 text-sm leading-6 text-slate-600">{{ document.caption }}</p>
                            </div>
                            <a :href="`/resources/downloads/${document.id}/file`"
                                class="inline-flex shrink-0 font-semibold text-[#9A6B00] hover:underline">
                                Download document <span class="ml-2">↓</span>
                            </a>
                        </article>
                    </div>
                    <p v-else class="mt-5 text-sm text-slate-500">
                        No downloadable documents have been published yet. Online account-opening forms are available above.
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <article v-for="item in [
                        { title: 'Corporate Profile', text: 'Company information and an overview of Heritage Capital Markets.', action: 'Request Profile' },
                        { title: 'Investment Solutions', text: 'An overview of our investment and capital market services.', action: 'View Solutions' },
                        { title: 'Research & Reports', text: 'Market research and published reports available through our Research Centre.', action: 'Visit Research Centre' },
                    ]" :key="item.title" class="border border-slate-200 p-8 transition hover:-translate-y-1 hover:shadow-lg">
                        <div class="mb-8 flex h-12 w-12 items-center justify-center bg-heritage-offwhite text-heritage-navy">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <path d="M6 3h9l3 3v15H6z"/>
                                <path d="M14 3v4h4M9 12h6M9 16h6"/>
                            </svg>
                        </div>
                        <h2 class="font-display text-2xl font-semibold text-heritage-navy">{{ item.title }}</h2>
                        <p class="mt-3 leading-7 text-slate-600">{{ item.text }}</p>
                        <a
                            :href="item.title === 'Investment Solutions' ? '/investment-solutions' : item.title === 'Research & Reports' ? '/research-centre' : '/contact-us'"
                            class="mt-7 inline-flex font-semibold text-heritage-navy hover:text-[#9A6B00]"
                        >
                            {{ item.action }} <span class="ml-2">→</span>
                        </a>
                    </article>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
