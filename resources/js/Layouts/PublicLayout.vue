<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import MarketTicker from '@/Components/MarketTicker.vue'
import LiveChatWidget from '@/Components/LiveChatWidget.vue'
</script>

<template>

    <div class="min-h-screen bg-white">

        <Header />

        <main>
            <slot />
        </main>

        <Footer />
        <MarketTicker />
        <LiveChatWidget />

    </div>

</template>