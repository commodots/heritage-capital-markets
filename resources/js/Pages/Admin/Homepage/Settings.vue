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

const formState = {
    homepage_why_eyebrow: setting('homepage_why_eyebrow', 'WHY HERITAGE'),
    homepage_why_title: setting(
        'homepage_why_title',
        'Built on Trust. Driven by Insight.'
    ),
    homepage_why_description: setting(
        'homepage_why_description',
        'We combine deep market knowledge, disciplined execution and a client-first approach to help investors make informed financial decisions.'
    ),

    homepage_stat_1_value: setting('homepage_stat_1_value', '20+'),
    homepage_stat_1_label: setting('homepage_stat_1_label', 'Years Experience'),

    homepage_stat_2_value: setting('homepage_stat_2_value', '100+'),
    homepage_stat_2_label: setting('homepage_stat_2_label', 'Clients Served'),

    homepage_stat_3_value: setting('homepage_stat_3_value', '₦50B+'),
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
    homepage_solutions_eyebrow: setting('homepage_solutions_eyebrow', 'Invest With Confidence'),
    homepage_solutions_title_line_1: setting('homepage_solutions_title_line_1', 'Solutions for Today.'),
    homepage_solutions_title_line_2: setting('homepage_solutions_title_line_2', 'A Stronger Tomorrow.'),
    homepage_solutions_heading: [
        setting('homepage_solutions_title_line_1', 'Solutions for Today.'),
        setting('homepage_solutions_title_line_2', 'A Stronger Tomorrow.'),
    ].join('\n'),
    homepage_solutions_description: setting(
        'homepage_solutions_description',
        'From wealth management to capital markets advisory, we provide tailored solutions to help you grow, protect and manage your wealth.'
    ),
    homepage_solutions_button: setting('homepage_solutions_button', 'Discover Our Solutions'),
    homepage_market_eyebrow: setting('homepage_market_eyebrow', 'Market Overview'),
    homepage_market_title: setting('homepage_market_title', "Today's Markets"),
    homepage_research_eyebrow: setting('homepage_research_eyebrow', 'Research & Insights'),
    homepage_research_title_line_1: setting('homepage_research_title_line_1', 'Knowledge That Moves'),
    homepage_research_title_line_2: setting('homepage_research_title_line_2', 'With the Market.'),
    homepage_research_heading: [
        setting('homepage_research_title_line_1', 'Knowledge That Moves'),
        setting('homepage_research_title_line_2', 'With the Market.'),
    ].join('\n'),
    homepage_research_button: setting('homepage_research_button', 'Explore Research'),
}

const benefitDefaults = [
    ['Proven Experience', 'Decades of experience in Nigeria’s capital markets and a deep understanding of evolving market conditions.'],
    ['Client Focused', 'We put your objectives, preferences and long-term financial goals at the centre of every engagement.'],
    ['Market Expertise', 'Research-driven insights and market intelligence support informed investment decisions.'],
    ['Trust & Transparency', 'Clear communication, disciplined processes and a strong commitment to professional standards.'],
]

const highlightDefaults = [
    ['20+ Years', 'Market Experience'],
    ['Client Focused', 'Solutions Built Around You'],
    ['SEC Regulated', 'Professional Standards'],
    ['Research Driven', 'Insights That Matter'],
]

const researchDefaults = [
    ['Market Insights', 'Understanding the Markets. Identifying the Opportunities.', 'Access timely market perspectives and informed analysis.'],
    ['Special Report', 'Research That Goes Deeper.', 'Explore analysis and perspectives across key market and investment areas.'],
    ['Market Perspective', 'Stay Ahead of the Conversation.', 'Discover news, commentary and insights from our research team.'],
]

for (let index = 0; index < benefitDefaults.length; index++) {
    const number = index + 1
    formState[`homepage_benefit_${number}_title`] = setting(`homepage_benefit_${number}_title`, benefitDefaults[index][0])
    formState[`homepage_benefit_${number}_text`] = setting(`homepage_benefit_${number}_text`, benefitDefaults[index][1])
    formState[`homepage_highlight_${number}_title`] = setting(`homepage_highlight_${number}_title`, highlightDefaults[index][0])
    formState[`homepage_highlight_${number}_text`] = setting(`homepage_highlight_${number}_text`, highlightDefaults[index][1])
}

for (let index = 0; index < researchDefaults.length; index++) {
    const number = index + 1
    formState[`homepage_research_${number}_category`] = setting(`homepage_research_${number}_category`, researchDefaults[index][0])
    formState[`homepage_research_${number}_title`] = setting(`homepage_research_${number}_title`, researchDefaults[index][1])
    formState[`homepage_research_${number}_text`] = setting(`homepage_research_${number}_text`, researchDefaults[index][2])
}

const form = reactive(formState)

const submit = () => {
    processing.value = true
    const splitHeading = (heading) => {
        const [firstLine = '', ...remainingLines] = heading.split(/\r?\n/)
        return [firstLine, remainingLines.join(' ')]
    }

    const headings = [
        [form.homepage_solutions_heading, 'homepage_solutions_title_line_1', 'homepage_solutions_title_line_2'],
        [form.homepage_research_heading, 'homepage_research_title_line_1', 'homepage_research_title_line_2'],
    ]

    headings.forEach(([heading, firstKey, secondKey]) => {
        const [firstLine, secondLine] = splitHeading(heading)
        form[firstKey] = firstLine
        form[secondKey] = secondLine
    })

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
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-heritage-navy px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#0a2748] disabled:cursor-not-allowed disabled:opacity-60"
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
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
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
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
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
                                    class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm leading-6 outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
                                ></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Homepage Highlight Strip -->
                    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-6 py-5">
                            <h2 class="text-base font-semibold text-slate-900">Homepage Highlights</h2>
                            <p class="mt-1 text-sm text-slate-500">Edit the four short messages shown beneath the hero.</p>
                        </div>
                        <div class="grid gap-5 p-6 sm:grid-cols-2">
                            <div v-for="number in 4" :key="number" class="space-y-4 rounded-lg border border-slate-200 p-4">
                                <h3 class="text-sm font-semibold text-slate-800">Highlight {{ number }}</h3>
                                <input v-model="form[`homepage_highlight_${number}_title`]" type="text"
                                    aria-label="Highlight title"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10" />
                                <textarea v-model="form[`homepage_highlight_${number}_text`]" rows="2"
                                    aria-label="Highlight description"
                                    class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Why Heritage Benefits -->
                    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-6 py-5">
                            <h2 class="text-base font-semibold text-slate-900">Why Heritage Benefits</h2>
                            <p class="mt-1 text-sm text-slate-500">Edit the four benefit cards beneath the Why Heritage introduction.</p>
                        </div>
                        <div class="grid gap-5 p-6 sm:grid-cols-2">
                            <div v-for="number in 4" :key="number" class="space-y-4 rounded-lg border border-slate-200 p-4">
                                <h3 class="text-sm font-semibold text-slate-800">Benefit {{ number }}</h3>
                                <input v-model="form[`homepage_benefit_${number}_title`]" type="text"
                                    aria-label="Benefit title"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10" />
                                <textarea v-model="form[`homepage_benefit_${number}_text`]" rows="3"
                                    aria-label="Benefit description"
                                    class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm outline-none focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Homepage Sections -->
                    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-6 py-5">
                            <h2 class="text-base font-semibold text-slate-900">Investment &amp; Market Sections</h2>
                            <p class="mt-1 text-sm text-slate-500">Edit the introduction to the investment cards and the market indices heading.</p>
                        </div>
                        <div class="grid gap-5 p-6 md:grid-cols-2">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Investment section eyebrow</span>
                                <input v-model="form.homepage_solutions_eyebrow" type="text" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" />
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Investment section button</span>
                                <input v-model="form.homepage_solutions_button" type="text" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" />
                            </label>
                            <label class="block md:col-span-2">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Investment section heading</span>
                                <textarea v-model="form.homepage_solutions_heading" rows="2" placeholder="Use one line per visual line"
                                    class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm"></textarea>
                            </label>
                            <label class="block md:col-span-2">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Investment description</span>
                                <textarea v-model="form.homepage_solutions_description" rows="3" class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"></textarea>
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Market section eyebrow</span>
                                <input v-model="form.homepage_market_eyebrow" type="text" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" />
                            </label>
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Market section title</span>
                                <input v-model="form.homepage_market_title" type="text" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" />
                            </label>
                        </div>
                    </section>

                    <!-- Research & Insights -->
                    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-6 py-5">
                            <h2 class="text-base font-semibold text-slate-900">Research &amp; Insights</h2>
                            <p class="mt-1 text-sm text-slate-500">Edit the section heading and the three research cards. Their images and destinations remain managed by the site.</p>
                        </div>
                        <div class="space-y-6 p-6">
                            <div class="grid gap-5 md:grid-cols-2">
                                <label class="block">
                                    <span class="mb-2 block text-sm font-medium text-slate-700">Eyebrow</span>
                                    <input v-model="form.homepage_research_eyebrow" type="text" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" />
                                </label>
                                <label class="block">
                                    <span class="mb-2 block text-sm font-medium text-slate-700">Button label</span>
                                    <input v-model="form.homepage_research_button" type="text" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" />
                                </label>
                                <label class="block md:col-span-2">
                                    <span class="mb-2 block text-sm font-medium text-slate-700">Heading</span>
                                    <textarea v-model="form.homepage_research_heading" rows="2" placeholder="Use one line per visual line"
                                        class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm"></textarea>
                                </label>
                            </div>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div v-for="number in 3" :key="number" class="space-y-4 rounded-lg border border-slate-200 p-4">
                                    <h3 class="text-sm font-semibold text-slate-800">Research card {{ number }}</h3>
                                    <input v-model="form[`homepage_research_${number}_category`]" type="text" aria-label="Research card category" placeholder="Category"
                                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" />
                                    <input v-model="form[`homepage_research_${number}_title`]" type="text" aria-label="Research card title" placeholder="Title"
                                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm" />
                                    <textarea v-model="form[`homepage_research_${number}_text`]" rows="3" aria-label="Research card description" placeholder="Description"
                                        class="w-full rounded-lg border border-slate-300 px-4 py-3 text-sm"></textarea>
                                </div>
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
                                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
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
                                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
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
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
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
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
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
                                    class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm leading-6 outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
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
                                    class="w-full resize-y rounded-lg border border-slate-300 px-4 py-3 text-sm leading-6 outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
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
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-heritage-navy focus:ring-2 focus:ring-heritage-navy/10"
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
                            class="inline-flex items-center justify-center rounded-lg bg-heritage-navy px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0a2748] disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{ processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>