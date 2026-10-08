<script setup>
import { onBeforeUnmount, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useAccessibleDialog } from '@/Composables/useAccessibleDialog'

defineProps({
    articles: {
        type: Array,
        default: () => [],
    },
})

const editing = ref(null)
const showModal = ref(false)
const { handleKeydown: handleDialogKeydown, setDialogElement } = useAccessibleDialog(showModal, reset)
const imagePreview = ref(null)
const imageInput = ref(null)
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

    if (imageInput.value) {
        imageInput.value.value = ''
    }
}

function openCreate() {
    reset()
    showModal.value = true
}

function edit(article) {
    editing.value = article
    clearPreviewObjectUrl()
    imagePreview.value = article.featured_image || null
    form.clearErrors()
    form.title = article.title || ''
    form.slug = article.slug || ''
    form.category = article.category || ''
    form.excerpt = article.excerpt || ''
    form.content = article.content || ''
    form.published_at = article.published_at ? article.published_at.slice(0, 10) : ''
    form.is_published = Boolean(article.is_published)
    form.sort_order = article.sort_order ?? 0
    form.featured_image = null

    if (imageInput.value) {
        imageInput.value.value = ''
    }

    showModal.value = true
}

function selectImage(event) {
    const file = event.target.files?.[0]
    clearPreviewObjectUrl()
    form.featured_image = file || null
    imagePreview.value = file ? URL.createObjectURL(file) : editing.value?.featured_image || null
    previewObjectUrl = file ? imagePreview.value : null
}

function submit() {
    form.post(
        editing.value ? `/admin/articles/${editing.value.id}` : '/admin/articles',
        {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: reset,
        }
    )
}

function toggle(article) {
    router.post(`/admin/articles/${article.id}/toggle`, {}, { preserveScroll: true })
}

function remove(article) {
    if (confirm(`Are you sure you want to delete "${article.title}"?`)) {
        router.delete(`/admin/articles/${article.id}`, { preserveScroll: true })
    }
}

function assetUrl(path) {
    if (!path) return ''
    if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('blob:') || path.startsWith('/')) {
        return path
    }
    return path.startsWith('articles/') ? `/storage/${path}` : `/${path}`
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
                    <h1 class="text-2xl font-semibold text-heritage-navy">Articles & News</h1>
                    <p class="mt-1 text-sm text-slate-500">Manage website content.</p>
                </div>
                <button type="button" class="btn-gold" @click="openCreate">+ New Article</button>
            </div>

            <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-4">Article</th>
                                <th class="p-4">Category</th>
                                <th class="p-4">Published</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="article in articles" :key="article.id" class="border-t border-slate-100">
                                <td class="p-4 font-medium text-heritage-navy">{{ article.title }}</td>
                                <td class="p-4 text-slate-600">{{ article.category || '—' }}</td>
                                <td class="whitespace-nowrap p-4 text-slate-600">{{ formatDate(article.published_at) }}</td>
                                <td class="p-4">
                                    <button
                                        type="button"
                                        class="rounded-full px-3 py-1 text-xs"
                                        :class="article.is_published ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'"
                                        :aria-label="`${article.is_published ? 'Unpublish' : 'Publish'} ${article.title}`"
                                        @click="toggle(article)"
                                    >
                                        {{ article.is_published ? 'Published' : 'Draft' }}
                                    </button>
                                </td>
                                <td class="whitespace-nowrap p-4 text-right">
                                    <button
                                        type="button"
                                        class="mr-3 font-medium text-heritage-navy hover:underline"
                                        @click="edit(article)"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="font-medium text-red-600 hover:underline"
                                        @click="remove(article)"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!articles.length">
                                <td colspan="5" class="p-10 text-center text-slate-500">
                                    No articles yet. Select “New Article” to create one.
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
                @keydown="handleDialogKeydown"
                :ref="setDialogElement"
                tabindex="-1"
                role="dialog"
                aria-modal="true"
                aria-labelledby="article-modal-title"
            >
                <form
                    class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white p-6 shadow-2xl"
                    @submit.prevent="submit"
                >
                    <div class="flex items-center justify-between">
                        <h2 id="article-modal-title" class="text-lg font-semibold text-heritage-navy">
                            {{ editing ? 'Edit article' : 'Create article' }}
                        </h2>
                        <button
                            type="button"
                            class="rounded-full px-2 py-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
                            aria-label="Close article form"
                            :disabled="form.processing"
                            @click="reset"
                        >
                            ×
                        </button>
                    </div>

                    <div class="mt-5 space-y-4">
                        <div>
                            <label for="article-title" class="mb-1 block text-sm font-medium text-slate-700">Title</label>
                            <input
                                id="article-title"
                                v-model="form.title"
                                type="text"
                                required
                                maxlength="255"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                        </div>

                        <div>
                            <label for="article-slug" class="mb-1 block text-sm font-medium text-slate-700">URL slug</label>
                            <input
                                id="article-slug"
                                v-model="form.slug"
                                type="text"
                                maxlength="255"
                                placeholder="Generated from title if left blank"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.slug" class="mt-1 text-xs text-red-600">{{ form.errors.slug }}</p>
                        </div>

                        <div>
                            <label for="article-category" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                            <input
                                id="article-category"
                                v-model="form.category"
                                type="text"
                                maxlength="100"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.category" class="mt-1 text-xs text-red-600">{{ form.errors.category }}</p>
                        </div>

                        <div>
                            <label for="article-excerpt" class="mb-1 block text-sm font-medium text-slate-700">Excerpt</label>
                            <textarea
                                id="article-excerpt"
                                v-model="form.excerpt"
                                rows="3"
                                maxlength="2000"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            ></textarea>
                            <p v-if="form.errors.excerpt" class="mt-1 text-xs text-red-600">{{ form.errors.excerpt }}</p>
                        </div>

                        <div>
                            <label for="article-content" class="mb-1 block text-sm font-medium text-slate-700">Content</label>
                            <textarea
                                id="article-content"
                                v-model="form.content"
                                rows="8"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            ></textarea>
                            <p v-if="form.errors.content" class="mt-1 text-xs text-red-600">{{ form.errors.content }}</p>
                        </div>

                        <div>
                            <label for="article-published-at" class="mb-1 block text-sm font-medium text-slate-700">Publication date</label>
                            <input
                                id="article-published-at"
                                v-model="form.published_at"
                                type="date"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.published_at" class="mt-1 text-xs text-red-600">{{ form.errors.published_at }}</p>
                        </div>

                        <div>
                            <label for="article-image" class="mb-1 block text-sm font-medium text-slate-700">Featured image</label>
                            <input
                                id="article-image"
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
                                alt="Article image preview"
                                class="mt-3 h-36 w-full rounded object-cover"
                            >
                        </div>

                        <div>
                            <label for="article-sort-order" class="mb-1 block text-sm font-medium text-slate-700">Display order</label>
                            <input
                                id="article-sort-order"
                                v-model.number="form.sort_order"
                                type="number"
                                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"
                            >
                            <p v-if="form.errors.sort_order" class="mt-1 text-xs text-red-600">{{ form.errors.sort_order }}</p>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input v-model="form.is_published" type="checkbox" class="rounded border-slate-300">
                            Publish article
                        </label>
                        <p v-if="form.errors.is_published" class="text-xs text-red-600">{{ form.errors.is_published }}</p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="btn-gold" :disabled="form.processing">
                            {{ form.processing ? 'Saving...' : editing ? 'Save changes' : 'Create article' }}
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
