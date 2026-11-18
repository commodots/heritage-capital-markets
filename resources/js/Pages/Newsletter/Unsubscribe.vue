<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3'
import PublicLayout from '@/Layouts/PublicLayout.vue'

const props = defineProps({
  email: { type: String, required: true },
  isSubscribed: { type: Boolean, required: true },
  actionUrl: { type: String, required: true },
})

const page = usePage()
const form = useForm({})

function unsubscribe() {
  form.post(props.actionUrl, { preserveScroll: true })
}
</script>

<template>
  <PublicLayout>
    <Head title="Unsubscribe from updates" />
    <main class="mx-auto max-w-2xl px-6 py-16">
      <h1 class="text-3xl font-semibold text-heritage-navy">Newsletter preferences</h1>
      <p class="mt-4 text-slate-600">
        {{ isSubscribed
          ? `Confirm that you want to stop newsletter emails to ${email}.`
          : `${email} is not currently subscribed to newsletter emails.` }}
      </p>
      <p v-if="page.props.flash?.success" class="mt-4 text-sm text-emerald-700">
        {{ page.props.flash.success }}
      </p>
      <button
        v-if="isSubscribed"
        type="button"
        class="btn-gold mt-6"
        :disabled="form.processing"
        @click="unsubscribe"
      >
        {{ form.processing ? 'Updating…' : 'Unsubscribe' }}
      </button>
    </main>
  </PublicLayout>
</template>
