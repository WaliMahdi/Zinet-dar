<template>
  <section class="relative min-h-[85vh] lg:min-h-screen flex items-center overflow-hidden">
    <!-- Background image -->
    <div class="absolute inset-0">
      <img
        :src="heroImageUrl"
        alt="Intérieur élégant Zinet Eddar"
        class="w-full h-full object-cover object-center"
      />
      <!-- Gradient overlay -->
      <div class="absolute inset-0 bg-hero-gradient"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 container-site w-full">
      <div class="max-w-xl">
        <!-- Eyebrow -->
        <p class="text-secondary text-sm font-medium uppercase tracking-widest mb-4 animate-slide-right">
          Collection 2026
        </p>

        <!-- Headline -->
        <h1 class="font-display text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-light text-white leading-none mb-6 animate-slide-up">
          {{ heroTitle || 'Donnez du caractère' }}<br />
          <em class="font-medium not-italic" v-if="!heroTitle">à votre intérieur</em>
        </h1>

        <!-- Subtitle -->
        <p class="text-white/80 text-base md:text-lg leading-relaxed mb-8 max-w-md animate-fade-in font-light">
          {{ heroDescription || 'Des meubles élégants et des pièces soigneusement sélectionnées pour créer un espace qui vous ressemble.' }}
        </p>

        <!-- CTA buttons -->
        <div class="flex flex-wrap gap-4 mb-12 animate-fade-in">
          <RouterLink :to="{ name: 'shop' }" class="btn-secondary btn-lg" id="hero-cta-primary">
            Découvrir la collection
          </RouterLink>
          <RouterLink :to="{ name: 'new-products' }" class="btn-lg border border-white/40 text-white hover:bg-white/10 btn" id="hero-cta-secondary">
            Voir les nouveautés
          </RouterLink>
        </div>

        <!-- Trust indicators -->
        <div class="flex flex-wrap gap-6">
          <div v-for="indicator in indicators" :key="indicator.label" class="flex items-center gap-2 text-white/70 text-sm">
            <component :is="indicator.icon" :size="16" class="text-secondary flex-shrink-0" />
            <span>{{ indicator.label }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Scroll hint -->
    <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-white/40 animate-bounce">
      <span class="text-xs tracking-widest uppercase">Découvrir</span>
      <ChevronDown :size="18" />
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Truck, Star, Shield, ChevronDown } from '@lucide/vue'
import api from '@/services/api'

const indicators = [
  { label: 'Design élégant',          icon: Star },
  { label: 'Qualité sélectionnée',    icon: Shield },
  { label: 'Livraison en Tunisie',    icon: Truck },
]

const heroImageUrl = ref('/hero-living-room.jpg')
const heroTitle = ref('')
const heroDescription = ref('')

onMounted(async () => {
  try {
    const res = await api.get('/homepage-images')
    if (res.data && res.data.length > 0) {
      const hero = res.data.find(img => img.section === 'hero')
      if (hero) {
        heroImageUrl.value = hero.image_url
        if (hero.title) heroTitle.value = hero.title
        if (hero.description) heroDescription.value = hero.description
      }
    }
  } catch (err) {
    console.error('Erreur chargement images homepage', err)
  }
})

</script>
