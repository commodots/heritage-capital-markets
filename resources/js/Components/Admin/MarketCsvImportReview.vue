<script setup>
import { computed } from 'vue'

const props = defineProps({
    preview: { type: Object, default: null },
    report: { type: Object, default: null },
    processing: { type: Boolean, default: false },
    error: { type: String, default: '' },
})

const emit = defineEmits(['commit', 'cancel'])

const validCount = computed(() => props.preview?.rows?.filter(row => row.valid).length ?? 0)
const invalidCount = computed(() => props.preview?.rows?.filter(row => !row.valid).length ?? 0)

function formatNumber(value) {
    if (value === null || value === undefined || value === '') return '—'
    return Number(value).toLocaleString('en-NG', { maximumFractionDigits: 4 })
}

function formatDate(value) {
    if (!value) return 'On import'
    const date = new Date(value)
    if (Number.isNaN(date.getTime())) return value

    return new Intl.DateTimeFormat('en-NG', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date)
}
</script>

<template>
    <section v-if="preview || report || error" class="mt-5 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div v-if="error" class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ error }}
        </div>

        <template v-if="preview">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-semibold text-heritage-navy">CSV import preview</h3>
                    <p class="mt-1 text-sm text-slate-600">
                        {{ validCount }} valid row(s) ready; {{ invalidCount }} invalid row(s) will be skipped.
                    </p>
                </div>
                <div class="flex gap-2">
                    <button type="button" :disabled="processing" @click="emit('cancel')"
                        class="rounded border border-slate-300 px-4 py-2 text-sm font-medium disabled:opacity-50">
                        Discard
                    </button>
                    <button type="button" :disabled="processing || validCount === 0" @click="emit('commit', preview.token)"
                        class="rounded bg-heritage-navy px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">
                        {{ processing ? 'Committing…' : `Commit ${validCount} valid row(s)` }}
                    </button>
                </div>
            </div>

            <div class="mt-4 max-h-96 overflow-auto rounded border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="sticky top-0 bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-2">Row</th>
                            <th class="px-3 py-2">Symbol</th>
                            <th class="px-3 py-2">Name</th>
                            <th class="px-3 py-2 text-right">Price / Value</th>
                            <th class="px-3 py-2 text-right">Movement</th>
                            <th class="px-3 py-2 text-right">Movement %</th>
                            <th class="px-3 py-2">Last updated</th>
                            <th class="px-3 py-2">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in preview.rows" :key="row.line" class="border-t border-slate-100 align-top">
                            <td class="px-3 py-2">{{ row.line }}</td>
                            <td class="px-3 py-2">{{ row.data.symbol || '—' }}</td>
                            <td class="px-3 py-2">{{ row.data.name || '—' }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ formatNumber(row.data.value) }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ formatNumber(row.data.change) }}</td>
                            <td class="px-3 py-2 text-right tabular-nums">{{ formatNumber(row.data.change_percent) }}%</td>
                            <td class="whitespace-nowrap px-3 py-2">{{ formatDate(row.data.as_of) }}</td>
                            <td class="min-w-52 px-3 py-2" :class="row.valid ? 'text-emerald-700' : 'text-red-700'">
                                {{ row.valid ? row.action : row.errors.join(' ') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template v-else-if="report">
            <h3 class="font-semibold text-heritage-navy">CSV import results</h3>
            <p class="mt-1 text-sm text-slate-600">
                {{ report.succeeded }} succeeded, {{ report.failed }} failed, {{ report.invalid }} invalid.
            </p>
            <div class="mt-4 max-h-72 overflow-auto rounded border border-slate-200">
                <table class="min-w-full text-left text-sm">
                    <thead class="sticky top-0 bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-2">Row</th>
                            <th class="px-3 py-2">Symbol</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="row in report.rows" :key="row.line" class="border-t border-slate-100 align-top">
                            <td class="px-3 py-2">{{ row.line }}</td>
                            <td class="px-3 py-2">{{ row.symbol || '—' }}</td>
                            <td class="px-3 py-2 capitalize" :class="row.status === 'success' ? 'text-emerald-700' : 'text-red-700'">
                                {{ row.status }}
                            </td>
                            <td class="px-3 py-2">{{ row.message }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </section>
</template>
