<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AdminSidebar from '@/Components/AdminSidebar.vue'

const sidebarOpen = ref(false);

const closeSidebar = () => {
    sidebarOpen.value = false;
};

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="min-h-screen bg-heritage-offwhite text-slate-800">

        <AdminSidebar :open="sidebarOpen" @close="closeSidebar" />


        <!-- =====================================================
             MAIN AREA
        ====================================================== -->
        <div class="min-h-screen lg:pl-64.5">

            <!-- TOP BAR -->
            <header class="sticky top-0 z-30 h-20.5 border-b border-slate-200 bg-white/95 backdrop-blur">

                <div class="flex h-full items-center justify-between px-5 sm:px-7">

                    <!-- MOBILE MENU -->
                    <button type="button" class="rounded-md p-2 text-slate-500 hover:bg-slate-100 lg:hidden"
                        @click="sidebarOpen = true">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>


                    <!-- SEARCH -->
                    <div class="hidden w-full max-w-md md:block">

                        <div class="relative">

                            <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <circle cx="11" cy="11" r="7" />
                                <path d="m20 20-4-4" />
                            </svg>

                            <input type="search" placeholder="Search..."
                                class="w-full rounded-md border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-heritage-gold focus:bg-white" />

                        </div>

                    </div>


                    <!-- RIGHT -->
                    <div class="ml-auto flex items-center gap-3 sm:gap-5">

                        <!-- Notifications -->
                        <button type="button" class="relative rounded-md p-2 text-slate-500 hover:bg-slate-100">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                                <path d="M10 21h4" />
                            </svg>

                            <span class="absolute right-1.5 top-1.5 h-1.5 w-1.5 rounded-full bg-heritage-gold"></span>
                        </button>


                        <!-- User -->
                        <div class="hidden items-center gap-3 border-l border-slate-200 pl-5 sm:flex">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-heritage-navy font-semibold text-white">
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

                            <button
                                type="button"
                                @click="logout"
                                class="rounded-md border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900"
                            >
                                Logout
                            </button>

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