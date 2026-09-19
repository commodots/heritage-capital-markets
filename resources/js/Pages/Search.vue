<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
    query: { type: String, default: '' },
    results: { type: Array, default: () => [] },
})

const search = ref(props.query)

function submit() {
    router.get('/search', { q: search.value }, { preserveState: true, replace: true })
}
</script>

<template>
    <PublicLayout>
        <section class="bg-[#061A33] pt-28 pb-16 text-white">
            <div class="container-heritage">
                <p class="mb-4 text-sm font-semibold uppercase tracking-[0.22em] text-[#F5B82E]">Search</p>
                <h1 class="font-display text-4xl font-semibold md:text-6xl">Search Heritage</h1>

                <form class="mt-8 flex max-w-3xl gap-3" @submit.prevent="submit">
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Search investment solutions, news and research..."
                        class="min-w-0 flex-1 border border-white/20 bg-white px-5 py-4 text-[#061A33] outline-none"
                    />
                    <button type="submit" class="btn-gold px-6">Search</button>
                </form>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage max-w-5xl">
                <div v-if="query" class="mb-8">
                    <p class="text-sm text-slate-500">
                        Search results for <span class="font-semibold text-[#061A33]">“{{ query }}”</span>
                    </p>
                </div>

                <div v-if="query && !results.length" class="border border-slate-200 bg-[#F7F8FA] p-10">
                    <h2 class="font-display text-2xl font-semibold text-[#061A33]">No results found</h2>
                    <p class="mt-3 text-slate-600">Try another search term or browse our investment solutions and resources.</p>
                </div>

                <div v-else-if="!query" class="border border-slate-200 p-10">
                    <h2 class="font-display text-2xl font-semibold text-[#061A33]">What are you looking for?</h2>
                    <p class="mt-3 leading-7 text-slate-600">Search across our investment solutions, news and research content.</p>
                </div>

                <div v-else class="space-y-4">
                    <a
                        v-for="result in results"
                        :key="`${result.type}-${result.url}`"
                        :href="result.url"
                        class="block border border-slate-200 p-6 transition hover:border-[#F5B82E] hover:shadow-sm"
                    >
                        <p class="text-xs font-bold uppercase tracking-[0.16em] text-[#9A6B00]">{{ result.type }}</p>
                        <h2 class="mt-2 font-display text-2xl font-semibold text-[#061A33]">{{ result.title }}</h2>
                        <p v-if="result.excerpt" class="mt-2 line-clamp-2 leading-7 text-slate-600">{{ result.excerpt }}</p>
                        <span class="mt-4 inline-flex font-semibold text-[#061A33]">View result →</span>
                    </a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
