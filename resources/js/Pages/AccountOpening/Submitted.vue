<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
defineProps({ reference: { type: String, required: true }, accountType: { type: String, required: true } })
</script>
<template>
  <PublicLayout>
    <SeoHead title="Application Received" description="Your account opening application has been received."
      canonical-path="/open-account" />
    <section class="bg-heritage-offwhite px-6 py-20">
      <div class="container-heritage max-w-2xl bg-white p-8 text-center shadow-sm md:p-12">
        <p class="text-sm font-semibold uppercase tracking-widest text-[#9A6B00]">Application received</p>
        <h1 class="mt-4 font-display text-3xl font-semibold text-heritage-navy">Thank you for applying</h1>
        <p class="mt-4 text-slate-600">Your application reference is <strong>{{ reference }}</strong>. Heritage Capital
          Markets will review the details and contact you regarding the next steps.</p>
        <a href="/" class="btn-gold mt-8 inline-flex">Return to homepage</a>
      </div>
    </section>
  </PublicLayout>
</template>
