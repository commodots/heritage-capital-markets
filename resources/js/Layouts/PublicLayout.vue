<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
</script>

<template>

    <div class="min-h-screen bg-white">

        <Header />

        <main>
            <slot />
        </main>

        <Footer />

    </div>

</template>