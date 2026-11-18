<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
  subscribers: { type: Array, default: () => [] },
  activeSubscriberCount: { type: Number, default: 0 },
})

const page = usePage()
const campaignForm = useForm({ subject: '', body: '' })

function toggle(subscriber) {
  useForm({}).post(`/admin/newsletter-subscribers/${subscriber.id}/toggle`, {
    preserveScroll: true,
  })
}

function sendCampaign() {
  if (!confirm(`Schedule this campaign for ${page.props.activeSubscriberCount} active subscriber(s)?`)) return

  campaignForm.post('/admin/newsletter-subscribers/campaign', {
    preserveScroll: true,
    onSuccess: () => campaignForm.reset(),
  })
}

function formatDate(value) {
  if (!value) return '—'
  const date = new Date(value)
  return Number.isNaN(date.getTime())
    ? value
    : new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(date)
}

function statusLabel(status) {
  return {
    subscribed: 'Subscribed',
    unsubscribed: 'Unsubscribed',
    inactive: 'Inactive',
  }[status] ?? status
}
</script>

<template>
  <AdminLayout>
    <div class="p-6 lg:p-8">
      <div class="mb-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-heritage-navy">Newsletter Subscribers</h1>
          <p class="mt-1 text-sm text-slate-500">Manage subscriptions and send campaigns to active subscribers.</p>
        </div>
        <a href="/admin/newsletter-subscribers/export" class="btn-gold">Export CSV</a>
      </div>

      <div v-if="page.props.flash?.success" class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
        {{ page.props.flash.success }}
      </div>
      <div v-if="page.props.flash?.error" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
        {{ page.props.flash.error }}
      </div>

      <form class="mb-8 space-y-4 rounded-lg border bg-white p-5" @submit.prevent="sendCampaign">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
          <h2 class="text-lg font-semibold text-heritage-navy">Send a campaign</h2>
          <p class="text-sm text-slate-500">{{ activeSubscriberCount }} active subscriber(s)</p>
        </div>
        <div>
          <label for="campaign-subject" class="mb-1 block text-sm font-medium text-slate-700">Email subject</label>
          <input
            id="campaign-subject"
            v-model="campaignForm.subject"
            type="text"
            maxlength="255"
            required
            class="w-full rounded-md border-slate-300 text-sm focus:border-heritage-gold focus:ring-heritage-gold"
          >
          <p v-if="campaignForm.errors.subject" class="mt-1 text-sm text-red-600">{{ campaignForm.errors.subject }}</p>
        </div>
        <div>
          <label for="campaign-body" class="mb-1 block text-sm font-medium text-slate-700">Email message</label>
          <textarea
            id="campaign-body"
            v-model="campaignForm.body"
            rows="8"
            maxlength="20000"
            required
            class="w-full rounded-md border-slate-300 text-sm focus:border-heritage-gold focus:ring-heritage-gold"
          />
          <p class="mt-1 text-xs text-slate-500">Plain text only. An unsubscribe link is added automatically.</p>
          <p v-if="campaignForm.errors.body" class="mt-1 text-sm text-red-600">{{ campaignForm.errors.body }}</p>
        </div>
        <button
          type="submit"
          class="btn-gold"
          :disabled="campaignForm.processing || activeSubscriberCount === 0"
        >
          {{ campaignForm.processing ? 'Scheduling…' : 'Schedule campaign' }}
        </button>
      </form>

      <div class="overflow-x-auto rounded-lg border bg-white">
        <table class="w-full min-w-[700px] text-left text-sm">
          <thead class="bg-slate-50">
            <tr>
              <th class="p-4">Email</th>
              <th class="p-4">Status</th>
              <th class="p-4">Subscribed</th>
              <th class="p-4">Unsubscribed</th>
              <th class="p-4">Admin action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="subscriber in subscribers" :key="subscriber.id" class="border-t">
              <td class="p-4">{{ subscriber.email }}</td>
              <td class="p-4">{{ statusLabel(subscriber.status) }}</td>
              <td class="p-4 text-slate-500">{{ formatDate(subscriber.subscribed_at) }}</td>
              <td class="p-4 text-slate-500">{{ formatDate(subscriber.unsubscribed_at) }}</td>
              <td class="p-4">
                <button
                  v-if="subscriber.status !== 'unsubscribed'"
                  type="button"
                  class="font-semibold text-heritage-navy underline"
                  @click="toggle(subscriber)"
                >
                  {{ subscriber.is_active ? 'Deactivate' : 'Reactivate' }}
                </button>
                <span v-else class="text-slate-500">Subscriber must opt in again</span>
              </td>
            </tr>
            <tr v-if="subscribers.length === 0">
              <td colspan="5" class="p-6 text-center text-slate-500">No newsletter subscribers yet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AdminLayout>
</template>
