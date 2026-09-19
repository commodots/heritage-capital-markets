<script setup>
import { ref } from 'vue';
import heritageLogo from '@/assets/logo/heritage-logo.png'

const sidebarOpen = ref(false);
const homepageOpen = ref(true);

const closeSidebar = () => {
    sidebarOpen.value = false;
};
</script>

<template>
    <div class="min-h-screen bg-[#f7f8fa] text-slate-800">

        <!-- MOBILE OVERLAY -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
            @click="closeSidebar"
        ></div>


        <!-- =====================================================
             SIDEBAR
        ====================================================== -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-[258px] flex-col bg-heritage-navy transition-transform duration-300 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >

            <!-- BRAND -->
            <div class="flex h-[82px] items-center border-b border-white/10 px-6">
			
			            <a href="/" class="flex w-[205px] shrink-0 items-center" @click="closeMobile">
                <div class="flex items-center">
                    <img :src="heritageLogo" alt="Heritage Capital Markets Limited" class="h-10 w-auto max-w-full object-contain">

                  
                </div>
            </a>

               

                <button
                    type="button"
                    class="ml-auto text-xl text-white/50 lg:hidden"
                    @click="closeSidebar"
                >
                    ×
                </button>

            </div>


            <!-- NAVIGATION -->
            <nav class="flex-1 overflow-y-auto px-3 py-5">

                <!-- MAIN -->
                <div class="mb-5">

                    <p class="px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                        Main
                    </p>

                    <a
                        href="/admin"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>

                        Dashboard
                    </a>

                </div>


                <!-- HOMEPAGE -->
                <div class="mb-5">

                    <button
                        type="button"
                        @click="homepageOpen = !homepageOpen"
                        class="admin-nav-item w-full"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V10Z" />
                            <path d="M9 21v-7h6v7" />
                        </svg>

                        <span class="flex-1 text-left">
                            Homepage
                        </span>

                        <span
                            class="text-white/40 transition"
                            :class="homepageOpen ? 'rotate-180' : ''"
                        >
                           ⌄
                        </span>
                    </button>

                    <div
                        v-if="homepageOpen"
                        class="ml-8 mt-1 space-y-0.5 border-l border-white/10 pl-2"
                    >

                        <a
                            href="/admin/homepage/hero-slides"
                            class="admin-sub-item"
                        >
                            Hero Slides
                        </a>

                        <a
                            href="/admin/homepage/settings"
                            class="admin-sub-item"
                        >
                            Homepage Settings
                        </a>

                    </div>

                </div>


                <!-- CONTENT -->
                <div class="mb-5">

                    <p class="px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                        Content Management
                    </p>

                    <a
                        href="/admin/pages"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" />
                            <path d="M14 2v6h6" />
                            <path d="M8 13h8M8 17h6" />
                        </svg>

                        Pages
                    </a>


                    <a
                        href="/admin/investment-solutions"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="3" width="18" height="18" rx="2" />
                            <path d="M7 15v-3M12 15V8M17 15v-6" />
                        </svg>

                        Investment Solutions
                    </a>


                    <a
                        href="/admin/market-indices"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M4 19V5M4 19h16" />
                            <path d="m7 15 4-5 3 2 5-7" />
                        </svg>

                        Market Indices
                    </a>


                    <a
                        href="/admin/research-reports"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M6 2h9l5 5v15H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Z" />
                            <path d="M14 2v6h6M8 13h8M8 17h6" />
                        </svg>

                        Research & Reports
                    </a>


                    <a
						href="/admin/articles"
						class="admin-nav-item"
					>
						<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
							<path d="M4 5h16v14H4z" />
							<path d="M7 9h10M7 13h6" />
						</svg>

						News & Articles
					</a>


                    <a
                        href="/admin/events"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="17" rx="2" />
                            <path d="M16 2v4M8 2v4M3 10h18" />
                        </svg>

                        Events
                    </a>


                    <a
                        href="/admin/faqs"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M9.5 9a2.5 2.5 0 1 1 4.1 1.9c-.9.7-1.6 1.1-1.6 2.6" />
                            <circle cx="12" cy="17" r=".7" fill="currentColor" />
                        </svg>

                        FAQs
                    </a>


                    <a
                        href="/admin/media"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="4" width="18" height="16" rx="2" />
                            <circle cx="8.5" cy="9" r="1.5" />
                            <path d="m4 17 5-5 4 4 3-3 4 4" />
                        </svg>

                        Media Library
                    </a>

                </div>


                <!-- COMMUNICATION -->
                <div class="mb-5">

                    <p class="px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                        Communication
                    </p>

                    <a
                        href="/admin/enquiries"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                            <path d="m3 7 9 6 9-6" />
                        </svg>

                        Enquiries
                    </a>


                    <a
                        href="/admin/newsletter-subscribers"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="8" r="3" />
                            <path d="M5 20c0-3.3 3.1-6 7-6s7 2.7 7 6" />
                        </svg>

                        Newsletter Subscribers
                    </a>

                </div>


                <!-- SYSTEM -->
                <div>

                    <p class="px-3 pb-2 text-[9px] font-semibold uppercase tracking-[0.2em] text-white/35">
                        System
                    </p>

                    <a
                        href="/admin/settings"
                        class="admin-nav-item"
                    >
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="3" />
                            <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-1.8 1.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V20h-2.5v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1-1.8-1.8.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H5v-2.5h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 1.8-1.8.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6V5h2.5v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1 1.8 1.8-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1v2.5h-.1a1.7 1.7 0 0 0-1.6 1Z" />
                        </svg>

                        Settings
                    </a>

                </div>

            </nav>


            <!-- SIDEBAR FOOTER -->
            <div class="border-t border-white/10 p-4">

                <a
                    href="/"
                    target="_blank"
                    class="flex items-center gap-3 rounded-md px-3 py-3 text-xs text-white/60 transition hover:bg-white/5 hover:text-white"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                    >
                        <path d="M14 3h7v7" />
                        <path d="M10 14 21 3" />
                        <path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" />
                    </svg>

                    View Website
                </a>

            </div>

        </aside>


        <!-- =====================================================
             MAIN AREA
        ====================================================== -->
        <div class="min-h-screen lg:pl-[258px]">

            <!-- TOP BAR -->
            <header class="sticky top-0 z-30 h-[82px] border-b border-slate-200 bg-white/95 backdrop-blur">

                <div class="flex h-full items-center justify-between px-5 sm:px-7">

                    <!-- MOBILE MENU -->
                    <button
                        type="button"
                        class="rounded-md p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
                        @click="sidebarOpen = true"
                    >
                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                        >
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>


                    <!-- SEARCH -->
                    <div class="hidden w-full max-w-md md:block">

                        <div class="relative">

                            <svg
                                class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.7"
                            >
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-4-4" />
                            </svg>

                            <input
                                type="search"
                                placeholder="Search..."
                                class="w-full rounded-md border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-heritage-gold focus:bg-white"
                            />

                        </div>

                    </div>


                    <!-- RIGHT -->
                    <div class="ml-auto flex items-center gap-3 sm:gap-5">

                        <!-- Notifications -->
                        <button
                            type="button"
                            class="relative rounded-md p-2 text-slate-500 hover:bg-slate-100"
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                            >
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                                <path d="M10 21h4" />
                            </svg>

                            <span class="absolute right-1.5 top-1.5 h-1.5 w-1.5 rounded-full bg-heritage-gold"></span>
                        </button>


                        <!-- User -->
                        <div class="hidden items-center gap-3 border-l border-slate-200 pl-5 sm:flex">

                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-heritage-navy font-semibold text-white">
                                A
                            </div>

                            <div class="leading-tight">
                                <div class="text-sm font-semibold text-slate-800">
                                    Administrator
                                </div>

                                <div class="text-[10px] text-slate-400">
                                    CMS Administrator
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </header>


            <!-- PAGE CONTENT -->
            <main>
                <slot />
            </main>

        </div>

    </div>
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
    color: rgba(255, 255, 255, 0.68);
    transition: all 0.2s ease;
}

.admin-nav-item:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #ffffff;
}

.admin-sub-item {
    display: block;
    border-radius: 0.25rem;
    padding: 0.5rem 0.7rem;
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.52);
    transition: all 0.2s ease;
}

.admin-sub-item:hover {
    background: rgba(255, 255, 255, 0.05);
    color: #ffffff;
}

.nav-icon {
    height: 16px;
    width: 16px;
    flex-shrink: 0;
    stroke-width: 1.6;
}
</style>