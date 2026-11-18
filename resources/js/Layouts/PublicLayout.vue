<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import LiveChatWidget from '@/Components/LiveChatWidget.vue'
import MarketTicker from '@/Components/MarketTicker.vue'
</script>

<template>

    <div class="min-h-screen bg-white">

        <Header />
        <MarketTicker />

        <main>
            <slot />
        </main>

        <Footer />
        <LiveChatWidget />

    </div>

</template>