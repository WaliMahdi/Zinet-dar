<template>
  <div class="min-h-screen bg-background">
    <!-- Breadcrumb -->
    <div class="bg-surface py-4 border-b border-border">
      <div class="container-site">
        <Breadcrumb />
      </div>
    </div>
    <!-- Hero -->
    <div class="bg-primary py-20 text-center">
      <div class="container-site">
        <p class="section-eyebrow text-secondary">Notre histoire</p>
        <h1 class="font-display text-4xl md:text-5xl font-light text-white mb-4">
          À propos de Zinet Eddar
        </h1>
        <p class="text-white/70 max-w-lg mx-auto text-base">
          "Zinet Eddar" — la beauté de la maison. Une vision, une promesse.
        </p>
      </div>
    </div>

    <div class="container-site py-16 max-w-4xl">
      <!-- Story -->
      <div class="grid md:grid-cols-2 gap-12 items-center mb-16">
        <div>
          <p class="section-eyebrow">Notre mission</p>
          <h2 class="section-title mb-5">Un intérieur qui vous ressemble</h2>
          <p class="text-muted leading-relaxed mb-4">
            Chez Zinet Eddar, nous croyons que chaque foyer mérite d'être beau. Nous sélectionnons
            avec soin des meubles et des objets de décoration qui allient esthétique moderne et
            qualité durable.
          </p>
          <p class="text-muted leading-relaxed">
            Basés en Tunisie, nous livrons partout dans le pays avec un service de proximité et un
            paiement simple à la livraison.
          </p>
        </div>
        <div class="aspect-square rounded-lg overflow-hidden bg-gradient-to-br from-primary/10 to-secondary/20 relative">
          <img src="/hero-living-room.jpg" alt="Showroom Zinet Eddar" class="w-full h-full object-cover opacity-80" />
        </div>
      </div>

      <!-- Values -->
      <div class="text-center mb-10">
        <p class="section-eyebrow">Nos valeurs</p>
        <h2 class="section-title mb-2">Ce qui nous guide</h2>
      </div>
      <div class="grid sm:grid-cols-3 gap-6 mb-16">
        <div v-for="value in values" :key="value.title" class="text-center p-6 bg-surface rounded-lg border border-border/50">
          <div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center mx-auto mb-3">
            <component :is="value.icon" :size="22" class="text-secondary" />
          </div>
          <h3 class="font-display text-lg font-medium text-primary mb-2">{{ value.title }}</h3>
          <p class="text-muted text-sm">{{ value.description }}</p>
        </div>
      </div>

      <!-- CTA -->
      <div class="text-center bg-background rounded-xl p-10 border border-border">
        <h3 class="font-display text-2xl font-medium text-primary mb-3">Prêt à transformer votre intérieur ?</h3>
        <p class="text-muted text-sm mb-6">Explorez notre collection et trouvez les pièces qui vous inspirent.</p>
        <RouterLink :to="{ name: 'shop' }" class="btn-primary btn-lg">Découvrir la boutique</RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Heart, Leaf, Award } from '@lucide/vue'
import Breadcrumb from '@/components/common/Breadcrumb.vue'

const values = [
  { icon: Heart,   title: 'Passion',    description: 'Nous aimons le design et la beauté des espaces de vie.' },
  { icon: Leaf,    title: 'Qualité',    description: 'Chaque produit est sélectionné selon des critères de qualité stricts.' },
  { icon: Award,   title: 'Confiance',  description: 'Paiement à la livraison pour une expérience d\'achat sans risque.' },
]
</script>
