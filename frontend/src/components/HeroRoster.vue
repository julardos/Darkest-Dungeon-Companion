<template>
  <div
    class="dd-panel h-full flex flex-col transition-colors duration-150"
    :class="{ 'border-rust': !isRosterDragOver, 'border-gold': isRosterDragOver }"
    :style="isRosterDragOver ? { boxShadow: '0 0 12px rgba(200,169,81,0.3)' } : {}"
    @dragover.prevent="isRosterDragOver = true"
    @dragleave="isRosterDragOver = false"
    @drop.prevent="onDropOnRoster"
  >
    <div class="p-3 border-b border-rust border-opacity-30">
      <h3 class="font-cinzel text-parchment-dark text-sm tracking-widest uppercase mb-2">Hero Roster</h3>
      <input
        v-model="search"
        type="text"
        placeholder="Search heroes..."
        class="w-full bg-abyss border border-rust border-opacity-40 text-parchment text-sm px-3 py-1.5 rounded-sm placeholder-parchment-muted focus:outline-none focus:border-gold font-body"
      />
    </div>

    <!-- Tap-mode instruction banner -->
    <div
      v-if="pendingHero"
      class="px-3 py-2 border-b border-gold border-opacity-40 flex items-center gap-2"
      style="background: rgba(200,169,81,0.08)"
    >
      <span class="font-cinzel text-gold text-xs flex-1 leading-tight">
        <span class="opacity-60">Placing:</span> {{ pendingHero.name }}
      </span>
      <button
        class="font-cinzel text-parchment-muted text-xs hover:text-parchment transition-colors"
        @click="emit('cancel-pending')"
      >✕ Cancel</button>
    </div>

    <ul class="flex-1 overflow-y-auto divide-y divide-rust divide-opacity-20 max-h-[45vh] sm:max-h-[60vh] xl:max-h-full">
      <li
        v-for="hero in filtered"
        :key="hero.id"
        :draggable="!pendingHero"
        @dragstart="onDragStart(hero, $event)"
        @dragend="emit('drag-end')"
        @click="emit('tap-hero', hero)"
        class="flex items-center gap-3 px-3 py-2.5 cursor-pointer select-none group transition-all duration-150"
        :class="[isPlaced(hero) && !isPendingHero(hero) ? 'opacity-40' : 'hover:bg-rust hover:bg-opacity-10']"
        :style="listStyle(hero)"
        :title="isPlaced(hero) ? `${hero.name} — tap to pick up and reposition` : `Tap to select, then tap a rank slot`"
      >
        <div
          class="hero-badge ring-1 ring-black ring-opacity-30 flex-shrink-0 transition-all duration-200"
          :style="badgeStyle(hero)"
        >
          {{ initials(hero.name) }}
        </div>
        <div class="min-w-0">
          <div class="font-cinzel text-parchment text-sm leading-tight truncate group-hover:text-gold transition-colors"
            :class="{ 'text-gold': isPendingHero(hero) }">
            {{ hero.name }}
          </div>
          <div class="font-body text-parchment-muted text-xs truncate">
            {{ isPlaced(hero) && !isPendingHero(hero) ? 'In party — tap to move' : `Ranks ${hero.optimalRanks.join(', ')}` }}
          </div>
        </div>
        <div class="ml-auto text-parchment-muted text-xs opacity-0 group-hover:opacity-100 transition-opacity">⠿</div>
      </li>

      <li v-if="!filtered.length" class="px-4 py-6 text-center text-parchment-muted font-cinzel text-xs">
        No heroes found.
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { heroDungeonFit } from '../composables/useSynergyEngine.js'

const props  = defineProps({
  heroes:        Array,
  team:          Object,
  dungeonFilter: Object,
  pendingHero:   Object,
  pendingFrom:   [Number, String],
})
const emit          = defineEmits(['drag-start', 'drag-end', 'remove-from-team', 'tap-hero', 'cancel-pending'])
const search        = ref('')
const isRosterDragOver = ref(false)

const filtered = computed(() =>
  props.heroes.filter(h =>
    h.name.toLowerCase().includes(search.value.toLowerCase())
  )
)

function isPlaced(hero) {
  return Object.values(props.team).some(h => h?.id === hero.id)
}

function isPendingHero(hero) {
  return props.pendingHero?.id === hero.id
}

function initials(name) {
  return name.split(/[\s-]/).map(w => w[0]).join('').slice(0, 2).toUpperCase()
}

function heroFit(hero) {
  return heroDungeonFit(hero.id, props.dungeonFilter)
}

function listStyle(hero) {
  if (isPendingHero(hero)) return {}
  if (!props.dungeonFilter || isPlaced(hero)) return {}
  const fit = heroFit(hero)
  if (fit === 'ineffective') return { filter: 'grayscale(70%) opacity(0.45)' }
  return {}
}

function badgeStyle(hero) {
  const base = { background: hero.color }
  if (isPendingHero(hero))
    return { ...base, boxShadow: '0 0 0 2px #c8a951, 0 0 14px rgba(200,169,81,0.6)' }
  if (!props.dungeonFilter || isPlaced(hero)) return base
  const fit = heroFit(hero)
  if (fit === 'optimal')
    return { ...base, boxShadow: '0 0 0 2px #c8a951, 0 0 10px rgba(200,169,81,0.5)' }
  return base
}

function onDragStart(hero, e) {
  e.dataTransfer.effectAllowed = 'move'
  e.dataTransfer.setData('heroId', hero.id)
  emit('drag-start', hero)
}

function onDropOnRoster(e) {
  isRosterDragOver.value = false
  const heroId = e.dataTransfer.getData('heroId')
  if (heroId) emit('remove-from-team', heroId)
}
</script>
