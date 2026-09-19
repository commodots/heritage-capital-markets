<script setup>
import { computed, ref } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
    slides: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()

const showForm = ref(false)
const editingSlide = ref(null)
const deleteTarget = ref(null)

const form = useForm({
    eyebrow: '',
    title: '',
    description: '',
    image: null,
    primary_label: '',
    primary_url: '',
    secondary_label: '',
    secondary_url: '',
    location_label: '',
    location_text: '',
    is_active: true,
    sort_order: 1,
})

const flashMessage = computed(() => page.props.flash?.success ?? '')

const assetUrl = (path) => {
    if (!path) return ''
    return path.startsWith('/') ? path : `/${path}`
}

const resetForm = () => {
    form.reset()
    form.clearErrors()
    form.eyebrow = ''
    form.title = ''
    form.description = ''
    form.image = null
    form.primary_label = ''
    form.primary_url = ''
    form.secondary_label = ''
    form.secondary_url = ''
    form.location_label = ''
    form.location_text = ''
    form.is_active = true
    form.sort_order = props.slides.length + 1
}

const openCreate = () => {
    editingSlide.value = null
    resetForm()
    showForm.value = true
}

const openEdit = (slide) => {
    editingSlide.value = slide
    form.clearErrors()

    form.eyebrow = slide.eyebrow ?? ''
    form.title = slide.title ?? ''
    form.description = slide.description ?? ''
    form.image = null
    form.primary_label = slide.primary_label ?? ''
    form.primary_url = slide.primary_url ?? ''
    form.secondary_label = slide.secondary_label ?? ''
    form.secondary_url = slide.secondary_url ?? ''
    form.location_label = slide.location_label ?? ''
    form.location_text = slide.location_text ?? ''
    form.is_active = Boolean(slide.is_active)
    form.sort_order = slide.sort_order ?? 1

    showForm.value = true
}

const closeForm = () => {
    if (form.processing) return
    showForm.value = false
    editingSlide.value = null
    form.reset()
    form.clearErrors()
}

const submit = () => {
    if (editingSlide.value) {
        form.post(`/admin/homepage/hero-slides/${editingSlide.value.id}`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: closeForm,
        })
    } else {
        form.post('/admin/homepage/hero-slides', {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: closeForm,
        })
    }
}

const toggle = (slide) => {
    form.clearErrors()

    useForm({}).post(`/admin/homepage/hero-slides/${slide.id}/toggle`, {
        preserveScroll: true,
    })
}

const confirmDelete = (slide) => {
    deleteTarget.value = slide
}

const cancelDelete = () => {
    deleteTarget.value = null
}

const deleteSlide = () => {
    if (!deleteTarget.value) return

    useForm({}).delete(`/admin/homepage/hero-slides/${deleteTarget.value.id}`, {
        preserveScroll: true,
        onSuccess: cancelDelete,
    })
}

const statusClass = (active) =>
    active
        ? 'bg-emerald-50 text-emerald-700'
        : 'bg-slate-100 text-slate-500'
</script>

<template>
    <Head title="Hero Slides | Admin" />

    <div class="min-h-screen bg-slate-50 text-heritage-text">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-40 hidden w-[258px] flex-col bg-heritage-deep text-white lg:flex">
            <div class="flex h-[82px] items-center border-b border-white/10 px-6">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center bg-heritage-gold font-display text-2xl font-semibold text-heritage-deep">
                    H
                </div>
                <div class="ml-3 leading-none">
                    <div class="font-display text-xl font-semibold">Heritage</div>
                    <div class="mt-1 text-[9px] font-semibold tracking-[0.16em] text-white/65">CAPITAL MARKETS</div>
                    <div class="mt-2 text-[9px] uppercase tracking-[0.16em] text-white/40">Admin Panel</div>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4">
                <Link href="/admin" class="mb-5 flex items-center gap-3 rounded-md px-4 py-3 text-sm font-medium text-white/75 transition hover:bg-white/5 hover:text-white">
                    <span>⌂</span>
                    Dashboard
                </Link>

                <div class="px-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/40">Content Management</div>

                <div class="mt-3 space-y-1">
                    <div class="rounded-md bg-white/10">
                        <div class="flex items-center gap-3 border-l-4 border-heritage-gold px-3 py-2 text-sm text-white">
                            <span>▣</span>
                            Homepage
                        </div>
                        <div class="ml-7 border-l border-white/10 pl-3 pb-2">
                            <Link href="/admin/homepage/hero-slides" class="block py-1.5 text-sm font-medium text-heritage-gold">Hero Slides</Link>
                            <Link href="/admin/homepage/market-indices" class="block py-1.5 text-sm text-white/60 hover:text-white">Market Indices</Link>
                            <Link href="/admin/homepage/investment-solutions" class="block py-1.5 text-sm text-white/60 hover:text-white">Investment Solutions</Link>
                            <Link href="/admin/homepage/settings" class="block py-1.5 text-sm text-white/60 hover:text-white">Homepage Settings</Link>
                        </div>
                    </div>

                    <Link href="/admin/pages" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">Pages</Link>
                    <Link href="/admin/investment-solutions" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">Investment Solutions</Link>
                    <Link href="/admin/research-reports" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">Research &amp; Reports</Link>
                    <Link href="/admin/articles" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">News &amp; Articles</Link>
                    <Link href="/admin/events" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">Events</Link>
                    <Link href="/admin/faqs" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">FAQs</Link>
                    <Link href="/admin/media" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">Media Library</Link>
                </div>

                <div class="mt-7 px-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/40">Communication</div>
                <div class="mt-3 space-y-1">
                    <Link href="/admin/enquiries" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">Enquiries</Link>
                    <Link href="/admin/newsletter-subscribers" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">Newsletter Subscribers</Link>
                </div>

                <div class="mt-7 px-3 text-[10px] font-semibold uppercase tracking-[0.16em] text-white/40">Settings</div>
                <div class="mt-3 space-y-1">
                    <Link href="/admin/settings" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">Site Settings</Link>
                    <Link href="/admin/users" class="block rounded-md px-3 py-2 text-sm text-white/75 hover:bg-white/5 hover:text-white">Users</Link>
                </div>
            </nav>

            <div class="border-t border-white/10 p-5">
                <a href="/" target="_blank" class="text-sm font-medium text-heritage-gold hover:text-heritage-gold-light">View Website ↗</a>
            </div>
        </aside>

        <div class="lg:pl-[258px]">
            <header class="sticky top-0 z-30 flex h-[82px] items-center justify-between border-b border-slate-200 bg-white/95 px-6 backdrop-blur lg:px-8">
                <div>
                    <div class="text-xs text-slate-400">Homepage Management</div>
                    <h1 class="font-display text-xl font-semibold text-heritage-navy">Hero Slides</h1>
                </div>

                <div class="flex items-center gap-4">
                    <Link href="/" target="_blank" class="hidden text-sm font-medium text-slate-600 sm:block">View Website ↗</Link>
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-heritage-deep text-sm font-semibold text-white">A</div>
                </div>
            </header>

            <main class="p-6 lg:p-8">
                <div v-if="flashMessage" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ flashMessage }}
                </div>

                <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <div class="text-xs font-medium text-slate-500">Homepage</div>
                        <h2 class="mt-1 font-display text-3xl font-semibold text-heritage-navy">Manage Hero Slides</h2>
                        <p class="mt-1 max-w-2xl text-sm text-slate-500">
                            Control the homepage carousel content, imagery, calls to action and display order.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="openCreate"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-heritage-gold px-5 py-3 text-sm font-semibold text-heritage-deep shadow-sm transition hover:bg-heritage-gold-light"
                    >
                        <span class="text-lg leading-none">+</span>
                        Add Hero Slide
                    </button>
                </div>

                <div class="mb-5 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="text-xs uppercase tracking-wider text-slate-400">Total Slides</div>
                        <div class="mt-1 text-2xl font-semibold text-heritage-navy">{{ slides.length }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="text-xs uppercase tracking-wider text-slate-400">Active</div>
                        <div class="mt-1 text-2xl font-semibold text-emerald-600">{{ slides.filter(s => s.is_active).length }}</div>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="text-xs uppercase tracking-wider text-slate-400">Inactive</div>
                        <div class="mt-1 text-2xl font-semibold text-slate-500">{{ slides.filter(s => !s.is_active).length }}</div>
                    </div>
                </div>

                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="hidden grid-cols-[80px_1fr_110px_90px_150px] gap-4 border-b border-slate-200 bg-slate-50 px-5 py-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500 md:grid">
                        <div>Preview</div>
                        <div>Slide</div>
                        <div>Status</div>
                        <div>Order</div>
                        <div class="text-right">Actions</div>
                    </div>

                    <div v-for="slide in slides" :key="slide.id" class="grid gap-4 border-b border-slate-100 p-5 last:border-0 md:grid-cols-[80px_1fr_110px_90px_150px] md:items-center">
                        <div>
                            <img
                                v-if="slide.image"
                                :src="assetUrl(slide.image)"
                                :alt="slide.title"
                                class="h-14 w-20 rounded-md object-cover"
                            />
                            <div v-else class="flex h-14 w-20 items-center justify-center rounded-md bg-slate-100 text-xs text-slate-400">No image</div>
                        </div>

                        <div class="min-w-0">
                            <div class="text-[10px] font-semibold uppercase tracking-wider text-heritage-gold">{{ slide.eyebrow }}</div>
                            <div class="mt-1 truncate font-display text-lg font-semibold text-heritage-navy">{{ slide.title }}</div>
                            <div class="mt-1 line-clamp-2 text-xs text-slate-500">{{ slide.description }}</div>
                            <div class="mt-2 text-xs text-slate-400">
                                {{ slide.primary_label || 'Primary CTA' }}
                                <span class="mx-1">·</span>
                                {{ slide.secondary_label || 'Secondary CTA' }}
                            </div>
                        </div>

                        <div>
                            <span :class="['rounded-full px-2.5 py-1 text-[10px] font-semibold', statusClass(slide.is_active)]">
                                {{ slide.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="text-sm font-semibold text-slate-600">
                            {{ slide.sort_order }}
                        </div>

                        <div class="flex items-center justify-start gap-2 md:justify-end">
                            <button type="button" @click="openEdit(slide)" class="rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:border-heritage-gold hover:text-heritage-navy">
                                Edit
                            </button>
                            <button type="button" @click="toggle(slide)" class="rounded-md border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:border-heritage-gold hover:text-heritage-navy">
                                {{ slide.is_active ? 'Hide' : 'Show' }}
                            </button>
                            <button type="button" @click="confirmDelete(slide)" class="rounded-md border border-red-100 px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50">
                                Delete
                            </button>
                        </div>
                    </div>

                    <div v-if="!slides.length" class="p-12 text-center">
                        <div class="text-4xl text-slate-300">▣</div>
                        <h3 class="mt-3 font-display text-xl font-semibold text-heritage-navy">No hero slides yet</h3>
                        <p class="mt-1 text-sm text-slate-500">Add the first slide to begin building the homepage carousel.</p>
                        <button type="button" @click="openCreate" class="mt-5 rounded-lg bg-heritage-gold px-5 py-2.5 text-sm font-semibold text-heritage-deep">Add Hero Slide</button>
                    </div>
                </section>
            </main>
        </div>

        <!-- Form Modal -->
        <div v-if="showForm" class="fixed inset-0 z-50 overflow-y-auto bg-heritage-deep/60 p-4 backdrop-blur-sm">
            <div class="flex min-h-full items-center justify-center">
                <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                        <div>
                            <div class="text-xs uppercase tracking-wider text-heritage-gold">Homepage Carousel</div>
                            <h3 class="mt-1 font-display text-2xl font-semibold text-heritage-navy">
                                {{ editingSlide ? 'Edit Hero Slide' : 'Add Hero Slide' }}
                            </h3>
                        </div>
                        <button type="button" @click="closeForm" class="text-2xl text-slate-400 hover:text-slate-700">×</button>
                    </div>

                    <form @submit.prevent="submit" class="max-h-[78vh] overflow-y-auto p-6">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="text-xs font-semibold text-slate-600">Eyebrow</label>
                                <input v-model="form.eyebrow" type="text" placeholder="BUILDING WEALTH. CREATING VALUE." class="mt-1.5 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm outline-none focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/10">
                                <p v-if="form.errors.eyebrow" class="mt-1 text-xs text-red-600">{{ form.errors.eyebrow }}</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-xs font-semibold text-slate-600">Title *</label>
                                <input v-model="form.title" type="text" required placeholder="Your Trusted Partner in Investment Excellence." class="mt-1.5 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm outline-none focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/10">
                                <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-xs font-semibold text-slate-600">Description</label>
                                <textarea v-model="form.description" rows="3" placeholder="Short supporting text for the slide." class="mt-1.5 w-full resize-none rounded-lg border border-slate-200 px-4 py-3 text-sm outline-none focus:border-heritage-gold focus:ring-2 focus:ring-heritage-gold/10"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label class="text-xs font-semibold text-slate-600">Hero Image</label>
                                <input type="file" accept=".jpg,.jpeg,.png,.webp" @change="form.image = $event.target.files[0]" class="mt-1.5 block w-full rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm">
                                <p class="mt-1 text-xs text-slate-400">JPG, PNG or WebP · maximum 5MB. Leave empty when editing to keep the current image.</p>
                                <p v-if="form.errors.image" class="mt-1 text-xs text-red-600">{{ form.errors.image }}</p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600">Primary Button</label>
                                <input v-model="form.primary_label" type="text" placeholder="Explore Our Solutions" class="mt-1.5 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Primary URL</label>
                                <input v-model="form.primary_url" type="text" placeholder="/investment-solutions" class="mt-1.5 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600">Secondary Button</label>
                                <input v-model="form.secondary_label" type="text" placeholder="Open an Account" class="mt-1.5 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Secondary URL</label>
                                <input v-model="form.secondary_url" type="text" placeholder="/open-account" class="mt-1.5 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600">Location Label</label>
                                <input v-model="form.location_label" type="text" placeholder="Heritage" class="mt-1.5 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Location Text</label>
                                <input v-model="form.location_text" type="text" placeholder="The Heritage Place" class="mt-1.5 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm">
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-600">Display Order *</label>
                                <input v-model.number="form.sort_order" type="number" min="1" required class="mt-1.5 w-full rounded-lg border border-slate-200 px-4 py-3 text-sm">
                            </div>

                            <div class="flex items-end">
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200 px-4 py-3">
                                    <input v-model="form.is_active" type="checkbox" class="h-4 w-4 accent-heritage-gold">
                                    <span class="text-sm font-medium text-slate-700">Active on homepage</span>
                                </label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3 border-t border-slate-200 pt-5">
                            <button type="button" @click="closeForm" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                Cancel
                            </button>
                            <button type="submit" :disabled="form.processing" class="rounded-lg bg-heritage-gold px-6 py-2.5 text-sm font-semibold text-heritage-deep disabled:cursor-not-allowed disabled:opacity-60">
                                {{ form.processing ? 'Saving...' : (editingSlide ? 'Save Changes' : 'Create Slide') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation -->
        <div v-if="deleteTarget" class="fixed inset-0 z-[60] flex items-center justify-center bg-heritage-deep/60 p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600">!</div>
                <h3 class="mt-4 font-display text-2xl font-semibold text-heritage-navy">Delete this slide?</h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    This will permanently remove “{{ deleteTarget.title }}” from the homepage carousel.
                </p>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="cancelDelete" class="rounded-lg border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600">Cancel</button>
                    <button type="button" @click="deleteSlide" class="rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white">Delete</button>
                </div>
            </div>
        </div>
    </div>
</template>
