<template>
  <div class="flex flex-col items-center justify-center py-16 px-6 text-center animate-fade-in">
    <div class="w-20 h-20 rounded-full bg-secondary/10 flex items-center justify-center mb-6">
      <component :is="iconComponent" :size="36" class="text-secondary" />
    </div>
    <h3 class="font-display text-2xl font-medium text-primary mb-2">{{ title }}</h3>
    <p class="text-muted text-sm max-w-xs">{{ description }}</p>
    <slot name="action" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { ShoppingBag, PackageX, SearchX, InboxIcon } from '@lucide/vue'

const props = defineProps({
  icon: { type: String, default: 'box' },
  title: { type: String, default: 'Aucun résultat' },
  description: { type: String, default: 'Rien à afficher pour le moment.' },
})

const iconComponent = computed(() => {
  const map = {
    cart:   ShoppingBag,
    package:PackageX,
    search: SearchX,
    box:    InboxIcon,
  }
  return map[props.icon] || InboxIcon
})
</script>
