<template>
  <div class="min-h-screen bg-abyss flex flex-col">
    <!-- Header -->
    <header class="border-b border-rust border-opacity-40 bg-slate shadow-panel sticky top-0 z-50">
      <div class="max-w-screen-xl mx-auto px-4 py-3 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="flex items-center gap-3">
          <span class="text-crimson text-2xl select-none">☽</span>
          <h1 class="font-gothic text-gold text-lg sm:text-xl leading-tight">
            Darkest Dungeon<br class="sm:hidden" />
            <span class="text-parchment-muted text-sm font-cinzel tracking-widest"> Companion</span>
          </h1>
        </div>
        <nav class="sm:ml-auto flex gap-1 overflow-x-auto">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            class="dd-tab whitespace-nowrap"
            :class="{ 'dd-tab-active': activeTab === tab.id }"
            @click="activeTab = tab.id"
          >
            {{ tab.label }}
          </button>
        </nav>
      </div>
    </header>

    <!-- Loading state -->
    <div v-if="loading" class="flex-1 flex items-center justify-center">
      <div class="text-center space-y-4">
        <div class="text-crimson text-5xl animate-pulse">☽</div>
        <p class="font-cinzel text-parchment-muted tracking-widest text-sm uppercase">Consulting the Ancestor...</p>
      </div>
    </div>

    <!-- Error state -->
    <div v-else-if="error" class="flex-1 flex items-center justify-center p-8">
      <div class="dd-panel p-8 max-w-md text-center space-y-4">
        <div class="text-crimson text-4xl">✖</div>
        <h2 class="font-gothic text-crimson text-lg">Connection Failed</h2>
        <p class="font-body text-parchment-dark text-sm">{{ error }}</p>
        <button class="dd-btn" @click="loadData">Retry</button>
      </div>
    </div>

    <!-- Main content -->
    <main v-else class="flex-1 max-w-screen-xl mx-auto w-full px-4 py-6">
      <Transition name="fade" mode="out-in">
        <HeroBuilder
          v-if="activeTab === 'builder'"
          :heroes="heroes"
        />
        <ProvisionCalculator
          v-else-if="activeTab === 'provisions'"
        />
        <CurioDatabase
          v-else-if="activeTab === 'curios'"
          :curios="curios"
        />
      </Transition>
    </main>

    <!-- Footer -->
    <footer class="border-t border-rust border-opacity-20 py-4 text-center">
      <p class="font-cinzel text-parchment-muted text-xs tracking-widest uppercase opacity-50">
        Memento Mori &nbsp;·&nbsp; Darkest Dungeon Companion &nbsp;·&nbsp; Fan Project
        <br class="sm:hidden" />
        <span class="sm:before:content-['·'] sm:before:mx-3 sm:before:opacity-50">
          Made with love by JulardoS &nbsp;·&nbsp; &copy; {{ new Date().getFullYear() }}
        </span>
      </p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { fetchHeroes, fetchCurios } from './api/index.js'
import HeroBuilder from './components/HeroBuilder.vue'
import ProvisionCalculator from './components/ProvisionCalculator.vue'
import CurioDatabase from './components/CurioDatabase.vue'

const tabs = [
  { id: 'builder',    label: 'Team Builder' },
  { id: 'provisions', label: 'Provisions' },
  { id: 'curios',     label: 'Curio Cheat Sheet' },
]

const activeTab = ref('builder')
const heroes    = ref([])
const curios    = ref([])
const loading   = ref(true)
const error     = ref(null)

async function loadData() {
  loading.value = true
  error.value   = null
  try {
    const [h, c] = await Promise.all([fetchHeroes(), fetchCurios()])
    heroes.value = h
    curios.value = c
  } catch (e) {
    error.value = 'Could not reach the backend API. Ensure the Laravel server is running on port 8000.'
  } finally {
    loading.value = false
  }
}

onMounted(loadData)
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>
