<template>
  <div class="dd-panel p-5 animate-slide-down space-y-5">
    <!-- Header -->
    <div class="flex items-start gap-4">
      <div
        class="hero-badge ring-2 ring-black ring-opacity-40 flex-shrink-0"
        :style="{ background: hero.color, boxShadow: `0 0 14px ${hero.color}55` }"
        style="width:60px;height:60px;font-size:18px"
      >
        {{ initials(hero.name) }}
      </div>
      <div class="flex-1 min-w-0">
        <h3 class="font-gothic text-gold text-base leading-tight">{{ hero.name }}</h3>
        <p class="font-cinzel text-parchment-muted text-xs tracking-wide">{{ hero.title }}</p>
        <div class="flex flex-wrap gap-1.5 mt-2">
          <span
            v-for="r in hero.optimalRanks"
            :key="r"
            class="px-2 py-0.5 rounded-sm text-xs font-cinzel border"
            :class="r === rank ? 'border-gold text-gold' : 'border-rust text-parchment-muted'"
          >
            Rank {{ r }}
          </span>
        </div>
      </div>
      <button class="text-parchment-muted hover:text-parchment text-lg leading-none" @click="emit('close')">✕</button>
    </div>

    <!-- Warning for current rank -->
    <div v-if="currentRankWarning" class="warning-badge w-full text-xs py-1.5 px-3">
      ⚠ {{ currentRankWarning }}
    </div>

    <p class="font-body text-parchment-dark text-sm leading-relaxed">{{ hero.description }}</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <!-- Stats -->
      <div>
        <h4 class="font-cinzel text-parchment-dark text-xs tracking-widest uppercase mb-2 border-b border-rust border-opacity-30 pb-1">Base Stats</h4>
        <div class="space-y-0.5">
          <div class="stat-row"><span class="stat-label">HP</span>          <span class="stat-value text-green-400">{{ hero.stats.hp }}</span></div>
          <div class="stat-row"><span class="stat-label">Speed</span>       <span class="stat-value">{{ hero.stats.speed }}</span></div>
          <div class="stat-row"><span class="stat-label">Dodge</span>       <span class="stat-value" :class="hero.stats.dodge < 0 ? 'text-crimson' : ''">{{ hero.stats.dodge >= 0 ? '+' : '' }}{{ hero.stats.dodge }}%</span></div>
          <div class="stat-row"><span class="stat-label">Accuracy</span>    <span class="stat-value" :class="hero.stats.accuracy < 0 ? 'text-crimson' : ''">{{ hero.stats.accuracy >= 0 ? '+' : '' }}{{ hero.stats.accuracy }}</span></div>
          <div class="stat-row"><span class="stat-label">Damage</span>      <span class="stat-value">{{ hero.stats.damageMin }}–{{ hero.stats.damageMax }}</span></div>
          <div class="stat-row"><span class="stat-label">Crit</span>        <span class="stat-value text-gold">{{ hero.stats.crit }}%</span></div>
          <div class="stat-row"><span class="stat-label">Protection</span>  <span class="stat-value">{{ hero.stats.prot }}%</span></div>
        </div>
      </div>

      <!-- Resistances -->
      <div>
        <h4 class="font-cinzel text-parchment-dark text-xs tracking-widest uppercase mb-2 border-b border-rust border-opacity-30 pb-1">Resistances</h4>
        <div class="space-y-0.5">
          <div v-for="(val, key) in hero.resistances" :key="key" class="stat-row">
            <span class="stat-label capitalize">{{ key.replace('deathBlow', 'Death Blow') }}</span>
            <span class="stat-value" :class="val >= 50 ? 'text-green-400' : val <= 20 ? 'text-crimson' : ''">{{ val }}%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Skills -->
    <div>
      <h4 class="font-cinzel text-parchment-dark text-xs tracking-widest uppercase mb-2 border-b border-rust border-opacity-30 pb-1">Skills</h4>
      <div class="space-y-1.5">
        <div
          v-for="skill in hero.skills"
          :key="skill.name"
          class="flex gap-3 p-2 rounded-sm border border-rust border-opacity-20 hover:border-opacity-40 transition-colors"
        >
          <span
            class="text-xs font-cinzel px-1.5 py-0.5 rounded-sm flex-shrink-0 self-start mt-0.5"
            :class="{
              'bg-crimson bg-opacity-20 text-crimson-glow border border-crimson border-opacity-40': skill.type === 'attack',
              'bg-blue-900 bg-opacity-20 text-blue-300 border border-blue-700 border-opacity-30': skill.type === 'heal',
              'bg-purple-900 bg-opacity-20 text-purple-300 border border-purple-700 border-opacity-30': skill.type === 'support',
              'bg-yellow-900 bg-opacity-20 text-yellow-300 border border-yellow-700 border-opacity-30': skill.type === 'debuff',
            }"
          >
            {{ skill.type }}
          </span>
          <div class="min-w-0">
            <div class="font-cinzel text-parchment text-xs">{{ skill.name }}</div>
            <div class="font-body text-parchment-muted text-xs">{{ skill.description }}</div>
            <div class="font-cinzel text-parchment-muted text-xs opacity-60 mt-0.5">
              Ranks: {{ skill.ranks.join(', ') }} &nbsp;·&nbsp;
              Targets: {{ skill.targets[0] === 0 ? 'Self' : skill.targets.join(', ') }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Synergy notes -->
    <div v-if="hero.synergyNotes?.length">
      <h4 class="font-cinzel text-parchment-dark text-xs tracking-widest uppercase mb-2 border-b border-rust border-opacity-30 pb-1">Synergies</h4>
      <ul class="space-y-1">
        <li v-for="(note, i) in hero.synergyNotes" :key="i" class="font-body text-parchment-dark text-sm flex gap-2">
          <span class="text-gold flex-shrink-0">◆</span>{{ note }}
        </li>
      </ul>
    </div>

    <!-- Incompatibilities -->
    <div v-if="hero.incompatibleWith?.length" class="warning-badge py-2 px-3 block">
      ⚠ Cannot party with:
      <span class="text-crimson-glow">{{ hero.incompatibleWith.map(id => id.replace('_', '-')).join(', ') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({ hero: Object, rank: Number })
const emit  = defineEmits(['close'])

const currentRankWarning = computed(() =>
  props.hero?.rankWarnings?.[String(props.rank)] ?? null
)

function initials(name) {
  return name.split(/[\s-]/).map(w => w[0]).join('').slice(0, 2).toUpperCase()
}
</script>
