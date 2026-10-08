<script setup>
import { ref, useId } from 'vue'
import { useForm } from '@inertiajs/vue3'

defineProps({
    compact: {
        type: Boolean,
        default: false,
    },
})

const instanceId = useId()
const form = useForm({
    email: '',
    website: '',
})

const feedback = ref('')

function submit() {
    feedback.value = ''

    form.post('/newsletter/subscribe', {
        preserveScroll: true,
        onSuccess: (page) => {
            form.reset()
            feedback.value = page.props.flash?.success || 'Your subscription request has been received.'
        },
    })
}
</script>

<template>
    <section
        :class="compact ? 'border-b border-white/10 bg-heritage-navy' : 'bg-heritage-offwhite'"
        :aria-labelledby="`${instanceId}-heading`"
    >
        <div
            class="mx-auto flex max-w-350 flex-col gap-6 px-5 py-8 lg:flex-row lg:items-center lg:justify-between lg:px-8"
            :class="compact ? '' : 'lg:py-12'"
        >
            <div class="max-w-xl">
                <h2
                    :id="`${instanceId}-heading`"
                    class="font-display text-2xl"
                    :class="compact ? 'text-white lg:text-3xl' : 'text-heritage-navy sm:text-3xl'"
                >
                    Stay Informed
                </h2>
                <p
                    class="mt-2 text-sm leading-6"
                    :class="compact ? 'text-white/70' : 'text-heritage-muted'"
                >
                    Get Heritage Capital Markets updates, research, market insights and investment perspectives. Subscribe to our Newsletter!
                </p>
            </div>

            <form
                class="w-full max-w-xl"
                :class="compact ? 'lg:max-w-2xl' : ''"
                @submit.prevent="submit"
            >
                <div class="flex flex-col gap-3 sm:flex-row">
                    <label class="sr-only" :for="`${instanceId}-email`">Email address</label>
                    <input
                        :id="`${instanceId}-email`"
                        v-model="form.email"
                        type="email"
                        name="email"
                        required
                        maxlength="255"
                        autocomplete="email"
                        placeholder="Email address"
                        :aria-invalid="Boolean(form.errors.email)"
                        :aria-describedby="form.errors.email || form.errors.website || feedback ? `${instanceId}-feedback` : undefined"
                        class="min-w-0 flex-1 rounded border px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-heritage-gold/40"
                        :class="compact
                            ? 'border-white/20 bg-white text-heritage-navy placeholder:text-slate-400'
                            : 'border-heritage-border bg-white text-heritage-navy placeholder:text-slate-400'"
                    >
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex shrink-0 items-center justify-center rounded bg-heritage-gold px-6 py-3 text-sm font-semibold text-heritage-deep transition hover:bg-heritage-gold-light disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        {{ form.processing ? 'Subscribing...' : 'Subscribe' }}
                    </button>
                </div>

                <div class="absolute left-[-10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                    <label :for="`${instanceId}-website`">Leave this field empty</label>
                    <input
                        :id="`${instanceId}-website`"
                        v-model="form.website"
                        type="text"
                        name="website"
                        tabindex="-1"
                        autocomplete="off"
                    >
                </div>

                <p
                    v-if="form.errors.email || form.errors.website || feedback"
                    :id="`${instanceId}-feedback`"
                    class="mt-2 text-sm"
                    :class="form.errors.email || form.errors.website
                        ? 'text-red-600'
                        : compact ? 'text-emerald-300' : 'text-green-700'"
                    aria-live="polite"
                >
                    {{ form.errors.email || form.errors.website || feedback }}
                </p>
            </form>
        </div>
    </section>
</template>
