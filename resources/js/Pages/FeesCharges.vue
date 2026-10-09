<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
</script>

<template>
    <PublicLayout>
        <SeoHead
            title="Fees & Charges"
            description="Contact Heritage Capital Markets for information about fees and charges applicable to our investment services."
            canonical-path="/fees-charges"
        />

        <section class="bg-heritage-deep py-24 text-white">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-heritage-gold">Investment Solutions</p>
                <h1 class="mt-4 font-display text-4xl font-semibold md:text-6xl">Fees &amp; Charges</h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-white/75">
                    Get clear information about the costs associated with our services.
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage max-w-4xl">
                <h2 class="font-display text-3xl font-semibold text-heritage-navy">Request current fee information</h2>
                <p class="mt-5 leading-7 text-heritage-muted">
                    Fees and charges may depend on the service, product and transaction. Contact our team for the current
                    fee schedule and details relevant to your needs before proceeding.
                </p>
                <a href="/contact-us" class="mt-7 inline-flex items-center gap-2 rounded bg-heritage-gold px-6 py-3 font-semibold text-heritage-deep transition hover:bg-heritage-gold-light">
                    Contact Us <span aria-hidden="true">→</span>
                </a>
            </div>
        </section>
    </PublicLayout>
</template>
