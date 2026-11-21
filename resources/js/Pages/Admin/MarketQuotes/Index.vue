<script setup>
import { ref } from 'vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import AdminSidebar from '@/Components/AdminSidebar.vue'
import MarketCsvImportReview from '@/Components/Admin/MarketCsvImportReview.vue'

const props = defineProps({
    quotes: { type: Array, default: () => [] },
    csvPreview: { type: Object, default: null },
    csvReport: { type: Object, default: null },
})

const page = usePage()
const sidebarOpen = ref(false)
const showModal = ref(false)
const editingQuote = ref(null)
const form = useForm({
    symbol: '',
    name: '',
    value: '',
    change: '',
    change_percent: '',
    is_active: true,
    sort_order: 1,
})
const csvForm = useForm({ csv_file: null })
const csvAction = useForm({ preview_token: '' })

function openCreate() {
    editingQuote.value = null
    form.reset()
    form.clearErrors()
    form.symbol = ''
    form.name = ''
    form.value = ''
    form.change = ''
    form.change_percent = ''
    form.is_active = true
    form.sort_order = props.quotes.length + 1
    showModal.value = true
}

function openEdit(quote) {
    editingQuote.value = quote
    form.symbol = quote.symbol
    form.name = quote.name
    form.value = quote.value
    form.change = quote.change_value
    form.change_percent = quote.change_percent
    form.is_active = quote.is_active
    form.sort_order = quote.sort_order
    form.clearErrors()
    showModal.value = true
}

function closeModal() {
    if (form.processing) return
    showModal.value = false
    editingQuote.value = null
    form.reset()
    form.clearErrors()
}

function saveQuote() {
    const url = editingQuote.value
        ? `/admin/market-quotes/${editingQuote.value.id}`
        : '/admin/market-quotes'
    form.post(url, {
        preserveScroll: true,
        onSuccess: closeModal,
    })
}

function selectCsvFile(event) {
    csvForm.csv_file = event.target.files?.[0] ?? null
    csvForm.clearErrors('csv_file')
}

function previewCsv() {
    csvForm.post('/admin/market-quotes/import-csv', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => csvForm.reset(),
    })
}

function commitCsv(token) {
    csvAction.preview_token = token
    csvAction.post('/admin/market-quotes/import-csv/commit', {
        preserveScroll: true,
        onSuccess: () => csvAction.reset(),
    })
}

function discardCsv() {
    csvAction.reset()
    csvAction.post('/admin/market-quotes/import-csv/cancel', {
        preserveScroll: true,
        onSuccess: () => csvAction.reset(),
    })
}

function toggleQuote(quote) {
    useForm({}).post(`/admin/market-quotes/${quote.id}/toggle`, { preserveScroll: true })
}

function deleteQuote(quote) {
    if (!confirm(`Delete the market quote for ${quote.symbol}?`)) return
    useForm({}).delete(`/admin/market-quotes/${quote.id}`, { preserveScroll: true })
}

function formatNumber(value) {
    if (value === null || value === undefined || value === '') return '—'
    return Number(value).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 4 })
}

function formatDate(value) {
    if (!value) return '—'
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return value
    return new Intl.DateTimeFormat('en-NG', { dateStyle: 'medium', timeStyle: 'short' }).format(date)
}
</script>

<template>
    <Head title="Market Quotes" />

    <div class="min-h-screen bg-slate-100">
        <AdminSidebar :open="sidebarOpen" @close="sidebarOpen = false" />

        <main class="lg:pl-64.5">
            <header class="sticky top-0 z-30 flex h-20 items-center justify-between border-b border-slate-200 bg-white/95 px-5 backdrop-blur sm:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-md p-2 text-slate-500 hover:bg-slate-100 lg:hidden" aria-label="Open admin navigation" @click="sidebarOpen = true">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-heritage-gold">Homepage Management</p>
                        <h1 class="mt-1 text-xl font-bold text-heritage-navy sm:text-2xl">Market Quotes</h1>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="/" target="_blank" rel="noopener noreferrer" class="hidden rounded-md border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 sm:inline-flex">
                        View Website
                    </a>
                    <button type="button" @click="openCreate" class="rounded-md bg-heritage-gold px-4 py-2.5 text-sm font-semibold text-heritage-navy">
                        + Add Stock Quote
                    </button>
                </div>
            </header>

            <div class="p-5 sm:p-8">
                <div v-if="page.props.flash?.success" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ page.props.flash.success }}
                </div>

                <div class="mb-6 max-w-3xl">
                    <h2 class="text-lg font-bold text-heritage-navy">Manage public stock prices</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        Add a symbol manually before importing its quotes. No prices are generated when this list is empty.
                        API updates are not enabled until HCML approves and provides a data provider.
                    </p>
                </div>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <form class="space-y-3" @submit.prevent="previewCsv">
                        <div>
                            <h2 class="font-bold text-heritage-navy">Import quote updates from CSV</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Required headers: <code>symbol,name,value,change,change_percent</code>.
                                Optional: <code>as_of,is_active,sort_order</code>. Use signed movement values; timestamps should be ISO 8601.
                                Unknown symbols and duplicate rows are reported and not imported.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <input type="file" accept=".csv,text/csv" required @change="selectCsvFile"
                                class="min-w-0 flex-1 border border-slate-300 px-3 py-2 text-sm" />
                            <button type="submit" :disabled="csvForm.processing || !csvForm.csv_file"
                                class="rounded-md bg-heritage-navy px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
                                {{ csvForm.processing ? 'Reading…' : 'Preview CSV' }}
                            </button>
                        </div>
                        <p v-if="csvForm.errors.csv_file" class="text-sm text-red-600">{{ csvForm.errors.csv_file }}</p>
                    </form>

                    <MarketCsvImportReview
                        :preview="csvPreview"
                        :report="csvReport"
                        :processing="csvAction.processing"
                        :error="csvAction.errors.csv_file"
                        @commit="commitCsv"
                        @cancel="discardCsv"
                    />
                </section>

                <section class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="font-bold text-heritage-navy">Stock quote records</h2>
                            <p class="mt-1 text-sm text-slate-500">Manual quotes and approved CSV updates shown in the public ticker.</p>
                        </div>
                        <span class="text-sm text-slate-500">{{ quotes.length }} record(s)</span>
                    </div>

                    <div v-if="quotes.length" class="overflow-x-auto">
                        <table class="w-full min-w-[900px] text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                                <tr>
                                    <th class="px-5 py-3">Symbol / Security</th>
                                    <th class="px-5 py-3 text-right">Current price</th>
                                    <th class="px-5 py-3 text-right">Movement</th>
                                    <th class="px-5 py-3 text-right">Movement %</th>
                                    <th class="px-5 py-3">Last updated</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="quote in quotes" :key="quote.id" class="border-t border-slate-100">
                                    <td class="px-5 py-4">
                                        <span class="font-semibold text-heritage-navy">{{ quote.symbol }}</span>
                                        <span class="mt-1 block text-slate-600">{{ quote.name }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-right tabular-nums">{{ formatNumber(quote.value) }}</td>
                                    <td class="px-5 py-4 text-right tabular-nums" :class="Number(quote.change_value) < 0 ? 'text-red-700' : 'text-emerald-700'">
                                        {{ Number(quote.change_value) > 0 ? '+' : '' }}{{ formatNumber(quote.change_value) }}
                                    </td>
                                    <td class="px-5 py-4 text-right tabular-nums" :class="Number(quote.change_percent) < 0 ? 'text-red-700' : 'text-emerald-700'">
                                        {{ Number(quote.change_percent) > 0 ? '+' : '' }}{{ formatNumber(quote.change_percent) }}%
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">{{ formatDate(quote.as_of) }}</td>
                                    <td class="px-5 py-4">
                                        <span :class="quote.is_active ? 'text-emerald-700' : 'text-slate-500'">
                                            {{ quote.is_active ? 'Published' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <div class="inline-flex gap-2">
                                            <button type="button" @click="openEdit(quote)" class="rounded border border-slate-300 px-3 py-1.5 text-xs font-semibold text-heritage-navy">Edit</button>
                                            <button type="button" @click="toggleQuote(quote)" class="rounded border border-slate-300 px-3 py-1.5 text-xs font-semibold text-heritage-navy">{{ quote.is_active ? 'Hide' : 'Publish' }}</button>
                                            <button type="button" @click="deleteQuote(quote)" class="rounded border border-red-200 px-3 py-1.5 text-xs font-semibold text-red-700">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="px-5 py-10 text-center text-sm text-slate-500">
                        No stock quotes have been entered. The public ticker will show market data as unavailable.
                    </p>
                </section>
            </div>
        </main>

        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-heritage-navy/70 p-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-heritage-gold">{{ editingQuote ? 'Edit quote' : 'New quote' }}</p>
                        <h2 class="mt-1 text-xl font-bold text-heritage-navy">{{ editingQuote ? `Edit ${editingQuote.symbol}` : 'Add stock quote' }}</h2>
                    </div>
                    <button type="button" @click="closeModal" class="text-2xl text-slate-400" aria-label="Close dialog">×</button>
                </div>
                <form @submit.prevent="saveQuote">
                    <div class="grid gap-5 p-6 sm:grid-cols-2">
                        <div>
                            <label for="quote-symbol" class="mb-1 block text-sm font-semibold text-slate-700">Stock symbol *</label>
                            <input id="quote-symbol" v-model="form.symbol" :disabled="!!editingQuote" required maxlength="100" class="w-full rounded border border-slate-300 px-3 py-2 text-sm uppercase disabled:bg-slate-100" />
                            <p v-if="form.errors.symbol" class="mt-1 text-xs text-red-600">{{ form.errors.symbol }}</p>
                        </div>
                        <div>
                            <label for="quote-name" class="mb-1 block text-sm font-semibold text-slate-700">Company / security name *</label>
                            <input id="quote-name" v-model="form.name" required maxlength="255" class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
                            <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                        </div>
                        <div>
                            <label for="quote-value" class="mb-1 block text-sm font-semibold text-slate-700">Current price *</label>
                            <input id="quote-value" v-model="form.value" type="number" min="0" step="0.0001" required class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
                            <p v-if="form.errors.value" class="mt-1 text-xs text-red-600">{{ form.errors.value }}</p>
                        </div>
                        <div>
                            <label for="quote-change" class="mb-1 block text-sm font-semibold text-slate-700">Price movement (signed) *</label>
                            <input id="quote-change" v-model="form.change" type="number" step="0.0001" required class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
                            <p v-if="form.errors.change" class="mt-1 text-xs text-red-600">{{ form.errors.change }}</p>
                        </div>
                        <div>
                            <label for="quote-percent" class="mb-1 block text-sm font-semibold text-slate-700">Percentage movement (signed) *</label>
                            <input id="quote-percent" v-model="form.change_percent" type="number" step="0.0001" required class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
                            <p v-if="form.errors.change_percent" class="mt-1 text-xs text-red-600">{{ form.errors.change_percent }}</p>
                        </div>
                        <div>
                            <label for="quote-order" class="mb-1 block text-sm font-semibold text-slate-700">Display order *</label>
                            <input id="quote-order" v-model.number="form.sort_order" type="number" min="1" required class="w-full rounded border border-slate-300 px-3 py-2 text-sm" />
                            <p v-if="form.errors.sort_order" class="mt-1 text-xs text-red-600">{{ form.errors.sort_order }}</p>
                        </div>
                        <label class="flex items-center gap-3 text-sm font-medium text-slate-700 sm:col-span-2">
                            <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-heritage-gold focus:ring-heritage-gold" />
                            Publish this quote in the public ticker
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                        <button type="button" @click="closeModal" class="rounded border border-slate-300 bg-white px-4 py-2 text-sm">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="rounded bg-heritage-navy px-5 py-2 text-sm font-semibold text-white disabled:opacity-50">
                            {{ form.processing ? 'Saving…' : editingQuote ? 'Save changes' : 'Create quote' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
