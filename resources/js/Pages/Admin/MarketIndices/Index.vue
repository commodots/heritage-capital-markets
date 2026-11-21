<script setup>
import { computed, ref } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AdminSidebar from '@/Components/AdminSidebar.vue'
import MarketCsvImportReview from '@/Components/Admin/MarketCsvImportReview.vue'

const props = defineProps({
    indices: {
        type: Array,
        default: () => [],
    },
    apiConfigured: {
        type: Boolean,
        default: false,
    },
    csvPreview: { type: Object, default: null },
    csvReport: { type: Object, default: null },
})

const page = usePage()

const sidebarOpen = ref(false)
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
const csvImportForm = useForm({ csv_file: null })
const csvActionForm = useForm({ preview_token: '' })
const apiImportForm = useForm({})

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

function selectCsvFile(event) {
    csvImportForm.csv_file = event.target.files?.[0] ?? null
    csvImportForm.clearErrors('csv_file')
}

function importCsv() {
    csvImportForm.post('/admin/market-indices/import-csv', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => csvImportForm.reset(),
    })
}

function commitCsv(token) {
    csvActionForm.preview_token = token
    csvActionForm.post('/admin/market-indices/import-csv/commit', {
        preserveScroll: true,
        onSuccess: () => csvActionForm.reset(),
    })
}

function discardCsv() {
    csvActionForm.reset()
    csvActionForm.post('/admin/market-indices/import-csv/cancel', {
        preserveScroll: true,
        onSuccess: () => csvActionForm.reset(),
    })
}

function importApi() {
    apiImportForm.post('/admin/market-indices/import-api', {
        preserveScroll: true,
    })
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

        <AdminSidebar :open="sidebarOpen" @close="sidebarOpen = false" />

        <!-- Main -->
        <main class="lg:pl-64.5">

            <!-- Header -->
            <header
                class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-8"
            >
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-md p-2 text-slate-500 hover:bg-slate-100 lg:hidden" aria-label="Open admin navigation" @click="sidebarOpen = true">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-heritage-gold">
                            Homepage Management
                        </div>
                        <h1 class="mt-1 text-xl font-bold text-heritage-navy sm:text-2xl">
                            Market Indices
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-3">

                    <a
                        href="/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-50 sm:inline-flex"
                    >
                        View Website
                    </a>

                    <button
                        type="button"
                        @click="openCreate"
                        class="inline-flex items-center gap-2 rounded-md bg-heritage-gold px-4 py-2.5 text-sm font-semibold text-heritage-navy shadow-sm transition hover:bg-heritage-gold-light"
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
                    <h2 class="text-lg font-bold text-heritage-navy">
                        Manage market indicators
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        Manage the market indices displayed in the Market
                        Overview section of the Heritage website.
                    </p>
                </div>

                <section class="mb-8 grid gap-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:grid-cols-2">
                    <form class="space-y-3" @submit.prevent="importCsv">
                        <div>
                            <h2 class="font-bold text-heritage-navy">Import a CSV file</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Required columns: name, value, change, change_percent. Optional: symbol, is_positive, is_active, sort_order, as_of.
                                Existing indices are matched by symbol, or by name when no symbol is provided.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <input type="file" accept=".csv,text/csv" required @change="selectCsvFile"
                                class="min-w-0 flex-1 border border-slate-300 px-3 py-2 text-sm" />
                            <button type="submit" :disabled="csvImportForm.processing || !csvImportForm.csv_file"
                                class="rounded-md bg-heritage-navy px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
                                {{ csvImportForm.processing ? 'Reading…' : 'Preview CSV' }}
                            </button>
                        </div>
                        <p v-if="csvImportForm.errors.csv_file" class="text-sm text-red-600">
                            {{ csvImportForm.errors.csv_file }}
                        </p>
                    </form>

                    <div class="space-y-3 border-t border-slate-200 pt-4 lg:border-l lg:border-t-0 lg:pl-5 lg:pt-0">
                        <div>
                            <h2 class="font-bold text-heritage-navy">Load from market API</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Uses the server-configured HTTPS endpoint. It must return a JSON list (or a data/indices list) with name, value, change and change_percent.
                            </p>
                        </div>
                        <button type="button" :disabled="apiImportForm.processing || !apiConfigured" @click="importApi"
                            class="rounded-md border border-heritage-gold px-4 py-2 text-sm font-semibold text-heritage-navy disabled:cursor-not-allowed disabled:opacity-50">
                            {{ apiImportForm.processing ? 'Loading…' : 'Load API prices' }}
                        </button>
                        <p v-if="!apiConfigured" class="text-xs text-slate-500">
                            HCML approval and a valid HTTPS provider endpoint are required before API loading is enabled.
                        </p>
                        <p v-if="apiImportForm.errors.api" class="text-sm text-red-600">
                            {{ apiImportForm.errors.api }}
                        </p>
                    </div>
                </section>

                <MarketCsvImportReview
                    :preview="csvPreview"
                    :report="csvReport"
                    :processing="csvActionForm.processing"
                    :error="csvActionForm.errors.csv_file"
                    @commit="commitCsv"
                    @cancel="discardCsv"
                />

                <!-- Stats -->
                <div class="mb-8 grid gap-4 sm:grid-cols-3">

                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Total Indices
                        </div>

                        <div class="mt-2 text-3xl font-bold text-heritage-navy">
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

                    <h3 class="mt-4 text-lg font-bold text-heritage-navy">
                        No market indices yet
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
                        Add your first market index to populate the Market
                        Overview section.
                    </p>

                    <button
                        type="button"
                        @click="openCreate"
                        class="mt-6 rounded-md bg-heritage-navy px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0B294D]"
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
                            <h3 class="font-bold text-heritage-navy">
                                Market Overview Data
                            </h3>

                            <p class="mt-1 text-xs text-slate-400">
                                Update values manually, import a CSV, or load from the configured market data API.
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

                                        <div class="flex h-8 w-8 items-center justify-center rounded-md bg-heritage-navy text-xs font-bold text-heritage-gold">
                                            {{ index.sort_order }}
                                        </div>

                                    </td>

                                    <!-- Name -->
                                    <td class="px-5 py-4">

                                        <div class="font-semibold text-heritage-navy">
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

                                        <span class="font-semibold tabular-nums text-heritage-navy">
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
                                                class="rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-heritage-navy transition hover:border-heritage-gold hover:bg-amber-50"
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
                                    <div class="font-semibold text-heritage-navy">
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

                                    <div class="mt-1 text-sm font-bold text-heritage-navy">
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
                                    class="flex-1 rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-heritage-navy"
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
            class="fixed inset-0 z-50 flex items-center justify-center bg-heritage-navy/70 p-4"
        >

            <div
                class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl"
            >

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-200 px-6">

                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-heritage-gold mt-2">
                            {{ editingIndex ? 'Edit Index' : 'New Index' }}
                        </div>

                        <h2 class="mt-1 text-xl font-bold text-heritage-navy">
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

                    <div class="space-y-5 px-6">

                        <!-- Name -->
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Index Name <span class="text-red-500">*</span>
                            </label>

                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. NGX All-Share Index"
                                class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/20"
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
                                class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm uppercase outline-none transition focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/20"
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
                                    placeholder="Enter supplied value"
                                    class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/20"
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
                                    class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/20"
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
                                    class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/20"
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
                                class="w-full rounded-md border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/20"
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
                                        ? 'bg-heritage-gold'
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
                            class="rounded-md bg-heritage-navy px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-[#0B294D] disabled:cursor-not-allowed disabled:opacity-50"
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