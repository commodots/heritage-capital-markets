<script setup>
import { ref } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import teamImage from '@/assets/about/heritage-team.jpg'
import receptionImage from '@/assets/about/heritage-reception.jpg'


const memberNumbers = Array.from({ length: 12 }, (_, i) => i + 1)


const expandedBios = ref({})

const toggleBio = (number) => {
    expandedBios.value[number] = !expandedBios.value[number]
}

function imageUrl(path) {
    if (!path) return ''
    const cleanPath = path.replace(/\.(jpg|jpeg|png|webp)\.(jpg|jpeg|png|webp)$/i, '.$1')
    return cleanPath.startsWith('/') ? cleanPath : `/storage/${cleanPath}`
}

defineProps({
    content: {
        type: Object,
        default: () => ({}),
    },
})
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="content.board_title"
            :description="content.board_intro"
            canonical-path="/board-management"
        />

        <section class="bg-heritage-deep py-24 text-white">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-heritage-gold">{{ content.board_eyebrow }}</p>
                <h1 class="mt-4 max-w-3xl font-display text-4xl font-semibold leading-tight md:text-6xl">
                    {{ content.board_title }}
                </h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-white/75">
                    {{ content.board_intro }}
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-heritage-gold">{{ content.board_leadership_eyebrow }}</p>
                    <h2 class="mt-3 font-display text-3xl font-semibold text-heritage-navy md:text-4xl">
                        {{ content.board_leadership_title }}
                    </h2>
                    <p class="mt-5 leading-7 text-heritage-muted">
                        {{ content.board_leadership_body }}
                    </p>
                    <p class="mt-4 leading-7 text-heritage-muted">
                        {{ content.board_profiles_note }}
                    </p>
                </div>
                <img :src="teamImage" alt="Heritage Capital Markets team" class="h-72 w-full rounded-md object-cover md:h-96" />
            </div>
        </section>

        <section v-if="memberNumbers.some(number => content[`board_member_${number}_name`])" class="bg-heritage-offwhite py-16 md:py-20">
            <div class="container-heritage">
                <h2 class="font-display text-3xl font-semibold text-heritage-navy md:text-4xl">Meet the team</h2>
                <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <template v-for="number in memberNumbers" :key="number">
                        <article
                            v-if="content[`board_member_${number}_name`]"
                            class="rounded border border-heritage-border bg-white p-6 flex flex-col justify-between"
                        >
                            <div>
                                <img
                                    v-if="content[`board_member_${number}_image`]"
                                    :src="content[`board_member_${number}_image`].startsWith('/') ? content[`board_member_${number}_image`] : `/storage/${content[`board_member_${number}_image`]}`"
                                    :alt="content[`board_member_${number}_name`]"
                                    class="mb-5 h-64 w-full rounded object-cover object-top sm:h-72 lg:h-80 max-w-full transform-gpu"
                                />
                                <div
                                    v-else
                                    class="mb-5 flex h-64 items-center justify-center rounded bg-heritage-offwhite font-display text-5xl text-heritage-gold"
                                    aria-hidden="true"
                                >
                                    {{ content[`board_member_${number}_name`].charAt(0) }}
                                </div>
                                <h3 class="font-display text-xl font-semibold text-heritage-navy">
                                    {{ content[`board_member_${number}_name`] }}
                                </h3>
                                <p v-if="content[`board_member_${number}_role`]" class="mt-1 text-sm font-semibold text-heritage-gold">
                                    {{ content[`board_member_${number}_role`] }}
                                </p>
                                
                                <div v-if="content[`board_member_${number}_bio`]">
                                    <p class="mt-4 leading-7 text-heritage-muted" :class="{ 'line-clamp-3': !expandedBios[number] }">
                                        {{ content[`board_member_${number}_bio`] }}
                                    </p>
                                    <button
                                        @click="toggleBio(number)"
                                        class="mt-2 text-sm font-semibold text-heritage-gold hover:underline focus:outline-none"
                                    >
                                        {{ expandedBios[number] ? 'See less' : 'See more' }}
                                    </button>
                                </div>
                            </div>
                        </article>
                    </template>
                </div>
            </div>
        </section>

        <section id="careers" class="scroll-mt-24 bg-heritage-offwhite py-16 md:py-24">
            <div class="container-heritage grid items-center gap-10 lg:grid-cols-2">
                <div class="order-2 overflow-hidden rounded-md lg:order-1">
                    <img :src="receptionImage" alt="Heritage Capital Markets office reception" class="h-72 w-full object-cover md:h-96" />
                </div>
                <div class="order-1 lg:order-2">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-heritage-gold">{{ content.board_careers_eyebrow }}</p>
                    <h2 class="mt-3 font-display text-3xl font-semibold text-heritage-navy md:text-4xl">
                        {{ content.board_careers_title }}
                    </h2>
                    <p class="mt-5 leading-7 text-heritage-muted">
                        {{ content.board_careers_body }}
                    </p>
                    <a href="/contact-us" class="mt-7 inline-flex items-center gap-2 rounded bg-heritage-gold px-6 py-3 font-semibold text-heritage-deep transition hover:bg-heritage-gold-light">
                        {{ content.board_careers_button }} <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>