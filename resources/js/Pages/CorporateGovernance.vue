<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
</script>

<template>
    <PublicLayout>
        <SeoHead
            title="Corporate Governance"
            description="Learn about Heritage Capital Markets’ commitment to integrity, accountability and responsible business practices."
            canonical-path="/corporate-governance"
        />

        <section class="bg-heritage-deep py-24 text-white">
            <div class="container-heritage">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-heritage-gold">About Heritage</p>
                <h1 class="mt-4 font-display text-4xl font-semibold md:text-6xl">Corporate Governance</h1>
                <p class="mt-6 max-w-2xl text-base leading-7 text-white/75">
                    Our approach is grounded in integrity, accountability and responsible service to our clients.
                </p>
            </div>
        </section>

        <section class="bg-white py-16 md:py-24">
            <div class="container-heritage max-w-4xl">
                <h2 class="font-display text-3xl font-semibold text-heritage-navy">Our principles</h2>
                <p class="mt-5 leading-7 text-heritage-muted">
                    Heritage Capital Markets is committed to conducting its business with professionalism and transparency.
                    We aim to act in our clients’ interests, uphold applicable requirements and maintain high standards in our work.
                </p>
                <div class="mt-10 grid gap-5 md:grid-cols-3">
                    <article v-for="principle in [
                        { title: 'Integrity', text: 'We value honest conduct and clear communication.' },
                        { title: 'Accountability', text: 'We take responsibility for our decisions and actions.' },
                        { title: 'Client focus', text: 'We work to understand and support our clients’ needs.' },
                    ]" :key="principle.title" class="border border-heritage-border p-6">
                        <h3 class="font-display text-xl font-semibold text-heritage-navy">{{ principle.title }}</h3>
                        <p class="mt-3 leading-6 text-heritage-muted">{{ principle.text }}</p>
                    </article>
                </div>
                <p class="mt-8 text-sm leading-6 text-heritage-muted">
                    For questions about our governance practices, please <a href="/contact-us" class="font-semibold text-heritage-navy underline">contact us</a>.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
