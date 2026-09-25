<template>
  <div class="min-h-screen bg-background flex items-center justify-center px-4 py-20">
    <div class="text-center max-w-md animate-scale-in">
      <!-- 404 display -->
      <p class="font-display text-8xl font-light text-secondary mb-4">404</p>
      <h1 class="font-display text-2xl font-medium text-primary mb-3">Page introuvable</h1>
      <p class="text-muted text-sm mb-8">
        La page que vous cherchez n'existe pas ou a été déplacée.
      </p>
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <RouterLink :to="{ name: 'home' }" class="btn-primary" id="404-home-btn">
          <Home :size="16" /> Accueil
        </RouterLink>
        <RouterLink :to="{ name: 'shop' }" class="btn-outline" id="404-shop-btn">
          Voir la boutique
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Home } from '@lucide/vue'
</script>
