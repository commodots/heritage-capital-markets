<script setup>
import { useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
const props=defineProps({media:{type:Array,default:()=>[]}})
const form=useForm({title:'',file:null})
function submit(){form.post('/admin/media',{forceFormData:true,onSuccess:()=>form.reset()})}
function remove(item){if(confirm('Delete this media?')) router.delete('/admin/media/'+item.id)}
function url(item){return item.file_path ? '/storage/'+item.file_path : '#'}
</script>
<template><AdminLayout><div class="p-6 lg:p-8"><div class="mb-6"><h1 class="text-2xl font-semibold text-heritage-navy">Media Library</h1><p class="mt-1 text-sm text-slate-500">Upload and manage website images and documents.</p></div>
<form @submit.prevent="submit" class="mb-8 grid gap-4 rounded-lg border bg-white p-6 md:grid-cols-[1fr_1fr_auto]"><input v-model="form.title" placeholder="Title" class="rounded border p-3"><input type="file" @change="form.file=$event.target.files[0]" class="rounded border p-3"><button class="btn-gold">Upload</button></form>
<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4"><div v-for="item in media" :key="item.id" class="overflow-hidden rounded-lg border bg-white"><div class="flex h-40 items-center justify-center bg-slate-50"><img v-if="/\.(jpg|jpeg|png|webp|gif|svg)$/i.test(item.file_path||'')" :src="url(item)" class="h-full w-full object-cover"><span v-else class="text-sm text-slate-500">Document</span></div><div class="p-4"><p class="truncate font-medium">{{item.title}}</p><div class="mt-3 flex justify-between"><a :href="url(item)" target="_blank" class="text-sm font-semibold text-heritage-navy">Open</a><button @click="remove(item)" class="text-sm text-red-600">Delete</button></div></div></div></div></div></AdminLayout></template>