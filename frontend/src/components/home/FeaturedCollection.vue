<template>
  <section class="py-16 md:py-24 bg-background">
    <div class="container-site">
      <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
        <!-- Images editorial composition -->
        <div class="grid grid-cols-2 gap-3 h-96 md:h-[520px]">
          <!-- Large left image -->
          <div class="row-span-2 rounded-lg overflow-hidden bg-gradient-to-br from-primary/20 to-secondary/20 relative group">
            <img
              src="/hero-living-room.jpg"
              alt="Collection phare Zinet Eddar"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-warm"
            />
            <div class="absolute inset-0 bg-primary/20" />
          </div>
          <!-- Top right -->
          <div class="rounded-lg overflow-hidden" style="background: linear-gradient(135deg, #EEE8E0, #D8CFC5);">
            <div class="w-full h-full flex items-center justify-center p-8">
              <div class="text-center">
                <p class="font-display text-4xl font-light text-primary/30">2026</p>
                <p class="text-muted text-xs tracking-widest uppercase mt-1">Collection</p>
              </div>
            </div>
          </div>
          <!-- Bottom right -->
          <div class="rounded-lg overflow-hidden bg-primary/5 relative">
            <div class="absolute inset-0 flex items-end p-4">
              <p class="font-display italic text-xl text-primary/40">"L'art de vivre<br/>commence chez soi"</p>
            </div>
          </div>
        </div>

        <!-- Text content -->
        <div>
          <p class="section-eyebrow">À la une</p>
          <h2 class="section-title mb-6">L'art de vivre<br/><em class="font-light not-italic">commence chez soi</em></h2>
          <p class="text-muted text-base leading-relaxed mb-4">
            Chaque pièce de mobilier raconte une histoire. Notre collection est le fruit d'une sélection rigoureuse
            de créateurs alliant esthétique contemporaine et artisanat de qualité.
          </p>
          <p class="text-muted text-base leading-relaxed mb-8">
            Des lignes épurées, des matières nobles, des finitions soignées — pour un intérieur à votre image.
          </p>
          <div class="flex flex-wrap gap-4">
            <RouterLink :to="{ name: 'shop' }" class="btn-primary" id="featured-cta">
              Explorer la collection <ArrowRight :size="16" />
            </RouterLink>
            <RouterLink :to="{ name: 'about' }" class="btn-ghost">
              Notre histoire
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ArrowRight } from '@lucide/vue'
</script>
