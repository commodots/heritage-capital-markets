<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    enquiries: {
        type: Array,
        default: () => [],
    },
})

const filter = ref('all')

const filteredEnquiries = computed(() => {
    if (filter.value === 'all') {
        return props.enquiries
    }

    return props.enquiries.filter(
        enquiry => enquiry.status === filter.value
    )
})

const statusLabel = (status) => {
    return {
        new: 'New',
        in_progress: 'In Progress',
        closed: 'Closed',
    }[status] || status
}

const statusClass = (status) => {
    return {
        new: 'bg-amber-50 text-amber-700',
        in_progress: 'bg-blue-50 text-blue-700',
        closed: 'bg-emerald-50 text-emerald-700',
    }[status] || 'bg-slate-100 text-slate-600'
}

const formatDate = (date) => {
    if (!date) return '—'

    return new Intl.DateTimeFormat('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(date))
}

const updateStatus = (enquiry, status) => {
    router.post(
        `/admin/enquiries/${enquiry.id}`,
        { status },
        {
            preserveScroll: true,
        }
    )
}
</script>

<template>
    <AdminLayout>
        <div class="min-h-screen bg-slate-50">

            <div class="border-b border-slate-200 bg-white">
                <div class="px-6 py-6 lg:px-8">

                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-600">
                        Communication
                    </p>

                    <h1 class="mt-1 text-2xl font-bold text-slate-900">
                        Enquiries & Contact Requests
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Review and manage enquiries submitted through the website.
                    </p>

                </div>
            </div>

            <div class="p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">

                    <div class="mb-6 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-xl border bg-white p-5 shadow-sm">
                            <p class="text-xs uppercase text-slate-500">
                                Total
                            </p>

                            <p class="mt-2 text-3xl font-bold">
                                {{ enquiries.length }}
                            </p>
                        </div>

                        <div class="rounded-xl border bg-white p-5 shadow-sm">
                            <p class="text-xs uppercase text-slate-500">
                                New
                            </p>

                            <p class="mt-2 text-3xl font-bold text-amber-600">
                                {{ enquiries.filter(e => e.status === 'new').length }}
                            </p>
                        </div>

                        <div class="rounded-xl border bg-white p-5 shadow-sm">
                            <p class="text-xs uppercase text-slate-500">
                                In Progress
                            </p>

                            <p class="mt-2 text-3xl font-bold text-blue-600">
                                {{ enquiries.filter(e => e.status === 'in_progress').length }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-5 flex flex-wrap gap-2">
                        <button
                            v-for="item in [
                                ['all', 'All'],
                                ['new', 'New'],
                                ['in_progress', 'In Progress'],
                                ['closed', 'Closed'],
                            ]"
                            :key="item[0]"
                            @click="filter = item[0]"
                            :class="filter === item[0]
                                ? 'bg-[#061A33] text-white'
                                : 'border bg-white text-slate-600'"
                            class="rounded-lg px-4 py-2 text-sm font-semibold"
                        >
                            {{ item[1] }}
                        </button>
                    </div>

                    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead class="border-b bg-slate-50">
                                    <tr>
                                        <th class="px-6 py-3 text-xs uppercase text-slate-500">
                                            Contact
                                        </th>

                                        <th class="px-6 py-3 text-xs uppercase text-slate-500">
                                            Subject
                                        </th>

                                        <th class="px-6 py-3 text-xs uppercase text-slate-500">
                                            Date
                                        </th>

                                        <th class="px-6 py-3 text-xs uppercase text-slate-500">
                                            Status
                                        </th>

                                        <th class="px-6 py-3 text-right text-xs uppercase text-slate-500">
                                            Action
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-100">
                                    <tr
                                        v-for="enquiry in filteredEnquiries"
                                        :key="enquiry.id"
                                        class="hover:bg-slate-50"
                                    >
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-semibold text-slate-900">
                                                {{ enquiry.name }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ enquiry.email }}
                                            </p>

                                            <p
                                                v-if="enquiry.phone"
                                                class="text-xs text-slate-500"
                                            >
                                                {{ enquiry.phone }}
                                            </p>
                                        </td>

                                        <td class="px-6 py-4 text-sm text-slate-700">
                                            {{ enquiry.subject || 'General Enquiry' }}
                                        </td>

                                        <td class="px-6 py-4 text-xs text-slate-500">
                                            {{ formatDate(enquiry.created_at) }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <select
                                                :value="enquiry.status"
                                                @change="updateStatus(enquiry, $event.target.value)"
                                                :class="statusClass(enquiry.status)"
                                                class="rounded-full border-0 px-3 py-1 text-xs font-semibold"
                                            >
                                                <option value="new">
                                                    New
                                                </option>

                                                <option value="in_progress">
                                                    In Progress
                                                </option>

                                                <option value="closed">
                                                    Closed
                                                </option>
                                            </select>
                                        </td>

                                        <td class="px-6 py-4 text-right">
                                            <a
                                                :href="`/admin/enquiries/${enquiry.id}`"
                                                class="rounded-lg border px-3 py-2 text-xs font-semibold"
                                            >
                                                View
                                            </a>
                                        </td>
                                    </tr>

                                    <tr v-if="!filteredEnquiries.length">
                                        <td
                                            colspan="5"
                                            class="px-6 py-16 text-center text-sm text-slate-500"
                                        >
                                            No enquiries found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AdminLayout>
</template>