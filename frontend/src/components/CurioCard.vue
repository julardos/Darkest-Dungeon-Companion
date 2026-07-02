<template>
  <div class="dd-panel flex flex-col gap-0 overflow-hidden hover:border-rust hover:border-opacity-60 transition-all duration-200">
    <!-- Header -->
    <div class="px-4 pt-4 pb-2">
      <h3 class="font-cinzel text-parchment text-sm leading-tight">{{ curio.name }}</h3>
      <div class="flex flex-wrap gap-1 mt-1.5">
        <span
          v-for="loc in curio.locations"
          :key="loc"
          class="text-xs font-cinzel px-1.5 py-0.5 rounded-sm border border-rust border-opacity-40 text-parchment-muted"
        >{{ capitalize(loc) }}</span>
      </div>
    </div>

    <div class="crimson-divider mx-4" />

    <!-- Safe interaction -->
    <div class="px-4 pb-3">
      <div class="flex items-start gap-2.5">
        <span class="safe-badge mt-0.5 flex-shrink-0">✓ Safe</span>
        <div class="min-w-0">
          <p v-if="curio.safe.itemName" class="font-cinzel text-green-400 text-xs mb-0.5">
            Use: {{ curio.safe.itemName }}
          </p>
          <p class="font-body text-parchment-dark text-sm leading-snug">{{ curio.safe.effect }}</p>
        </div>
      </div>
    </div>

    <!-- Risk interaction -->
    <div class="px-4 pb-4 mt-auto">
      <div class="flex items-start gap-2.5">
        <span class="warning-badge mt-0.5 flex-shrink-0">⚠ Risk</span>
        <div class="min-w-0">
          <p class="font-body text-parchment-muted text-xs leading-snug">{{ curio.risk.effects.join(' / ') }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ curio: Object })
function capitalize(s) { return s.charAt(0).toUpperCase() + s.slice(1) }
</script>
