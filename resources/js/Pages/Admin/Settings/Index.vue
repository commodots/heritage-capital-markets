<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
const props = defineProps(
  {
    settings:
    {
      type: Object,
      default: () => ({})
    }
  })
const form = useForm(
  {
    company_name: props.settings.company_name || '',
    phone: props.settings.phone || '', email: props.settings.email || '', address: props.settings.address || '',
    address_aba: props.settings.address_aba || '',
    facebook: props.settings.facebook || '',
    linkedin: props.settings.linkedin || '',
    instagram: props.settings.instagram || '',
    footer_text: props.settings.footer_text || '',
    chat_enabled: props.settings.chat_enabled === undefined ? true : props.settings.chat_enabled === '1',
    chat_working_days: props.settings.chat_working_days || ['Monday','Tuesday','Wednesday','Thursday','Friday'],
    chat_open_time: props.settings.chat_open_time || '08:00',
    chat_close_time: props.settings.chat_close_time || '17:00',
    chat_timezone: props.settings.chat_timezone || 'Africa/Lagos',
    chat_offline_message: props.settings.chat_offline_message || 'Our office is currently closed. Please leave an enquiry and our team will respond.',
    chat_notification_email: props.settings.chat_notification_email || ''
  }
)
const workingDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']
function submit() {
  form.post('/admin/settings')
}
</script>

<template>
  <AdminLayout>
    <div class="p-6 lg:p-8">
      <div class="mb-6">
        <h1 class="text-2xl font-semibold text-heritage-navy">Site Settings</h1>
        <p class="mt-1 text-sm text-slate-500">Manage global company and footer information.</p>
      </div>
      <form @submit.prevent="submit" class="max-w-3xl space-y-5 rounded-lg border bg-white p-6">
        <label class="block">
          <span class="text-sm font-medium">Company Name</span>
          <input v-model="form.company_name" class="mt-1 w-full rounded border p-3">
        </label>
        <div class="grid gap-5 md:grid-cols-2">
          <label class="block">
            <span class="text-sm font-medium">Phone</span>
            <input v-model="form.phone" class="mt-1 w-full rounded border p-3">
          </label>
          <label class="block">
            <span class="text-sm font-medium">Email</span>
            <input v-model="form.email" type="email" class="mt-1 w-full rounded border p-3">
          </label>
        </div>
        <label class="block">
          <span class="text-sm font-medium">Head Office Address</span>
          <textarea v-model="form.address" rows="3" class="mt-1 w-full rounded border p-3">
          </textarea>
        </label>
        <label class="block">
          <span class="text-sm font-medium">Aba Office Address</span>
          <textarea v-model="form.address_aba" rows="3" class="mt-1 w-full rounded border p-3">
          </textarea>
        </label>
        <div class="grid gap-5 md:grid-cols-3">
          <input v-model="form.facebook" placeholder="Facebook URL" class="rounded border p-3">
          <input v-model="form.linkedin" placeholder="LinkedIn URL" class="rounded border p-3">
          <input v-model="form.instagram" placeholder="Instagram URL" class="rounded border p-3">
        </div>
        <label class="block">
          <span class="text-sm font-medium">Footer
            Text</span>
          <textarea v-model="form.footer_text" rows="3" class="mt-1 w-full rounded border p-3">
          </textarea>
        </label>
        <section class="space-y-4 border-t pt-5">
          <div>
            <h2 class="text-lg font-semibold text-heritage-navy">Live Chat Availability</h2>
            <p class="text-sm text-slate-500">Configure visitor live chat office hours.</p>
          </div>
          <label class="flex items-center gap-3 text-sm font-medium">
            <input v-model="form.chat_enabled" type="checkbox" class="rounded border-slate-300">
            Enable live chat
          </label>
          <fieldset>
            <legend class="mb-2 text-sm font-medium">Working days</legend>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
              <label v-for="day in workingDays" :key="day" class="flex items-center gap-2 text-sm">
                <input v-model="form.chat_working_days" type="checkbox" :value="day" class="rounded border-slate-300">
                {{ day }}
              </label>
            </div>
          </fieldset>
          <div class="grid gap-4 sm:grid-cols-2">
            <label class="text-sm font-medium">Opening time
              <input v-model="form.chat_open_time" type="time" class="mt-1 w-full rounded border p-3">
            </label>
            <label class="text-sm font-medium">Closing time
              <input v-model="form.chat_close_time" type="time" class="mt-1 w-full rounded border p-3">
            </label>
          </div>
          <label class="block text-sm font-medium">Timezone
            <input v-model="form.chat_timezone" class="mt-1 w-full rounded border p-3">
          </label>
          <label class="block text-sm font-medium">Offline message
            <textarea v-model="form.chat_offline_message" rows="3" class="mt-1 w-full rounded border p-3" />
          </label>
          <label class="block text-sm font-medium">Notification email
            <input v-model="form.chat_notification_email" type="email" class="mt-1 w-full rounded border p-3">
          </label>
        </section>
        <button class="btn-gold">Save Settings</button>
      </form>
    </div>
  </AdminLayout>
</template>