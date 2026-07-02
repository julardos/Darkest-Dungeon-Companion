<template>
  <div v-if="activeSynergies.length || compositionWarnings.length" class="space-y-3 animate-fade-in">

    <!-- Active synergy cards -->
    <div
      v-for="syn in activeSynergies"
      :key="syn.id"
      class="dd-panel p-4 border transition-all duration-300"
      :style="{
        borderColor: syn.uiColor,
        boxShadow: `0 0 20px ${syn.glowColor}, inset 0 0 40px rgba(0,0,0,0.4)`,
      }"
    >
      <div class="flex items-start gap-3">
        <span class="text-xl mt-0.5 leading-none flex-shrink-0" :style="{ color: syn.uiColor }">
          {{ syn.icon }}
        </span>
        <div class="min-w-0">
          <div class="font-cinzel text-sm font-semibold mb-1" :style="{ color: syn.uiColor }">
            {{ syn.name }}
            <span class="font-body text-parchment-muted text-xs font-normal ml-2">Synergy Active</span>
          </div>
          <p class="font-body text-parchment-dark text-sm leading-relaxed">
            {{ syn.description }}
          </p>
          <div class="mt-2 flex flex-wrap gap-1.5">
            <span
              v-for="hero in syn.involvedHeroes"
              :key="hero.id"
              class="font-cinzel text-xs px-2 py-0.5 rounded-sm border border-opacity-50"
              :style="{ borderColor: syn.uiColor, color: syn.uiColor, background: `${syn.glowColor}` }"
            >
              {{ hero.name }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Composition warnings from engine (no healer / no stress healer) -->
    <div v-if="compositionWarnings.length" class="dd-panel p-3 space-y-1.5 border-crimson border-opacity-60">
      <div
        v-for="(w, i) in compositionWarnings"
        :key="i"
        class="warning-badge w-full"
      >
        ⚠ {{ w }}
      </div>
    </div>

  </div>
</template>

<script setup>
defineProps({
  activeSynergies:     { type: Array,  default: () => [] },
  compositionWarnings: { type: Array,  default: () => [] },
})
</script>
