<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    conversations: { type: Array, default: () => [] },
    conversation: { type: Object, default: null },
    admins: { type: Array, default: () => [] },
})

const selected = ref(props.conversation)
const replyForm = useForm({ message: '' })
const assignForm = useForm({ user_id: '' })
let timer

const groups = computed(() => ({
    waiting: props.conversations.filter(c => c.status === 'waiting'),
    active: props.conversations.filter(c => c.status === 'active'),
    closed: props.conversations.filter(c => c.status === 'closed'),
}))

function openConversation(conversation) {
    router.get(`/admin/live-chat/${conversation.id}`, {}, { preserveScroll: true })
}

function reply() {
    if (!selected.value) return
    replyForm.post(`/admin/live-chat/${selected.value.id}/reply`, {
        preserveScroll: true,
        onSuccess: () => replyForm.reset(),
    })
}

function assign() {
    if (!selected.value) return
    assignForm.post(`/admin/live-chat/${selected.value.id}/assign`, { preserveScroll: true })
}

function closeConversation() {
    if (!selected.value) return
    router.post(`/admin/live-chat/${selected.value.id}/close`, {}, { preserveScroll: true })
}

async function poll() {
    if (!selected.value) return
    const response = await fetch(`/admin/live-chat/${selected.value.id}/poll`, {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    })
    if (!response.ok) return
    const data = await response.json()
    selected.value = data.conversation
}

onMounted(() => {
    timer = window.setInterval(() => {
        poll()
        router.reload({ only: ['conversations', 'unreadCount'], preserveScroll: true })
    }, 4000)
})
onUnmounted(() => timer && window.clearInterval(timer))
</script>

<template>
    <AdminLayout>
        <div class="border-b border-heritage-border bg-white px-6 py-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-heritage-gold">Visitor Support</p>
            <h1 class="mt-1 font-display text-3xl text-heritage-navy">Live Chat</h1>
        </div>
        <div class="grid min-h-[calc(100vh-12rem)] lg:grid-cols-[22rem_minmax(0,1fr)]">
            <aside class="border-r border-heritage-border bg-white">
                <section v-for="[key, title] in [['waiting','Waiting'],['active','Active'],['closed','Closed']]" :key="key" class="border-b border-heritage-border">
                    <h2 class="px-4 py-3 text-xs font-semibold uppercase text-slate-500">{{ title }}</h2>
                    <button v-for="conversation in groups[key]" :key="conversation.id" type="button"
                        class="w-full border-t px-4 py-3 text-left hover:bg-slate-50"
                        :class="{ 'bg-amber-50': selected?.id === conversation.id }" @click="openConversation(conversation)">
                        <div class="flex justify-between"><span class="font-semibold text-heritage-navy">{{ conversation.visitor_name }}</span><span v-if="conversation.unread" class="h-2 w-2 rounded-full bg-heritage-gold"></span></div>
                        <p class="mt-1 truncate text-xs text-slate-500">{{ conversation.last_message || 'No messages' }}</p>
                        <p class="mt-1 text-[10px] text-slate-400">{{ conversation.assignee?.name || 'Unassigned' }}</p>
                    </button>
                </section>
            </aside>
            <section class="flex min-h-[32rem] flex-col bg-slate-50">
                <template v-if="selected">
                    <header class="flex flex-wrap items-center justify-between gap-3 border-b bg-white px-5 py-4">
                        <div><h2 class="font-semibold text-heritage-navy">{{ selected.visitor_name }}</h2><p class="text-xs text-slate-500">{{ selected.visitor_email }} · {{ selected.visitor_phone || 'No phone' }}</p></div>
                        <div class="flex gap-2">
                            <select v-model="assignForm.user_id" class="rounded border px-2 py-2 text-xs">
                                <option value="" disabled>Assign administrator</option>
                                <option v-for="admin in admins" :key="admin.id" :value="admin.id">{{ admin.name }}</option>
                            </select>
                            <button type="button" class="rounded border px-3 py-2 text-xs" @click="assign">Assign</button>
                            <button v-if="selected.status !== 'closed'" type="button" class="rounded bg-heritage-navy px-3 py-2 text-xs text-white" @click="closeConversation">Close</button>
                        </div>
                    </header>
                    <div class="flex-1 space-y-3 overflow-y-auto p-5">
                        <div v-for="message in selected.messages" :key="message.id" class="max-w-[75%] rounded-xl px-4 py-3 text-sm"
                            :class="message.sender_type === 'visitor' ? 'mr-auto border bg-white' : 'ml-auto bg-heritage-navy text-white'">
                            {{ message.message }}
                        </div>
                    </div>
                    <form v-if="selected.status !== 'closed'" class="border-t bg-white p-4" @submit.prevent="reply">
                        <textarea v-model="replyForm.message" required maxlength="3000" rows="3" class="w-full rounded-lg border px-3 py-2" placeholder="Write a reply..." />
                        <button :disabled="replyForm.processing" class="mt-2 rounded bg-heritage-gold px-4 py-2 text-sm font-semibold">Send Reply</button>
                    </form>
                </template>
                <div v-else class="flex flex-1 items-center justify-center text-sm text-slate-400">Select a conversation</div>
            </section>
        </div>
    </AdminLayout>
</template>
