<template>
  <header class="bg-surface border-b border-border h-16 flex items-center justify-between px-4 lg:px-8 sticky top-0 z-20">
    <div class="flex items-center gap-3">
      <!-- Hamburger Menu (Mobile/Tablet) -->
      <button 
        @click="$emit('toggle-sidebar')" 
        class="lg:hidden text-primary hover:text-secondary transition-colors"
      >
        <Menu :size="24" />
      </button>
      
      <h1 class="font-display text-lg lg:text-xl font-medium text-primary hidden sm:block">
        {{ title }}
      </h1>
      <!-- Mobile title fallback if sidebar is hidden -->
      <span class="font-display font-medium text-lg text-primary sm:hidden">
        Zinet Admin
      </span>
    </div>

    <div class="flex items-center gap-4">
      <RouterLink :to="{ name: 'home' }" class="text-sm text-muted hover:text-secondary flex items-center gap-2 transition-colors">
        <ExternalLink :size="16" /> <span class="hidden sm:inline">Voir le site</span>
      </RouterLink>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { Menu, ExternalLink } from '@lucide/vue'

defineEmits(['toggle-sidebar'])

const route = useRoute()
const title = computed(() => route.meta.title || 'Administration')
</script>
