<script setup>
import { computed, reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    groupKey: {
        type: String,
        required: true,
    },
    group: {
        type: Object,
        required: true,
    },
    settings: {
        type: Object,
        default: () => ({}),
    },
})

const groups = computed(() => ({ [props.groupKey]: props.group }))
const imageKeys = Object.entries(props.group.fields)
    .filter(([, field]) => field[3] === 'image')
    .map(([key]) => key)
const imageKeySet = new Set(imageKeys)
const form = reactive(Object.fromEntries(
    Object.entries(props.settings).filter(([key]) => !imageKeySet.has(key))
))
const imageFiles = ref({})
const imagePreviews = ref({})
const processing = ref(false)

const boardFields = computed(() => Object.entries(props.group.fields ?? {}))
const boardIntroFields = computed(() => boardFields.value.filter(([key]) =>
    !key.startsWith('board_member_') && !key.startsWith('board_careers_')
))
const boardCareerFields = computed(() => boardFields.value.filter(([key]) =>
    key.startsWith('board_careers_')
))
const boardMemberFields = computed(() => Array.from({ length: 6 }, (_, index) => {
    const number = index + 1

    return {
        number,
        fields: ['name', 'role', 'bio', 'image']
            .map(part => {
                const key = `board_member_${number}_${part}`
                return [key, props.group.fields?.[key]]
            })
            .filter(([, field]) => field),
    }
}))

function fieldLabel(key) {
    return key
        .replace(/^board_member_(\d+)_/, 'Member $1 ')
        .replace(/^[a-z]+_/, '')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, letter => letter.toUpperCase())
}

function imageUrl(path) {
    if (!path) return ''
    return path.startsWith('/') ? path : `/storage/${path}`
}

function selectImage(key, event) {
    const file = event.target.files?.[0] ?? null
    if (!file) return

    if (imagePreviews.value[key]?.startsWith('blob:')) {
        URL.revokeObjectURL(imagePreviews.value[key])
    }

    imageFiles.value[key] = file
    imagePreviews.value[key] = URL.createObjectURL(file)
}

function submit() {
    processing.value = true

    router.post(`/admin/page-content/${props.groupKey}`, {
        ...form,
        ...imageFiles.value,
    }, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            Object.assign(
                form,
                Object.fromEntries(
                    Object.entries(props.settings).filter(([key]) => !imageKeySet.has(key))
                )
            )
            Object.values(imagePreviews.value).forEach(preview => {
                if (preview.startsWith('blob:')) URL.revokeObjectURL(preview)
            })
            imageFiles.value = {}
            imagePreviews.value = {}
        },
        onFinish: () => {
            processing.value = false
        },
    })
}
</script>

<template>
    <AdminLayout>
        <div class="border-b border-heritage-border bg-white px-6 py-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-heritage-gold">Content Management</p>
                    <h1 class="mt-1 font-display text-3xl text-heritage-navy">{{ group.title }}</h1>
                    <p class="mt-1 text-sm text-heritage-muted">Edit the content shown on the {{ group.title }} public page.</p>
                </div>
                <button
                    type="button"
                    :disabled="processing"
                    @click="submit"
                    class="rounded bg-heritage-navy px-5 py-3 text-sm font-semibold text-white transition hover:bg-heritage-deep disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6 p-6 lg:p-8">
            <section
                v-for="(group, groupKey) in groups"
                :key="groupKey"
                class="overflow-hidden rounded-xl border border-heritage-border bg-white"
            >
                <div class="border-b border-heritage-border px-6 py-5">
                    <h2 class="font-display text-xl font-semibold text-heritage-navy">Page Content</h2>
                    <p v-if="group.description || groupKey === 'resources'" class="mt-1 text-sm text-heritage-muted">
                        {{ group.description || 'Market index values are edited separately under Market Indices.' }}
                    </p>
                </div>
                <div v-if="groupKey === 'board'" class="space-y-8 p-6">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div
                            v-for="[key, field] in boardIntroFields"
                            :key="key"
                            :class="field[2] ? 'md:col-span-2' : ''"
                        >
                            <label :for="key" class="mb-2 block text-sm font-medium text-heritage-navy">{{ fieldLabel(key) }}</label>
                            <textarea
                                v-if="field[2]"
                                :id="key"
                                v-model="form[key]"
                                rows="3"
                                class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                            ></textarea>
                            <input
                                v-else
                                :id="key"
                                v-model="form[key]"
                                type="text"
                                class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                            />
                        </div>
                    </div>

                    <section class="border-t border-heritage-border pt-6">
                        <div>
                            <h3 class="font-display text-lg font-semibold text-heritage-navy">Board &amp; Management Members</h3>
                            <p class="mt-1 text-sm text-heritage-muted">Add each member’s name, role, biography, and photo. Leave the name blank to hide that profile.</p>
                        </div>
                        <div class="mt-5 grid gap-5 xl:grid-cols-2">
                            <article
                                v-for="member in boardMemberFields"
                                :key="member.number"
                                class="grid gap-5 rounded-lg border border-heritage-border bg-heritage-offwhite/40 p-5 sm:grid-cols-2"
                            >
                                <h4 class="sm:col-span-2 text-sm font-semibold text-heritage-navy">Member {{ member.number }}</h4>
                                <div
                                    v-for="[key, field] in member.fields"
                                    :key="key"
                                    :class="field[2] || field[3] === 'image' ? 'sm:col-span-2' : ''"
                                >
                                    <label :for="key" class="mb-2 block text-sm font-medium text-heritage-navy">{{ fieldLabel(key) }}</label>
                                    <div v-if="field[3] === 'image'" class="space-y-3">
                                        <img
                                            v-if="imagePreviews[key] || props.settings[key]"
                                            :src="imagePreviews[key] || imageUrl(props.settings[key])"
                                            :alt="`${fieldLabel(key)} preview`"
                                            class="h-36 w-32 rounded border border-heritage-border object-cover"
                                        />
                                        <p v-else class="text-sm text-heritage-muted">No photo uploaded.</p>
                                        <input
                                            :id="key"
                                            type="file"
                                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                            @change="selectImage(key, $event)"
                                            class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition file:mr-4 file:rounded file:border-0 file:bg-white file:px-3 file:py-2 file:font-semibold file:text-heritage-navy focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                        />
                                        <p class="text-xs text-heritage-muted">JPG, JPEG, PNG or WebP; maximum 5 MB.</p>
                                    </div>
                                    <textarea
                                        v-else-if="field[2]"
                                        :id="key"
                                        v-model="form[key]"
                                        rows="3"
                                        class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                    ></textarea>
                                    <input
                                        v-else
                                        :id="key"
                                        v-model="form[key]"
                                        type="text"
                                        class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                                    />
                                </div>
                            </article>
                        </div>
                    </section>

                    <div class="grid gap-5 border-t border-heritage-border pt-6 md:grid-cols-2">
                        <div
                            v-for="[key, field] in boardCareerFields"
                            :key="key"
                            :class="field[2] ? 'md:col-span-2' : ''"
                        >
                            <label :for="key" class="mb-2 block text-sm font-medium text-heritage-navy">{{ fieldLabel(key) }}</label>
                            <textarea
                                v-if="field[2]"
                                :id="key"
                                v-model="form[key]"
                                rows="3"
                                class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                            ></textarea>
                            <input
                                v-else
                                :id="key"
                                v-model="form[key]"
                                type="text"
                                class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                            />
                        </div>
                    </div>
                </div>
                <div v-else class="grid gap-5 p-6 md:grid-cols-2">
                    <div v-for="(field, key) in group.fields" :key="key" :class="field[2] ? 'md:col-span-2' : ''">
                        <label :for="key" class="mb-2 block text-sm font-medium text-heritage-navy">
                            {{ fieldLabel(key) }}
                        </label>
                        <textarea
                            v-if="field[2]"
                            :id="key"
                            v-model="form[key]"
                            rows="3"
                            class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                        ></textarea>
                        <input
                            v-else
                            :id="key"
                            v-model="form[key]"
                            type="text"
                            class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"
                        />
                    </div>
                </div>
            </section>
            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="processing"
                    class="rounded bg-heritage-gold px-6 py-3 text-sm font-semibold text-heritage-deep transition hover:bg-heritage-gold-light disabled:cursor-not-allowed disabled:opacity-60"
                >
                    {{ processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
