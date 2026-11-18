<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const indices = computed(() => page.props.marketTicker ?? [])
const tickerItems = computed(() => [...indices.value, ...indices.value])

function formattedValue(value) {
  return Number(value ?? 0).toLocaleString('en-NG', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}
</script>

<template>
  <div class="sticky top-[4.625rem] z-40 overflow-hidden border-b border-heritage-gold/30 bg-heritage-navy text-white"
    role="region" aria-label="Market price ticker">
    <div v-if="indices.length" class="ticker-track flex w-max items-center py-2">
      <div v-for="(index, itemIndex) in tickerItems" :key="`${index.id}-${itemIndex}`"
        class="flex shrink-0 items-center gap-2 border-r border-white/20 px-5 text-xs">
        <span class="font-semibold">{{ index.symbol || index.name }}</span>
        <span class="tabular-nums text-white/90">{{ formattedValue(index.value) }}</span>
        <span :class="index.is_positive ? 'text-emerald-300' : 'text-red-300'" class="tabular-nums">
          {{ index.is_positive ? '+' : '-' }}{{ formattedValue(index.change) }}
          ({{ index.is_positive ? '+' : '-' }}{{ Math.abs(Number(index.change_percent ?? 0)).toFixed(2) }}%)
        </span>
      </div>
    </div>
    <p v-else class="px-5 py-2 text-center text-xs text-white/70">Market prices are currently unavailable.</p>
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
