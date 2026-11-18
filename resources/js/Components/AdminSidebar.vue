<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import heritageLogo from '@/assets/logo/heritage-logo.png'

const props = defineProps({
    open: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['close'])
const page = usePage()
const permissions = computed(() => page.props.auth?.user?.permissions ?? [])
const isSuperAdmin = computed(() => page.props.auth?.user?.is_super_admin === true)

const can = (feature) => isSuperAdmin.value || permissions.value.includes(feature)
const canShowHomepage = computed(() =>
    ['homepage', 'market_indices', 'investment_solutions'].some(can)
)
const canShowContent = computed(() =>
    ['pages', 'board_management', 'research', 'articles', 'events', 'faqs', 'media'].some(can)
)
const canShowCommunication = computed(() =>
    ['enquiries', 'newsletter', 'account_applications', 'live_chat'].some(can)
)
const chatUnreadCount = ref(0)
let chatUnreadTimer

async function refreshChatUnreadCount() {
    if (!can('live_chat')) return
    try {
        const response = await fetch('/admin/live-chat/unread-count', {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        })
        if (response.ok) {
            const data = await response.json()
            chatUnreadCount.value = Number(data.count) || 0
        }
    } catch (_) {}
}

onMounted(() => {
    refreshChatUnreadCount()
    chatUnreadTimer = window.setInterval(refreshChatUnreadCount, 4000)
})

onUnmounted(() => {
    if (chatUnreadTimer) window.clearInterval(chatUnreadTimer)
})

const currentPath = computed(() => (page.url || '/').split('?')[0])

function isActive(path) {
    return currentPath.value === path
        || (path !== '/admin' && currentPath.value.startsWith(`${path}/`))
}

function navClass(path) {
    return isActive(path)
        ? 'bg-white/10 text-white'
        : 'text-white/70 hover:bg-white/5 hover:text-white'
}

function subItemClass(path) {
    return isActive(path)
        ? 'font-medium text-heritage-gold'
        : 'text-white/60 hover:text-white'
}

function closeSidebar() {
    emit('close')
}
</script>

<template>
    <div v-if="props.open" class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden" @click="closeSidebar"></div>
    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-64.5 flex-col bg-heritage-navy transition-transform duration-300 lg:translate-x-0"
        :class="props.open ? 'translate-x-0' : '-translate-x-full'"
    >
        <div class="flex h-20.5 items-center border-b border-white/10 px-6">
            <a href="/admin" class="flex items-center" @click="closeSidebar">
                <img :src="heritageLogo" alt="Heritage Capital Markets Limited" class="h-10 w-auto">
            </a>
            <button type="button" class="ml-auto text-xl text-white/50 lg:hidden" @click="closeSidebar">
                ×
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-5">
            <div v-if="can('dashboard')" class="mb-5">
                <p class="px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                    Main
                </p>
                <a href="/admin" :class="['admin-nav-item', navClass('/admin')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <rect x="3" y="3" width="7" height="7" rx="1" />
                        <rect x="14" y="3" width="7" height="7" rx="1" />
                        <rect x="3" y="14" width="7" height="7" rx="1" />
                        <rect x="14" y="14" width="7" height="7" rx="1" />
                    </svg>
                    Dashboard
                </a>
            </div>

            <div v-if="canShowHomepage" class="mb-5">
                <p class="flex items-center gap-2 px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z" />
                        <path d="M9 21v-7h6v7" />
                    </svg>
                    Homepage
                </p>
                <div class="ml-3 space-y-0.5 border-l border-white/10 pl-2">
                    <a v-if="can('homepage')" href="/admin/homepage/hero-slides" :class="['admin-sub-item', subItemClass('/admin/homepage/hero-slides')]">
                        <svg class="nav-sub-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="16" rx="2" />
                            <path d="M7 8h10M7 12h6" />
                        </svg>
                        Hero Slides
                    </a>
                    <a v-if="can('market_indices')" href="/admin/market-indices" :class="['admin-sub-item', subItemClass('/admin/market-indices')]">
                        <svg class="nav-sub-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path d="M4 19V5M4 19h16" />
                            <path d="m7 15 4-5 3 2 5-7" />
                        </svg>
                        Market Indices
                    </a>
                    <a v-if="can('investment_solutions')" href="/admin/investment-solutions" :class="['admin-sub-item', subItemClass('/admin/investment-solutions')]">
                        <svg class="nav-sub-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <rect x="3" y="7" width="18" height="13" rx="2" />
                            <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18" />
                        </svg>
                        Investment Solutions
                    </a>
                    <a v-if="can('homepage')" href="/admin/homepage/settings" :class="['admin-sub-item', subItemClass('/admin/homepage/settings')]">
                        <svg class="nav-sub-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                            <circle cx="8" cy="6" r="2" fill="currentColor" />
                            <circle cx="16" cy="12" r="2" fill="currentColor" />
                            <circle cx="10" cy="18" r="2" fill="currentColor" />
                        </svg>
                        Homepage Settings
                    </a>
                </div>
            </div>

            <div v-if="canShowContent" class="mb-5">
                <p class="px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                    Content Management
                </p>
                <a v-if="can('pages')" href="/admin/pages" :class="['admin-nav-item', navClass('/admin/pages')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" />
                        <path d="M14 2v6h6M8 13h8M8 17h6" />
                    </svg>
                    Pages
                </a>
                <div v-if="can('pages') || can('board_management')" class="ml-8 mt-1 space-y-0.5 border-l border-white/10 pl-2">
                    <template v-if="can('pages')">
                    <a href="/admin/page-content/about" :class="['admin-sub-item', subItemClass('/admin/page-content/about')]">About Us</a>
                    <a href="/admin/page-content/investment-solutions" :class="['admin-sub-item', subItemClass('/admin/page-content/investment-solutions')]">Investment Solutions Page</a>
                    <a href="/admin/page-content/investment-details" :class="['admin-sub-item', subItemClass('/admin/page-content/investment-details')]">Solution Details</a>
                    <a href="/admin/page-content/investor-login" :class="['admin-sub-item', subItemClass('/admin/page-content/investor-login')]">Investor Login</a>
                    </template>
                    <a v-if="can('board_management')" href="/admin/page-content/board" :class="['admin-sub-item', subItemClass('/admin/page-content/board')]">Board &amp; Management</a>
                    <template v-if="can('pages')">
                    <a href="/admin/page-content/governance" :class="['admin-sub-item', subItemClass('/admin/page-content/governance')]">Corporate Governance</a>
                    <a href="/admin/page-content/fees" :class="['admin-sub-item', subItemClass('/admin/page-content/fees')]">Fees &amp; Charges</a>
                    <a href="/admin/page-content/resources" :class="['admin-sub-item', subItemClass('/admin/page-content/resources')]">Investor Resources</a>
                    <p class="px-2 pb-1 pt-3 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                        Legal
                    </p>
                    <a href="/admin/page-content/privacy" :class="['admin-sub-item', subItemClass('/admin/page-content/privacy')]">Privacy Policy</a>
                    <a href="/admin/page-content/terms" :class="['admin-sub-item', subItemClass('/admin/page-content/terms')]">Terms of Use</a>
                    <a href="/admin/page-content/risk" :class="['admin-sub-item', subItemClass('/admin/page-content/risk')]">Risk Disclosure</a>
                    </template>
                </div>
                <a v-if="can('research')" href="/admin/research-reports" :class="['admin-nav-item', navClass('/admin/research-reports')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" />
                        <path d="M14 2v6h6M8 13h8M8 17h6" />
                    </svg>
                    Research &amp; Reports
                </a>
                <a v-if="can('articles')" href="/admin/articles" :class="['admin-nav-item', navClass('/admin/articles')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M4 5h16v14H4z" />
                        <path d="M7 9h10M7 13h6" />
                    </svg>
                    News &amp; Articles
                </a>
                <a v-if="can('events')" href="/admin/events" :class="['admin-nav-item', navClass('/admin/events')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="17" rx="2" />
                        <path d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    Events
                </a>
                <a v-if="can('faqs')" href="/admin/faqs" :class="['admin-nav-item', navClass('/admin/faqs')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" />
                        <path d="M9.5 9a2.5 2.5 0 1 1 4.1 1.9c-.9.7-1.6 1.1-1.6 2.6" />
                        <circle cx="12" cy="17" r=".7" fill="currentColor" />
                    </svg>
                    FAQs
                </a>
                <a v-if="can('media')" href="/admin/media" :class="['admin-nav-item', navClass('/admin/media')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="16" rx="2" />
                        <circle cx="8.5" cy="9" r="1.5" />
                        <path d="m4 17 5-5 4 4 3-3 4 4" />
                    </svg>
                    Media Library
                </a>
            </div>

            <div v-if="canShowCommunication" class="mb-5">
                <p class="px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                    Communication
                </p>
                <a v-if="can('enquiries')" href="/admin/enquiries" :class="['admin-nav-item', navClass('/admin/enquiries')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                        <path d="m3 7 9 6 9-6" />
                    </svg>
                    Enquiries
                </a>
                <a v-if="can('newsletter')" href="/admin/newsletter-subscribers" :class="['admin-nav-item', navClass('/admin/newsletter-subscribers')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="12" cy="8" r="3" />
                        <path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6" />
                    </svg>
                    Newsletter Subscribers
                </a>
                <a v-if="can('account_applications')" href="/admin/account-applications" :class="['admin-nav-item', navClass('/admin/account-applications')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" />
                        <path d="M14 2v6h6M8 13h8M8 17h5" />
                    </svg>
                    Account Applications
                </a>
                <a v-if="can('live_chat')" href="/admin/live-chat" :class="['admin-nav-item', navClass('/admin/live-chat')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M4 5h16v12H9l-5 4V5Z" />
                        <path d="M8 9h8M8 13h5" />
                    </svg>
                    <span class="flex-1">Live Chat</span>
                    <span v-if="chatUnreadCount > 0" class="rounded-full bg-heritage-gold px-2 py-0.5 text-[10px] font-semibold text-heritage-navy">
                        {{ chatUnreadCount }}
                    </span>
                </a>
            </div>

            <div v-if="can('settings') || isSuperAdmin">
                <p class="px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                    System
                </p>
                <a v-if="can('settings')" href="/admin/settings" :class="['admin-nav-item', navClass('/admin/settings')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 1.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V20h-2.5v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1-1.8-1.8.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H5v-2.5h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 1.8-1.8.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V5h2.5v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1 1.8 1.8-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1v2.5h-.1a1.7 1.7 0 0 0-1.6 1Z" />
                    </svg>
                    Settings
                </a>
                <a v-if="isSuperAdmin" href="/admin/users" :class="['admin-nav-item', navClass('/admin/users')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <circle cx="9" cy="8" r="3" /><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6M17 11a3 3 0 1 0-2-5.7M17 14c2.4.4 4 2.6 4 5" />
                    </svg>
                    Admin Users
                </a>
                <a v-if="isSuperAdmin" href="/admin/user-types" :class="['admin-nav-item', navClass('/admin/user-types')]">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M4 5h16M4 12h16M4 19h16" /><circle cx="8" cy="5" r="2" /><circle cx="16" cy="12" r="2" /><circle cx="10" cy="19" r="2" />
                    </svg>
                    User Types &amp; Permissions
                </a>
            </div>
        </nav>

        <div class="border-t border-white/10 p-4">
            <a href="/" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 rounded-md px-3 py-3 text-xs text-white/60 transition hover:bg-white/5 hover:text-white">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                    <path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" />
                </svg>
                View Website
            </a>
        </div>
    </aside>
</template>

<style scoped>
.admin-nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-radius: 0.375rem;
    padding: 0.65rem 0.75rem;
    font-size: 0.78rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.nav-icon {
    height: 16px;
    width: 16px;
    flex-shrink: 0;
    stroke-width: 1.6;
}

.admin-sub-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-radius: 0.25rem;
    padding: 0.5rem 0.7rem;
    font-size: 0.72rem;
    transition: all 0.2s ease;
}

.nav-sub-icon {
    height: 14px;
    width: 14px;
    flex-shrink: 0;
    stroke-width: 1.6;
}
</style>
