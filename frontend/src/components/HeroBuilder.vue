<template>
  <div class="space-y-6 animate-fade-in">
    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
      <h2 class="font-gothic text-gold text-xl">Party Formation</h2>
      <span class="font-cinzel text-parchment-muted text-xs tracking-widest uppercase">Drag or tap heroes into rank slots</span>
    </div>

    <div class="flex flex-col xl:flex-row gap-6">
      <!-- Sidebar: Hero Roster — pushed below party box on mobile -->
      <aside class="xl:w-64 flex-shrink-0 order-2 xl:order-1">
        <HeroRoster
          :heroes="heroes"
          :team="team"
          :dungeon-filter="dungeonFilter"
          :pending-hero="pendingHero"
          :pending-from="pendingFrom"
          @drag-start="onRosterDrag"
          @drag-end="draggingHero = null"
          @remove-from-team="removeHeroById"
          @tap-hero="onTapRosterHero"
          @cancel-pending="cancelPending"
        />
      </aside>

      <!-- Main column — first on mobile -->
      <div class="flex-1 min-w-0 space-y-4 order-1 xl:order-2">

        <!-- ── Sticky party area ── -->
        <div class="xl:sticky xl:top-16 xl:z-40 space-y-3 bg-abyss xl:pb-4">

          <DungeonRecommender @filter-change="dungeonFilter = $event" />

          <div class="dd-panel p-5 space-y-4">
            <div class="flex items-center gap-3 mb-2">
              <span class="font-cinzel text-parchment-muted text-xs tracking-widest uppercase">← Enemies</span>
              <div class="flex-1 h-px bg-rust opacity-30"></div>
              <span class="font-cinzel text-parchment-muted text-xs tracking-widest uppercase">Party →</span>
            </div>

            <div class="grid grid-cols-4 gap-2 sm:gap-3">
              <RankSlot
                v-for="rank in [4, 3, 2, 1]"
                :key="rank"
                :rank="rank"
                :hero="team[rank]"
                :dragged-hero="draggingHero"
                :pending-hero="pendingHero"
                :pending-from="pendingFrom"
                @drop="onDrop(rank, $event)"
                @remove="removeHero(rank)"
                @click-hero="selectHero"
                @drag-start="onRosterDrag"
                @drag-end="draggingHero = null"
                @tap-place="onTapPlace"
              />
            </div>

            <!-- Rank-placement & religious-conflict warnings -->
            <div v-if="rankWarnings.length" class="mt-3 space-y-1">
              <div v-for="(w, i) in rankWarnings" :key="i" class="warning-badge">
                ⚠ {{ w }}
              </div>
            </div>

            <button v-if="hasTeam" class="dd-btn text-xs mt-2" @click="clearTeam">
              Clear Party
            </button>
          </div>
        </div>

        <!-- Synergy panel + hero detail scroll freely below -->
        <TeamSynergyPanel
          :active-synergies="activeSynergies"
          :composition-warnings="compositionWarnings"
        />

        <HeroDetailPanel
          v-if="selectedHero"
          :hero="selectedHero"
          :rank="selectedRank"
          @close="selectedHero = null"
        />

        <div v-else-if="!activeSynergies.length && !compositionWarnings.length" class="dd-panel p-6 text-center opacity-60">
          <p class="font-cinzel text-parchment-muted text-sm">Tap or drag a hero to a rank slot. Tap a placed hero to view their details.</p>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import HeroRoster         from './HeroRoster.vue'
import RankSlot           from './RankSlot.vue'
import HeroDetailPanel    from './HeroDetailPanel.vue'
import TeamSynergyPanel   from './TeamSynergyPanel.vue'
import DungeonRecommender from './DungeonRecommender.vue'
import { useSynergyEngine } from '../composables/useSynergyEngine.js'

const props = defineProps({ heroes: Array })

const team          = ref({ 1: null, 2: null, 3: null, 4: null })
const draggingHero  = ref(null)
const selectedHero  = ref(null)
const selectedRank  = ref(null)
const dungeonFilter = ref(null)

// Tap-to-place state
const pendingHero = ref(null)   // hero held for placement
const pendingFrom = ref(null)   // 'roster' | rank number

const hasTeam = computed(() => Object.values(team.value).some(Boolean))

const { activeSynergies, compositionWarnings } = useSynergyEngine(team)

const rankWarnings = computed(() => {
  const placed = Object.entries(team.value).filter(([, h]) => h)
  const warnings = []
  placed.forEach(([rank, hero]) => {
    const w = hero.rankWarnings?.[String(rank)]
    if (w) warnings.push(w)
  })
  const ids = placed.map(([, h]) => h.id)
  if (ids.includes('abomination')) {
    if (ids.includes('vestal'))   warnings.push('Abomination and Vestal cannot party together — religious conflict!')
    if (ids.includes('crusader')) warnings.push('Abomination and Crusader cannot party together — religious conflict!')
  }
  return warnings
})

// ── Drag handlers (desktop) ────────────────────────────────────────────────

function onRosterDrag(hero) { draggingHero.value = hero }

function onDrop(targetRank, { heroId, sourceRank }) {
  const hero = props.heroes.find(h => h.id === heroId)
  if (!hero) return
  if (sourceRank != null && sourceRank !== targetRank) {
    const displaced = team.value[targetRank]
    team.value[targetRank] = hero
    team.value[sourceRank] = displaced
  } else {
    Object.keys(team.value).forEach(r => {
      if (team.value[r]?.id === hero.id) team.value[r] = null
    })
    team.value[targetRank] = hero
  }
  draggingHero.value = null
}

// ── Tap handlers (mobile) ──────────────────────────────────────────────────

function onTapRosterHero(hero) {
  // Tapping the currently-pending hero cancels
  if (pendingHero.value?.id === hero.id) {
    cancelPending()
    return
  }
  // Find if this hero is already placed in a slot
  const entry = Object.entries(team.value).find(([, h]) => h?.id === hero.id)
  pendingHero.value = hero
  pendingFrom.value = entry ? parseInt(entry[0]) : 'roster'
}

function onTapPlace(targetRank) {
  if (!pendingHero.value) return
  const hero = pendingHero.value
  const from = pendingFrom.value

  if (from === targetRank) {
    cancelPending()
    return
  }

  if (from === 'roster') {
    // Hero came from roster: clear any existing slot it occupied, then place
    Object.keys(team.value).forEach(r => {
      if (team.value[r]?.id === hero.id) team.value[r] = null
    })
    team.value[targetRank] = hero
  } else {
    // Slot-to-slot: swap
    const displaced = team.value[targetRank]
    team.value[targetRank] = hero
    team.value[from] = displaced
  }

  cancelPending()
}

function cancelPending() {
  pendingHero.value = null
  pendingFrom.value = null
}

// ── Common ─────────────────────────────────────────────────────────────────

function removeHero(rank) {
  if (selectedHero.value?.id === team.value[rank]?.id) selectedHero.value = null
  team.value[rank] = null
}

function removeHeroById(heroId) {
  Object.keys(team.value).forEach(r => {
    if (team.value[r]?.id === heroId) {
      if (selectedHero.value?.id === heroId) selectedHero.value = null
      team.value[r] = null
    }
  })
  draggingHero.value = null
}

function selectHero(hero, rank) {
  cancelPending()
  selectedHero.value = hero
  selectedRank.value = rank
}

function clearTeam() {
  team.value         = { 1: null, 2: null, 3: null, 4: null }
  selectedHero.value = null
  draggingHero.value = null
  cancelPending()
}
</script>
