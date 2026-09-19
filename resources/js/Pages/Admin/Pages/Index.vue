<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'


const props = defineProps({
    pages: {
        type: Array,
        default: () => [],
    },
})

const showModal = ref(false)
const editingPage = ref(null)
const imagePreview = ref(null)
const processing = ref(false)

const form = ref({
    title: '',
    slug: '',
    eyebrow: '',
    excerpt: '',
    content: '',
    featured_image: null,
    meta_title: '',
    meta_description: '',
    is_published: true,
    sort_order: 1,
})

const sortedPages = computed(() => {
    return [...props.pages].sort((a, b) => {
        return (a.sort_order ?? 0) - (b.sort_order ?? 0)
    })
})

const publishedCount = computed(() => {
    return props.pages.filter(page => page.is_published).length
})

function resetForm() {
    form.value = {
        title: '',
        slug: '',
        eyebrow: '',
        excerpt: '',
        content: '',
        featured_image: null,
        meta_title: '',
        meta_description: '',
        is_published: true,
        sort_order: props.pages.length + 1,
    }

    editingPage.value = null
    imagePreview.value = null
}

function openCreate() {
    resetForm()
    showModal.value = true
}

function openEdit(page) {
    editingPage.value = page

    form.value = {
        title: page.title ?? '',
        slug: page.slug ?? '',
        eyebrow: page.eyebrow ?? '',
        excerpt: page.excerpt ?? '',
        content: page.content ?? '',
        featured_image: null,
        meta_title: page.meta_title ?? '',
        meta_description: page.meta_description ?? '',
        is_published: Boolean(page.is_published),
        sort_order: page.sort_order ?? 1,
    }

    imagePreview.value = page.featured_image
        ? assetUrl(page.featured_image)
        : null

    showModal.value = true
}

function closeModal() {
    if (processing.value) return

    showModal.value = false
    resetForm()
}

function handleImage(event) {
    const file = event.target.files?.[0] ?? null

    form.value.featured_image = file

    if (file) {
        imagePreview.value = URL.createObjectURL(file)
    }
}

function generateSlug() {
    if (editingPage.value) return

    form.value.slug = form.value.title
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
}

function submit() {
    processing.value = true

    const url = editingPage.value
        ? `/admin/pages/${editingPage.value.id}`
        : '/admin/pages'

    router.post(
        url,
        {
            ...form.value,
        },
        {
            forceFormData: true,

            onSuccess: () => {
                showModal.value = false
                resetForm()
            },

            onFinish: () => {
                processing.value = false
            },
        }
    )
}

function togglePage(page) {
    router.post(
        `/admin/pages/${page.id}/toggle`,
        {},
        {
            preserveScroll: true,
        }
    )
}

function deletePage(page) {
    if (
        !confirm(
            `Are you sure you want to delete "${page.title}"?`
        )
    ) {
        return
    }

    router.delete(
        `/admin/pages/${page.id}`,
        {
            preserveScroll: true,
        }
    )
}

function assetUrl(path) {
    if (!path) return ''

    return path.startsWith('/')
        ? path
        : `/${path}`
}
</script>

<template>
    <AdminLayout>

        <!-- =========================================================
             PAGE HEADER
        ========================================================== -->
        <div class="border-b border-heritage-border bg-white">

            <div class="w-full p-6 lg:p-8">

                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div>

                        <div
                            class="text-[10px] font-semibold uppercase tracking-[0.25em] text-heritage-gold"
                        >
                            Content Management
                        </div>

                        <h1
                            class="mt-1 font-display text-3xl text-heritage-navy"
                        >
                            Pages
                        </h1>

                        <p class="mt-1 text-sm text-heritage-muted">
                            Manage the main pages and content published on the Heritage website.
                        </p>

                    </div>

                    <button
                        type="button"
                        @click="openCreate"
                        class="inline-flex items-center justify-center gap-2 rounded bg-heritage-gold px-5 py-2.5 text-sm font-semibold text-heritage-deep transition hover:bg-heritage-gold-light"
                    >
                        <span class="text-lg leading-none">+</span>
                        Add Page
                    </button>

                </div>

            </div>

        </div>


        <!-- =========================================================
             CONTENT
        ========================================================== -->
        <div class="p-6 lg:p-8">

            <!-- SUMMARY -->
            <div
                class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >

                <div
                    class="rounded-xl border border-heritage-border bg-white p-5 shadow-sm"
                >
                    <p class="text-xs text-heritage-muted">
                        Total Pages
                    </p>

                    <p
                        class="mt-2 text-3xl font-semibold text-heritage-navy"
                    >
                        {{ pages.length }}
                    </p>

                    <p class="mt-1 text-xs text-heritage-muted">
                        Website content pages
                    </p>
                </div>


                <div
                    class="rounded-xl border border-heritage-border bg-white p-5 shadow-sm"
                >
                    <p class="text-xs text-heritage-muted">
                        Published Pages
                    </p>

                    <p
                        class="mt-2 text-3xl font-semibold text-heritage-navy"
                    >
                        {{ publishedCount }}
                    </p>

                    <p class="mt-1 text-xs text-heritage-muted">
                        Currently visible on the website
                    </p>
                </div>


                <div
                    class="rounded-xl border border-heritage-border bg-white p-5 shadow-sm"
                >
                    <p class="text-xs text-heritage-muted">
                        Draft Pages
                    </p>

                    <p
                        class="mt-2 text-3xl font-semibold text-heritage-navy"
                    >
                        {{ pages.length - publishedCount }}
                    </p>

                    <p class="mt-1 text-xs text-heritage-muted">
                        Not currently published
                    </p>
                </div>

            </div>


            <!-- =====================================================
                 PAGES TABLE
            ====================================================== -->
            <div
                class="overflow-hidden rounded-xl border border-heritage-border bg-white shadow-sm"
            >

                <div
                    class="flex flex-col gap-2 border-b border-heritage-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div>

                        <h2
                            class="text-sm font-semibold text-heritage-navy"
                        >
                            Website Pages
                        </h2>

                        <p
                            class="mt-1 text-xs text-heritage-muted"
                        >
                            Manage page content, publication status and display order.
                        </p>

                    </div>

                    <span
                        class="text-xs text-heritage-muted"
                    >
                        {{ pages.length }}
                        page{{ pages.length === 1 ? '' : 's' }}
                    </span>

                </div>


                <!-- =================================================
                     DESKTOP TABLE
                ================================================== -->
                <div class="hidden overflow-x-auto lg:block">

                    <table class="w-full text-left">

                        <thead>

                            <tr
                                class="border-b border-heritage-border bg-slate-50"
                            >

                                <th
                                    class="px-5 py-3 text-[10px] font-semibold uppercase tracking-wider text-heritage-muted"
                                >
                                    Page
                                </th>

                                <th
                                    class="px-5 py-3 text-[10px] font-semibold uppercase tracking-wider text-heritage-muted"
                                >
                                    Slug
                                </th>

                                <th
                                    class="px-5 py-3 text-[10px] font-semibold uppercase tracking-wider text-heritage-muted"
                                >
                                    Position
                                </th>

                                <th
                                    class="px-5 py-3 text-[10px] font-semibold uppercase tracking-wider text-heritage-muted"
                                >
                                    Status
                                </th>

                                <th
                                    class="px-5 py-3 text-right text-[10px] font-semibold uppercase tracking-wider text-heritage-muted"
                                >
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-for="page in sortedPages"
                                :key="page.id"
                                class="border-b border-heritage-border last:border-b-0"
                            >

                                <!-- PAGE -->
                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-4">

                                        <div
                                            class="h-14 w-20 shrink-0 overflow-hidden rounded-lg bg-heritage-offwhite"
                                        >

                                            <img
                                                v-if="page.featured_image"
                                                :src="assetUrl(page.featured_image)"
                                                :alt="page.title"
                                                class="h-full w-full object-cover"
                                            />

                                            <div
                                                v-else
                                                class="flex h-full items-center justify-center text-[10px] text-heritage-muted"
                                            >
                                                No image
                                            </div>

                                        </div>

                                        <div class="min-w-0">

                                            <p
                                                class="font-semibold text-heritage-navy"
                                            >
                                                {{ page.title }}
                                            </p>

                                            <p
                                                v-if="page.eyebrow"
                                                class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-heritage-gold"
                                            >
                                                {{ page.eyebrow }}
                                            </p>

                                            <p
                                                v-if="page.excerpt"
                                                class="mt-1 max-w-md line-clamp-2 text-xs leading-5 text-heritage-muted"
                                            >
                                                {{ page.excerpt }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                <!-- SLUG -->
                                <td class="px-5 py-4">

                                    <code
                                        class="rounded bg-slate-50 px-2 py-1 text-xs text-slate-600"
                                    >
                                        /{{ page.slug }}
                                    </code>

                                </td>


                                <!-- POSITION -->
                                <td class="px-5 py-4">

                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-heritage-offwhite text-xs font-semibold text-heritage-navy"
                                    >
                                        {{ page.sort_order }}
                                    </div>

                                </td>


                                <!-- STATUS -->
                                <td class="px-5 py-4">

                                    <button
                                        type="button"
                                        @click="togglePage(page)"
                                        class="rounded-full px-3 py-1 text-[10px] font-semibold transition"
                                        :class="
                                            page.is_published
                                                ? 'bg-green-50 text-green-700 hover:bg-green-100'
                                                : 'bg-slate-100 text-slate-500 hover:bg-slate-200'
                                        "
                                    >
                                        {{
                                            page.is_published
                                                ? 'Published'
                                                : 'Draft'
                                        }}
                                    </button>

                                </td>


                                <!-- ACTIONS -->
                                <td class="px-5 py-4">

                                    <div
                                        class="flex justify-end gap-2"
                                    >

                                        <a
                                            v-if="page.is_published"
                                            :href="`/${page.slug}`"
                                            target="_blank"
                                            class="rounded border border-heritage-border px-3 py-2 text-xs font-semibold text-heritage-muted transition hover:border-heritage-gold hover:text-heritage-gold"
                                        >
                                            View
                                        </a>

                                        <button
                                            type="button"
                                            @click="openEdit(page)"
                                            class="rounded border border-heritage-border px-3 py-2 text-xs font-semibold text-heritage-navy transition hover:border-heritage-gold hover:text-heritage-gold"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            @click="deletePage(page)"
                                            class="rounded border border-red-200 px-3 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                                        >
                                            Delete
                                        </button>

                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->
                            <tr v-if="!sortedPages.length">

                                <td
                                    colspan="5"
                                    class="px-5 py-16 text-center"
                                >

                                    <div
                                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-heritage-offwhite text-xl text-heritage-muted"
                                    >
                                        +
                                    </div>

                                    <h3
                                        class="mt-4 text-sm font-semibold text-heritage-navy"
                                    >
                                        No pages yet
                                    </h3>

                                    <p
                                        class="mt-1 text-xs text-heritage-muted"
                                    >
                                        Add your first website page.
                                    </p>

                                    <button
                                        type="button"
                                        @click="openCreate"
                                        class="mt-4 text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                                    >
                                        + Add Page
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- =================================================
                     MOBILE CARDS
                ================================================== -->
                <div
                    class="divide-y divide-heritage-border lg:hidden"
                >

                    <div
                        v-for="page in sortedPages"
                        :key="page.id"
                        class="p-5"
                    >

                        <div class="flex gap-4">

                            <div
                                class="h-20 w-28 shrink-0 overflow-hidden rounded-lg bg-heritage-offwhite"
                            >

                                <img
                                    v-if="page.featured_image"
                                    :src="assetUrl(page.featured_image)"
                                    :alt="page.title"
                                    class="h-full w-full object-cover"
                                />

                                <div
                                    v-else
                                    class="flex h-full items-center justify-center text-[10px] text-heritage-muted"
                                >
                                    No image
                                </div>

                            </div>


                            <div class="min-w-0 flex-1">

                                <div
                                    class="mb-1 text-[10px] font-semibold uppercase tracking-wider text-heritage-gold"
                                >
                                    Page {{ page.sort_order }}
                                </div>

                                <h3
                                    class="text-sm font-semibold text-heritage-navy"
                                >
                                    {{ page.title }}
                                </h3>

                                <p
                                    class="mt-1 truncate text-[11px] text-slate-500"
                                >
                                    /{{ page.slug }}
                                </p>

                                <p
                                    v-if="page.excerpt"
                                    class="mt-1 line-clamp-2 text-xs leading-5 text-heritage-muted"
                                >
                                    {{ page.excerpt }}
                                </p>

                                <div class="mt-3">

                                    <button
                                        type="button"
                                        @click="togglePage(page)"
                                        class="rounded-full px-3 py-1 text-[10px] font-semibold"
                                        :class="
                                            page.is_published
                                                ? 'bg-green-50 text-green-700'
                                                : 'bg-slate-100 text-slate-500'
                                        "
                                    >
                                        {{
                                            page.is_published
                                                ? 'Published'
                                                : 'Draft'
                                        }}
                                    </button>

                                </div>

                            </div>

                        </div>


                        <div
                            class="mt-4 grid grid-cols-2 gap-2"
                        >

                            <a
                                v-if="page.is_published"
                                :href="`/${page.slug}`"
                                target="_blank"
                                class="rounded border border-heritage-border px-3 py-2 text-center text-xs font-semibold text-heritage-muted"
                            >
                                View
                            </a>

                            <button
                                type="button"
                                @click="openEdit(page)"
                                class="rounded border border-heritage-border px-3 py-2 text-xs font-semibold text-heritage-navy"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                @click="deletePage(page)"
                                class="rounded border border-red-200 px-3 py-2 text-xs font-semibold text-red-600"
                            >
                                Delete
                            </button>

                        </div>

                    </div>


                    <div
                        v-if="!sortedPages.length"
                        class="px-5 py-12 text-center"
                    >

                        <p
                            class="text-sm font-semibold text-heritage-navy"
                        >
                            No pages yet.
                        </p>

                        <button
                            type="button"
                            @click="openCreate"
                            class="mt-3 text-xs font-semibold text-heritage-navy hover:text-heritage-gold"
                        >
                            + Add Page
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             CREATE / EDIT MODAL
        ========================================================== -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-heritage-deep/60 p-4 backdrop-blur-sm"
            @click.self="closeModal"
        >

            <div
                class="max-h-[92vh] w-full max-w-4xl overflow-y-auto rounded-xl bg-white shadow-2xl"
            >

                <!-- MODAL HEADER -->
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-heritage-border bg-white px-6 py-5"
                >

                    <div>

                        <div
                            class="text-[10px] font-semibold uppercase tracking-[0.2em] text-heritage-gold"
                        >
                            Content Management
                        </div>

                        <h2
                            class="mt-1 font-display text-2xl text-heritage-navy"
                        >
                            {{ editingPage ? 'Edit Page' : 'Add Page' }}
                        </h2>

                    </div>

                    <button
                        type="button"
                        @click="closeModal"
                        class="flex h-9 w-9 items-center justify-center rounded-full text-lg text-heritage-muted transition hover:bg-slate-100 hover:text-heritage-navy"
                    >
                        ×
                    </button>

                </div>


                <form
                    @submit.prevent="submit"
                    class="space-y-7 p-6"
                >

                    <!-- =================================================
                         BASIC INFORMATION
                    ================================================== -->
                    <div>

                        <h3
                            class="mb-4 text-xs font-semibold uppercase tracking-wider text-heritage-navy"
                        >
                            Page Information
                        </h3>

                        <div class="grid gap-5 md:grid-cols-2">

                            <!-- TITLE -->
                            <div class="md:col-span-2">

                                <label
                                    class="mb-2 block text-xs font-semibold text-heritage-navy"
                                >
                                    Page Title
                                </label>

                                <input
                                    v-model="form.title"
                                    @blur="generateSlug"
                                    type="text"
                                    required
                                    placeholder="About Us"
                                    class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                />

                            </div>


                            <!-- SLUG -->
                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold text-heritage-navy"
                                >
                                    URL Slug
                                </label>

                                <div class="flex">

                                    <span
                                        class="inline-flex items-center rounded-l border border-r-0 border-heritage-border bg-slate-50 px-3 text-xs text-heritage-muted"
                                    >
                                        /
                                    </span>

                                    <input
                                        v-model="form.slug"
                                        type="text"
                                        required
                                        placeholder="about-us"
                                        class="w-full rounded-r border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                    />

                                </div>

                            </div>


                            <!-- EYEBROW -->
                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold text-heritage-navy"
                                >
                                    Eyebrow
                                </label>

                                <input
                                    v-model="form.eyebrow"
                                    type="text"
                                    placeholder="ABOUT HERITAGE"
                                    class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                />

                            </div>


                            <!-- EXCERPT -->
                            <div class="md:col-span-2">

                                <label
                                    class="mb-2 block text-xs font-semibold text-heritage-navy"
                                >
                                    Excerpt
                                </label>

                                <textarea
                                    v-model="form.excerpt"
                                    rows="3"
                                    placeholder="Short introduction or summary of this page."
                                    class="w-full resize-none rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                ></textarea>

                                <p
                                    class="mt-1 text-[11px] text-heritage-muted"
                                >
                                    Used as a short page summary.
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         FEATURED IMAGE
                    ================================================== -->
                    <div>

                        <h3
                            class="mb-4 text-xs font-semibold uppercase tracking-wider text-heritage-navy"
                        >
                            Featured Image
                        </h3>

                        <div
                            class="grid gap-5 md:grid-cols-[220px_1fr]"
                        >

                            <div
                                class="h-32 overflow-hidden rounded-lg bg-heritage-offwhite"
                            >

                                <img
                                    v-if="imagePreview"
                                    :src="imagePreview"
                                    alt="Featured image preview"
                                    class="h-full w-full object-cover"
                                />

                                <div
                                    v-else
                                    class="flex h-full items-center justify-center text-xs text-heritage-muted"
                                >
                                    No image
                                </div>

                            </div>


                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold text-heritage-navy"
                                >
                                    Upload Image
                                </label>

                                <input
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    @change="handleImage"
                                    class="block w-full rounded border border-heritage-border bg-white px-3 py-2 text-xs text-heritage-muted"
                                />

                                <p
                                    class="mt-2 text-[11px] leading-5 text-heritage-muted"
                                >
                                    Recommended: high-quality landscape image.
                                    JPG, PNG or WebP. Maximum file size: 5MB.
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         PAGE CONTENT
                    ================================================== -->
                    <div>

                        <h3
                            class="mb-4 text-xs font-semibold uppercase tracking-wider text-heritage-navy"
                        >
                            Page Content
                        </h3>

                        <div>

                            <label
                                class="mb-2 block text-xs font-semibold text-heritage-navy"
                            >
                                Content
                            </label>

                            <textarea
                                v-model="form.content"
                                rows="12"
                                placeholder="Enter the main content for this page..."
                                class="w-full resize-y rounded border border-heritage-border px-4 py-3 text-sm leading-6 outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                            ></textarea>

                            <p
                                class="mt-1 text-[11px] text-heritage-muted"
                            >
                                Enter the page content here. Rich text editing can be added later if required.
                            </p>

                        </div>

                    </div>


                    <!-- =================================================
                         SEO
                    ================================================== -->
                    <div>

                        <h3
                            class="mb-4 text-xs font-semibold uppercase tracking-wider text-heritage-navy"
                        >
                            Search Engine Optimisation
                        </h3>

                        <div class="grid gap-5">

                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold text-heritage-navy"
                                >
                                    Meta Title
                                </label>

                                <input
                                    v-model="form.meta_title"
                                    type="text"
                                    maxlength="255"
                                    placeholder="Heritage Capital Markets | About Us"
                                    class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                />

                            </div>


                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold text-heritage-navy"
                                >
                                    Meta Description
                                </label>

                                <textarea
                                    v-model="form.meta_description"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Brief description of this page for search engines."
                                    class="w-full resize-none rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                ></textarea>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         DISPLAY SETTINGS
                    ================================================== -->
                    <div>

                        <h3
                            class="mb-4 text-xs font-semibold uppercase tracking-wider text-heritage-navy"
                        >
                            Display Settings
                        </h3>

                        <div
                            class="grid gap-5 md:grid-cols-2"
                        >

                            <!-- SORT ORDER -->
                            <div>

                                <label
                                    class="mb-2 block text-xs font-semibold text-heritage-navy"
                                >
                                    Sort Order
                                </label>

                                <input
                                    v-model.number="form.sort_order"
                                    type="number"
                                    min="0"
                                    required
                                    class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                />

                                <p
                                    class="mt-1 text-[11px] text-heritage-muted"
                                >
                                    Lower numbers appear first.
                                </p>

                            </div>


                            <!-- PUBLISHED -->
                            <div class="flex items-center">

                                <label
                                    class="flex cursor-pointer items-center gap-3"
                                >

                                    <input
                                        v-model="form.is_published"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-heritage-border text-heritage-gold focus:ring-heritage-gold"
                                    />

                                    <span>

                                        <span
                                            class="block text-xs font-semibold text-heritage-navy"
                                        >
                                            Published
                                        </span>

                                        <span
                                            class="mt-1 block text-[11px] text-heritage-muted"
                                        >
                                            Make this page visible on the website.
                                        </span>

                                    </span>

                                </label>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         ACTIONS
                    ================================================== -->
                    <div
                        class="flex flex-col-reverse gap-3 border-t border-heritage-border pt-5 sm:flex-row sm:justify-end"
                    >

                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded border border-heritage-border px-5 py-2.5 text-sm font-semibold text-heritage-navy transition hover:bg-slate-50"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            :disabled="processing"
                            class="rounded bg-heritage-gold px-5 py-2.5 text-sm font-semibold text-heritage-deep transition hover:bg-heritage-gold-light disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {{
                                processing
                                    ? 'Saving...'
                                    : editingPage
                                        ? 'Update Page'
                                        : 'Create Page'
                            }}
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </AdminLayout>
</template>