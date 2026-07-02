// Mechanical tags and synergy role for each hero, keyed by backend hero ID.
// Tags drive both dungeon highlighting and synergy detection.
export const HERO_SYNERGY_DATA = {
  crusader: {
    tags: ['frontline', 'dps', 'stress_healer', 'stunner', 'unholy_slayer'],
    synergyProviders: ['stun'],
    synergyExploiters: [],
  },
  plague_doctor: {
    tags: ['blight_heavy', 'stunner', 'backline', 'support'],
    synergyProviders: ['blight', 'stun'],
    synergyExploiters: [],
  },
  leper: {
    tags: ['frontline', 'dps', 'high_damage'],
    synergyProviders: [],
    synergyExploiters: ['mark'],
  },
  highwayman: {
    tags: ['bleed_heavy', 'dps', 'high_crit', 'high_dodge'],
    synergyProviders: ['bleed'],
    synergyExploiters: ['bleed'],
  },
  grave_robber: {
    tags: ['blight_heavy', 'bleed_heavy', 'backline', 'dps', 'high_crit', 'high_dodge'],
    synergyProviders: ['bleed', 'blight'],
    synergyExploiters: ['bleed', 'blight'],
  },
  jester: {
    tags: ['stress_healer', 'backline', 'support', 'bleed_heavy', 'buffer'],
    synergyProviders: ['bleed'],
    synergyExploiters: [],
  },
  arbalest: {
    tags: ['backline', 'dps', 'high_accuracy'],
    synergyProviders: [],
    synergyExploiters: ['mark'],
  },
  bounty_hunter: {
    tags: ['human_slayer', 'stunner', 'frontline', 'dps', 'puller'],
    synergyProviders: ['mark', 'stun'],
    synergyExploiters: ['mark', 'stun'],
  },
  hellion: {
    tags: ['bleed_heavy', 'frontline', 'dps', 'stress_healer'],
    synergyProviders: ['bleed', 'stun'],
    synergyExploiters: ['bleed'],
  },
  man_at_arms: {
    tags: ['frontline', 'support', 'buffer', 'guard', 'stress_healer', 'stunner'],
    synergyProviders: ['stun'],
    synergyExploiters: [],
  },
  occultist: {
    tags: ['eldritch_slayer', 'main_healer', 'puller', 'backline'],
    synergyProviders: ['mark'],
    synergyExploiters: ['mark', 'prot_bypass'],
  },
  vestal: {
    tags: ['main_healer', 'stress_healer', 'backline', 'stunner', 'unholy_slayer'],
    synergyProviders: ['stun'],
    synergyExploiters: [],
  },
  houndmaster: {
    tags: ['bleed_heavy', 'stunner', 'stress_healer', 'scout', 'dps'],
    synergyProviders: ['mark', 'bleed', 'stun'],
    synergyExploiters: ['mark', 'stun', 'bleed'],
  },
  abomination: {
    tags: ['bleed_heavy', 'frontline', 'dps', 'stunner'],
    synergyProviders: ['bleed', 'stun'],
    synergyExploiters: ['stun'],
  },
  flagellant: {
    tags: ['bleed_heavy', 'backline', 'dps'],
    synergyProviders: ['bleed', 'blight'],
    synergyExploiters: ['bleed'],
  },
  musketeer: {
    tags: ['backline', 'dps', 'high_accuracy'],
    synergyProviders: [],
    synergyExploiters: ['mark'],
  },
}

export const SYNERGY_ARCHETYPES = [
  {
    id: 'syn_mark_for_death',
    name: 'Mark for Death',
    description:
      'A hero marks the target to reduce their PROT and grant your entire party ACC bonuses. Then a hard-hitting exploiter capitalises for massive bonus damage against the highlighted enemy.',
    triggerCondition: { requiresProvider: 'mark', requiresExploiter: 'mark' },
    uiColor: '#e8a030',
    glowColor: 'rgba(232,160,48,0.25)',
    icon: '◎',
  },
  {
    id: 'syn_hemorrhage',
    name: 'Hemorrhage',
    description:
      'Multiple heroes stack Bleed DoTs simultaneously. Each tick compounds — and because Bleed bypasses PROT entirely, heavily armoured enemies are just as vulnerable as unarmoured ones.',
    triggerCondition: { requiresProvider: 'bleed', requiresExploiter: 'bleed' },
    uiColor: '#c03030',
    glowColor: 'rgba(192,48,48,0.3)',
    icon: '♦',
  },
  {
    id: 'syn_toxic_pierce',
    name: 'Toxic Armor Pierce',
    description:
      "Blight DoTs completely ignore enemy PROT — making them especially brutal in the Cove and Warrens. Pair with the Occultist's Vulnerability Hex to lower PROT further so physical attacks also start to bite.",
    triggerCondition: { requiresProvider: 'blight', requiresExploiter: 'prot_bypass' },
    uiColor: '#40b040',
    glowColor: 'rgba(64,176,64,0.2)',
    icon: '☠',
  },
  {
    id: 'syn_stun_execute',
    name: 'Stun & Execute',
    description:
      "Stun an enemy to cancel their turn and make them helpless — then the Bounty Hunter's Collect Bounty deals catastrophic bonus damage to the incapacitated target.",
    triggerCondition: { requiresProvider: 'stun', requiresExploiter: 'stun' },
    uiColor: '#8080e0',
    glowColor: 'rgba(128,128,224,0.2)',
    icon: '⚡',
  },
]

export const DUNGEONS = [
  {
    id: 'ruins',
    name: 'The Ruins',
    enemyTypes: 'Undead & Unholy',
    optimalTags: ['unholy_slayer', 'stunner', 'high_accuracy', 'stress_healer'],
    ineffectiveTags: ['blight_heavy', 'eldritch_slayer'],
    hazards: ['Undead immune to Bleed', 'High stress', 'Shambler ambush risk'],
    tip: 'Undead are immune to Bleed and highly resistant to Blight. Bring Holy damage (Crusader, Vestal) and reliable sustained healing. High ACC matters — undead have solid dodge.',
  },
  {
    id: 'weald',
    name: 'The Weald',
    enemyTypes: 'Human & Beast',
    optimalTags: ['bleed_heavy', 'high_accuracy', 'high_dodge', 'stunner'],
    ineffectiveTags: ['eldritch_slayer'],
    hazards: ['Enemy high dodge', 'Heavy Blight on traps', 'Dense foliage traps'],
    tip: 'Humans and Beasts bleed freely — stack Bleed with Hellion or Highwayman. High ACC is essential: many Weald enemies have exceptional dodge.',
  },
  {
    id: 'warrens',
    name: 'The Warrens',
    enemyTypes: 'Beast (Swine)',
    optimalTags: ['blight_heavy', 'high_accuracy', 'stunner', 'stress_healer'],
    ineffectiveTags: ['bleed_heavy', 'unholy_slayer'],
    hazards: ['Heavy Bleed from Swine', 'Disease pots', 'Formation-disrupting attacks'],
    tip: 'Swine are highly vulnerable to Blight but resist Bleed. Plague Doctor is nearly mandatory. Bring stress management — long corridors grind your party down.',
  },
  {
    id: 'cove',
    name: 'The Cove',
    enemyTypes: 'Eldritch & Fish-men',
    optimalTags: ['blight_heavy', 'eldritch_slayer', 'stunner', 'high_accuracy'],
    ineffectiveTags: ['bleed_heavy', 'unholy_slayer'],
    hazards: ['Very high PROT on sea creatures', 'Heavy Blight DoTs', 'Shuffle attacks'],
    tip: "Fish-men have very high PROT — Blight bypasses it completely. Occultist's Vulnerability Hex is invaluable here. Prepare for shuffle attacks disrupting your ranks.",
  },
  {
    id: 'darkest_dungeon',
    name: 'The Darkest Dungeon',
    enemyTypes: 'All Types',
    optimalTags: ['main_healer', 'stress_healer', 'stunner', 'high_accuracy'],
    ineffectiveTags: [],
    hazards: ['Extreme stress', 'Heart attack risk', 'Combined Bleed+Blight DoTs'],
    tip: 'Bring your most battle-tested composition. Sustained HP healing and stress management are non-negotiable. No room for experiments — every hero must be optimal.',
  },
]
