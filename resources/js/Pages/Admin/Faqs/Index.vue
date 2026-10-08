<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    faqs: {
        type: Array,
        default: () => [],
    },
})

const editing = ref(null)
const showModal = ref(false)

const form = useForm({
    question: '',
    answer: '',
    category: 'General',
    is_active: true,
    sort_order: 0,
})

function reset() {
    showModal.value = false
    editing.value = null
    form.reset()
    form.clearErrors()
}

function openCreate() {
    reset()
    showModal.value = true
}

function edit(faq) {
    editing.value = faq
    form.clearErrors()
    form.question = faq.question || ''
    form.answer = faq.answer || ''
    form.category = faq.category || ''
    form.is_active = Boolean(faq.is_active)
    form.sort_order = faq.sort_order ?? 0
    showModal.value = true
}

function submit() {
    form.post(
        editing.value ? `/admin/faqs/${editing.value.id}` : '/admin/faqs',
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: reset,
        }
    )
}

function toggle(faq) {
    router.post(`/admin/faqs/${faq.id}/toggle`, {}, { preserveScroll: true })
}

function remove(faq) {
    if (confirm(`Are you sure you want to delete this FAQ?`)) {
        router.delete(`/admin/faqs/${faq.id}`, { preserveScroll: true })
    }
}
</script>

<template>
    <AdminLayout>
        <div class="p-6 lg:p-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-heritage-navy">FAQs</h1>
                    <p class="mt-1 text-sm text-slate-500">Create and manage frequently asked questions.</p>
                </div>
                <button type="button" class="btn-gold" @click="openCreate">+ New FAQ</button>
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-4">Question</th>
                                <th class="p-4">Category</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="faq in faqs" :key="faq.id" class="border-t border-slate-100">
                                <td class="max-w-xl p-4 font-medium text-heritage-navy">{{ faq.question }}</td>
                                <td class="p-4 text-slate-600">{{ faq.category || '—' }}</td>
                                <td class="p-4">
                                    <button
                                        type="button"
                                        class="rounded-full px-3 py-1 text-xs"
                                        :class="faq.is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'"
                                        :aria-label="`${faq.is_active ? 'Deactivate' : 'Activate'} FAQ`"
                                        @click="toggle(faq)"
                                    >
                                        {{ faq.is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="whitespace-nowrap p-4 text-right">
                                    <button
                                        type="button"
                                        class="mr-3 font-medium text-heritage-navy hover:underline"
                                        @click="edit(faq)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="font-medium text-red-600 hover:underline"
                                        @click="remove(faq)"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!faqs.length">
                                <td colspan="4" class="p-10 text-center text-slate-500">
                                    No FAQs yet. Select “New FAQ” to create one.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-heritage-navy/70 p-4"
                @click.self="reset"
                @keydown.esc.prevent="reset"
                role="dialog"
                aria-modal="true"
                aria-labelledby="faq-modal-title"
            >
                <form
                    class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white p-6 shadow-2xl"
                    @submit.prevent="submit"
                >
                    <div class="flex items-center justify-between">
                        <h2 id="faq-modal-title" class="text-lg font-semibold text-heritage-navy">
                            {{ editing ? 'Edit FAQ' : 'Create FAQ' }}
                        </h2>
                        <button
                            type="button"
                            class="rounded-full px-2 py-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                            aria-label="Close FAQ form"
                            :disabled="form.processing"
                            @click="reset"
                        >
                            ×
                        </button>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="faq-question" class="mb-1 block text-sm font-medium text-slate-700">Question</label>
                            <input
                                id="faq-question"
                                v-model="form.question"
                                type="text"
                                required
                                maxlength="1000"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.question" class="mt-1 text-xs text-red-600">{{ form.errors.question }}</p>
                        </div>

                        <div>
                            <label for="faq-answer" class="mb-1 block text-sm font-medium text-slate-700">Answer</label>
                            <textarea
                                id="faq-answer"
                                v-model="form.answer"
                                rows="6"
                                required
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            ></textarea>
                            <p v-if="form.errors.answer" class="mt-1 text-xs text-red-600">{{ form.errors.answer }}</p>
                        </div>

                        <div>
                            <label for="faq-category" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                            <input
                                id="faq-category"
                                v-model="form.category"
                                type="text"
                                maxlength="100"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.category" class="mt-1 text-xs text-red-600">{{ form.errors.category }}</p>
                        </div>

                        <div>
                            <label for="faq-sort-order" class="mb-1 block text-sm font-medium text-slate-700">Display order</label>
                            <input
                                id="faq-sort-order"
                                v-model.number="form.sort_order"
                                type="number"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.sort_order" class="mt-1 text-xs text-red-600">{{ form.errors.sort_order }}</p>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300">
                            Active
                        </label>
                        <p v-if="form.errors.is_active" class="text-xs text-red-600">{{ form.errors.is_active }}</p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="btn-gold" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : editing ? 'Save changes' : 'Create FAQ' }}
                        </button>
                        <button
                            type="button"
                            class="rounded border border-slate-300 px-4 py-2 text-sm"
                            :disabled="form.processing"
                            @click="reset"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
