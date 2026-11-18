<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    roles: { type: Array, default: () => [] },
    features: { type: Object, default: () => ({}) },
})

const editingRole = ref(null)
const form = useForm({
    name: '',
    description: '',
    permissions: [],
    is_active: true,
})

function editRole(role) {
    editingRole.value = role
    form.name = role.name
    form.description = role.description || ''
    form.permissions = [...(role.permissions || [])]
    form.is_active = role.is_active
    form.clearErrors()
}

function resetForm() {
    editingRole.value = null
    form.reset()
    form.clearErrors()
}

function submit() {
    if (editingRole.value) {
        form.put(`/admin/user-types/${editingRole.value.id}`, {
            preserveScroll: true,
            onSuccess: resetForm,
        })
        return
    }

    form.post('/admin/user-types', {
        preserveScroll: true,
        onSuccess: resetForm,
    })
}
</script>

<template>
    <AdminLayout>
        <div class="border-b border-heritage-border bg-white px-6 py-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-heritage-gold">Access Management</p>
            <h1 class="mt-1 font-display text-3xl text-heritage-navy">User Types &amp; Permissions</h1>
            <p class="mt-1 text-sm text-heritage-muted">Group administrators by the modules they are authorized to manage.</p>
        </div>

        <div class="space-y-6 p-6 lg:p-8">
            <form class="space-y-5 rounded-xl border border-heritage-border bg-white p-5" @submit.prevent="submit">
                <h2 class="font-semibold text-heritage-navy">
                    {{ editingRole ? `Edit ${editingRole.name}` : 'Create User Type' }}
                </h2>
                <div class="grid gap-4 md:grid-cols-2">
                    <label class="text-sm font-medium text-heritage-navy">
                        Name
                        <input v-model="form.name" required maxlength="100" class="mt-1 w-full rounded border border-heritage-border px-3 py-2.5">
                        <span v-if="form.errors.name" class="mt-1 block text-xs text-red-600">{{ form.errors.name }}</span>
                    </label>
                    <label class="text-sm font-medium text-heritage-navy">
                        Description
                        <input v-model="form.description" class="mt-1 w-full rounded border border-heritage-border px-3 py-2.5">
                        <span v-if="form.errors.description" class="mt-1 block text-xs text-red-600">{{ form.errors.description }}</span>
                    </label>
                </div>

                <fieldset>
                    <legend class="mb-3 text-sm font-semibold text-heritage-navy">Module access</legend>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <label v-for="(label, feature) in props.features" :key="feature"
                            class="flex items-center gap-2 rounded border border-heritage-border px-3 py-2 text-sm text-slate-700">
                            <input v-model="form.permissions" type="checkbox" :value="feature"
                                class="rounded border-slate-300 text-heritage-gold">
                            {{ label }}
                        </label>
                    </div>
                    <span v-if="form.errors.permissions" class="mt-2 block text-xs text-red-600">{{ form.errors.permissions }}</span>
                </fieldset>

                <label v-if="editingRole" class="flex items-center gap-2 text-sm text-heritage-navy">
                    <input v-model="form.is_active" type="checkbox" class="rounded border-slate-300 text-heritage-gold">
                    Active user type
                </label>

                <div class="flex gap-2">
                    <button :disabled="form.processing" class="rounded bg-heritage-navy px-4 py-2.5 text-sm font-semibold text-white disabled:opacity-50">
                        {{ form.processing ? 'Saving...' : editingRole ? 'Save Changes' : 'Create User Type' }}
                    </button>
                    <button v-if="editingRole" type="button" class="rounded border border-heritage-border px-4 py-2.5 text-sm" @click="resetForm">
                        Cancel
                    </button>
                </div>
            </form>

            <section class="overflow-hidden rounded-xl border border-heritage-border bg-white">
                <div class="border-b border-heritage-border px-5 py-4">
                    <h2 class="font-semibold text-heritage-navy">Existing User Types</h2>
                </div>
                <div class="divide-y divide-heritage-border">
                    <article v-for="role in props.roles" :key="role.id" class="flex flex-col gap-4 p-5 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-semibold text-heritage-navy">{{ role.name }}</h3>
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold"
                                    :class="role.is_active ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500'">
                                    {{ role.is_active ? 'Active' : 'Inactive' }}
                                </span>
                                <span class="text-xs text-slate-500">{{ role.users_count }} administrator(s)</span>
                            </div>
                            <p v-if="role.description" class="mt-1 text-sm text-slate-600">{{ role.description }}</p>
                            <p class="mt-2 text-xs text-slate-500">
                                {{ (role.permissions || []).map(feature => props.features[feature]).filter(Boolean).join(', ') || 'No modules assigned' }}
                            </p>
                        </div>
                        <button type="button" class="rounded border border-heritage-border px-3 py-2 text-xs font-semibold text-heritage-navy" @click="editRole(role)">
                            Edit
                        </button>
                    </article>
                    <p v-if="!props.roles.length" class="p-8 text-center text-sm text-slate-500">No user types yet.</p>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
