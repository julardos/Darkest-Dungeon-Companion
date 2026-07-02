<template>
  <div class="dd-panel overflow-hidden">
    <!-- Header / toggle -->
    <button
      class="w-full flex items-center justify-between px-3 py-2.5 hover:bg-rust hover:bg-opacity-10 transition-colors"
      @click="expanded = !expanded"
    >
      <div class="flex items-center gap-2">
        <span class="text-gold opacity-70 text-sm">⚑</span>
        <span class="font-cinzel text-parchment-dark text-xs tracking-widest uppercase">
          Dungeon Filter
        </span>
      </div>
      <span class="text-parchment-muted text-xs">{{ expanded ? '▲' : '▼' }}</span>
    </button>

    <div v-if="expanded" class="px-3 pb-3 space-y-3 border-t border-rust border-opacity-20 pt-3 animate-slide-down">
      <!-- Dungeon selector -->
      <select
        v-model="selectedId"
        class="w-full bg-abyss border border-rust border-opacity-40 text-parchment text-sm px-2 py-1.5 rounded-sm font-body focus:outline-none focus:border-gold"
      >
        <option value="">— Select a Dungeon —</option>
        <option v-for="d in DUNGEONS" :key="d.id" :value="d.id">{{ d.name }}</option>
      </select>

      <!-- Dungeon detail card -->
      <template v-if="selected">
        <div class="space-y-2">
          <div class="font-cinzel text-parchment text-xs tracking-wide">
            {{ selected.enemyTypes }}
          </div>

          <p class="font-body text-parchment-dark text-xs leading-relaxed">
            {{ selected.tip }}
          </p>

          <!-- Hazards -->
          <ul class="space-y-0.5">
            <li
              v-for="h in selected.hazards"
              :key="h"
              class="font-body text-parchment-muted text-xs flex items-start gap-1"
            >
              <span class="text-rust-light mt-0.5 flex-shrink-0">•</span> {{ h }}
            </li>
          </ul>

          <!-- Optimal tags -->
          <div v-if="selected.optimalTags.length" class="space-y-1">
            <div class="font-cinzel text-xs tracking-wide text-green-400 opacity-80">Bring</div>
            <div class="flex flex-wrap gap-1">
              <span
                v-for="tag in selected.optimalTags"
                :key="tag"
                class="text-xs px-1.5 py-0.5 rounded font-cinzel"
                style="background: rgba(45,107,60,0.25); border: 1px solid rgba(45,107,60,0.8); color: #6ee7a0;"
              >
                {{ formatTag(tag) }}
              </span>
            </div>
          </div>

          <!-- Ineffective tags -->
          <div v-if="selected.ineffectiveTags.length" class="space-y-1">
            <div class="font-cinzel text-xs tracking-wide text-crimson-glow opacity-80">Avoid</div>
            <div class="flex flex-wrap gap-1">
              <span
                v-for="tag in selected.ineffectiveTags"
                :key="tag"
                class="text-xs px-1.5 py-0.5 rounded font-cinzel"
                style="background: rgba(90,2,2,0.3); border: 1px solid rgba(138,3,3,0.7); color: #c00505;"
              >
                ✗ {{ formatTag(tag) }}
              </span>
            </div>
          </div>
        </div>

        <button
          class="font-cinzel text-xs text-parchment-muted hover:text-parchment transition-colors"
          @click="selectedId = ''"
        >
          Clear filter
        </button>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { DUNGEONS } from '../data/gameData.js'

const emit = defineEmits(['filter-change'])

const expanded  = ref(false)
const selectedId = ref('')

const selected = computed(() => DUNGEONS.find(d => d.id === selectedId.value) || null)

watch(selected, d => {
  emit('filter-change', d ? { optimalTags: d.optimalTags, ineffectiveTags: d.ineffectiveTags } : null)
})

function formatTag(tag) {
  return tag.replace(/_/g, ' ')
}
</script>
