<template>
  <div
    class="relative"
    @dragover.prevent="isDragOver = true"
    @dragleave="isDragOver = false"
    @drop.prevent="onDrop"
  >
    <!-- Label -->
    <div class="text-center mb-1.5">
      <span class="font-cinzel text-parchment-muted text-xs tracking-widest uppercase">Rank {{ rank }}</span>
    </div>

    <!-- Empty slot -->
    <div
      v-if="!hero"
      class="rank-slot-empty cursor-pointer"
      :class="{ 'drag-over': isDragOver }"
      :style="emptySlotStyle"
      @click="handleEmptyClick"
    >
      <template v-if="isDragOver">
        <span class="text-gold">Drop</span>
      </template>
      <template v-else-if="pendingHero">
        <span class="font-cinzel text-xs tracking-widest uppercase text-gold">Tap to place</span>
      </template>
      <template v-else-if="heatmapLabel">
        <span class="font-cinzel text-xs tracking-widest uppercase" :style="{ color: heatmapLabelColor }">
          {{ heatmapLabel }}
        </span>
      </template>
      <template v-else>
        <span class="opacity-40">Empty</span>
      </template>
    </div>

    <!-- Filled slot -->
    <div
      v-else
      draggable="true"
      class="slot-filled border rounded-sm p-1.5 sm:p-2 transition-all duration-200 select-none relative"
      :class="filledSlotClass"
      :style="filledSlotStyle"
      @dragstart="onHeroDragStart"
      @dragend="emit('drag-end')"
      @click="handleFilledClick"
    >
      <!-- Hero badge + name -->
      <div class="flex flex-col items-center gap-1">
        <div
          class="hero-badge ring-2 slot-badge"
          :style="{ background: hero.color, boxShadow: `0 0 10px ${hero.color}44` }"
          :class="hasWarning ? 'ring-crimson' : 'ring-black ring-opacity-40'"
        >
          {{ initials(hero.name) }}
        </div>
        <span class="font-cinzel text-parchment text-xs text-center leading-tight hidden sm:block">{{ hero.name }}</span>
        <span class="font-body text-parchment-muted text-xs opacity-70 hidden sm:block">♥ {{ hero.stats.hp }}</span>
      </div>

      <!-- "selected for move" label — visible only on mobile when this slot is the pending source -->
      <div
        v-if="isPendingSource"
        class="absolute inset-0 flex items-end justify-center pb-1 pointer-events-none sm:hidden"
      >
        <span class="font-cinzel text-gold text-xs leading-none opacity-80">held</span>
      </div>

      <!-- "tap to swap" hint when another hero is pending -->
      <div
        v-else-if="pendingHero"
        class="absolute inset-0 flex items-end justify-center pb-1 pointer-events-none sm:hidden"
      >
        <span class="font-cinzel text-gold text-xs leading-none opacity-60">⇄</span>
      </div>

      <!-- Warning icon -->
      <div
        v-if="hasWarning"
        class="absolute top-1 right-1 text-crimson text-xs"
        :title="warningText"
      >⚠</div>

      <!-- Remove button -->
      <button
        class="absolute -top-1.5 -left-1.5 w-4 h-4 rounded-full bg-rust text-parchment text-xs leading-none opacity-0 hover:opacity-100 focus:opacity-100 flex items-center justify-center transition-opacity"
        @click.stop="emit('remove', rank)"
        title="Remove from party"
      >×</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  rank:        Number,
  hero:        Object,
  draggedHero: Object,   // hero being dragged (desktop)
  pendingHero: Object,   // hero selected via tap (mobile)
  pendingFrom: [Number, String],  // rank number or 'roster'
})
const emit = defineEmits(['drop', 'remove', 'click-hero', 'drag-start', 'drag-end', 'tap-place'])

const isDragOver = ref(false)

const hasWarning = computed(() =>
  !!props.hero?.rankWarnings?.[String(props.rank)]
)
const warningText = computed(() =>
  props.hero?.rankWarnings?.[String(props.rank)] ?? ''
)

const isPendingSource = computed(() =>
  props.pendingHero && props.pendingFrom === props.rank
)

// ── Empty slot visuals ────────────────────────────────────────────────────────

const heatmapTier = computed(() => {
  if (!props.draggedHero || props.hero) return null
  const h = props.draggedHero
  if (h.optimalRanks?.includes(props.rank)) return 'optimal'
  if (h.targetRanks?.includes(props.rank))  return 'acceptable'
  return 'danger'
})

const heatmapStyle = computed(() => {
  switch (heatmapTier.value) {
    case 'optimal':    return { borderColor: 'rgba(34,197,94,0.85)',  background: 'rgba(34,197,94,0.07)',  boxShadow: '0 0 10px rgba(34,197,94,0.25)' }
    case 'acceptable': return { borderColor: 'rgba(234,179,8,0.75)',  background: 'rgba(234,179,8,0.05)' }
    case 'danger':     return { borderColor: 'rgba(192,5,5,0.75)',    background: 'rgba(192,5,5,0.07)' }
    default:           return {}
  }
})

const heatmapLabel = computed(() => {
  switch (heatmapTier.value) {
    case 'optimal':    return 'Optimal'
    case 'acceptable': return 'Viable'
    case 'danger':     return 'Poor'
    default:           return ''
  }
})
const heatmapLabelColor = computed(() => {
  switch (heatmapTier.value) {
    case 'optimal':    return 'rgba(34,197,94,0.9)'
    case 'acceptable': return 'rgba(234,179,8,0.9)'
    case 'danger':     return 'rgba(192,5,5,0.9)'
    default:           return 'inherit'
  }
})

const emptySlotStyle = computed(() => {
  if (isDragOver.value) return {}
  if (props.pendingHero)
    return { borderColor: 'rgba(200,169,81,0.8)', background: 'rgba(200,169,81,0.06)', boxShadow: '0 0 12px rgba(200,169,81,0.2)' }
  return heatmapStyle.value
})

// ── Filled slot visuals ───────────────────────────────────────────────────────

const filledSlotClass = computed(() => {
  if (isPendingSource.value)
    return 'border-gold shadow-gold-glow cursor-pointer'
  if (props.pendingHero)
    return 'border-gold border-opacity-60 cursor-pointer hover:shadow-gold-glow bg-slate'
  return [
    props.hasWarning ? 'border-crimson hover:shadow-crimson-glow' : 'border-rust hover:border-gold',
    'hover:shadow-gold-glow bg-slate cursor-grab active:cursor-grabbing',
  ]
})

const filledSlotStyle = computed(() => {
  if (isPendingSource.value)
    return { boxShadow: '0 0 16px rgba(200,169,81,0.5)' }
  return {}
})

// ── Handlers ──────────────────────────────────────────────────────────────────

function handleEmptyClick() {
  if (props.pendingHero) emit('tap-place', props.rank)
}

function handleFilledClick() {
  if (props.pendingHero) {
    emit('tap-place', props.rank)
  } else {
    emit('click-hero', props.hero, props.rank)
  }
}

function initials(name) {
  return name.split(/[\s-]/).map(w => w[0]).join('').slice(0, 2).toUpperCase()
}

function onHeroDragStart(e) {
  if (props.pendingHero) return   // don't start drag while tap-mode is active
  e.dataTransfer.effectAllowed = 'move'
  e.dataTransfer.setData('heroId', props.hero.id)
  e.dataTransfer.setData('sourceRank', String(props.rank))
  emit('drag-start', props.hero)
}

function onDrop(e) {
  isDragOver.value = false
  const heroId     = e.dataTransfer.getData('heroId')
  const raw        = e.dataTransfer.getData('sourceRank')
  const sourceRank = raw ? parseInt(raw) : null
  emit('drop', { heroId, sourceRank })
}
</script>

<style scoped>
@media (max-width: 639px) {
  .slot-badge {
    width: 38px !important;
    height: 38px !important;
    font-size: 11px !important;
  }
  .slot-filled {
    min-height: 52px;
  }
  :global(.rank-slot-empty) {
    min-height: 60px !important;
  }
}
</style>
