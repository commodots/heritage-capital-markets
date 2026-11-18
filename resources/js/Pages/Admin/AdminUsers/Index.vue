<script setup>
import { ref } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    users: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
})

const editingUser = ref(null)
const form = useForm({
    name: '',
    email: '',
    password: '',
    admin_role_id: '',
})

function editUser(user) {
    editingUser.value = user
    form.name = user.name
    form.email = user.email
    form.password = ''
    form.admin_role_id = user.admin_role_id || ''
    form.clearErrors()
}

function resetForm() {
    editingUser.value = null
    form.reset()
    form.clearErrors()
}

function submit() {
    if (editingUser.value) {
        form.put(`/admin/users/${editingUser.value.id}`, {
            preserveScroll: true,
            onSuccess: resetForm,
        })
        return
    }

    form.post('/admin/users', {
        preserveScroll: true,
        onSuccess: resetForm,
    })
}

function toggleUser(user) {
    if (user.is_super_admin) return
    router.post(`/admin/users/${user.id}/toggle`, {}, { preserveScroll: true })
}
</script>

<template>
    <AdminLayout>
        <div class="border-b border-heritage-border bg-white px-6 py-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-heritage-gold">Access Management</p>
            <h1 class="mt-1 font-display text-3xl text-heritage-navy">Administrator Users</h1>
            <p class="mt-1 text-sm text-heritage-muted">Create administrators, assign their user type, and control access.</p>
        </div>

        <div class="space-y-6 p-6 lg:p-8">
            <form class="grid gap-4 rounded-xl border border-heritage-border bg-white p-5 md:grid-cols-2 xl:grid-cols-5" @submit.prevent="submit">
                <h2 class="font-semibold text-heritage-navy md:col-span-2 xl:col-span-5">
                    {{ editingUser ? `Edit ${editingUser.name}` : 'Add Administrator' }}
                </h2>
                <label class="text-sm font-medium text-heritage-navy">
                    Full name
                    <input v-model="form.name" required maxlength="255" class="mt-1 w-full rounded border border-heritage-border px-3 py-2.5 text-sm">
                    <span v-if="form.errors.name" class="mt-1 block text-xs text-red-600">{{ form.errors.name }}</span>
                </label>
                <label class="text-sm font-medium text-heritage-navy">
                    Email
                    <input v-model="form.email" required type="email" class="mt-1 w-full rounded border border-heritage-border px-3 py-2.5 text-sm">
                    <span v-if="form.errors.email" class="mt-1 block text-xs text-red-600">{{ form.errors.email }}</span>
                </label>
                <label v-if="!editingUser" class="text-sm font-medium text-heritage-navy">
                    Temporary password
                    <input v-model="form.password" required minlength="10" type="password" autocomplete="new-password" class="mt-1 w-full rounded border border-heritage-border px-3 py-2.5 text-sm">
                    <span v-if="form.errors.password" class="mt-1 block text-xs text-red-600">{{ form.errors.password }}</span>
                </label>
                <label v-else class="text-sm font-medium text-heritage-navy">
                    Reset temporary password (optional)
                    <input v-model="form.password" minlength="10" type="password" autocomplete="new-password" class="mt-1 w-full rounded border border-heritage-border px-3 py-2.5 text-sm">
                    <span v-if="form.errors.password" class="mt-1 block text-xs text-red-600">{{ form.errors.password }}</span>
                </label>
                <label class="text-sm font-medium text-heritage-navy">
                    User type
                    <select v-model="form.admin_role_id" required class="mt-1 w-full rounded border border-heritage-border px-3 py-2.5 text-sm">
                        <option value="" disabled>Select a user type</option>
                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                    </select>
                    <span v-if="form.errors.admin_role_id" class="mt-1 block text-xs text-red-600">{{ form.errors.admin_role_id }}</span>
                </label>
                <div class="flex items-end gap-2">
                    <button :disabled="form.processing || !roles.length" class="rounded bg-heritage-navy px-4 py-3 text-sm font-semibold text-white hover:bg-heritage-deep disabled:opacity-50">
                        {{ form.processing ? 'Saving...' : editingUser ? 'Save Changes' : 'Create Administrator' }}
                    </button>
                    <button v-if="editingUser" type="button" class="rounded border border-heritage-border px-3 py-3 text-sm" @click="resetForm">Cancel</button>
                </div>
                <p v-if="!roles.length" class="text-xs text-amber-700 md:col-span-2 xl:col-span-5">Create an active user type before adding administrators.</p>
            </form>

            <section class="overflow-hidden rounded-xl border border-heritage-border bg-white">
                <div class="border-b border-heritage-border px-5 py-4">
                    <h2 class="font-semibold text-heritage-navy">Administrators</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[42rem] text-left">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Name</th>
                                <th class="px-5 py-3">Email</th>
                                <th class="px-5 py-3">User type</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Created</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-heritage-border text-sm">
                            <tr v-for="user in users" :key="user.id">
                                <td class="px-5 py-4 font-medium text-heritage-navy">
                                    {{ user.name }}
                                    <span v-if="user.is_super_admin" class="ml-1 text-[10px] text-heritage-gold">Super Admin</span>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ user.email }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ user.admin_role?.name || 'No user type' }}</td>
                                <td class="px-5 py-4">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                        :class="user.is_admin ? 'bg-green-50 text-green-700' : 'bg-slate-100 text-slate-500'">
                                        {{ user.is_admin ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button v-if="!user.is_super_admin" type="button" class="rounded border px-3 py-1.5 text-xs font-semibold" @click="editUser(user)">Edit</button>
                                        <button v-if="!user.is_super_admin" type="button"
                                            class="rounded border px-3 py-1.5 text-xs font-semibold"
                                            :class="user.is_admin ? 'border-red-200 text-red-700' : 'border-green-200 text-green-700'"
                                            @click="toggleUser(user)">
                                            {{ user.is_admin ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!users.length">
                                <td colspan="6" class="px-5 py-10 text-center text-slate-500">No administrator users found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </AdminLayout>
</template>
