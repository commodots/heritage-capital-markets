<script setup>
import { reactive, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    users: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
})

const assignedRoles = reactive(Object.fromEntries(
    props.users.map(user => [user.id, user.admin_role_id ?? ''])
))
const form = useForm({ admin_role_id: '' })
const savingUserId = ref(null)
const errorUserId = ref(null)

function assignRole(user) {
    form.admin_role_id = assignedRoles[user.id]
    savingUserId.value = user.id
    errorUserId.value = null

    form.put(`/admin/role-assignments/${user.id}`, {
        preserveScroll: true,
        onError: () => { errorUserId.value = user.id },
        onSuccess: () => { errorUserId.value = null },
        onFinish: () => { savingUserId.value = null },
    })
}
</script>

<template>
    <AdminLayout>
        <div class="border-b border-heritage-border bg-white px-6 py-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-heritage-gold">Access Management</p>
            <h1 class="mt-1 font-display text-3xl text-heritage-navy">Assign Admin Roles</h1>
            <p class="mt-1 text-sm text-heritage-muted">Assign an active user type to each administrator.</p>
        </div>

        <section class="m-6 overflow-hidden rounded-xl border border-heritage-border bg-white lg:m-8">
            <p v-if="!roles.length" class="border-b border-heritage-border px-5 py-4 text-sm text-amber-700">
                Create an active user type before assigning administrator roles.
            </p>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[42rem] text-left">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Administrator</th>
                            <th class="px-5 py-3">Email</th>
                            <th class="px-5 py-3">Assigned role</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-heritage-border text-sm">
                        <tr v-for="user in users" :key="user.id">
                            <td class="px-5 py-4 font-medium text-heritage-navy">
                                {{ user.name }}
                                <span v-if="!user.is_admin" class="ml-1 text-xs text-slate-500">(Inactive)</span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ user.email }}</td>
                            <td class="px-5 py-4">
                                <select v-model.number="assignedRoles[user.id]"
                                    class="w-full max-w-sm rounded border border-heritage-border px-3 py-2 text-sm">
                                    <option value="" disabled>Select a role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                                </select>
                                <p v-if="form.errors.admin_role_id && errorUserId === user.id"
                                    class="mt-1 text-xs text-red-600">{{ form.errors.admin_role_id }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <button type="button" :disabled="form.processing || !assignedRoles[user.id]"
                                    @click="assignRole(user)"
                                    class="rounded bg-heritage-navy px-3 py-2 text-xs font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">
                                    {{ savingUserId === user.id ? 'Saving...' : 'Save Role' }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!users.length">
                            <td colspan="4" class="px-5 py-10 text-center text-slate-500">No administrator users found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </AdminLayout>
</template>
