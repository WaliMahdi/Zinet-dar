<template>
  <div class="bg-surface rounded-lg border border-border/50 p-5 space-y-3 sticky top-24">
    <h3 class="font-display text-lg font-medium text-primary">Récapitulatif</h3>
    <div class="divider-warm" />

    <div class="space-y-2 text-sm">
      <div v-for="item in items" :key="item.id" class="flex justify-between text-muted">
        <span class="line-clamp-1 flex-1 mr-2">{{ item.nom }} × {{ item.quantite }}</span>
        <span class="flex-shrink-0">{{ formatPrice(item.montant) }}</span>
      </div>
    </div>

    <div class="divider-warm" />

    <div class="flex justify-between text-sm text-muted">
      <span>Sous-total</span>
      <span>{{ formatPrice(sousTotal) }}</span>
    </div>
    <div class="flex justify-between items-center">
      <span class="font-semibold text-primary">Total</span>
      <span class="font-display text-2xl font-medium text-primary">{{ formatPrice(montantTotal) }}</span>
    </div>

    <div class="flex items-center gap-2 text-xs text-muted bg-background rounded-sm px-3 py-2">
      <Truck :size="14" class="text-secondary flex-shrink-0" />
      <span>Livraison gratuite partout en Tunisie</span>
    </div>

    <slot name="action" />
  </div>
</template>

<script setup>
import { Truck } from '@lucide/vue'
import { formatPrice } from '@/composables/useFormatPrice'

defineProps({
  items:        { type: Array,  default: () => [] },
  sousTotal:    { type: [Number, String], default: 0 },
  montantTotal: { type: [Number, String], default: 0 },
})
</script>
