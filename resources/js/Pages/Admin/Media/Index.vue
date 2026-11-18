<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    media: {
        type: Array,
        default: () => [],
    },
    documents: {
        type: Array,
        default: () => [],
    },
    canManageDocuments: {
        type: Boolean,
        default: false,
    },
})

const fileInput = ref(null)
const documentInput = ref(null)
const form = useForm({ file: null })
const documentForm = useForm({ name: '', caption: '', file: null })

function submit() {
    form.post('/admin/media', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            form.clearErrors()
            if (fileInput.value) fileInput.value.value = ''
        },
    })
}

function remove(item) {
    if (confirm(`Are you sure you want to delete "${fileName(item)}"?`)) {
        router.delete(`/admin/media/${item.id}`, { preserveScroll: true })
    }
}

function submitDocument() {
    documentForm.post('/admin/media/documents', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            documentForm.reset()
            documentForm.clearErrors()
            if (documentInput.value) documentInput.value.value = ''
        },
    })
}

function fileName(item) {
    return item.name || item.file_path?.split('/').pop() || 'Unnamed image'
}

function fileType(item) {
    if (item.mime_type) return item.mime_type

    const extension = fileName(item).split('.').pop()?.toLowerCase()
    return extension ? `${extension.toUpperCase()} image` : 'Image'
}

function fileSize(bytes) {
    if (bytes === null || bytes === undefined) return 'Size unavailable'

    const size = Number(bytes)
    if (size < 1024) return `${size} B`
    if (size < 1024 * 1024) return `${(size / 1024).toFixed(1)} KB`
    return `${(size / (1024 * 1024)).toFixed(2)} MB`
}

function url(item) {
    const disk = item.disk || 'public'
    return disk === 'public' ? `/storage/${item.file_path}` : `/${item.file_path}`
}
</script>

<template>
    <AdminLayout>
        <div class="p-6 lg:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-heritage-navy">Media Library</h1>
                <p class="mt-1 text-sm text-slate-500">Upload and manage website images.</p>
            </div>

            <form class="mb-8 rounded-lg border border-slate-200 bg-white p-6" @submit.prevent="submit">
                <label for="media-file" class="mb-2 block text-sm font-medium text-slate-700">
                    Choose an image
                </label>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                    <div class="min-w-0 flex-1">
                        <input id="media-file" ref="fileInput" type="file"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required
                            class="block w-full rounded border border-slate-300 p-3 text-sm text-slate-700 file:mr-4 file:rounded file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium"
                            @change="form.file = $event.target.files?.[0] || null">
                        <p class="mt-2 text-xs text-slate-500">Allowed: JPG, JPEG, PNG, WebP. Maximum size: 5 MB.</p>
                        <p v-if="form.errors.file" class="mt-2 text-sm text-red-600">{{ form.errors.file }}</p>
                    </div>
                    <button type="submit" class="btn-gold shrink-0" :disabled="form.processing || !form.file">
                        {{ form.processing ? 'Uploading...' : 'Upload image' }}
                    </button>
                </div>
            </form>

            <section class="mb-8 rounded-lg border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-semibold text-heritage-navy">Downloadable documents</h2>
                <p class="mt-1 text-sm text-slate-500">
                    <template v-if="canManageDocuments">
                        Publish PDF, DOC or DOCX forms and resources on the public Downloads page. Maximum size: 10 MB.
                    </template>
                    <template v-else>
                        Downloadable forms and resources published on the public Downloads page.
                    </template>
                </p>
                <form v-if="canManageDocuments" class="mt-5 grid gap-4 md:grid-cols-2" @submit.prevent="submitDocument">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Document title <span class="text-red-600">*</span></span>
                        <input v-model="documentForm.name" type="text" required maxlength="255"
                            class="mt-2 w-full rounded border border-slate-300 p-3 text-sm">
                        <span v-if="documentForm.errors.name" class="mt-1 block text-sm text-red-600">{{ documentForm.errors.name }}</span>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Description</span>
                        <input v-model="documentForm.caption" type="text" maxlength="1000"
                            class="mt-2 w-full rounded border border-slate-300 p-3 text-sm">
                        <span v-if="documentForm.errors.caption" class="mt-1 block text-sm text-red-600">{{ documentForm.errors.caption }}</span>
                    </label>
                    <label class="block md:col-span-2">
                        <span class="text-sm font-medium text-slate-700">Document file <span class="text-red-600">*</span></span>
                        <input id="download-document" ref="documentInput" type="file" required accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            class="mt-2 block w-full rounded border border-slate-300 p-3 text-sm"
                            @change="documentForm.file = $event.target.files?.[0] || null">
                        <span v-if="documentForm.errors.file" class="mt-1 block text-sm text-red-600">{{ documentForm.errors.file }}</span>
                    </label>
                    <div class="md:col-span-2">
                        <button type="submit" class="btn-gold" :disabled="documentForm.processing || !documentForm.file">
                            {{ documentForm.processing ? 'Publishing...' : 'Publish document' }}
                        </button>
                    </div>
                </form>

                <div v-if="documents.length" class="mt-6 divide-y divide-slate-200 border-y border-slate-200">
                    <article v-for="document in documents" :key="document.id"
                        class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-heritage-navy">{{ document.name }}</p>
                            <p v-if="document.caption" class="mt-1 text-sm text-slate-500">{{ document.caption }}</p>
                        </div>
                        <button v-if="canManageDocuments" type="button" class="shrink-0 text-sm font-medium text-red-600 hover:underline"
                            @click="remove(document)">Delete document</button>
                    </article>
                </div>
                <p v-else-if="canManageDocuments" class="mt-5 text-sm text-slate-500">No downloadable documents have been published.</p>
            </section>

            <div v-if="media.length" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <article v-for="item in media" :key="item.id"
                    class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                    <a :href="url(item)" target="_blank" rel="noopener noreferrer" class="block">
                        <div class="flex h-48 items-center justify-center bg-slate-50">
                            <img :src="url(item)" :alt="item.alt_text || fileName(item)"
                                class="h-full w-full object-cover" loading="lazy">
                        </div>
                    </a>
                    <div class="p-4">
                        <p class="truncate font-medium text-heritage-navy" :title="fileName(item)">
                            {{ fileName(item) }}
                        </p>
                        <dl class="mt-3 space-y-1 text-xs text-slate-500">
                            <div class="flex justify-between gap-2">
                                <dt>File type</dt>
                                <dd class="truncate text-right">{{ fileType(item) }}</dd>
                            </div>
                            <div class="flex justify-between gap-2">
                                <dt>File size</dt>
                                <dd>{{ fileSize(item.file_size) }}</dd>
                            </div>
                        </dl>
                        <div class="mt-4 flex justify-end border-t border-slate-100 pt-3">
                            <button type="button" class="text-sm font-medium text-red-600 hover:underline"
                                @click="remove(item)">
                                Delete image
                            </button>
                        </div>
                    </div>
                </article>
            </div>

            <div v-else class="rounded-lg border border-slate-200 bg-white p-10 text-center text-sm text-slate-500">
                No images uploaded yet.
            </div>
        </div>
    </AdminLayout>
</template>
