<script setup>
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import socialImage from '@/assets/hero/slide-02-market-insights.webp'

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    canonicalPath: {
        type: String,
        required: true,
    },
    robots: {
        type: String,
        default: 'index,follow',
    },
})

const origin = typeof window === 'undefined' ? '' : window.location.origin
const canonicalUrl = computed(() => new URL(props.canonicalPath, origin || 'http://localhost').href)
const socialImageUrl = computed(() => new URL(socialImage, origin || 'http://localhost').href)
</script>

<template>
    <Head :title="title">
        <meta head-key="description" name="description" :content="description">
        <meta head-key="robots" name="robots" :content="robots">
        <link head-key="canonical" rel="canonical" :href="canonicalUrl">
        <meta head-key="og:type" property="og:type" content="website">
        <meta head-key="og:site_name" property="og:site_name" content="Heritage Capital Markets">
        <meta head-key="og:title" property="og:title" :content="title">
        <meta head-key="og:description" property="og:description" :content="description">
        <meta head-key="og:url" property="og:url" :content="canonicalUrl">
        <meta head-key="og:image" property="og:image" :content="socialImageUrl">
    </Head>
</template>
