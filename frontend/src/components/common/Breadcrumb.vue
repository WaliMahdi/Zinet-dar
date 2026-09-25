<template>
  <nav aria-label="Breadcrumb" class="w-full">
    <ol class="text-xs md:text-sm text-muted flex items-center gap-1.5 flex-wrap">
      <li>
        <RouterLink 
          :to="{ name: 'home' }" 
          class="hover:text-primary transition-colors flex items-center gap-1"
        >
          Accueil
        </RouterLink>
      </li>
      
      <li v-for="(item, index) in breadcrumbItems" :key="index" class="flex items-center gap-1.5">
        <ChevronRight :size="14" class="text-muted/50" />
        
        <RouterLink 
          v-if="item.to" 
          :to="item.to" 
          class="hover:text-primary transition-colors truncate max-w-[150px] md:max-w-[300px]"
          :title="item.label"
        >
          {{ item.label }}
        </RouterLink>
        
        <span 
          v-else 
          class="text-primary font-medium truncate max-w-[150px] md:max-w-[300px]"
          :aria-current="index === breadcrumbItems.length - 1 ? 'page' : undefined"
          :title="item.label"
        >
          {{ item.label }}
        </span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { ChevronRight } from '@lucide/vue'

const props = defineProps({
  /**
   * Array of objects representing the breadcrumb trail.
   * Format: { label: String, to: Object|String (optional) }
   * If not provided, it falls back to the current route's meta.title.
   */
  items: {
    type: Array,
    default: null
  }
})

const route = useRoute()

const breadcrumbItems = computed(() => {
  if (props.items && props.items.length > 0) {
    return props.items
  }

  // Fallback to route meta
  if (route.name !== 'home' && route.meta?.title) {
    return [{ label: route.meta.title }]
  }

  return []
})
</script>
