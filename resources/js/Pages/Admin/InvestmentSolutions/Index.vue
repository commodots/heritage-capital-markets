<script setup>
import { computed, ref } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
    solutions: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()

const showModal = ref(false)
const editingSolution = ref(null)
const imagePreview = ref(null)

const form = useForm({
    title: '',
    slug: '',
    short_description: '',
    description: '',
    featured_image: null,
    icon: '',
    button_label: 'Learn More',
    button_url: '',
    is_active: true,
    sort_order: 1,
})

const activeCount = computed(() =>
    props.solutions.filter(solution => solution.is_active).length
)

const inactiveCount = computed(() =>
    props.solutions.filter(solution => !solution.is_active).length
)

function openCreate() {
    editingSolution.value = null
    imagePreview.value = null

    form.reset()
    form.title = ''
    form.slug = ''
    form.short_description = ''
    form.description = ''
    form.featured_image = null
    form.icon = ''
    form.button_label = 'Learn More'
    form.button_url = ''
    form.is_active = true
    form.sort_order = props.solutions.length + 1

    showModal.value = true
}

function openEdit(solution) {
    editingSolution.value = solution
    imagePreview.value = solution.featured_image || null

    form.title = solution.title || ''
    form.slug = solution.slug || ''
    form.short_description = solution.short_description || ''
    form.description = solution.description || ''
    form.featured_image = null
    form.icon = solution.icon || ''
    form.button_label = solution.button_label || 'Learn More'
    form.button_url = solution.button_url || ''
    form.is_active = Boolean(solution.is_active)
    form.sort_order = solution.sort_order || 1

    showModal.value = true
}

function closeModal() {
    if (form.processing) return

    showModal.value = false
    editingSolution.value = null
    imagePreview.value = null
    form.reset()
    form.clearErrors()
}

function handleImage(event) {
    const file = event.target.files[0]

    if (!file) {
        form.featured_image = null
        return
    }

    form.featured_image = file
    imagePreview.value = URL.createObjectURL(file)
}

function submit() {
    if (editingSolution.value) {
        form.post(
            `/admin/investment-solutions/${editingSolution.value.id}`,
            {
                forceFormData: true,
                preserveScroll: true,
                onSuccess: () => closeModal(),
            }
        )
    } else {
        form.post('/admin/investment-solutions', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => closeModal(),
        })
    }
}

function toggleSolution(solution) {
    useForm({}).post(
        `/admin/investment-solutions/${solution.id}/toggle`,
        {
            preserveScroll: true,
        }
    )
}

function deleteSolution(solution) {
    if (
        !confirm(
            `Are you sure you want to delete "${solution.title}"?`
        )
    ) {
        return
    }

    useForm({}).delete(
        `/admin/investment-solutions/${solution.id}`,
        {
            preserveScroll: true,
        }
    )
}

function slugifyTitle() {
    if (
        editingSolution.value ||
        form.slug.trim() !== ''
    ) {
        return
    }

    form.slug = form.title
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '')
}

function assetUrl(path) {
    if (!path) return ''

    if (
        path.startsWith('http://') ||
        path.startsWith('https://') ||
        path.startsWith('/')
    ) {
        return path
    }

    return `/${path}`
}

function flashMessage() {
    return page.props.flash?.success || null
}
</script>

<template>
    <Head title="Investment Solutions" />

    <div class="min-h-screen bg-slate-100">

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-[#061A33] text-white lg:flex"
        >
            <!-- Logo -->
            <div class="flex h-20 items-center border-b border-white/10 px-6">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F5B82E] text-xl font-bold text-[#061A33]"
                >
                    H
                </div>

                <div class="ml-3">
                    <div class="text-sm font-bold tracking-wide">
                        HERITAGE
                    </div>
                    <div class="text-[10px] uppercase tracking-[0.2em] text-white/50">
                        Capital Markets
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1 px-3 py-6">

                <a
                    href="/admin"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/70 transition hover:bg-white/5 hover:text-white"
                >
                    <span class="text-lg">⌂</span>
                    Dashboard
                </a>

                <div class="px-4 pb-2 pt-5 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35">
                    Homepage
                </div>

                <a
                    href="/admin/homepage/hero-slides"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/70 transition hover:bg-white/5 hover:text-white"
                >
                    <span class="text-lg">▣</span>
                    Hero Slides
                </a>

                <a
                    href="/admin/investment-solutions"
                    class="flex items-center gap-3 rounded-lg bg-[#F5B82E] px-4 py-3 text-sm font-semibold text-[#061A33]"
                >
                    <span class="text-lg">◆</span>
                    Investment Solutions
                </a>

                <a
                    href="/admin/market-indices"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/70 transition hover:bg-white/5 hover:text-white"
                >
                    <span class="text-lg">↗</span>
                    Market Indices
                </a>

                <div class="px-4 pb-2 pt-5 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35">
                    Content
                </div>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/50"
                >
                    <span class="text-lg">▤</span>
                    Pages
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/50"
                >
                    <span class="text-lg">▥</span>
                    Research & Reports
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/50"
                >
                    <span class="text-lg">◈</span>
                    News & Articles
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/50"
                >
                    <span class="text-lg">?</span>
                    FAQs
                </a>

                <div class="px-4 pb-2 pt-5 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35">
                    Administration
                </div>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/50"
                >
                    <span class="text-lg">▧</span>
                    Media Library
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/50"
                >
                    <span class="text-lg">✉</span>
                    Enquiries
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/50"
                >
                    <span class="text-lg">⚙</span>
                    Site Settings
                </a>
            </nav>

            <!-- Footer -->
            <div class="border-t border-white/10 p-4">
                <div class="rounded-lg bg-white/5 p-4">
                    <div class="text-xs text-white/40">
                        Heritage CMS
                    </div>
                    <div class="mt-1 text-sm font-medium">
                        Administrator
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <main class="lg:pl-64">

            <!-- Header -->
            <header
                class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-8"
            >
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[#F5B82E]">
                        Homepage Management
                    </div>

                    <h1 class="mt-1 text-xl font-bold text-[#061A33] sm:text-2xl">
                        Investment Solutions
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="/"
                        target="_blank"
                        class="hidden rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 sm:inline-flex"
                    >
                        View Website
                    </a>

                    <button
                        type="button"
                        @click="openCreate"
                        class="inline-flex items-center gap-2 rounded-md bg-[#F5B82E] px-4 py-2.5 text-sm font-semibold text-[#061A33] shadow-sm transition hover:bg-[#FFCA45]"
                    >
                        <span class="text-lg leading-none">+</span>
                        Add Solution
                    </button>
                </div>
            </header>

            <div class="p-5 sm:p-8">

                <!-- Flash -->
                <div
                    v-if="flashMessage()"
                    class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
                >
                    {{ flashMessage() }}
                </div>

                <!-- Intro -->
                <div class="mb-7 max-w-3xl">
                    <h2 class="text-lg font-bold text-[#061A33]">
                        Manage investment offerings
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Manage the investment solutions displayed on the
                        Heritage Capital Markets website, including descriptions,
                        imagery, links and display order.
                    </p>
                </div>

                <!-- Stats -->
                <div class="mb-8 grid gap-4 sm:grid-cols-3">

                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Total Solutions
                        </div>

                        <div class="mt-2 text-3xl font-bold text-[#061A33]">
                            {{ solutions.length }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Active
                        </div>

                        <div class="mt-2 text-3xl font-bold text-emerald-600">
                            {{ activeCount }}
                        </div>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Inactive
                        </div>

                        <div class="mt-2 text-3xl font-bold text-slate-400">
                            {{ inactiveCount }}
                        </div>
                    </div>

                </div>

                <!-- Empty state -->
                <div
                    v-if="solutions.length === 0"
                    class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"
                >
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                        ◆
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-[#061A33]">
                        No investment solutions yet
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                        Add your first investment solution to begin managing
                        the Investment Solutions section.
                    </p>

                    <button
                        type="button"
                        @click="openCreate"
                        class="mt-6 rounded-md bg-[#061A33] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0B294D]"
                    >
                        Add Investment Solution
                    </button>
                </div>

                <!-- Solutions -->
                <div
                    v-else
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                >

                    <!-- Table heading -->
                    <div
                        class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h3 class="font-bold text-[#061A33]">
                                Investment Solutions
                            </h3>

                            <p class="mt-0.5 text-xs text-slate-400">
                                Drag/order management will be added with the CMS refinement.
                            </p>
                        </div>

                        <div class="text-xs text-slate-400">
                            {{ solutions.length }} solution{{ solutions.length === 1 ? '' : 's' }}
                        </div>
                    </div>

                    <!-- Desktop table -->
                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-left">
                                    <th class="w-20 px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Order
                                    </th>

                                    <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Solution
                                    </th>

                                    <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Description
                                    </th>

                                    <th class="w-28 px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Status
                                    </th>

                                    <th class="w-40 px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr
                                    v-for="solution in solutions"
                                    :key="solution.id"
                                    class="border-b border-slate-100 last:border-0 hover:bg-slate-50/70"
                                >
                                    <!-- Order -->
                                    <td class="px-5 py-4">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-[#061A33] text-xs font-bold text-[#F5B82E]">
                                            {{ solution.sort_order }}
                                        </div>
                                    </td>

                                    <!-- Solution -->
                                    <td class="px-5 py-4">
                                        <div class="flex min-w-[240px] items-center gap-3">
                                            <div
                                                class="h-14 w-20 shrink-0 overflow-hidden rounded-lg bg-slate-100"
                                            >
                                                <img
                                                    v-if="solution.featured_image"
                                                    :src="assetUrl(solution.featured_image)"
                                                    :alt="solution.title"
                                                    class="h-full w-full object-cover"
                                                >

                                                <div
                                                    v-else
                                                    class="flex h-full w-full items-center justify-center text-xl text-[#061A33]"
                                                >
                                                    ◆
                                                </div>
                                            </div>

                                            <div>
                                                <div class="font-semibold text-[#061A33]">
                                                    {{ solution.title }}
                                                </div>

                                                <div
                                                    v-if="solution.slug"
                                                    class="mt-1 text-xs text-slate-400"
                                                >
                                                    /{{ solution.slug }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Description -->
                                    <td class="px-5 py-4">
                                        <p class="max-w-md text-sm leading-5 text-slate-500">
                                            {{
                                                solution.short_description ||
                                                solution.description ||
                                                'No description provided.'
                                            }}
                                        </p>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-5 py-4">
                                        <button
                                            type="button"
                                            @click="toggleSolution(solution)"
                                            :class="
                                                solution.is_active
                                                    ? 'bg-emerald-100 text-emerald-700'
                                                    : 'bg-slate-100 text-slate-500'
                                            "
                                            class="rounded-full px-3 py-1 text-xs font-semibold transition"
                                        >
                                            {{
                                                solution.is_active
                                                    ? 'Active'
                                                    : 'Inactive'
                                            }}
                                        </button>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-5 py-4">
                                        <div class="flex justify-end gap-2">
                                            <button
                                                type="button"
                                                @click="openEdit(solution)"
                                                class="rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-[#061A33] transition hover:border-[#F5B82E] hover:bg-amber-50"
                                            >
                                                Edit
                                            </button>

                                            <button
                                                type="button"
                                                @click="deleteSolution(solution)"
                                                class="rounded-md border border-red-100 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile cards -->
                    <div class="divide-y divide-slate-100 md:hidden">
                        <div
                            v-for="solution in solutions"
                            :key="solution.id"
                            class="p-5"
                        >
                            <div class="flex gap-4">

                                <div
                                    class="h-20 w-24 shrink-0 overflow-hidden rounded-lg bg-slate-100"
                                >
                                    <img
                                        v-if="solution.featured_image"
                                        :src="assetUrl(solution.featured_image)"
                                        :alt="solution.title"
                                        class="h-full w-full object-cover"
                                    >

                                    <div
                                        v-else
                                        class="flex h-full w-full items-center justify-center text-xl text-[#061A33]"
                                    >
                                        ◆
                                    </div>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="font-semibold text-[#061A33]">
                                                {{ solution.title }}
                                            </div>

                                            <div class="mt-1 text-xs text-slate-400">
                                                Order {{ solution.sort_order }}
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            @click="toggleSolution(solution)"
                                            :class="
                                                solution.is_active
                                                    ? 'bg-emerald-100 text-emerald-700'
                                                    : 'bg-slate-100 text-slate-500'
                                            "
                                            class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                        >
                                            {{
                                                solution.is_active
                                                    ? 'Active'
                                                    : 'Inactive'
                                            }}
                                        </button>
                                    </div>
                                </div>

                            </div>

                            <p class="mt-4 text-sm leading-6 text-slate-500">
                                {{
                                    solution.short_description ||
                                    solution.description ||
                                    'No description provided.'
                                }}
                            </p>

                            <div class="mt-4 flex gap-2">
                                <button
                                    type="button"
                                    @click="openEdit(solution)"
                                    class="flex-1 rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-[#061A33]"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    @click="deleteSolution(solution)"
                                    class="flex-1 rounded-md border border-red-100 px-3 py-2 text-xs font-semibold text-red-600"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </main>

        <!-- Modal -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-[#061A33]/70 p-4"
        >
            <div
                class="max-h-[92vh] w-full max-w-3xl overflow-hidden rounded-xl bg-white shadow-2xl"
            >

                <!-- Modal header -->
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[#F5B82E]">
                            {{ editingSolution ? 'Edit Solution' : 'New Solution' }}
                        </div>

                        <h2 class="mt-1 text-xl font-bold text-[#061A33]">
                            {{
                                editingSolution
                                    ? 'Edit Investment Solution'
                                    : 'Add Investment Solution'
                            }}
                        </h2>
                    </div>

                    <button
                        type="button"
                        @click="closeModal"
                        class="flex h-9 w-9 items-center justify-center rounded-full text-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        ×
                    </button>
                </div>

                <!-- Form -->
                <form
                    @submit.prevent="submit"
                    class="max-h-[calc(92vh-150px)] overflow-y-auto"
                >
                    <div class="space-y-6 px-6 py-6">

                        <!-- Basic details -->
                        <div>
                            <h3 class="text-sm font-bold text-[#061A33]">
                                Basic Information
                            </h3>

                            <p class="mt-1 text-xs text-slate-400">
                                Define how the solution appears on the website.
                            </p>
                        </div>

                        <!-- Title -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Title <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.title"
                                @blur="slugifyTitle"
                                type="text"
                                placeholder="e.g. Portfolio Management"
                                class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                            >

                            <div
                                v-if="form.errors.title"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.title }}
                            </div>
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Slug
                            </label>

                            <input
                                v-model="form.slug"
                                type="text"
                                placeholder="portfolio-management"
                                class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                            >

                            <p class="mt-1 text-xs text-slate-400">
                                Used for the public page URL.
                            </p>

                            <div
                                v-if="form.errors.slug"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.slug }}
                            </div>
                        </div>

                        <!-- Short description -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Short Description
                            </label>

                            <textarea
                                v-model="form.short_description"
                                rows="3"
                                placeholder="Brief description shown on solution cards..."
                                class="w-full resize-none rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                            ></textarea>

                            <div
                                v-if="form.errors.short_description"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.short_description }}
                            </div>
                        </div>

                        <!-- Full description -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Full Description
                            </label>

                            <textarea
                                v-model="form.description"
                                rows="6"
                                placeholder="Detailed description for the solution page..."
                                class="w-full resize-y rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                            ></textarea>

                            <div
                                v-if="form.errors.description"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Image -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Featured Image
                            </label>

                            <div class="grid gap-4 sm:grid-cols-[180px_1fr]">

                                <div
                                    class="flex h-32 items-center justify-center overflow-hidden rounded-lg bg-slate-100"
                                >
                                    <img
                                        v-if="imagePreview"
                                        :src="imagePreview"
                                        alt="Preview"
                                        class="h-full w-full object-cover"
                                    >

                                    <div
                                        v-else
                                        class="text-center text-xs text-slate-400"
                                    >
                                        No image
                                    </div>
                                </div>

                                <div>
                                    <input
                                        type="file"
                                        accept=".jpg,.jpeg,.png,.webp"
                                        @change="handleImage"
                                        class="block w-full rounded-md border border-slate-200 bg-white px-3 py-3 text-sm text-slate-500 file:mr-4 file:rounded-md file:border-0 file:bg-[#061A33] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white"
                                    >

                                    <p class="mt-2 text-xs leading-5 text-slate-400">
                                        JPG, PNG or WebP. Maximum file size: 5MB.
                                    </p>

                                    <div
                                        v-if="form.errors.featured_image"
                                        class="mt-1 text-xs text-red-600"
                                    >
                                        {{ form.errors.featured_image }}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Display -->
                        <div class="border-t border-slate-100 pt-6">

                            <h3 class="text-sm font-bold text-[#061A33]">
                                Display & Link
                            </h3>

                            <div class="mt-5 grid gap-5 sm:grid-cols-2">

                                <!-- Icon -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                                        Icon
                                    </label>

                                    <input
                                        v-model="form.icon"
                                        type="text"
                                        placeholder="e.g. briefcase"
                                        class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                                    >

                                    <p class="mt-1 text-xs text-slate-400">
                                        Icon name/class for future icon integration.
                                    </p>
                                </div>

                                <!-- Sort -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                                        Display Order <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        v-model.number="form.sort_order"
                                        type="number"
                                        min="1"
                                        class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                                    >

                                    <div
                                        v-if="form.errors.sort_order"
                                        class="mt-1 text-xs text-red-600"
                                    >
                                        {{ form.errors.sort_order }}
                                    </div>
                                </div>

                                <!-- Button label -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                                        Button Label
                                    </label>

                                    <input
                                        v-model="form.button_label"
                                        type="text"
                                        placeholder="Learn More"
                                        class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                                    >
                                </div>

                                <!-- Button URL -->
                                <div>
                                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                                        Button URL
                                    </label>

                                    <input
                                        v-model="form.button_url"
                                        type="text"
                                        placeholder="/investment-solutions"
                                        class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                                    >
                                </div>

                            </div>

                            <!-- Active -->
                            <div class="mt-5 flex items-center justify-between rounded-lg bg-slate-50 p-4">
                                <div>
                                    <div class="text-sm font-semibold text-slate-700">
                                        Publish Solution
                                    </div>

                                    <div class="mt-1 text-xs text-slate-400">
                                        Active solutions are displayed on the public website.
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    @click="form.is_active = !form.is_active"
                                    :class="
                                        form.is_active
                                            ? 'bg-[#F5B82E]'
                                            : 'bg-slate-300'
                                    "
                                    class="relative h-6 w-11 rounded-full transition"
                                >
                                    <span
                                        :class="
                                            form.is_active
                                                ? 'translate-x-6'
                                                : 'translate-x-1'
                                        "
                                        class="absolute left-0 top-1 h-4 w-4 rounded-full bg-white shadow transition"
                                    ></span>
                                </button>
                            </div>

                            <div
                                v-if="form.errors.is_active"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.is_active }}
                            </div>

                        </div>

                    </div>

                    <!-- Modal footer -->
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">

                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-md border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="rounded-md bg-[#061A33] px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0B294D] disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            {{
                                form.processing
                                    ? 'Saving...'
                                    : editingSolution
                                        ? 'Save Changes'
                                        : 'Create Solution'
                            }}
                        </button>

                    </div>
                </form>
            </div>
        </div>

    </div>
</template>