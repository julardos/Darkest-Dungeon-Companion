import { computed } from 'vue'
import { HERO_SYNERGY_DATA, SYNERGY_ARCHETYPES } from '../data/gameData.js'

function enrich(hero) {
  if (!hero) return null
  const data = HERO_SYNERGY_DATA[hero.id] || {}
  return { ...hero, ...data }
}

export function useSynergyEngine(team) {
  const enrichedTeam = computed(() =>
    Object.values(team.value).map(enrich)
  )

  const placedHeroes = computed(() => enrichedTeam.value.filter(Boolean))

  const activeSynergies = computed(() =>
    SYNERGY_ARCHETYPES
      .map(syn => {
        const { requiresProvider, requiresExploiter } = syn.triggerCondition
        const providers  = placedHeroes.value.filter(h => h.synergyProviders?.includes(requiresProvider))
        const exploiters = placedHeroes.value.filter(h => h.synergyExploiters?.includes(requiresExploiter))
        // At least one provider and one exploiter must be DIFFERENT heroes
        const active = providers.length > 0 && exploiters.length > 0 &&
          providers.some(p => exploiters.some(e => e.id !== p.id))
        if (!active) return null
        const involvedIds = new Set([...providers.map(h => h.id), ...exploiters.map(h => h.id)])
        const involvedHeroes = placedHeroes.value.filter(h => involvedIds.has(h.id))
        return { ...syn, involvedHeroes }
      })
      .filter(Boolean)
  )

  const allTags = computed(() => {
    const tags = new Set()
    placedHeroes.value.forEach(h => h.tags?.forEach(t => tags.add(t)))
    return tags
  })

  const compositionWarnings = computed(() => {
    if (placedHeroes.value.length === 0) return []
    const warnings = []
    if (!allTags.value.has('main_healer'))
      warnings.push('Danger: This composition lacks reliable HP healing.')
    if (!allTags.value.has('stress_healer'))
      warnings.push('Warning: High risk of afflictions on Medium/Long expeditions.')
    return warnings
  })

  // Returns 'optimal' | 'acceptable' | 'danger' for heatmap coloring during drag
  function rankHeatmap(hero, rank) {
    if (!hero) return null
    if (hero.optimalRanks?.includes(rank)) return 'optimal'
    if (hero.targetRanks?.includes(rank))  return 'acceptable'
    return 'danger'
  }

  return { activeSynergies, compositionWarnings, rankHeatmap }
}

// Used by HeroRoster to evaluate dungeon filter without needing full team state
export function heroDungeonFit(heroId, dungeonFilter) {
  if (!dungeonFilter) return null
  const tags = HERO_SYNERGY_DATA[heroId]?.tags || []
  if (dungeonFilter.ineffectiveTags.some(t => tags.includes(t))) return 'ineffective'
  if (dungeonFilter.optimalTags.some(t => tags.includes(t)))     return 'optimal'
  return null
}
