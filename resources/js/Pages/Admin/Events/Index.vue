<script setup>
import { onBeforeUnmount, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
  events: {
    type: Array,
    default: () => [],
  },
})

const editing = ref(null)
const showModal = ref(false)
const imagePreview = ref(null)
const imageInput = ref(null)
let previewObjectUrl = null

const form = useForm({
  title: '',
  slug: '',
  excerpt: '',
  description: '',
  event_date: '',
  location: '',
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

function descriptionAsPlainText(description) {
  if (!description) return ''

  const withLineBreaks = description
    .replace(/<br\s*\/?>/gi, '\n')
    .replace(/<\/(?:p|div|li|h[1-6])\s*>/gi, '\n')

  const parsed = new DOMParser().parseFromString(withLineBreaks, 'text/html')
  return (parsed.body.textContent || '').replace(/\n{3,}/g, '\n\n').trim()
}

function edit(item) {
  editing.value = item
  clearPreviewObjectUrl()
  imagePreview.value = item.featured_image || null
  form.clearErrors()
  form.title = item.title || ''
  form.slug = item.slug || ''
  form.excerpt = item.excerpt || ''
  form.description = descriptionAsPlainText(item.description)
  form.event_date = formatDateTimeInput(item.event_date)
  form.location = item.location || ''
  form.is_published = Boolean(item.is_published)
  form.sort_order = item.sort_order ?? 0
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
  const options = {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: reset,
  }

  form.post(
    editing.value ? `/admin/events/${editing.value.id}` : '/admin/events',
    options
  )
}

function toggle(item) {
  router.post(`/admin/events/${item.id}/toggle`, {}, { preserveScroll: true })
}

function remove(item) {
  if (confirm(`Are you sure you want to delete "${item.title}"?`)) {
    router.delete(`/admin/events/${item.id}`, { preserveScroll: true })
  }
}

function assetUrl(path) {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('blob:') || path.startsWith('/')) {
    return path
  }
  return path.startsWith('events/') ? `/storage/${path}` : `/${path}`
}

function formatDate(value) {
  if (!value) return 'Not scheduled'
  return new Date(value).toLocaleString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

function formatDateTimeInput(value) {
  if (!value) return ''

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value.slice(0, 16)

  const pad = part => String(part).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}

onBeforeUnmount(clearPreviewObjectUrl)
</script>

<template>
  <AdminLayout>
    <div class="p-6 lg:p-8">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-heritage-navy">Events</h1>
          <p class="mt-1 text-sm text-slate-500">Create and manage website events.</p>
        </div>
        <button type="button" class="btn-gold" @click="openCreate">+ New Event</button>
      </div>

      <div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-slate-50">
              <tr>
                <th class="p-4">Event</th>
                <th class="p-4">Date</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in events" :key="item.id" class="border-t border-slate-100">
                <td class="p-4">
                  <div class="font-medium text-heritage-navy">{{ item.title }}</div>
                  <div v-if="item.location" class="mt-1 text-xs text-slate-500">{{ item.location }}</div>
                </td>
                <td class="whitespace-nowrap p-4 text-slate-600">{{ formatDate(item.event_date) }}</td>
                <td class="p-4">
                  <button type="button" class="rounded-full px-3 py-1 text-xs"
                    :class="item.is_published ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'"
                    :aria-label="`${item.is_published ? 'Unpublish' : 'Publish'} ${item.title}`" @click="toggle(item)">
                    {{ item.is_published ? 'Published' : 'Draft' }}
                  </button>
                </td>
                <td class="whitespace-nowrap p-4 text-right">
                  <button type="button" class="mr-3 font-medium text-heritage-navy hover:underline" @click="edit(item)">
                    Edit
                  </button>
                  <button type="button" class="font-medium text-red-600 hover:underline" @click="remove(item)">
                    Delete
                  </button>
                </td>
              </tr>
              <tr v-if="!events.length">
                <td colspan="4" class="p-10 text-center text-slate-500">No events yet. Select “New Event” to create one.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-heritage-navy/70 p-4"
        @click.self="reset"@keydown.esc.prevent="reset" role="dialog" aria-modal="true"
        aria-labelledby="event-modal-title">
        <form class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white p-6 shadow-2xl"
          @submit.prevent="submit">
          <div class="flex items-center justify-between">
            <h2 id="event-modal-title" class="text-lg font-semibold text-heritage-navy">
              {{ editing ? 'Edit event' : 'Create event' }}
            </h2>
            <button type="button" class="rounded-full px-2 py-1 text-slate-500 hover:bg-slate-100 hover:text-slate-700"
              aria-label="Close event form" :disabled="form.processing" @click="reset">
              ×
            </button>
          </div>

          <div class="mt-5 space-y-4">
            <div>
              <label for="event-title" class="mb-1 block text-sm font-medium text-slate-700">Title</label>
              <input id="event-title" v-model="form.title" type="text" required maxlength="255"
                class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
              <p v-if="form.errors.title" class="mt-1 text-xs text-red-600">{{ form.errors.title }}</p>
            </div>

            <div>
              <label for="event-slug" class="mb-1 block text-sm font-medium text-slate-700">URL slug</label>
              <input id="event-slug" v-model="form.slug" type="text" maxlength="255"
                placeholder="Generated from title if left blank"
                class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
              <p v-if="form.errors.slug" class="mt-1 text-xs text-red-600">{{ form.errors.slug }}</p>
            </div>

            <div>
              <label for="event-excerpt" class="mb-1 block text-sm font-medium text-slate-700">Short summary</label>
              <textarea id="event-excerpt" v-model="form.excerpt" rows="2" maxlength="2000"
                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"></textarea>
              <p v-if="form.errors.excerpt" class="mt-1 text-xs text-red-600">{{ form.errors.excerpt }}</p>
            </div>

            <div>
              <label for="event-description" class="mb-1 block text-sm font-medium text-slate-700">Description</label>
              <textarea id="event-description" v-model="form.description" rows="4"
                class="w-full rounded border border-slate-300 px-3 py-2 text-sm"></textarea>
              <p v-if="form.errors.description" class="mt-1 text-xs text-red-600">{{ form.errors.description }}</p>
            </div>

            <div>
              <label for="event-date" class="mb-1 block text-sm font-medium text-slate-700">Date and time</label>
              <input id="event-date" v-model="form.event_date" type="datetime-local"
                class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
              <p v-if="form.errors.event_date" class="mt-1 text-xs text-red-600">{{ form.errors.event_date }}</p>
            </div>

            <div>
              <label for="event-location" class="mb-1 block text-sm font-medium text-slate-700">Location</label>
              <input id="event-location" v-model="form.location" type="text" maxlength="255"
                class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
              <p v-if="form.errors.location" class="mt-1 text-xs text-red-600">{{ form.errors.location }}</p>
            </div>

            <div>
              <label for="event-image" class="mb-1 block text-sm font-medium text-slate-700">Featured image</label>
              <input id="event-image" ref="imageInput" type="file" accept="image/*"
                class="text-sm text-slate-600 border border-gray-600 rounded-sm py-2 cursor-pointer" @change="selectImage">
              <p v-if="form.errors.featured_image" class="mt-1 text-xs text-red-600">{{ form.errors.featured_image }}
              </p>
              <img v-if="imagePreview" :src="assetUrl(imagePreview)" alt="Event image preview"
                class="mt-3 h-36 w-full rounded object-cover">
            </div>

            <div>
              <label for="event-sort-order" class="mb-1 block text-sm font-medium text-slate-700">Display order</label>
              <input id="event-sort-order" v-model.number="form.sort_order" type="number"
                class="w-full rounded border border-slate-300 px-3 py-2 text-sm">
              <p v-if="form.errors.sort_order" class="mt-1 text-xs text-red-600">{{ form.errors.sort_order }}</p>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-700">
              <input v-model="form.is_published" type="checkbox" class="rounded border-slate-300">
              Publish event
            </label>
            <p v-if="form.errors.is_published" class="text-xs text-red-600">{{ form.errors.is_published }}</p>
          </div>

          <div class="mt-6 flex gap-3">
            <button type="submit" class="btn-gold" :disabled="form.processing">
              {{ form.processing ? 'Saving...' : editing ? 'Save changes' : 'Create event' }}
            </button>
            <button type="button" class="rounded border border-slate-300 px-4 py-2 text-sm" :disabled="form.processing"
              @click="reset">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
