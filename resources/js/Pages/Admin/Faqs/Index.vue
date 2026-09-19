<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({ faqs: { type: Array, default: () => [] } })
const editing = ref(null)
const form = useForm({  question:'', answer:'', category:'General', is_active:true, sort_order:0  })
function reset(){ editing.value=null; form.reset(); }
function edit(item){ editing.value=item; form.defaults(item); form.reset(); }
function submit(){ form.post(editing.value ? '/admin/faqs/'+editing.value.id : '/admin/faqs', { forceFormData:true, onSuccess:reset }) }
function toggle(item){ router.post('/admin/faqs/'+item.id+'/toggle') }
function remove(item){ if(confirm('Delete this item?')) router.delete('/admin/faqs/'+item.id) }
</script>
<template>
<AdminLayout>
<div class="p-6 lg:p-8">
<div class="mb-6 flex items-center justify-between"><div><h1 class="text-2xl font-semibold text-heritage-navy">FAQs</h1><p class="mt-1 text-sm text-slate-500">Manage website content.</p></div><button @click="reset" class="btn-gold">+ New</button></div>
<div class="grid gap-6 lg:grid-cols-[1fr_380px]">
<div class="overflow-hidden rounded-lg border border-slate-200 bg-white">
<table class="w-full text-left text-sm"><thead class="bg-slate-50"><tr><th class="p-4">Title</th><th class="p-4">Status</th><th class="p-4 text-right">Actions</th></tr></thead>
<tbody><tr v-for="item in faqs" :key="item.id" class="border-t border-slate-100"><td class="p-4 font-medium">{{ item.title || item.question }}</td><td class="p-4"><button @click="toggle(item)" class="rounded-full px-3 py-1 text-xs" :class="item.is_published || item.is_active ? 'bg-green-100 text-green-700':'bg-slate-100 text-slate-500'">{{ item.is_published || item.is_active ? 'Active':'Inactive' }}</button></td><td class="p-4 text-right"><button @click="edit(item)" class="mr-3 font-medium text-heritage-navy">Edit</button><button @click="remove(item)" class="font-medium text-red-600">Delete</button></td></tr><tr v-if="!faqs.length"><td colspan="3" class="p-10 text-center text-slate-500">No records yet.</td></tr></tbody></table>
</div>
<form @submit.prevent="submit" class="rounded-lg border border-slate-200 bg-white p-6">
<h2 class="text-lg font-semibold text-heritage-navy">{{ editing ? 'Edit' : 'Create' }} FAQs</h2>
<div class="mt-5 space-y-4">FORM_{ question:'', answer:'', category:'General', is_active:true, sort_order:0 }</div>
<div class="mt-6 flex gap-3"><button type="submit" class="btn-gold" :disabled="form.processing">{{ form.processing ? 'Saving...' : 'Save' }}</button><button type="button" @click="reset" class="rounded border px-4 py-2">Clear</button></div>
</form>
</div></div>
</AdminLayout>
</template>