<script setup>
import PublicPageContentEditor from '@/Components/Admin/PublicPageContentEditor.vue'

defineProps({
    groupKey: String,
    group: Object,
    settings: Object,
})
</script>

<template>
    <PublicPageContentEditor :group-key="groupKey" :group="group" :settings="settings" />
</template>
