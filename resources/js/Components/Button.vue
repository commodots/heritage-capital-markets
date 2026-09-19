<script setup>
defineProps({
    href: {
        type: String,
        default: '#'
    },
    variant: {
        type: String,
        default: 'gold'
    }
})
</script>

<template>

    <a
        :href="href"
        :class="[
            'inline-flex items-center justify-center gap-2 rounded px-6 py-3 text-sm font-semibold transition',
            variant === 'gold'
                ? 'bg-heritage-gold text-heritage-deep hover:bg-heritage-gold-light'
                : 'border border-heritage-gold text-white hover:bg-white/5'
        ]"
    >
        <slot />
    </a>

</template>