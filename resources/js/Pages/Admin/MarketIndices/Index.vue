<script setup>
import { computed, ref } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
    indices: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()

const showModal = ref(false)
const editingIndex = ref(null)

const form = useForm({
    name: '',
    symbol: '',
    value: '',
    change: '',
    change_percent: '',
    is_positive: true,
    is_active: true,
    sort_order: 1,
})

const activeCount = computed(() =>
    props.indices.filter(index => index.is_active).length
)

const inactiveCount = computed(() =>
    props.indices.filter(index => !index.is_active).length
)

function openCreate() {
    editingIndex.value = null

    form.reset()

    form.name = ''
    form.symbol = ''
    form.value = ''
    form.change = ''
    form.change_percent = ''
    form.is_positive = true
    form.is_active = true
    form.sort_order = props.indices.length + 1

    showModal.value = true
}

function openEdit(index) {
    editingIndex.value = index

    form.name = index.name || ''
    form.symbol = index.symbol || ''
    form.value = index.value ?? ''
    form.change = index.change ?? ''
    form.change_percent = index.change_percent ?? ''
    form.is_positive = Boolean(index.is_positive)
    form.is_active = Boolean(index.is_active)
    form.sort_order = index.sort_order || 1

    showModal.value = true
}

function closeModal() {
    if (form.processing) {
        return
    }

    showModal.value = false
    editingIndex.value = null

    form.reset()
    form.clearErrors()
}

function submit() {
    if (editingIndex.value) {
        form.post(
            `/admin/market-indices/${editingIndex.value.id}`,
            {
                preserveScroll: true,
                onSuccess: () => closeModal(),
            }
        )
    } else {
        form.post('/admin/market-indices', {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        })
    }
}

function toggleIndex(index) {
    useForm({}).post(
        `/admin/market-indices/${index.id}/toggle`,
        {
            preserveScroll: true,
        }
    )
}

function deleteIndex(index) {
    if (
        !confirm(
            `Are you sure you want to delete "${index.name}"?`
        )
    ) {
        return
    }

    useForm({}).delete(
        `/admin/market-indices/${index.id}`,
        {
            preserveScroll: true,
        }
    )
}

function formatValue(value) {
    if (value === null || value === undefined || value === '') {
        return '—'
    }

    return Number(value).toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })
}

function formatChange(value) {
    if (value === null || value === undefined || value === '') {
        return '0.00'
    }

    const number = Number(value)

    return Math.abs(number).toLocaleString('en-NG', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    })
}

function formatPercent(value) {
    if (value === null || value === undefined || value === '') {
        return '0.00%'
    }

    return `${Math.abs(Number(value)).toFixed(2)}%`
}

function flashMessage() {
    return page.props.flash?.success || null
}
</script>

<template>
    <Head title="Market Indices" />

    <div class="min-h-screen bg-slate-100">

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 hidden w-64 flex-col bg-[#061A33] text-white lg:flex"
        >
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
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/70 transition hover:bg-white/5 hover:text-white"
                >
                    <span class="text-lg">◆</span>
                    Investment Solutions
                </a>

                <a
                    href="/admin/market-indices"
                    class="flex items-center gap-3 rounded-lg bg-[#F5B82E] px-4 py-3 text-sm font-semibold text-[#061A33]"
                >
                    <span class="text-lg">↗</span>
                    Market Indices
                </a>

                <div class="px-4 pb-2 pt-5 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35">
                    Content
                </div>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/40"
                >
                    <span class="text-lg">▤</span>
                    Pages
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/40"
                >
                    <span class="text-lg">▥</span>
                    Research & Reports
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/40"
                >
                    <span class="text-lg">◈</span>
                    News & Articles
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/40"
                >
                    <span class="text-lg">?</span>
                    FAQs
                </a>

                <div class="px-4 pb-2 pt-5 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35">
                    Administration
                </div>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/40"
                >
                    <span class="text-lg">▧</span>
                    Media Library
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/40"
                >
                    <span class="text-lg">✉</span>
                    Enquiries
                </a>

                <a
                    href="#"
                    class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm text-white/40"
                >
                    <span class="text-lg">⚙</span>
                    Site Settings
                </a>
            </nav>

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
                        Market Indices
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
                        Add Index
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
                        Manage market indicators
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Manage the market indices displayed in the Market
                        Overview section of the Heritage website.
                    </p>
                </div>

                <!-- Stats -->
                <div class="mb-8 grid gap-4 sm:grid-cols-3">

                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Total Indices
                        </div>

                        <div class="mt-2 text-3xl font-bold text-[#061A33]">
                            {{ indices.length }}
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

                <!-- Empty -->
                <div
                    v-if="indices.length === 0"
                    class="rounded-xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"
                >
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                        ↗
                    </div>

                    <h3 class="mt-4 text-lg font-bold text-[#061A33]">
                        No market indices yet
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                        Add your first market index to populate the Market
                        Overview section.
                    </p>

                    <button
                        type="button"
                        @click="openCreate"
                        class="mt-6 rounded-md bg-[#061A33] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0B294D]"
                    >
                        Add Market Index
                    </button>
                </div>

                <!-- Desktop table -->
                <div
                    v-else
                    class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"
                >

                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h3 class="font-bold text-[#061A33]">
                                Market Overview Data
                            </h3>

                            <p class="mt-1 text-xs text-slate-400">
                                Values are currently manually managed.
                            </p>
                        </div>

                        <div class="text-xs text-slate-400">
                            {{ indices.length }}
                            {{ indices.length === 1 ? 'index' : 'indices' }}
                        </div>
                    </div>

                    <div class="hidden overflow-x-auto md:block">
                        <table class="w-full">

                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-left">

                                    <th class="w-20 px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Order
                                    </th>

                                    <th class="px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Index
                                    </th>

                                    <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Value
                                    </th>

                                    <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        Change
                                    </th>

                                    <th class="px-5 py-3 text-right text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                                        %
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
                                    v-for="index in indices"
                                    :key="index.id"
                                    class="border-b border-slate-100 last:border-0 hover:bg-slate-50/70"
                                >

                                    <!-- Order -->
                                    <td class="px-5 py-4">

                                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-[#061A33] text-xs font-bold text-[#F5B82E]">
                                            {{ index.sort_order }}
                                        </div>

                                    </td>

                                    <!-- Name -->
                                    <td class="px-5 py-4">

                                        <div class="font-semibold text-[#061A33]">
                                            {{ index.name }}
                                        </div>

                                        <div
                                            v-if="index.symbol"
                                            class="mt-1 text-xs uppercase tracking-wider text-slate-400"
                                        >
                                            {{ index.symbol }}
                                        </div>

                                    </td>

                                    <!-- Value -->
                                    <td class="px-5 py-4 text-right">

                                        <span class="font-semibold tabular-nums text-[#061A33]">
                                            {{ formatValue(index.value) }}
                                        </span>

                                    </td>

                                    <!-- Change -->
                                    <td class="px-5 py-4 text-right">

                                        <span
                                            :class="
                                                index.is_positive
                                                    ? 'text-emerald-600'
                                                    : 'text-red-600'
                                            "
                                            class="font-semibold tabular-nums"
                                        >
                                            {{ index.is_positive ? '+' : '-' }}{{ formatChange(index.change) }}
                                        </span>

                                    </td>

                                    <!-- Percentage -->
                                    <td class="px-5 py-4 text-right">

                                        <span
                                            :class="
                                                index.is_positive
                                                    ? 'text-emerald-600'
                                                    : 'text-red-600'
                                            "
                                            class="font-semibold tabular-nums"
                                        >
                                            {{ index.is_positive ? '+' : '-' }}{{ formatPercent(index.change_percent) }}
                                        </span>

                                    </td>

                                    <!-- Status -->
                                    <td class="px-5 py-4">

                                        <button
                                            type="button"
                                            @click="toggleIndex(index)"
                                            :class="
                                                index.is_active
                                                    ? 'bg-emerald-100 text-emerald-700'
                                                    : 'bg-slate-100 text-slate-500'
                                            "
                                            class="rounded-full px-3 py-1 text-xs font-semibold"
                                        >
                                            {{
                                                index.is_active
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
                                                @click="openEdit(index)"
                                                class="rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-[#061A33] transition hover:border-[#F5B82E] hover:bg-amber-50"
                                            >
                                                Edit
                                            </button>

                                            <button
                                                type="button"
                                                @click="deleteIndex(index)"
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

                    <!-- Mobile -->
                    <div class="divide-y divide-slate-100 md:hidden">

                        <div
                            v-for="index in indices"
                            :key="index.id"
                            class="p-5"
                        >

                            <div class="flex items-start justify-between gap-4">

                                <div>
                                    <div class="font-semibold text-[#061A33]">
                                        {{ index.name }}
                                    </div>

                                    <div
                                        v-if="index.symbol"
                                        class="mt-1 text-xs uppercase tracking-wider text-slate-400"
                                    >
                                        {{ index.symbol }}
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    @click="toggleIndex(index)"
                                    :class="
                                        index.is_active
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-slate-100 text-slate-500'
                                    "
                                    class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold"
                                >
                                    {{
                                        index.is_active
                                            ? 'Active'
                                            : 'Inactive'
                                    }}
                                </button>

                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-3">

                                <div class="rounded-lg bg-slate-50 p-3">
                                    <div class="text-[10px] uppercase tracking-wider text-slate-400">
                                        Value
                                    </div>

                                    <div class="mt-1 text-sm font-bold text-[#061A33]">
                                        {{ formatValue(index.value) }}
                                    </div>
                                </div>

                                <div class="rounded-lg bg-slate-50 p-3">
                                    <div class="text-[10px] uppercase tracking-wider text-slate-400">
                                        Change
                                    </div>

                                    <div
                                        :class="
                                            index.is_positive
                                                ? 'text-emerald-600'
                                                : 'text-red-600'
                                        "
                                        class="mt-1 text-sm font-bold"
                                    >
                                        {{ index.is_positive ? '+' : '-' }}{{ formatChange(index.change) }}
                                    </div>
                                </div>

                                <div class="rounded-lg bg-slate-50 p-3">
                                    <div class="text-[10px] uppercase tracking-wider text-slate-400">
                                        Percent
                                    </div>

                                    <div
                                        :class="
                                            index.is_positive
                                                ? 'text-emerald-600'
                                                : 'text-red-600'
                                        "
                                        class="mt-1 text-sm font-bold"
                                    >
                                        {{ index.is_positive ? '+' : '-' }}{{ formatPercent(index.change_percent) }}
                                    </div>
                                </div>

                            </div>

                            <div class="mt-4 flex gap-2">

                                <button
                                    type="button"
                                    @click="openEdit(index)"
                                    class="flex-1 rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-[#061A33]"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    @click="deleteIndex(index)"
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
                class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl"
            >

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-[#F5B82E]">
                            {{ editingIndex ? 'Edit Index' : 'New Index' }}
                        </div>

                        <h2 class="mt-1 text-xl font-bold text-[#061A33]">
                            {{
                                editingIndex
                                    ? 'Edit Market Index'
                                    : 'Add Market Index'
                            }}
                        </h2>
                    </div>

                    <button
                        type="button"
                        @click="closeModal"
                        class="flex h-9 w-9 items-center justify-center rounded-full text-xl text-slate-400 transition hover:bg-slate-100"
                    >
                        ×
                    </button>

                </div>

                <!-- Form -->
                <form @submit.prevent="submit">

                    <div class="space-y-5 px-6 py-6">

                        <!-- Name -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Index Name <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. NGX All-Share Index"
                                class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                            >

                            <div
                                v-if="form.errors.name"
                                class="mt-1 text-xs text-red-600"
                            >
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Symbol -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Symbol / Code
                            </label>

                            <input
                                v-model="form.symbol"
                                type="text"
                                placeholder="e.g. NGXASI"
                                class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm uppercase outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                            >
                        </div>

                        <!-- Values -->
                        <div class="grid gap-5 sm:grid-cols-3">

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">
                                    Current Value <span class="text-red-500">*</span>
                                </label>

                                <input
                                    v-model="form.value"
                                    type="number"
                                    step="0.01"
                                    placeholder="102026.75"
                                    class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                                >

                                <div
                                    v-if="form.errors.value"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.value }}
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">
                                    Change <span class="text-red-500">*</span>
                                </label>

                                <input
                                    v-model="form.change"
                                    type="number"
                                    step="0.01"
                                    placeholder="640.25"
                                    class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                                >

                                <div
                                    v-if="form.errors.change"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.change }}
                                </div>
                            </div>

                            <div>
                                <label class="mb-2 block text-sm font-semibold text-slate-700">
                                    Change %
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    v-model="form.change_percent"
                                    type="number"
                                    step="0.01"
                                    placeholder="0.63"
                                    class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-[#F5B82E] focus:ring-2 focus:ring-[#F5B82E]/20"
                                >

                                <div
                                    v-if="form.errors.change_percent"
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {{ form.errors.change_percent }}
                                </div>
                            </div>

                        </div>

                        <!-- Direction -->
                        <div>

                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Market Direction
                            </label>

                            <div class="grid grid-cols-2 gap-3">

                                <button
                                    type="button"
                                    @click="form.is_positive = true"
                                    :class="
                                        form.is_positive
                                            ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                                            : 'border-slate-200 bg-white text-slate-500'
                                    "
                                    class="rounded-lg border px-4 py-3 text-sm font-semibold transition"
                                >
                                    ↑ Positive
                                </button>

                                <button
                                    type="button"
                                    @click="form.is_positive = false"
                                    :class="
                                        !form.is_positive
                                            ? 'border-red-500 bg-red-50 text-red-700'
                                            : 'border-slate-200 bg-white text-slate-500'
                                    "
                                    class="rounded-lg border px-4 py-3 text-sm font-semibold transition"
                                >
                                    ↓ Negative
                                </button>

                            </div>

                        </div>

                        <!-- Order -->
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

                        <!-- Active -->
                        <div class="flex items-center justify-between rounded-lg bg-slate-50 p-4">

                            <div>
                                <div class="text-sm font-semibold text-slate-700">
                                    Publish Index
                                </div>

                                <div class="mt-1 text-xs text-slate-400">
                                    Active indices appear on the public website.
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

                    </div>

                    <!-- Footer -->
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
                                    : editingIndex
                                        ? 'Save Changes'
                                        : 'Create Index'
                            }}
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</template>