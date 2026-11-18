<script setup>
import { computed } from 'vue'

const props = defineProps({
  items: { type: Array, default: () => [] },
})

const tickerItems = computed(() => [...props.items, ...props.items])

function formattedValue(value) {
  if (value === null || value === undefined || value === '') return '—'
  const number = Number(value)
  if (!Number.isFinite(number)) return '—'

  return number.toLocaleString('en-NG', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 4,
  })
}

function formattedMovement(value) {
  if (value === null || value === undefined || value === '') return '—'
  const number = Number(value)
  if (!Number.isFinite(number)) return '—'

  return `${number > 0 ? '+' : ''}${formattedValue(number)}`
}

function movementClass(item) {
  const hasChangeValue = item.change_value !== null && item.change_value !== undefined && item.change_value !== ''
  const movement = Number(hasChangeValue ? item.change_value : item.change_percent)
  if (!Number.isFinite(movement) || movement === 0) return 'text-white/75'

  return movement > 0 ? 'text-emerald-300' : 'text-red-300'
}

function formattedDateTime(value) {
  if (!value) return 'Time unavailable'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return 'Time unavailable'

  return new Intl.DateTimeFormat('en-NG', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(date)
}
</script>

<template>
  <div class="overflow-hidden border-t border-heritage-gold/30 bg-heritage-navy text-white"
    role="region" aria-label="Market price ticker">
    <div v-if="items.length" class="ticker-track flex w-max items-center py-2">
      <div v-for="(item, itemIndex) in tickerItems" :key="`${item.id}-${itemIndex}`"
        :aria-hidden="itemIndex >= items.length"
        class="flex shrink-0 items-center gap-2 border-r border-white/20 px-5 text-xs">
        <span class="font-semibold">{{ item.symbol || item.name }}</span>
        <span class="text-white/75">{{ item.name }}</span>
        <span class="tabular-nums text-white/90">
          <span class="sr-only">Current price </span>{{ formattedValue(item.value) }}
        </span>
        <span :class="movementClass(item)" class="tabular-nums">
          <span class="sr-only">Price movement </span>
          {{ formattedMovement(item.change_value) }}
        </span>
        <span :class="movementClass(item)" class="tabular-nums">
          <span class="sr-only">Percentage movement </span>
          {{ formattedMovement(item.change_percent) }}%
        </span>
        <time v-if="item.as_of" :datetime="item.as_of" class="text-white/70">
          Last updated {{ formattedDateTime(item.as_of) }}
        </time>
        <span v-else class="text-white/70">Last updated time unavailable</span>
      </div>
    </div>
    <p v-else class="px-5 py-2 text-center text-xs text-white/70">Market data is currently unavailable.</p>
  </div>
</template>

<style scoped>
.ticker-track {
  animation: ticker-scroll 40s linear infinite;
}

.ticker-track:hover {
  animation-play-state: paused;
}

@keyframes ticker-scroll {
  to {
    transform: translateX(-50%);
  }
}

@media (prefers-reduced-motion: reduce) {
  .ticker-track {
    animation: none;
  }
}
</style>
