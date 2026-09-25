<template>
  <div class="flex flex-col items-center justify-center py-16 px-6 text-center animate-fade-in">
    <div class="w-20 h-20 rounded-full bg-red-50 flex items-center justify-center mb-6">
      <AlertCircle :size="36" class="text-red-400" />
    </div>
    <h3 class="font-display text-2xl font-medium text-primary mb-2">Une erreur est survenue</h3>
    <p class="text-muted text-sm max-w-xs mb-6">{{ message }}</p>
    <button v-if="showRetry" @click="$emit('retry')" class="btn-outline btn-sm">
      <RefreshCw :size="14" /> Réessayer
    </button>
  </div>
</template>

<script setup>
import { AlertCircle, RefreshCw } from '@lucide/vue'

defineProps({
  message: { type: String, default: 'Impossible de charger les données. Vérifiez votre connexion.' },
  showRetry: { type: Boolean, default: true },
})
defineEmits(['retry'])
</script>
