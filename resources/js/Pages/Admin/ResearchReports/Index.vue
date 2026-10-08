<script setup>
import { onBeforeUnmount, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    reports: {
        type: Array,
        default: () => [],
    },
})

const editing = ref(null)
const showModal = ref(false)
const imagePreview = ref(null)
const imageInput = ref(null)
const pdfInput = ref(null)
let previewObjectUrl = null

const form = useForm({
    title: '',
    slug: '',
    category: '',
    excerpt: '',
    content: '',
    published_at: '',
    is_published: true,
    sort_order: 0,
    featured_image: null,
    pdf_file: null,
})

function clearPreviewObjectUrl() {
    if (previewObjectUrl) {
        URL.revokeObjectURL(previewObjectUrl)
        previewObjectUrl = null
    }
}

function reset() {
    showModal.value = false
    editing.value = null
    clearPreviewObjectUrl()
    imagePreview.value = null
    form.reset()
    form.clearErrors()

    if (imageInput.value) imageInput.value.value = ''
    if (pdfInput.value) pdfInput.value.value = ''
}

function openCreate() {
    reset()
    showModal.value = true
}

function edit(report) {
    editing.value = report
    clearPreviewObjectUrl()
    imagePreview.value = report.featured_image || null
    form.clearErrors()
    form.title = report.title || ''
    form.slug = report.slug || ''
    form.category = report.category || ''
    form.excerpt = report.excerpt || ''
    form.content = report.content || ''
    form.published_at = report.published_at ? report.published_at.slice(0, 10) : ''
    form.is_published = Boolean(report.is_published)
    form.sort_order = report.sort_order ?? 0
    form.featured_image = null
    form.pdf_file = null

    if (imageInput.value) imageInput.value.value = ''
    if (pdfInput.value) pdfInput.value.value = ''

    showModal.value = true
}

function selectImage(event) {
    const file = event.target.files?.[0]
    clearPreviewObjectUrl()
    form.featured_image = file || null
    imagePreview.value = file ? URL.createObjectURL(file) : editing.value?.featured_image || null
    previewObjectUrl = file ? imagePreview.value : null
}

function selectPdf(event) {
    form.pdf_file = event.target.files?.[0] || null
}

function submit() {
    form.post(
        editing.value ? `/admin/research-reports/${editing.value.id}` : '/admin/research-reports',
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: reset,
        }
    )
}

function toggle(report) {
    router.post(`/admin/research-reports/${report.id}/toggle`, {}, { preserveScroll: true })
}

function remove(report) {
    if (confirm(`Are you sure you want to delete "${report.title}"?`)) {
        router.delete(`/admin/research-reports/${report.id}`, { preserveScroll: true })
    }
}

function assetUrl(path) {
    if (!path) return ''
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('blob:') || path.startsWith('/')) {
        return path
    }
    return path.startsWith('research-reports/') ? `/storage/${path}` : `/${path}`
}

function formatDate(value) {
    if (!value) return 'Not set'
    return new Date(value).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}

onBeforeUnmount(clearPreviewObjectUrl)
</script>

<template>
    <AdminLayout>
        <div class="p-6 lg:p-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-heritage-navy">Research Reports</h1>
                    <p class="mt-1 text-sm text-slate-500">Create and manage published research.</p>
                </div>
                <button type="button" class="btn-gold" @click="openCreate">+ New Report</button>
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-4">Report</th>
                                <th class="p-4">Category</th>
                                <th class="p-4">Published</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="report in reports" :key="report.id" class="border-t border-slate-100">
                                <td class="p-4 font-medium text-heritage-navy">{{ report.title }}</td>
                                <td class="p-4 text-slate-600">{{ report.category || '—' }}</td>
                                <td class="whitespace-nowrap p-4 text-slate-600">{{ formatDate(report.published_at) }}</td>
                                <td class="p-4">
                                    <button
                                        type="button"
                                        class="rounded-full px-3 py-1 text-xs"
                                        :class="report.is_published ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'"
                                        :aria-label="`${report.is_published ? 'Unpublish' : 'Publish'} ${report.title}`"
                                        @click="toggle(report)"
                                    >
                                        {{ report.is_published ? 'Published' : 'Draft' }}
                                    </button>
                                </td>
                                <td class="whitespace-nowrap p-4 text-right">
                                    <button
                                        type="button"
                                        class="mr-3 font-medium text-heritage-navy hover:underline"
                                        @click="edit(report)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="font-medium text-red-600 hover:underline"
                                        @click="remove(report)"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!reports.length">
                                <td colspan="5" class="p-10 text-center text-slate-500">
                                    No research reports yet. Select “New Report” to create one.
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
                aria-labelledby="research-report-modal-title"
            >
                <form
                    class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white p-6 shadow-2xl"
                    @submit.prevent="submit"
                >
                    <div class="flex items-center justify-between">
                        <h2 id="research-report-modal-title" class="text-lg font-semibold text-heritage-navy">
                            {{ editing ? 'Edit research report' : 'Create research report' }}
                        </h2>
                        <button
                            type="button"
                            class="rounded-full px-2 py-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                            aria-label="Close report form"
                            :disabled="form.processing"
                            @click="reset"
                        >
                            ×
                        </button>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="report-title" class="mb-1 block text-sm font-medium text-slate-700">Title</label>
                            <input
                                id="report-title"
                                v-model="form.title"
                                type="text"
                                required
                                maxlength="255"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label for="report-slug" class="mb-1 block text-sm font-medium text-slate-700">URL slug</label>
                            <input
                                id="report-slug"
                                v-model="form.slug"
                                type="text"
                                maxlength="255"
                                placeholder="Generated from title if left blank"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.slug" class="mt-1 text-xs text-red-600">{{ form.errors.slug }}</p>
                        </div>

                        <div>
                            <label for="report-category" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                            <input
                                id="report-category"
                                v-model="form.category"
                                type="text"
                                maxlength="255"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.category" class="mt-1 text-xs text-red-600">{{ form.errors.category }}</p>
                        </div>

                        <div>
                            <label for="report-excerpt" class="mb-1 block text-sm font-medium text-slate-700">Excerpt</label>
                            <textarea
                                id="report-excerpt"
                                v-model="form.excerpt"
                                rows="3"
                                maxlength="2000"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            ></textarea>
                            <p v-if="form.errors.excerpt" class="mt-1 text-xs text-red-600">{{ form.errors.excerpt }}</p>
                        </div>

                        <div>
                            <label for="report-content" class="mb-1 block text-sm font-medium text-slate-700">Content</label>
                            <textarea
                                id="report-content"
                                v-model="form.content"
                                rows="8"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            ></textarea>
                            <p v-if="form.errors.content" class="mt-1 text-xs text-red-600">{{ form.errors.content }}</p>
                        </div>

                        <div>
                            <label for="report-published-at" class="mb-1 block text-sm font-medium text-slate-700">Publication date</label>
                            <input
                                id="report-published-at"
                                v-model="form.published_at"
                                type="date"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.published_at" class="mt-1 text-xs text-red-600">{{ form.errors.published_at }}</p>
                        </div>

                        <div>
                            <label for="report-image" class="mb-1 block text-sm font-medium text-slate-700">Featured image</label>
                            <input
                                id="report-image"
                                ref="imageInput"
                                type="file"
                                accept="image/*"
                                class="w-full text-sm text-slate-600"
                                @change="selectImage"
                            >
                            <p v-if="form.errors.featured_image" class="mt-1 text-xs text-red-600">{{ form.errors.featured_image }}</p>
                            <img
                                v-if="imagePreview"
                                :src="assetUrl(imagePreview)"
                                alt="Research report image preview"
                                class="mt-3 h-36 w-full rounded object-cover"
                            >
                        </div>

                        <div>
                            <label for="report-pdf" class="mb-1 block text-sm font-medium text-slate-700">PDF report</label>
                            <input
                                id="report-pdf"
                                ref="pdfInput"
                                type="file"
                                accept="application/pdf,.pdf"
                                class="w-full text-sm text-slate-600"
                                @change="selectPdf"
                            >
                            <p v-if="editing?.pdf_file && !form.pdf_file" class="mt-1 text-xs text-slate-500">
                                A PDF is already attached. Choose a new file to replace it.
                            </p>
                            <p v-if="form.errors.pdf_file" class="mt-1 text-xs text-red-600">{{ form.errors.pdf_file }}</p>
                        </div>

                        <div>
                            <label for="report-sort-order" class="mb-1 block text-sm font-medium text-slate-700">Display order</label>
                            <input
                                id="report-sort-order"
                                v-model.number="form.sort_order"
                                type="number"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.sort_order" class="mt-1 text-xs text-red-600">{{ form.errors.sort_order }}</p>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input v-model="form.is_published" type="checkbox" class="rounded border-slate-300">
                            Publish report
                        </label>
                        <p v-if="form.errors.is_published" class="text-xs text-red-600">{{ form.errors.is_published }}</p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="btn-gold" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : editing ? 'Save changes' : 'Create report' }}
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
