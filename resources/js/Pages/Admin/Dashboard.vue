<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    stats: {
        type: Object,
        default: () => ({
            heroSlides: 0,
            marketIndices: 0,
            investmentSolutions: 0,
            researchReports: 0,
            enquiries: 0,
            newsletterSubscribers: 0,
        }),
    },

    recentEnquiries: {
        type: Array,
        default: () => [],
    },

    latestHeroSlides: {
        type: Array,
        default: () => [],
    },

    latestSolutions: {
        type: Array,
        default: () => [],
    },

    latestReports: {
        type: Array,
        default: () => [],
    },
})

const formatDate = (date) => {
    if (!date) return '—'

    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

const statusClass = (status) => {
    const classes = {
        open: 'bg-amber-50 text-amber-700',
        pending: 'bg-blue-50 text-blue-700',
        replied: 'bg-green-50 text-green-700',
        closed: 'bg-slate-100 text-slate-600',
    }

    return classes[status] ?? 'bg-slate-100 text-slate-600'
}
</script>

<template>
    <AdminLayout>

        <!-- =========================================================
             PAGE HEADER
        ========================================================== -->
        <div class="border-b border-heritage-border bg-white">
            <div class="px-6 py-6 lg:px-8">

                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div>
                        <div
                            class="text-[10px] font-semibold uppercase tracking-[0.25em] text-heritage-gold"
                        >
                            Main
                        </div>

                        <h1
                            class="mt-1 font-display text-3xl text-heritage-navy"
                        >
                            Dashboard
                        </h1>

                        <p class="mt-1 text-sm text-heritage-muted">
                            Welcome back. Here's an overview of your Heritage website.
                        </p>
                    </div>

                    <a
                        href="/"
                        target="_blank"
                        class="inline-flex items-center justify-center gap-2 rounded bg-heritage-gold px-5 py-2.5 text-sm font-semibold text-heritage-deep transition hover:bg-heritage-gold-light"
                    >
                        View Website
                        <span>↗</span>
                    </a>

                </div>

            </div>
        </div>


        <!-- =========================================================
             CONTENT
        ========================================================== -->
        <div class="space-y-8 p-6 lg:p-8">

            <!-- =====================================================
                 STATISTICS
            ====================================================== -->
            <section>

                <div class="mb-4">
                    <h2 class="text-sm font-semibold text-heritage-navy">
                        Website Overview
                    </h2>

                    <p class="mt-1 text-xs text-heritage-muted">
                        Current content and communication activity.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                    <!-- Hero Slides -->
                    <div
                        class="rounded-xl border border-heritage-border bg-white p-5 shadow-sm"
                    >
                        <div class="flex items-start justify-between">

                            <div>
                                <p class="text-xs font-medium text-heritage-muted">
                                    Active Hero Slides
                                </p>

                                <p class="mt-2 text-3xl font-semibold text-heritage-navy">
                                    {{ stats.heroSlides }}
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-heritage-navy text-heritage-gold"
                            >
                                ▣
                            </div>

                        </div>

                        <a
                            href="/admin/homepage/hero-slides"
                            class="mt-4 inline-flex text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                        >
                            Manage slides →
                        </a>
                    </div>


                    <!-- Market Indices -->
                    <div
                        class="rounded-xl border border-heritage-border bg-white p-5 shadow-sm"
                    >
                        <div class="flex items-start justify-between">

                            <div>
                                <p class="text-xs font-medium text-heritage-muted">
                                    Market Indices
                                </p>

                                <p class="mt-2 text-3xl font-semibold text-heritage-navy">
                                    {{ stats.marketIndices }}
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-heritage-navy text-heritage-gold"
                            >
                                ↗
                            </div>

                        </div>

                        <a
                            href="/admin/market-indices"
                            class="mt-4 inline-flex text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                        >
                            Manage indices →
                        </a>
                    </div>


                    <!-- Investment Solutions -->
                    <div
                        class="rounded-xl border border-heritage-border bg-white p-5 shadow-sm"
                    >
                        <div class="flex items-start justify-between">

                            <div>
                                <p class="text-xs font-medium text-heritage-muted">
                                    Investment Solutions
                                </p>

                                <p class="mt-2 text-3xl font-semibold text-heritage-navy">
                                    {{ stats.investmentSolutions }}
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-heritage-navy text-heritage-gold"
                            >
                                ◈
                            </div>

                        </div>

                        <a
                            href="/admin/investment-solutions"
                            class="mt-4 inline-flex text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                        >
                            Manage solutions →
                        </a>
                    </div>


                    <!-- Research -->
                    <div
                        class="rounded-xl border border-heritage-border bg-white p-5 shadow-sm"
                    >
                        <div class="flex items-start justify-between">

                            <div>
                                <p class="text-xs font-medium text-heritage-muted">
                                    Published Research
                                </p>

                                <p class="mt-2 text-3xl font-semibold text-heritage-navy">
                                    {{ stats.researchReports }}
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-heritage-navy text-heritage-gold"
                            >
                                ◫
                            </div>

                        </div>

                        <a
                            href="/admin/research-reports"
                            class="mt-4 inline-flex text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                        >
                            Manage research →
                        </a>
                    </div>


                    <!-- Enquiries -->
                    <div
                        class="rounded-xl border border-heritage-border bg-white p-5 shadow-sm"
                    >
                        <div class="flex items-start justify-between">

                            <div>
                                <p class="text-xs font-medium text-heritage-muted">
                                    Open Enquiries
                                </p>

                                <p class="mt-2 text-3xl font-semibold text-heritage-navy">
                                    {{ stats.enquiries }}
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-heritage-navy text-heritage-gold"
                            >
                                ?
                            </div>

                        </div>

                        <a
                            href="/admin/enquiries"
                            class="mt-4 inline-flex text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                        >
                            View enquiries →
                        </a>
                    </div>


                    <!-- Subscribers -->
                    <div
                        class="rounded-xl border border-heritage-border bg-white p-5 shadow-sm"
                    >
                        <div class="flex items-start justify-between">

                            <div>
                                <p class="text-xs font-medium text-heritage-muted">
                                    Newsletter Subscribers
                                </p>

                                <p class="mt-2 text-3xl font-semibold text-heritage-navy">
                                    {{ stats.newsletterSubscribers }}
                                </p>
                            </div>

                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-lg bg-heritage-navy text-heritage-gold"
                            >
                                ✉
                            </div>

                        </div>

                        <a
                            href="/admin/newsletter-subscribers"
                            class="mt-4 inline-flex text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                        >
                            View subscribers →
                        </a>
                    </div>

                </div>
            </section>


            <!-- =====================================================
                 QUICK ACTIONS
            ====================================================== -->
            <section>

                <div class="mb-4">
                    <h2 class="text-sm font-semibold text-heritage-navy">
                        Quick Actions
                    </h2>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">

                    <a
                        href="/admin/homepage/hero-slides"
                        class="group rounded-xl border border-heritage-border bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-heritage-gold hover:shadow-md"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-heritage-navy text-lg text-heritage-gold"
                        >
                            +
                        </div>

                        <h3 class="mt-4 text-sm font-semibold text-heritage-navy">
                            Manage Homepage
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-heritage-muted">
                            Update hero slides and homepage content.
                        </p>
                    </a>


                    <a
                        href="/admin/investment-solutions"
                        class="group rounded-xl border border-heritage-border bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-heritage-gold hover:shadow-md"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-heritage-navy text-lg text-heritage-gold"
                        >
                            ◈
                        </div>

                        <h3 class="mt-4 text-sm font-semibold text-heritage-navy">
                            Investment Solutions
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-heritage-muted">
                            Manage investment products and services.
                        </p>
                    </a>


                    <a
                        href="/admin/research-reports"
                        class="group rounded-xl border border-heritage-border bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-heritage-gold hover:shadow-md"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-heritage-navy text-lg text-heritage-gold"
                        >
                            ◫
                        </div>

                        <h3 class="mt-4 text-sm font-semibold text-heritage-navy">
                            Research & Reports
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-heritage-muted">
                            Publish and manage research materials.
                        </p>
                    </a>


                    <a
                        href="/admin/enquiries"
                        class="group rounded-xl border border-heritage-border bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-heritage-gold hover:shadow-md"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-heritage-navy text-lg text-heritage-gold"
                        >
                            ?
                        </div>

                        <h3 class="mt-4 text-sm font-semibold text-heritage-navy">
                            Enquiries
                        </h3>

                        <p class="mt-1 text-xs leading-5 text-heritage-muted">
                            Review and respond to website enquiries.
                        </p>
                    </a>

                </div>

            </section>


            <!-- =====================================================
                 LOWER GRID
            ====================================================== -->
            <div class="grid gap-6 xl:grid-cols-2">

                <!-- =================================================
                     RECENT ENQUIRIES
                ================================================== -->
                <section
                    class="overflow-hidden rounded-xl border border-heritage-border bg-white shadow-sm"
                >

                    <div
                        class="flex items-center justify-between border-b border-heritage-border px-5 py-4"
                    >
                        <div>
                            <h2 class="text-sm font-semibold text-heritage-navy">
                                Recent Enquiries
                            </h2>

                            <p class="mt-1 text-xs text-heritage-muted">
                                Latest contact requests from the website.
                            </p>
                        </div>

                        <a
                            href="/admin/enquiries"
                            class="text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                        >
                            View All
                        </a>
                    </div>


                    <div v-if="recentEnquiries.length">

                        <div
                            v-for="enquiry in recentEnquiries"
                            :key="enquiry.id"
                            class="flex items-center justify-between gap-4 border-b border-heritage-border px-5 py-4 last:border-b-0"
                        >

                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-heritage-navy">
                                    {{ enquiry.name }}
                                </p>

                                <p class="mt-1 truncate text-xs text-heritage-muted">
                                    {{ enquiry.subject || enquiry.email }}
                                </p>

                                <p class="mt-1 text-[11px] text-slate-400">
                                    {{ formatDate(enquiry.created_at) }}
                                </p>
                            </div>

                            <span
                                class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-semibold capitalize"
                                :class="statusClass(enquiry.status)"
                            >
                                {{ enquiry.status }}
                            </span>

                        </div>

                    </div>


                    <div
                        v-else
                        class="px-5 py-12 text-center"
                    >
                        <div class="text-sm font-medium text-heritage-navy">
                            No enquiries yet
                        </div>

                        <p class="mt-1 text-xs text-heritage-muted">
                            Website contact requests will appear here.
                        </p>
                    </div>

                </section>


                <!-- =================================================
                     LATEST RESEARCH
                ================================================== -->
                <section
                    class="overflow-hidden rounded-xl border border-heritage-border bg-white shadow-sm"
                >

                    <div
                        class="flex items-center justify-between border-b border-heritage-border px-5 py-4"
                    >
                        <div>
                            <h2 class="text-sm font-semibold text-heritage-navy">
                                Latest Research
                            </h2>

                            <p class="mt-1 text-xs text-heritage-muted">
                                Recently published research reports.
                            </p>
                        </div>

                        <a
                            href="/admin/research-reports"
                            class="text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                        >
                            View All
                        </a>
                    </div>


                    <div v-if="latestReports.length">

                        <div
                            v-for="report in latestReports"
                            :key="report.id"
                            class="flex gap-4 border-b border-heritage-border px-5 py-4 last:border-b-0"
                        >

                            <div
                                class="h-14 w-14 shrink-0 overflow-hidden rounded-lg bg-heritage-offwhite"
                            >
                                <img
                                    v-if="report.featured_image"
                                    :src="report.featured_image"
                                    :alt="report.title"
                                    class="h-full w-full object-cover"
                                />

                                <div
                                    v-else
                                    class="flex h-full w-full items-center justify-center text-xs text-heritage-muted"
                                >
                                    PDF
                                </div>
                            </div>


                            <div class="min-w-0">
                                <h3
                                    class="line-clamp-2 text-sm font-semibold leading-5 text-heritage-navy"
                                >
                                    {{ report.title }}
                                </h3>

                                <p class="mt-1 text-[11px] text-heritage-muted">
                                    Published {{ formatDate(report.published_at) }}
                                </p>
                            </div>

                        </div>

                    </div>


                    <div
                        v-else
                        class="px-5 py-12 text-center"
                    >
                        <div class="text-sm font-medium text-heritage-navy">
                            No research reports yet
                        </div>

                        <p class="mt-1 text-xs text-heritage-muted">
                            Published reports will appear here.
                        </p>
                    </div>

                </section>

            </div>


            <!-- =====================================================
                 HOMEPAGE CONTENT SNAPSHOT
            ====================================================== -->
            <section
                class="overflow-hidden rounded-xl border border-heritage-border bg-white shadow-sm"
            >

                <div
                    class="flex flex-col gap-3 border-b border-heritage-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h2 class="text-sm font-semibold text-heritage-navy">
                            Homepage Content
                        </h2>

                        <p class="mt-1 text-xs text-heritage-muted">
                            Current homepage slides and investment solutions.
                        </p>
                    </div>

                    <a
                        href="/admin/homepage/hero-slides"
                        class="text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                    >
                        Manage Homepage →
                    </a>
                </div>


                <div class="grid gap-6 p-5 lg:grid-cols-2">

                    <!-- Hero Slides -->
                    <div>

                        <div class="mb-3 text-xs font-semibold uppercase tracking-wider text-heritage-muted">
                            Hero Slides
                        </div>

                        <div
                            v-if="latestHeroSlides.length"
                            class="space-y-3"
                        >

                            <div
                                v-for="slide in latestHeroSlides"
                                :key="slide.id"
                                class="flex items-center gap-3 rounded-lg border border-heritage-border p-3"
                            >

                                <div
                                    class="h-12 w-16 shrink-0 overflow-hidden rounded bg-heritage-offwhite"
                                >
                                    <img
                                        v-if="slide.image"
                                        :src="slide.image"
                                        :alt="slide.title"
                                        class="h-full w-full object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-full items-center justify-center text-[9px] text-heritage-muted"
                                    >
                                        No image
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-semibold text-heritage-navy">
                                        {{ slide.title }}
                                    </p>

                                    <p class="mt-1 text-[10px] text-heritage-muted">
                                        Position {{ slide.sort_order }}
                                    </p>
                                </div>

                                <span
                                    class="rounded-full px-2 py-1 text-[9px] font-semibold"
                                    :class="
                                        slide.is_active
                                            ? 'bg-green-50 text-green-700'
                                            : 'bg-slate-100 text-slate-500'
                                    "
                                >
                                    {{ slide.is_active ? 'Active' : 'Inactive' }}
                                </span>

                            </div>

                        </div>

                        <div
                            v-else
                            class="rounded-lg border border-dashed border-heritage-border p-6 text-center text-xs text-heritage-muted"
                        >
                            No hero slides available.
                        </div>

                    </div>


                    <!-- Investment Solutions -->
                    <div>

                        <div class="mb-3 text-xs font-semibold uppercase tracking-wider text-heritage-muted">
                            Investment Solutions
                        </div>

                        <div
                            v-if="latestSolutions.length"
                            class="space-y-3"
                        >

                            <div
                                v-for="solution in latestSolutions"
                                :key="solution.id"
                                class="flex items-center gap-3 rounded-lg border border-heritage-border p-3"
                            >

                                <div
                                    class="h-12 w-16 shrink-0 overflow-hidden rounded bg-heritage-offwhite"
                                >
                                    <img
                                        v-if="solution.featured_image"
                                        :src="solution.featured_image"
                                        :alt="solution.title"
                                        class="h-full w-full object-cover"
                                    />

                                    <div
                                        v-else
                                        class="flex h-full items-center justify-center text-[9px] text-heritage-muted"
                                    >
                                        No image
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-xs font-semibold text-heritage-navy">
                                        {{ solution.title }}
                                    </p>

                                    <p class="mt-1 text-[10px] text-heritage-muted">
                                        Position {{ solution.sort_order }}
                                    </p>
                                </div>

                                <span
                                    class="rounded-full px-2 py-1 text-[9px] font-semibold"
                                    :class="
                                        solution.is_active
                                            ? 'bg-green-50 text-green-700'
                                            : 'bg-slate-100 text-slate-500'
                                    "
                                >
                                    {{ solution.is_active ? 'Active' : 'Inactive' }}
                                </span>

                            </div>

                        </div>

                        <div
                            v-else
                            class="rounded-lg border border-dashed border-heritage-border p-6 text-center text-xs text-heritage-muted"
                        >
                            No investment solutions available.
                        </div>

                    </div>

                </div>

            </section>

        </div>

    </AdminLayout>
</template>