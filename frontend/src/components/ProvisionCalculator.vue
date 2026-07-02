<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex items-baseline gap-3">
      <h2 class="font-gothic text-gold text-xl">Provision Calculator</h2>
    </div>

    <div class="dd-panel p-5 space-y-5">
      <!-- Controls -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
          <label class="font-cinzel text-parchment-dark text-xs tracking-widest uppercase block mb-2">Location</label>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="loc in locations"
              :key="loc.id"
              class="dd-btn text-xs"
              :class="{ 'dd-btn-active': location === loc.id }"
              @click="location = loc.id"
            >{{ loc.label }}</button>
          </div>
        </div>

        <div>
          <label class="font-cinzel text-parchment-dark text-xs tracking-widest uppercase block mb-2">Dungeon Length</label>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="len in lengths"
              :key="len.id"
              class="dd-btn text-xs"
              :class="{ 'dd-btn-active': length === len.id }"
              @click="length = len.id"
            >{{ len.label }}</button>
          </div>
        </div>
      </div>

      <div class="crimson-divider" />

      <!-- Loading -->
      <div v-if="loading" class="text-center py-8">
        <div class="text-crimson animate-pulse text-3xl">☽</div>
      </div>

      <div v-else-if="data" class="space-y-5">

        <!-- Overview note -->
        <p class="font-body text-parchment-dark text-sm italic opacity-80 leading-relaxed">
          {{ data.note }}
        </p>

        <!-- Provision grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
          <div
            v-for="item in data.provisions"
            :key="item.id"
            class="relative flex flex-col items-center gap-2 p-3 rounded-sm border transition-all duration-200"
            :class="[
              item.quantity === 0
                ? 'border-rust border-opacity-20 opacity-40'
                : item.highlight
                  ? 'border-gold bg-gold bg-opacity-5 shadow-gold-glow'
                  : 'border-rust border-opacity-30 hover:border-rust',
            ]"
          >
            <div
              v-if="item.highlight"
              class="absolute -top-2 left-1/2 -translate-x-1/2 text-xs font-cinzel bg-gold text-abyss px-1.5 py-0.5 rounded-sm tracking-wide whitespace-nowrap"
            >KEY ITEM</div>

            <span class="text-3xl leading-none" role="img" :aria-label="item.name">{{ item.icon }}</span>
            <span class="font-cinzel text-parchment text-xs tracking-wide text-center">{{ item.name }}</span>
            <span
              class="font-gothic text-2xl leading-none"
              :class="item.quantity === 0 ? 'text-parchment-muted' : item.highlight ? 'text-gold' : 'text-parchment'"
            >{{ item.quantity }}</span>

            <p v-if="item.note" class="font-body text-xs text-parchment-muted text-center leading-tight opacity-80">
              {{ item.note }}
            </p>
          </div>
        </div>

        <p class="font-cinzel text-parchment-muted text-xs tracking-widest uppercase opacity-50 text-right">
          {{ capitalize(location) }} · {{ capitalize(length) }} dungeon
        </p>

        <!-- Community tips -->
        <div v-if="data.tips?.length" class="space-y-2 pt-1">
          <div class="crimson-divider" />
          <h4 class="font-cinzel text-parchment-dark text-xs tracking-widest uppercase pb-1">
            Expedition Notes
          </h4>
          <ul class="space-y-2">
            <li
              v-for="(tip, i) in data.tips"
              :key="i"
              class="font-body text-sm leading-snug flex gap-2"
              :class="tip.startsWith('⚠') ? 'text-crimson-glow' : 'text-parchment-dark'"
            >
              <span class="flex-shrink-0 mt-0.5 opacity-60">
                {{ tip.startsWith('⚠') ? '' : '◆' }}
              </span>
              <span>{{ tip.startsWith('⚠') ? tip : tip.replace(/^[^\s]+\s/, '') }}</span>
            </li>
          </ul>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { fetchProvisions } from '../api/index.js'

const locations = [
  { id: 'ruins',      label: 'Ruins' },
  { id: 'warrens',   label: 'Warrens' },
  { id: 'weald',     label: 'Weald' },
  { id: 'cove',      label: 'Cove' },
  { id: 'courtyard', label: 'Courtyard' },
]

const lengths = [
  { id: 'short',  label: 'Short' },
  { id: 'medium', label: 'Medium' },
  { id: 'long',   label: 'Long' },
]

const location = ref('ruins')
const length   = ref('medium')
const data     = ref(null)
const loading  = ref(false)

async function load() {
  loading.value = true
  try {
    data.value = await fetchProvisions(location.value, length.value)
  } finally {
    loading.value = false
  }
}

function capitalize(s) { return s.charAt(0).toUpperCase() + s.slice(1) }

watch([location, length], load)
onMounted(load)
</script>
