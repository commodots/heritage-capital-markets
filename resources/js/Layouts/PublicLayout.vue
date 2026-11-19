<script setup>
import { usePage } from '@inertiajs/vue3'
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
import MarketTicker from '@/Components/MarketTicker.vue'
import LiveChatWidget from '@/Components/LiveChatWidget.vue'
import WhatsAppChatWidget from '@/Components/WhatsAppChatWidget.vue'

const page = usePage()
</script>

<template>

    <div class="min-h-screen bg-white">

        <Header />

        <main>
            <slot />
        </main>

        <Footer />
        <MarketTicker :items="page.props.marketTicker ?? []" />
        <LiveChatWidget />
        <WhatsAppChatWidget />

    </div>

</template>