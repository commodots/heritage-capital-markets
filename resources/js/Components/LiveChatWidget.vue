<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'

const open = ref(false)
const started = ref(false)
const sending = ref(false)
const availability = ref({ is_open: false, offline_message: 'We are currently offline.' })
const messages = ref([])
const status = ref('')
const error = ref('')
const form = ref({ name: '', email: '', phone: '', message: '' })
let timer = null

const isOpen = computed(() => availability.value.is_open)
const headers = () => ({
    Accept: 'application/json',
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
})

async function checkAvailability() {
    try {
        const response = await fetch('/chat/availability', { headers: { Accept: 'application/json' } })
        availability.value = await response.json()
    } catch (_) {}
}

async function start() {
    sending.value = true
    error.value = ''
    try {
        const response = await fetch('/chat/start', {
            method: 'POST', headers: headers(), credentials: 'same-origin', body: JSON.stringify(form.value),
        })
        const data = await response.json()
        if (!response.ok) throw new Error(data.message || 'Unable to start chat.')
        started.value = true
        status.value = data.status
        messages.value = data.messages || []
        form.value.message = ''
        timer = window.setInterval(refresh, 4000)
    } catch (e) {
        error.value = e.message
    } finally {
        sending.value = false
    }
}

async function send() {
    if (!form.value.message.trim()) return
    sending.value = true
    try {
        const response = await fetch('/chat/message', {
            method: 'POST', headers: headers(), credentials: 'same-origin',
            body: JSON.stringify({ message: form.value.message }),
        })
        const data = await response.json()
        if (!response.ok) throw new Error(data.message || 'Unable to send message.')
        form.value.message = ''
        status.value = data.status
        await refresh()
    } catch (e) {
        error.value = e.message
    } finally {
        sending.value = false
    }
}

async function refresh() {
    const response = await fetch('/chat/messages', { headers: { Accept: 'application/json' }, credentials: 'same-origin' })
    if (!response.ok) return
    const data = await response.json()
    status.value = data.status
    messages.value = data.messages || []
    if (data.status === 'closed' && timer) {
        window.clearInterval(timer)
        timer = null
    }
}

async function offlineEnquiry() {
    sending.value = true
    error.value = ''
    try {
        const response = await fetch('/chat/offline-enquiry', {
            method: 'POST', headers: headers(), credentials: 'same-origin', body: JSON.stringify(form.value),
        })
        const data = await response.json()
        if (!response.ok) throw new Error(data.message || 'Unable to submit enquiry.')
        error.value = data.message
        form.value = { name: '', email: '', phone: '', message: '' }
    } catch (e) {
        error.value = e.message
    } finally {
        sending.value = false
    }
}

function close() {
    open.value = false
    if (timer) window.clearInterval(timer)
}

onMounted(checkAvailability)
onUnmounted(close)
</script>

<template>
    <div class="fixed bottom-5 right-5 z-[70]">
        <section v-if="open" class="mb-3 w-[min(92vw,23rem)] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
            <header class="flex items-center justify-between bg-heritage-navy px-4 py-3 text-white">
                <div><p class="text-sm font-semibold">Chat with HCML</p><p class="text-xs text-white/70">{{ isOpen ? 'We are online' : 'We are offline' }}</p></div>
                <button type="button" class="text-xl" @click="close" aria-label="Close chat">×</button>
            </header>
            <div v-if="started" class="flex max-h-[70vh] min-h-[28rem] flex-col">
                <div class="flex-1 space-y-3 overflow-y-auto bg-slate-50 p-4">
                    <div v-for="message in messages" :key="message.id" class="max-w-[85%] rounded-xl px-3 py-2 text-sm"
                        :class="message.sender_type === 'visitor' ? 'ml-auto bg-heritage-navy text-white' : 'mr-auto border bg-white'">
                        {{ message.message }}
                    </div>
                </div>
                <form v-if="status !== 'closed'" class="border-t p-3" @submit.prevent="send">
                    <textarea v-model="form.message" required maxlength="3000" rows="3" class="w-full rounded-lg border px-3 py-2" placeholder="Type your message..." />
                    <button :disabled="sending" class="mt-2 w-full rounded-lg bg-heritage-gold px-4 py-2 font-semibold">Send</button>
                </form>
            </div>
            <form v-else-if="isOpen" class="space-y-3 p-4" @submit.prevent="start">
                <input v-model="form.name" required maxlength="150" placeholder="Name" class="w-full rounded-lg border px-3 py-2">
                <input v-model="form.email" required type="email" maxlength="150" placeholder="Email" class="w-full rounded-lg border px-3 py-2">
                <input v-model="form.phone" maxlength="50" placeholder="Phone (optional)" class="w-full rounded-lg border px-3 py-2">
                <textarea v-model="form.message" required maxlength="3000" rows="4" placeholder="How can we help?" class="w-full rounded-lg border px-3 py-2" />
                <p v-if="error" class="text-xs text-red-600">{{ error }}</p>
                <button :disabled="sending" class="w-full rounded-lg bg-heritage-gold px-4 py-2.5 font-semibold">Start Chat</button>
            </form>
            <form v-else class="space-y-3 p-4" @submit.prevent="offlineEnquiry">
                <p class="rounded-lg bg-amber-50 px-3 py-2 text-sm text-amber-800">{{ availability.offline_message }}</p>
                <input v-model="form.name" required maxlength="150" placeholder="Name" class="w-full rounded-lg border px-3 py-2">
                <input v-model="form.email" required type="email" maxlength="150" placeholder="Email" class="w-full rounded-lg border px-3 py-2">
                <input v-model="form.phone" maxlength="50" placeholder="Phone (optional)" class="w-full rounded-lg border px-3 py-2">
                <textarea v-model="form.message" required maxlength="3000" rows="4" placeholder="Enquiry message" class="w-full rounded-lg border px-3 py-2" />
                <p v-if="error" class="text-xs text-slate-600">{{ error }}</p>
                <button :disabled="sending" class="w-full rounded-lg bg-heritage-gold px-4 py-2.5 font-semibold">Submit Enquiry</button>
            </form>
        </section>
        <button type="button" aria-label="Open live chat" class="ml-auto flex h-14 w-14 items-center justify-center rounded-full bg-heritage-gold text-heritage-navy shadow-xl"
            @click="open = !open">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 5h16v12H9l-5 4V5Z"/><path d="M8 9h8M8 13h5"/></svg>
        </button>
    </div>
</template>
