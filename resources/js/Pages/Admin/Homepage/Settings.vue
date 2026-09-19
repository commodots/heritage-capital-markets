<script setup>
import { computed, reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    settings: {
        type: Object,
        default: () => ({}),
    },
})

const processing = ref(false)

const setting = (key, fallback = '') => {
    return props.settings?.[key] ?? fallback
}

const form = reactive({
    homepage_why_eyebrow: setting('homepage_why_eyebrow', 'WHY HERITAGE'),
    homepage_why_title: setting(
        'homepage_why_title',
        'Built on Trust. Driven by Insight.'
    ),
    homepage_why_description: setting(
        'homepage_why_description',
        'We combine deep market knowledge, disciplined execution and a client-first approach to help investors make informed financial decisions.'
    ),

    homepage_stat_1_value: setting('homepage_stat_1_value', '30+'),
    homepage_stat_1_label: setting('homepage_stat_1_label', 'Years Experience'),

    homepage_stat_2_value: setting('homepage_stat_2_value', '100+'),
    homepage_stat_2_label: setting('homepage_stat_2_label', 'Clients Served'),

    homepage_stat_3_value: setting('homepage_stat_3_value', '₦850B+'),
    homepage_stat_3_label: setting(
        'homepage_stat_3_label',
        'Assets Under Management'
    ),

    homepage_stat_4_value: setting('homepage_stat_4_value', '120+'),
    homepage_stat_4_label: setting(
        'homepage_stat_4_label',
        'Dedicated Professionals'
    ),

    homepage_about_eyebrow: setting(
        'homepage_about_eyebrow',
        'ABOUT HERITAGE CAPITAL MARKETS'
    ),
    homepage_about_title: setting(
        'homepage_about_title',
        'A Trusted Partner for Your Financial Future'
    ),
    homepage_about_text_1: setting(
        'homepage_about_text_1',
        'Heritage Capital Markets Limited provides investment and financial services designed to help individuals, institutions and businesses navigate the opportunities and complexities of the capital market.'
    ),
    homepage_about_text_2: setting(
        'homepage_about_text_2',
        'Our approach combines market intelligence, professional expertise and disciplined execution to deliver solutions aligned with our clients’ objectives.'
    ),
    homepage_about_button: setting(
        'homepage_about_button',
        'Discover Our Story'
    ),
})

const submit = () => {
    processing.value = true

    router.post('/admin/homepage/settings', form, {
        preserveScroll: true,
        onFinish: () => {
            processing.value = false
        },
    })
}
</script>

<template>
    <AdminLayout>
        <div class="min-h-screen bg-slate-50">
            <!-- Header -->
            <div class="border-b border-slate-200 bg-white">
                <div class="px-6 py-6 lg:px-8">
                    <div
                        class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
                    >
                        <div>
                            <p
                                class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-600"
                            >
                                Homepage
                            </p>

                            <h1
                                class="mt-1 text-2xl font-bold tracking-tight text-slate-900"
                            >
                                Homepage Settings
                            </h1>

                            <p class="mt-1 text-sm text-slate-500">
                                Manage the editable content displayed across
                                the Heritage homepage.
                            </p>
                        </div>

                        <button
                            type="button"
                            :disabled="processing"
                            @click="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#061A33] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0a2748] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <svg
                                v-if="processing"
                                class="h-4 w-4 animate-spin"
                                viewBox="0 0 24 24"
                                fill="none"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                />
                            </svg>

                            {{ processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="px-6 py-8 lg:px-8">
                <div class="mx-auto max-w-6xl space-y-6">
                    <!-- Why Heritage -->
                    <section
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                    >
                        <div
                            class="border-b border-slate-200 px-6 py-5"
                        >
                            <h2
                                class="text-base font-semibold text-slate-900"
                            >
                                Why Heritage
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Content displayed in the “Why Heritage”
                                section of the homepage.
                            </p>
                        </div>

                        <div class="grid gap-5 p-6">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Eyebrow
                                </label>

                                <input
                                    v-model="form.homepage_why_eyebrow"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Title
                                </label>

                                <input
                                    v-model="form.homepage_why_title"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Description
                                </label>

                                <textarea
                                    v-model="form.homepage_why_description"
                                    rows="5"
                                    class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm leading-6 outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                ></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Statistics -->
                    <section
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                    >
                        <div
                            class="border-b border-slate-200 px-6 py-5"
                        >
                            <h2
                                class="text-base font-semibold text-slate-900"
                            >
                                Homepage Statistics
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                These figures appear in the credibility /
                                statistics section of the homepage.
                            </p>
                        </div>

                        <div class="grid gap-5 p-6 sm:grid-cols-2">
                            <div
                                v-for="number in 4"
                                :key="number"
                                class="rounded-xl border border-slate-200 bg-slate-50 p-5"
                            >
                                <div
                                    class="mb-4 flex items-center justify-between"
                                >
                                    <h3
                                        class="text-sm font-semibold text-slate-800"
                                    >
                                        Statistic {{ number }}
                                    </h3>

                                    <span
                                        class="rounded-full bg-white px-2.5 py-1 text-xs font-medium text-slate-500"
                                    >
                                        {{ number }}
                                    </span>
                                </div>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label
                                            class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500"
                                        >
                                            Value
                                        </label>

                                        <input
                                            v-model="
                                                form[
                                                    `homepage_stat_${number}_value`
                                                ]
                                            "
                                            type="text"
                                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                        />
                                    </div>

                                    <div>
                                        <label
                                            class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500"
                                        >
                                            Label
                                        </label>

                                        <input
                                            v-model="
                                                form[
                                                    `homepage_stat_${number}_label`
                                                ]
                                            "
                                            type="text"
                                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="border-t border-slate-200 bg-amber-50 px-6 py-4"
                        >
                            <p class="text-xs leading-5 text-amber-800">
                                <strong>Before production:</strong>
                                confirm all figures with Heritage Capital
                                Markets. The current values are content
                                placeholders pending client verification.
                            </p>
                        </div>
                    </section>

                    <!-- About Heritage -->
                    <section
                        class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                    >
                        <div
                            class="border-b border-slate-200 px-6 py-5"
                        >
                            <h2
                                class="text-base font-semibold text-slate-900"
                            >
                                About Heritage
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Homepage introduction to Heritage Capital
                                Markets.
                            </p>
                        </div>

                        <div class="grid gap-5 p-6">
                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Eyebrow
                                </label>

                                <input
                                    v-model="form.homepage_about_eyebrow"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Title
                                </label>

                                <input
                                    v-model="form.homepage_about_title"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                />
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Paragraph 1
                                </label>

                                <textarea
                                    v-model="form.homepage_about_text_1"
                                    rows="5"
                                    class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm leading-6 outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                ></textarea>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Paragraph 2
                                </label>

                                <textarea
                                    v-model="form.homepage_about_text_2"
                                    rows="5"
                                    class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm leading-6 outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                ></textarea>
                            </div>

                            <div>
                                <label
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Button Label
                                </label>

                                <input
                                    v-model="form.homepage_about_button"
                                    type="text"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-[#061A33] focus:ring-2 focus:ring-[#061A33]/10"
                                />
                            </div>
                        </div>
                    </section>

                    <!-- Bottom Save -->
                    <div
                        class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <p
                                class="text-sm font-semibold text-slate-800"
                            >
                                Ready to update the homepage?
                            </p>
                            <p class="mt-1 text-xs text-slate-500">
                                Changes will be reflected on the public
                                homepage after saving.
                            </p>
                        </div>

                        <button
                            type="submit"
                            :disabled="processing"
                            class="inline-flex items-center justify-center rounded-lg bg-[#061A33] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0a2748] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>