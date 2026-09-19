<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
const props=defineProps({subscribers:{type:Array,default:()=>[]}})
function toggle(x){router.post('/admin/newsletter-subscribers/'+x.id+'/toggle')}
</script>
<template><AdminLayout><div class="p-6 lg:p-8"><div class="mb-6 flex items-center justify-between"><div><h1 class="text-2xl font-semibold text-heritage-navy">Newsletter Subscribers</h1><p class="mt-1 text-sm text-slate-500">Manage newsletter subscriptions.</p></div><a href="/admin/newsletter-subscribers/export" class="btn-gold">Export CSV</a></div><div class="overflow-hidden rounded-lg border bg-white"><table class="w-full text-left text-sm"><thead class="bg-slate-50"><tr><th class="p-4">Email</th><th class="p-4">Status</th><th class="p-4">Subscribed</th></tr></thead><tbody><tr v-for="x in subscribers" :key="x.id" class="border-t"><td class="p-4">{{x.email}}</td><td class="p-4"><button @click="toggle(x)" class="text-sm font-semibold">{{x.is_active?'Active':'Inactive'}}</button></td><td class="p-4 text-slate-500">{{x.created_at}}</td></tr></tbody></table></div></div></AdminLayout></template>