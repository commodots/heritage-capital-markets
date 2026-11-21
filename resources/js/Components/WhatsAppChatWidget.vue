<script setup>
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const isOpen = ref(false)
const message = ref('')
const businessPhone = computed(() => page.props.whatsappBusinessPhone ?? '')
const canSend = computed(() => message.value.trim().length > 0)

function submit() {
    const trimmedMessage = message.value.trim()
    const cleanPhone = String(businessPhone.value).replace(/\D/g, '')

    if (!trimmedMessage || !cleanPhone) return

    const url = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(trimmedMessage)}`
    window.open(url, '_blank', 'noopener,noreferrer')
    message.value = ''
    isOpen.value = false
}
</script>

<template>
    <div v-if="businessPhone" class="fixed bottom-24 right-5 z-[69]">
        <section v-if="isOpen"
            class="mb-3 w-[min(92vw,23rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
            <header class="flex items-center justify-between bg-[#075E54] px-4 py-3 text-white">
                <p class="text-sm font-semibold">Chat with us on WhatsApp</p>
                <button type="button" aria-label="Close WhatsApp chat"
                    class="rounded p-1 text-xl leading-none text-white/80 transition hover:bg-white/10 hover:text-white"
                    @click="isOpen = false">
                    ×
                </button>
            </header>

            <form class="space-y-3 p-4" @submit.prevent="submit">
                <textarea v-model="message" rows="4" maxlength="2000"
                    aria-label="Type your message"
                    placeholder="Type your message..."
                    class="w-full resize-y rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none transition focus:border-[#25D366] focus:ring-2 focus:ring-[#25D366]/20"
                    @keydown.enter.exact.prevent="submit"></textarea>
                <button type="submit" :disabled="!canSend"
                    class="w-full rounded-lg bg-[#25D366] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[#1fb95a] disabled:cursor-not-allowed disabled:opacity-50">
                    Send on WhatsApp
                </button>
            </form>
        </section>

        <button type="button" :aria-expanded="isOpen" aria-label="Let's Chat on WhatsApp"
            class="ml-auto flex h-14 items-center justify-center gap-2 rounded-full bg-[#25D366] px-5 text-sm font-semibold text-white shadow-xl transition hover:bg-[#1fb95a] focus:outline-none focus:ring-2 focus:ring-[#25D366] focus:ring-offset-2"
            @click="isOpen = !isOpen">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12.04 2a9.9 9.9 0 0 0-8.48 15.02L2.2 22l5.1-1.34A9.95 9.95 0 1 0 12.04 2Zm0 18.1a8.1 8.1 0 0 1-4.13-1.13l-.3-.18-3.03.8.81-2.95-.2-.31A8.14 8.14 0 1 1 12.04 20.1Zm4.46-6.1c-.24-.12-1.43-.7-1.65-.78-.22-.08-.38-.12-.54.12-.16.24-.62.78-.76.94-.14.16-.28.18-.52.06-.24-.12-1.02-.38-1.95-1.2-.72-.63-1.2-1.42-1.34-1.66-.14-.24-.02-.37.1-.49.1-.1.24-.28.36-.42.12-.14.16-.24.24-.4.08-.16.04-.3-.02-.42-.06-.12-.54-1.3-.74-1.78-.2-.46-.4-.4-.54-.4h-.46c-.16 0-.42.06-.64.3-.22.24-.84.82-.84 2s.86 2.32.98 2.48c.12.16 1.7 2.6 4.12 3.64.58.25 1.03.4 1.38.51.58.18 1.1.15 1.51.09.46-.07 1.43-.58 1.63-1.14.2-.56.2-1.04.14-1.14-.06-.1-.22-.16-.46-.28Z" />
            </svg>
            <span>Let's Chat</span>
        </button>
    </div>
</template>
