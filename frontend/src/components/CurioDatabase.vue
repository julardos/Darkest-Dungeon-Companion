<template>
  <div class="space-y-5 animate-fade-in">
    <div class="flex items-baseline gap-3">
      <h2 class="font-gothic text-gold text-xl">Curio Cheat Sheet</h2>
      <span class="font-cinzel text-parchment-muted text-xs tracking-widest uppercase">{{ filtered.length }} curios</span>
    </div>

    <!-- Filters (sticky) -->
    <div class="dd-panel p-4 space-y-3 sticky top-[72px] z-40">
      <input
        v-model="search"
        type="text"
        placeholder="Search curios by name or effect..."
        class="w-full bg-abyss border border-rust border-opacity-40 text-parchment px-3 py-2 rounded-sm placeholder-parchment-muted focus:outline-none focus:border-gold font-body text-sm"
      />
      <div class="flex flex-wrap gap-2">
        <button
          class="dd-btn text-xs"
          :class="{ 'dd-btn-active': activeLocation === null }"
          @click="activeLocation = null"
        >All</button>
        <button
          v-for="loc in locationFilters"
          :key="loc.id"
          class="dd-btn text-xs"
          :class="{ 'dd-btn-active': activeLocation === loc.id }"
          @click="activeLocation = activeLocation === loc.id ? null : loc.id"
        >
          {{ loc.label }}
        </button>
      </div>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <CurioCard v-for="curio in filtered" :key="curio.id" :curio="curio" />
    </div>

    <div v-if="!filtered.length" class="dd-panel p-10 text-center">
      <p class="font-cinzel text-parchment-muted tracking-widest uppercase text-sm">No curios found.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import CurioCard from './CurioCard.vue'

const props = defineProps({ curios: Array })

const search         = ref('')
const activeLocation = ref(null)

const locationFilters = [
  { id: 'ruins',     label: 'Ruins' },
  { id: 'warrens',  label: 'Warrens' },
  { id: 'weald',    label: 'Weald' },
  { id: 'cove',     label: 'Cove' },
  { id: 'courtyard', label: 'Courtyard' },
]

const filtered = computed(() => {
  let list = props.curios
  if (activeLocation.value) {
    list = list.filter(c => c.locations.includes(activeLocation.value))
  }
  if (search.value.trim()) {
    const q = search.value.toLowerCase()
    list = list.filter(c =>
      c.name.toLowerCase().includes(q) ||
      c.safe.effect.toLowerCase().includes(q) ||
      c.risk.effects.join(' ').toLowerCase().includes(q)
    )
  }
  return list
})
</script>
