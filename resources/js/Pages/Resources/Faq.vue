<script setup>
import { computed, ref } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    faqs: {
        type: Array,
        default: () => [],
    },
})

const openIndex = ref(null)

const groups = computed(() => {
    const grouped = {}

    props.faqs.forEach((faq) => {
        const category = faq.category || 'General'
        if (!grouped[category]) grouped[category] = []
        grouped[category].push(faq)
    })

    return Object.entries(grouped).map(([name, items]) => ({ name, items }))
})

function toggle(index) {
    openIndex.value = openIndex.value === index ? null : index
}
</script>

<template>
    <PublicLayout>
        <section class="bg-[#061A33] pt-28 pb-20 text-white">
            <div class="container-heritage">
                <p class="mb-4 text-sm font-semibold uppercase tracking-[0.22em] text-[#F5B82E]">Resources</p>
                <h1 class="font-display text-4xl font-semibold leading-tight md:text-6xl">Frequently Asked Questions</h1>
                <p class="mt-6 max-w-2xl text-lg leading-8 text-white/75">
                    Find answers to common questions about Heritage Capital Markets, our investment solutions and how to engage our team.
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage max-w-4xl">
                <div v-if="!groups.length" class="rounded-xl border border-slate-200 bg-slate-50 p-10 text-center">
                    <h2 class="font-display text-2xl font-semibold text-[#061A33]">FAQs are being prepared</h2>
                    <p class="mt-3 text-slate-600">Please contact our team if you need assistance.</p>
                    <a href="/contact-us" class="btn-gold mt-6">Contact Us</a>
                </div>

                <div v-for="group in groups" :key="group.name" class="mb-12">
                    <div class="mb-5">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#F5B82E]">{{ group.name }}</p>
                        <div class="mt-2 h-px bg-slate-200"></div>
                    </div>

                    <div class="divide-y divide-slate-200 border-y border-slate-200">
                        <div v-for="(faq, itemIndex) in group.items" :key="faq.id">
                            <button
                                type="button"
                                class="flex w-full items-center justify-between gap-6 py-6 text-left"
                                @click="toggle(`${group.name}-${itemIndex}`)"
                            >
                                <span class="text-base font-semibold text-[#061A33] md:text-lg">{{ faq.question }}</span>
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-[#F5B82E] text-[#061A33]">
                                    {{ openIndex === `${group.name}-${itemIndex}` ? '−' : '+' }}
                                </span>
                            </button>

                            <div
                                v-if="openIndex === `${group.name}-${itemIndex}`"
                                class="pb-6 pr-12 text-sm leading-7 text-slate-600"
                                v-html="faq.answer"
                            />
                        </div>
                    </div>
                </div>

                <div class="mt-16 bg-[#F7F8FA] p-8 md:p-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#F5B82E]">Need more information?</p>
                    <h2 class="mt-3 font-display text-3xl font-semibold text-[#061A33]">Talk to our team</h2>
                    <p class="mt-3 max-w-xl leading-7 text-slate-600">
                        If you cannot find what you are looking for, send us an enquiry and our team will assist you.
                    </p>
                    <a href="/contact-us" class="btn-gold mt-6">Contact Us</a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
