<template>
  <div class="w-full relative bg-background">
    <!-- Si la bannière existe (Affichage type Hero) -->
    <div v-if="banner" class="relative min-h-[40vh] md:min-h-[50vh] flex items-center overflow-hidden">
      <!-- Background image -->
      <div class="absolute inset-0">
        <img 
          :src="banner.image_url" 
          :alt="title || section"
          class="w-full h-full object-cover object-center"
        />
        <!-- Gradient overlay -->
        <div class="absolute inset-0 bg-hero-gradient"></div>
      </div>

      <!-- Content superposé -->
      <div class="relative z-10 container-site w-full">
        <div class="max-w-xl">
          <p v-if="eyebrow" class="text-secondary text-sm font-medium uppercase tracking-widest mb-4 animate-slide-right">
            {{ eyebrow }}
          </p>
          <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-light text-white leading-none mb-6 animate-slide-up capitalize">
            {{ banner.title || title || section }}
          </h1>
        </div>
      </div>
    </div>
    
    <!-- Fallback si aucune bannière (Affichage classique) -->
    <div v-else-if="title" class="bg-surface py-12 md:py-16 border-b border-border">
      <div class="container-site">
        <p v-if="eyebrow" class="text-secondary text-sm font-medium uppercase tracking-widest mb-3">
          {{ eyebrow }}
        </p>
        <h1 class="section-title capitalize">{{ title }}</h1>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const props = defineProps({
  section: {
    type: String,
    required: true,
    validator: (value) => ['accueil', 'boutique', 'nouveautes', 'promotions', 'categories'].includes(value)
  },
  title: {
    type: String,
    default: ''
  },
  eyebrow: {
    type: String,
    default: ''
  }
})

const banner = ref(null)

onMounted(async () => {
  try {
    const res = await api.get(`/bannieres/section/${props.section}`)
    if (res.data && res.data.success && res.data.data) {
      banner.value = res.data.data
    }
  } catch (error) {
    console.error(`Erreur lors du chargement de la bannière pour la section ${props.section}:`, error)
  }
})
</script>
