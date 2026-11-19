<script setup>
import { computed, reactive, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    groupKey: { type: String, required: true },
    group: { type: Object, required: true },
    settings: { type: Object, default: () => ({}) },
})

const page = usePage()
const groups = computed(() => ({ [props.groupKey]: props.group }))
const imageKeys = Object.entries(props.group.fields)
    .filter(([, field]) => field[3] === 'image')
    .map(([key]) => key)
const imageKeySet = new Set(imageKeys)

// Initialize form with all settings
const form = reactive(Object.fromEntries(
    Object.entries(props.settings).filter(([key]) => !imageKeySet.has(key))
))

// Track processing states and files per member or section
const processingMembers = ref({})
const processingIntro = ref(false)
const imageFiles = ref({})
const imagePreviews = ref({})

const memberCategories = [
    { prefix: 'board_member', title: 'Board Members' },
    { prefix: 'management_member', title: 'Management Team' },
]
function initialMemberCount(prefix) {
    const populatedNumbers = Object.keys(props.settings)
        .filter(key => key.startsWith(`${prefix}_`))
        .map(key => parseInt(key.split('_')[2]) || 0)
        .filter(number => ['name', 'role', 'bio', 'image'].some(part =>
            Boolean(props.settings[`${prefix}_${number}_${part}`])
        ))

    return Math.max(1, ...populatedNumbers)
}

const activeMemberCounts = reactive(Object.fromEntries(memberCategories.map(({ prefix }) => [
    prefix,
    initialMemberCount(prefix),
])))

const boardFields = computed(() => Object.entries(props.group.fields ?? {}))
const boardIntroFields = computed(() => boardFields.value.filter(([key]) =>
    !key.startsWith('board_member_') &&
    !key.startsWith('management_member_') &&
    !key.startsWith('board_careers_')
))
const boardCareerFields = computed(() => boardFields.value.filter(([key]) =>
    key.startsWith('board_careers_')
))
const headlineGroups = computed(() => {
    const groups = new Map()

    for (const [key] of boardFields.value) {
        const lineMatch = key.match(/^(.*_title)_line_(\d+)$/)
        const accentMatch = key.match(/^(.*_title)_accent$/)
        const prefix = lineMatch?.[1] ?? accentMatch?.[1]
        if (!prefix) continue

        if (!groups.has(prefix)) groups.set(prefix, { prefix, parts: [] })
        groups.get(prefix).parts.push({
            key,
            order: lineMatch ? Number(lineMatch[2]) : Number.MAX_SAFE_INTEGER,
        })
    }

    return Array.from(groups.values())
        .filter(group => group.parts.length > 1)
        .map(group => ({
            ...group,
            key: `__headline_${group.prefix}`,
            label: fieldLabel(group.prefix),
            parts: group.parts.sort((a, b) => a.order - b.order),
        }))
})
const mergedHeadlines = reactive(Object.fromEntries(headlineGroups.value.map(group => [
    group.key,
    group.parts.map(({ key }) => form[key] ?? '').join('\n'),
])))
const generalSections = computed(() => {
    const sections = new Map()
    const groupsByPart = new Map()
    const firstPartByKey = new Map()

    for (const group of headlineGroups.value) {
        for (const part of group.parts) groupsByPart.set(part.key, group)
        firstPartByKey.set(group.parts[0].key, group)
    }

    for (const [key, field] of boardFields.value) {
        const title = field[4] || 'Page Content'
        if (!sections.has(title)) sections.set(title, [])
        const group = groupsByPart.get(key)
        if (!group) {
            sections.get(title).push([key, field])
        } else if (firstPartByKey.has(key)) {
            sections.get(title).push([
                group.key,
                [group.label, 4000, true, 'text', title, group],
            ])
        }
    }

    return Array.from(sections, ([title, fields]) => ({ title, fields }))
})

const memberGroups = computed(() => memberCategories.map(category => ({
    ...category,
    members: Array.from({ length: activeMemberCounts[category.prefix] }, (_, index) => {
        const number = index + 1
        return {
            number,
            fields: ['name', 'role', 'bio', 'image'].map(part => {
                const key = `${category.prefix}_${number}_${part}`
                const fieldDef = props.group.fields?.[key] ?? [
                    part, true, part === 'bio', part === 'image' ? 'image' : 'text'
                ]
                return [key, fieldDef]
            }),
        }
    }),
})))

function addMember(prefix) {
    if (activeMemberCounts[prefix] < 12) activeMemberCounts[prefix]++
}

function removeMember(prefix, number) {
    ['name', 'role', 'bio'].forEach(part => {
        const key = `${prefix}_${number}_${part}`
        if (form[key] !== undefined) form[key] = ''
    })
    const imageKey = `${prefix}_${number}_image`
    delete imageFiles.value[imageKey]
    if (imagePreviews.value[imageKey]?.startsWith('blob:')) {
        URL.revokeObjectURL(imagePreviews.value[imageKey])
    }
    delete imagePreviews.value[imageKey]
}

function fieldLabel(key) {
    return key
        .replace(/^(?:board_member|management_member)_(\d+)_/, 'Member $1 ')
        .replace(new RegExp(`^${props.groupKey}_`), '')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, letter => letter.toUpperCase())
}

function imageUrl(path) {
    if (!path) return ''
    const cleanPath = path.replace(/\.(jpg|jpeg|png|webp)\.(jpg|jpeg|png|webp)$/i, '.$1')
    return cleanPath.startsWith('/') ? cleanPath : `/storage/${cleanPath}`
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

function saveMember(prefix, number) {
    const processingKey = `${prefix}_${number}`
    processingMembers.value[processingKey] = true

    const payload = {}
    
    for (const part of ['name', 'role', 'bio']) {
        const key = `${prefix}_${number}_${part}`
        payload[key] = form[key] ?? ''
    }

    const imageKey = `${prefix}_${number}_image`
    if (imageFiles.value[imageKey]) {
        payload[imageKey] = imageFiles.value[imageKey]
    }

    router.post(`/admin/page-content/${props.groupKey}`, payload, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            delete imageFiles.value[imageKey]
            if (imagePreviews.value[imageKey]?.startsWith('blob:')) {
                URL.revokeObjectURL(imagePreviews.value[imageKey])
            }
            delete imagePreviews.value[imageKey]
        },
        onFinish: () => {
            processingMembers.value[processingKey] = false
        },
    })
}

// General submit for intro/careers/other groups
function submitGeneral() {
    processingIntro.value = true
    const payload = Object.fromEntries(Object.entries(form))
    Object.assign(payload, imageFiles.value)

    for (const group of headlineGroups.value) {
        const lines = (mergedHeadlines[group.key] ?? '').split(/\r?\n/)
        group.parts.forEach(({ key }, index) => {
            payload[key] = lines[index] ?? ''
        })
    }

    router.post(`/admin/page-content/${props.groupKey}`, payload, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            for (const [key, file] of Object.entries(imageFiles.value)) {
                if (!file) continue
                delete imageFiles.value[key]
                if (imagePreviews.value[key]?.startsWith('blob:')) {
                    URL.revokeObjectURL(imagePreviews.value[key])
                }
                delete imagePreviews.value[key]
            }
        },
        onFinish: () => { processingIntro.value = false },
    })
}

function fieldValue(key, field) {
    return field[5] ? mergedHeadlines[key] : form[key]
}

function setFieldValue(key, field, value) {
    if (field[5]) {
        mergedHeadlines[key] = value
    } else {
        form[key] = value
    }
}

function fieldError(key, field) {
    const keys = field[5]?.parts.map(part => part.key) ?? [key]
    return keys.map(errorKey => page.props.errors[errorKey]).filter(Boolean).join(' ')
        || page.props.errors[key]
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
                <button type="button" :disabled="processingIntro" @click="submitGeneral"
                    class="rounded bg-heritage-navy px-5 py-3 text-sm font-semibold text-white transition hover:bg-heritage-deep disabled:cursor-not-allowed disabled:opacity-60">
                    {{ processingIntro ? 'Saving...' : groupKey === 'board' ? 'Save Intro & General Sections' : 'Save Page Content' }}
                </button>
            </div>
        </div>

        <form @submit.prevent="submitGeneral" class="space-y-6 p-6 lg:p-8">
            <section v-for="(grp, groupKey) in groups" :key="groupKey"
                class="overflow-hidden rounded-xl border border-heritage-border bg-white">
                <div class="border-b border-heritage-border px-6 py-5">
                    <h2 class="font-display text-xl font-semibold text-heritage-navy">Page Content</h2>
                    <p v-if="grp.description || groupKey === 'resources'" class="mt-1 text-sm text-heritage-muted">
                        {{ grp.description || 'Market index values are edited separately under Market Indices.' }}
                    </p>
                </div>

                <div v-if="groupKey === 'board'" class="space-y-8 p-6">
                    <!-- Page Intro Section -->
                    <div class="grid gap-5 md:grid-cols-2">
                        <div v-for="[key, field] in boardIntroFields" :key="key"
                            :class="field[2] ? 'md:col-span-2' : ''">
                            <label :for="key" class="mb-2 block text-sm font-medium text-heritage-navy">{{ fieldLabel(key) }}</label>
                            <textarea v-if="field[2]" :id="key" v-model="form[key]" rows="3"
                                class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"></textarea>
                            <input v-else :id="key" v-model="form[key]" type="text"
                                class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold" />
                            <p v-if="page.props.errors[key]" class="text-xs font-medium text-red-600 mt-1">
                                {{ page.props.errors[key] }}
                            </p>
                        </div>
                    </div>

                    <section v-for="memberGroup in memberGroups" :key="memberGroup.prefix"
                        class="border-t border-heritage-border pt-6">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-display text-lg font-semibold text-heritage-navy">{{ memberGroup.title }}</h3>
                                <p class="mt-1 text-sm text-heritage-muted">Add each person’s name, role, biography, and photo. Save each profile individually.</p>
                            </div>
                            <button v-if="activeMemberCounts[memberGroup.prefix] < 12" type="button"
                                @click="addMember(memberGroup.prefix)"
                                class="inline-flex items-center gap-1.5 rounded border border-heritage-border bg-white px-4 py-2 text-xs font-semibold text-heritage-navy transition hover:bg-heritage-offwhite">
                                + Add {{ memberGroup.prefix === 'board_member' ? 'Board Member' : 'Management Team Member' }}
                            </button>
                        </div>

                        <div class="mt-5 grid gap-5 xl:grid-cols-2">
                            <article v-for="member in memberGroup.members" :key="member.number"
                                class="grid gap-5 rounded-lg border border-heritage-border bg-heritage-offwhite/40 p-5 sm:grid-cols-2">
                                <div class="sm:col-span-2 flex items-center justify-between border-b border-heritage-border/60 pb-3">
                                    <h4 class="text-sm font-semibold text-heritage-navy">{{ memberGroup.title }} {{ member.number }}</h4>
                                    <div class="flex items-center gap-3">
                                        <button type="button" @click="removeMember(memberGroup.prefix, member.number)"
                                            class="text-xs text-red-600 hover:underline">
                                            Clear Fields
                                        </button>
                                        <button type="button"
                                            :disabled="processingMembers[`${memberGroup.prefix}_${member.number}`]"
                                            @click="saveMember(memberGroup.prefix, member.number)"
                                            class="rounded bg-heritage-navy px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-heritage-deep disabled:opacity-50">
                                            {{ processingMembers[`${memberGroup.prefix}_${member.number}`] ? 'Saving...' : `Save ${memberGroup.title} ${member.number}` }}
                                        </button>
                                    </div>
                                </div>

                                <div v-for="[key, field] in member.fields" :key="key"
                                    :class="field[2] || field[3] === 'image' ? 'sm:col-span-2' : ''">
                                    <label :for="key" class="mb-2 block text-sm font-medium text-heritage-navy">{{ fieldLabel(key) }}</label>

                                    <div v-if="field[3] === 'image'" class="space-y-3">
                                        <img v-if="imagePreviews[key] || props.settings[key]"
                                            :src="imagePreviews[key] || imageUrl(props.settings[key])"
                                            :alt="`${fieldLabel(key)} preview`"
                                            class="h-44 w-36 rounded border border-heritage-border object-cover object-top" />
                                        <p v-else class="text-sm text-heritage-muted">No photo uploaded.</p>
                                        <input :id="key" type="file"
                                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                            @change="selectImage(key, $event)"
                                            class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition file:mr-4 file:rounded file:border-0 file:bg-white file:px-3 file:py-2 file:font-semibold file:text-heritage-navy focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold" />
                                        <p class="text-xs text-heritage-muted">JPG, JPEG, PNG or WebP; maximum 10 MB.</p>
                                        <p v-if="page.props.errors[key]" class="text-xs font-medium text-red-600">
                                            {{ page.props.errors[key] }}
                                        </p>
                                    </div>

                                    <textarea v-else-if="field[2]" :id="key" v-model="form[key]" rows="4"
                                        :placeholder="`Enter ${memberGroup.title.toLowerCase()} biography...`"
                                        class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"></textarea>

                                    <input v-else :id="key" v-model="form[key]" type="text"
                                        class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold" />

                                    <p v-if="field[3] !== 'image' && page.props.errors[key]" class="text-xs font-medium text-red-600 mt-1">
                                        {{ page.props.errors[key] }}
                                    </p>
                                </div>
                            </article>
                        </div>
                    </section>

                    <!-- Careers Section Fields -->
                    <div class="grid gap-5 border-t border-heritage-border pt-6 md:grid-cols-2">
                        <div v-for="[key, field] in boardCareerFields" :key="key"
                            :class="field[2] ? 'md:col-span-2' : ''">
                            <label :for="key" class="mb-2 block text-sm font-medium text-heritage-navy">{{ fieldLabel(key) }}</label>
                            <textarea v-if="field[2]" :id="key" v-model="form[key]" rows="3"
                                class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"></textarea>
                            <input v-else :id="key" v-model="form[key]" type="text"
                                class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold" />
                            <p v-if="page.props.errors[key]" class="text-xs font-medium text-red-600 mt-1">
                                {{ page.props.errors[key] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fallback General Groups -->
                <div v-else class="space-y-8 p-6">
                    <section v-for="section in generalSections" :key="section.title">
                        <h3 v-if="section.title !== 'Page Content'"
                            class="mb-4 border-b border-heritage-border pb-2 font-display text-lg font-semibold text-heritage-navy">
                            {{ section.title }}
                        </h3>
                        <div class="grid gap-5 md:grid-cols-2">
                            <div v-for="[key, field] in section.fields" :key="key"
                                :class="field[2] || field[3] === 'image' ? 'md:col-span-2' : ''">
                                <label :for="key" class="mb-2 block text-sm font-medium text-heritage-navy">
                                    {{ field[5]?.label || fieldLabel(key) }}
                                </label>
                                <div v-if="field[3] === 'image'" class="space-y-3">
                                    <img v-if="imagePreviews[key] || props.settings[key]"
                                        :src="imagePreviews[key] || imageUrl(props.settings[key])"
                                        :alt="`${fieldLabel(key)} preview`"
                                        class="max-h-56 max-w-full rounded border border-heritage-border object-cover" />
                                    <p v-else class="text-sm text-heritage-muted">No image uploaded. The public page will use its current default image.</p>
                                    <input :id="key" type="file"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        @change="selectImage(key, $event)"
                                        class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition file:mr-4 file:rounded file:border-0 file:bg-white file:px-3 file:py-2 file:font-semibold file:text-heritage-navy focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold" />
                                    <p class="text-xs text-heritage-muted">JPG, JPEG, PNG or WebP; maximum 10 MB.</p>
                                </div>
                                <textarea v-else-if="field[2]" :id="key"
                                    :value="fieldValue(key, field)"
                                    :rows="field[5] ? field[5].parts.length : 3"
                                    @input="setFieldValue(key, field, $event.target.value)"
                                    class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold"></textarea>
                                <input v-else :id="key" v-model="form[key]"
                                    :type="field[3] === 'url' ? 'url' : 'text'"
                                    class="w-full rounded border border-heritage-border px-4 py-3 text-sm outline-none transition focus:border-heritage-gold focus:ring-1 focus:ring-heritage-gold" />
                                <p v-if="fieldError(key, field)" class="mt-1 text-xs font-medium text-red-600">
                                    {{ fieldError(key, field) }}
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </form>
    </AdminLayout>
</template>