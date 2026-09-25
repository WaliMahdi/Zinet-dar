<template>
  <section class="py-16 md:py-24 bg-surface">
    <div class="container-site">
      <div class="text-center mb-10">
        <p class="section-eyebrow">Avis clients</p>
        <h2 class="section-title">Ce que nos clients pensent</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          v-for="testimonial in testimonials"
          :key="testimonial.name"
          class="card p-6 flex flex-col gap-4"
        >
          <!-- Stars -->
          <div class="flex gap-1">
            <Star v-for="n in 5" :key="n" :size="14" class="fill-secondary text-secondary" />
          </div>
          <!-- Quote -->
          <p class="text-muted text-sm leading-relaxed flex-1 italic">
            "{{ testimonial.text }}"
          </p>
          <!-- Author -->
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-secondary/20 flex items-center justify-center flex-shrink-0">
              <span class="font-display font-medium text-secondary text-sm">{{ testimonial.name[0] }}</span>
            </div>
            <div>
              <p class="text-sm font-medium text-primary">{{ testimonial.name }}</p>
              <p class="text-xs text-muted">{{ testimonial.location }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Demo data notice — visible in source code only -->
      <!-- DEMO: ces témoignages sont des données de démonstration fictives.
           Cette section est prête pour une future API de reviews. -->
    </div>
  </section>
</template>

<script setup>
import { Star } from '@lucide/vue'

// NOTE: Demo testimonials — not real customer data
// Ready to be replaced by a reviews API when available
const testimonials = [
  {
    name: 'Sana M.',
    location: 'Tunis',
    text: 'Un service impeccable et des produits à la hauteur de mes attentes. Le canapé que j\'ai commandé est magnifique et la livraison était rapide.',
  },
  {
    name: 'Hedi B.',
    location: 'Sfax',
    text: 'Je recommande vivement Zinet Eddar. La table de salle à manger est vraiment de belle qualité. Le paiement à la livraison m\'a mis en confiance.',
  },
  {
    name: 'Rania K.',
    location: 'Sousse',
    text: 'Une expérience d\'achat très agréable. Le service client est à l\'écoute et les meubles sont exactement comme sur les photos.',
  },
]
</script>
