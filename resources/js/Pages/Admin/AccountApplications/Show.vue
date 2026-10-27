<script setup>
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
const props = defineProps({ application: { type: Object, required: true }, documentNames: { type: Array, default: () => [] } })
const form = useForm({ status: props.application.status, review_notes: props.application.review_notes || '' })
function save() { form.post(`/admin/account-applications/${props.application.id}`) }
</script>
<template>
  <AdminLayout>
    <div class="min-h-screen bg-slate-50">
      <header class="border-b bg-white px-6 py-6"><a href="/admin/account-applications" class="text-sm text-amber-700">←
          All applications</a>
        <h1 class="mt-3 text-2xl font-bold">Application {{ application.reference }}</h1>
        <p class="mt-1 text-sm text-slate-500 capitalize">{{ application.account_type }} · {{
          application.status.replaceAll('_',' ') }}</p>
      </header>
      <div class="grid gap-6 p-6 lg:grid-cols-[1.3fr_0.7fr]">
        <section class="space-y-5">
          <div class="rounded-xl border bg-white p-6">
            <h2 class="mb-4 text-lg font-semibold">Applicant details</h2>
            <dl class="grid gap-4 sm:grid-cols-2"><template v-for="(value, key) in (application.form_data || {})"
                :key="key">
                <div>
                  <dt class="text-xs uppercase tracking-wide text-slate-500">{{ key.replaceAll('_', ' ') }}</dt>
                  <dd class="mt-1 wrap-break-word text-sm text-slate-900">{{ typeof value === 'object' ? '—' : (value || '—') }}
                  </dd>
                </div>
              </template>
            </dl>
          </div>
          <div class="rounded-xl border bg-white p-6">
            <h2 class="mb-3 text-lg font-semibold">Supporting documents</h2>
            <p class="mb-3 text-xs text-slate-500">Personal/identity documents. Download only when required for review.
            </p>
            <div v-if="!documentNames.length" class="text-sm text-slate-500">No documents uploaded.</div>
            <div v-for="name in documentNames" :key="name" class="flex items-center justify-between border-t py-3"><span
                class="text-sm">{{ name.replaceAll('_', ' ') }}</span><a
                :href="`/admin/account-applications/${application.id}/documents/${name}`"
                class="text-sm font-semibold text-amber-700 underline">Secure download</a></div>
          </div>
        </section>
        <aside class="h-fit rounded-xl border bg-white p-6">
          <h2 class="text-lg font-semibold">Review decision</h2>
          <form class="mt-5 space-y-4" @submit.prevent="save"><label class="block"><span
                class="text-sm font-semibold">Status</span><select v-model="form.status"
                class="mt-2 w-full border px-3 py-3">
                <option value="submitted">Submitted</option>
                <option value="under_review">Under review</option>
                <option value="more_information_required">More information required</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="closed">Closed</option>
              </select></label><label class="block"><span class="text-sm font-semibold">Internal review
                notes</span><textarea v-model="form.review_notes" rows="6"
                class="mt-2 w-full border px-3 py-3" /></label><button :disabled="form.processing"
              class="btn-gold w-full">Save review</button></form>
        </aside>
      </div>
    </div>
  </AdminLayout>
</template>
