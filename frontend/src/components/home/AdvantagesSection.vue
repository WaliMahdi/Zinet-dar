<template>
  <section class="py-16 md:py-24 bg-surface border-y border-border/50">
    <div class="container-site">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10">
        <div
          v-for="advantage in advantages"
          :key="advantage.title"
          class="text-center group"
        >
          <!-- Icon -->
          <div class="w-14 h-14 rounded-full bg-secondary/10 flex items-center justify-center mx-auto mb-4
                      group-hover:bg-secondary/20 transition-colors duration-300">
            <component :is="advantage.icon" :size="24" class="text-secondary" />
          </div>
          <!-- Text -->
          <h3 class="font-display text-lg font-medium text-primary mb-2">{{ advantage.title }}</h3>
          <p class="text-muted text-sm leading-relaxed">{{ advantage.description }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Truck, Banknote, Star, HeadphonesIcon } from '@lucide/vue'

const advantages = [
  {
    icon: Truck,
    title: 'Livraison en Tunisie',
    description: 'Nous livrons partout en Tunisie. Votre commande arrive directement chez vous.',
  },
  {
    icon: Banknote,
    title: 'Paiement à la livraison',
    description: 'Réglez en espèces à la réception de votre commande, sans souci.',
  },
  {
    icon: Star,
    title: 'Produits sélectionnés',
    description: 'Chaque article est choisi avec soin pour sa qualité et son esthétique.',
  },
  {
    icon: HeadphonesIcon,
    title: 'Service client à votre écoute',
    description: 'Notre équipe est disponible pour répondre à toutes vos questions.',
  },
]
</script>
