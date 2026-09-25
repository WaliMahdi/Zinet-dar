<template>
  <div class="min-h-screen bg-background flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <RouterLink :to="{ name: 'home' }" class="inline-flex flex-col items-center gap-1 group">
          <ZinetLogo class="h-12 w-auto" />
        </RouterLink>
      </div>

      <!-- Auth card -->
      <div class="bg-surface rounded-xl shadow-warm-lg border border-border/50 p-8 animate-scale-in">
        <RouterView />
      </div>

      <!-- Footer text -->
      <p class="text-center text-xs text-muted mt-6">
        © {{ new Date().getFullYear() }} Zinet Eddar. Tous droits réservés.
      </p>
    </div>
  </div>
</template>

<script setup>
import ZinetLogo from '@/components/common/ZinetLogo.vue'
</script>
