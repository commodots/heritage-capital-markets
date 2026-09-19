<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    enquiry: {
        type: Object,
        required: true,
    },
})

const processing = ref(false)

const updateStatus = (status) => {
    processing.value = true

    router.post(
        `/admin/enquiries/${props.enquiry.id}`,
        { status },
        {
            preserveScroll: true,
            onFinish: () => {
                processing.value = false
            },
        }
    )
}

const formatDate = (date) => {
    if (!date) return '—'

    return new Intl.DateTimeFormat('en-GB', {
        dateStyle: 'full',
        timeStyle: 'short',
    }).format(new Date(date))
}
</script>

<template>
    <AdminLayout>
        <div class="min-h-screen bg-slate-50">

            <div class="border-b bg-white">
                <div class="px-6 py-6 lg:px-8">

                    <a
                        href="/admin/enquiries"
                        class="text-sm font-semibold text-slate-500 hover:text-[#061A33]"
                    >
                        ← Back to Enquiries
                    </a>

                    <h1 class="mt-3 text-2xl font-bold text-slate-900">
                        Enquiry Details
                    </h1>

                </div>
            </div>

            <div class="p-6 lg:p-8">
                <div class="mx-auto max-w-4xl space-y-6">

                    <div class="rounded-xl border bg-white p-6 shadow-sm">

                        <div class="flex flex-col gap-4 border-b pb-6 md:flex-row md:items-start md:justify-between">

                            <div>
                                <h2 class="text-xl font-bold text-slate-900">
                                    {{ enquiry.subject || 'General Enquiry' }}
                                </h2>

                                <p class="mt-2 text-sm text-slate-500">
                                    {{ formatDate(enquiry.created_at) }}
                                </p>
                            </div>

                            <select
                                :value="enquiry.status"
                                @change="updateStatus($event.target.value)"
                                :disabled="processing"
                                class="rounded-lg border px-4 py-2 text-sm font-semibold"
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

                        </div>

                        <div class="grid gap-6 py-6 md:grid-cols-2">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Name
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ enquiry.name }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Email
                                </p>

                                <a
                                    :href="`mailto:${enquiry.email}`"
                                    class="mt-1 block text-sm font-semibold text-[#061A33]"
                                >
                                    {{ enquiry.email }}
                                </a>
                            </div>

                            <div v-if="enquiry.phone">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Phone
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-900">
                                    {{ enquiry.phone }}
                                </p>
                            </div>

                        </div>

                        <div class="border-t pt-6">

                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Message
                            </p>

                            <div class="mt-4 whitespace-pre-line rounded-lg bg-slate-50 p-5 text-sm leading-7 text-slate-700">
                                {{ enquiry.message }}
                            </div>

                        </div>

                    </div>

                </div>
            </div>

        </div>
    </AdminLayout>
</template>