<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
const props = defineProps({ applications: { type: Object, required: true }, filters: { type: Object, default: () => ({}) } })
function filter(name, value) { router.get('/admin/account-applications', { ...props.filters, [name]: value || undefined }, { preserveState: true, replace: true }) }
const date = value => value ? new Intl.DateTimeFormat('en-GB', { dateStyle: 'medium' }).format(new Date(value)) : '—'
</script>
<template>
  <AdminLayout>
    <div class="min-h-screen bg-slate-50">
      <header class="border-b bg-white px-6 py-6 lg:px-8">
        <p class="text-xs font-semibold uppercase tracking-widest text-amber-600">Investor services</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Account Applications</h1>
        <p class="mt-1 text-sm text-slate-500">Review submitted applications. Identity documents are private and
          available to authorised administrators only.</p>
      </header>
      <div class="p-6 lg:p-8">
        <div class="mb-5 flex flex-wrap gap-3"><select class="border bg-white px-3 py-2"
            :value="filters.account_type || ''" @change="filter('account_type', $event.target.value)">
            <option value="">All account types</option>
            <option v-for="t in ['individual', 'corporate', 'joint', 'minor', 'estate']" :key="t" :value="t">{{ t }}
            </option>
          </select><select class="border bg-white px-3 py-2" :value="filters.status || ''"
            @change="filter('status', $event.target.value)">
            <option value="">All statuses</option>
            <option
              v-for="s in ['draft', 'submitted', 'under_review', 'more_information_required', 'approved', 'rejected', 'closed']"
              :key="s" :value="s">{{ s.replaceAll('_', ' ') }}</option>
          </select></div>
        <div class="overflow-x-auto rounded-xl border bg-white">
          <table class="w-full text-left text-sm">
            <thead class="border-b bg-slate-50">
              <tr>
                <th class="p-4">Reference</th>
                <th class="p-4">Applicant</th>
                <th class="p-4">Type</th>
                <th class="p-4">Date</th>
                <th class="p-4">Status</th>
                <th class="p-4"></th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr v-for="app in applications.data" :key="app.id">
                <td class="p-4 font-semibold">{{ app.reference }}</td>
                <td class="p-4">{{ app.applicant_name || '—' }}<div class="text-xs text-slate-500">{{ app.email }}</div>
                </td>
                <td class="p-4 capitalize">{{ app.account_type }}</td>
                <td class="p-4">{{ date(app.submitted_at || app.created_at) }}</td>
                <td class="p-4 capitalize">{{ app.status.replaceAll('_', ' ') }}</td>
                <td class="p-4"><a :href="`/admin/account-applications/${app.id}`"
                    class="font-semibold text-amber-700 hover:underline">Review</a></td>
              </tr>
              <tr v-if="!applications.data.length">
                <td colspan="6" class="p-8 text-center text-slate-500">No applications found.</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="mt-4 flex gap-3 text-sm"><a v-if="applications.prev_page_url" :href="applications.prev_page_url"
            class="underline">Previous</a><span class="text-slate-500">Page {{ applications.current_page }} of {{
              applications.last_page }}</span><a v-if="applications.next_page_url" :href="applications.next_page_url"
            class="underline">Next</a></div>
      </div>
    </div>
  </AdminLayout>
</template>
