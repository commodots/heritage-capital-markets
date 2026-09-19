<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
</script>

<template>
    <PublicLayout>
        <section class="flex min-h-[70vh] items-center bg-[#061A33] text-white">
            <div class="container-heritage py-24">
                <p class="text-7xl font-display font-semibold text-[#F5B82E] md:text-9xl">404</p>
                <h1 class="mt-6 font-display text-4xl font-semibold md:text-6xl">Page not found</h1>
                <p class="mt-5 max-w-xl text-lg leading-8 text-white/70">
                    The page you requested may have moved, been removed, or is not available at this address.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="/" class="btn-gold">Back to Home</a>
                    <a href="/contact-us" class="btn-outline">Contact Us</a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
