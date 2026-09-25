<template>
  <div>
    <!-- Logo Cloudinary s'il existe -->
    <img 
      v-if="settingsStore.logoUrl" 
      :src="settingsStore.logoUrl" 
      alt="Zinet Eddar Logo"
      :class="['object-contain transition-all', iconOnly ? 'w-11 h-11' : 'h-11 max-w-[180px]']"
    />
    
    <!-- Fallback SVG Zinet Eddar Logo s'il n'y a pas de logo custom -->
    <svg
      v-else
      :class="['zinet-logo', variant === 'white' ? 'logo-white' : 'logo-dark', iconOnly ? 'logo-icon' : '']"
      viewBox="0 0 180 44"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      role="img"
      aria-label="Zinet Eddar"
    >
      <g>
        <path d="M22 6L8 16.5V36H16.5V27H27.5V36H36V16.5L22 6Z" :fill="variant === 'white' ? '#FFFFFF' : '#2D2926'" />
        <path d="M13.5 19.5H30.5M13.5 19.5L26 26.5H13.5M30.5 19.5L18 26.5H30.5" stroke="#B68B5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
      </g>
      <g v-if="!iconOnly">
        <text x="48" y="22" font-family="'Cormorant Garamond', Georgia, serif" font-size="18" font-weight="600" letter-spacing="2" :fill="variant === 'white' ? '#FFFFFF' : '#2D2926'">ZINET</text>
        <text x="48" y="36" font-family="'Cormorant Garamond', Georgia, serif" font-size="11" font-weight="400" letter-spacing="4" :fill="variant === 'white' ? 'rgba(255,255,255,0.7)' : '#B68B5E'">EDDAR</text>
        <line x1="44" y1="25" x2="44" y2="25" :stroke="variant === 'white' ? 'rgba(255,255,255,0.3)' : '#E8E0D8'" stroke-width="1" />
      </g>
    </svg>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useSettingsStore } from '@/stores/settingsStore'

const props = defineProps({
  variant: {
    type: String,
    default: 'dark', // 'dark' | 'white'
  },
  iconOnly: {
    type: Boolean,
    default: false,
  },
})

const settingsStore = useSettingsStore()

onMounted(() => {
  settingsStore.fetchLogo()
})
</script>

<style scoped>
.zinet-logo {
  display: inline-block;
  flex-shrink: 0;
  width: 180px;
  height: 44px;
}
.logo-icon {
  width: 44px;
  height: 44px;
}
</style>

