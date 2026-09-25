<template>
  <div class="min-h-screen bg-background flex items-center justify-center py-16 px-4">
    <div class="max-w-md w-full text-center animate-scale-in">
      <!-- Success icon -->
      <div class="w-20 h-20 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
        <CheckCircle :size="40" class="text-green-600" />
      </div>

      <h1 class="font-display text-3xl font-medium text-primary mb-3">Commande confirmée !</h1>
      <p class="text-muted text-base mb-2">Merci pour votre achat.</p>
      <p v-if="orderId" class="text-sm text-muted mb-8">
        Numéro de commande :
        <span class="font-semibold text-primary">#{{ orderId }}</span>
      </p>
      <p class="text-muted text-sm mb-8 max-w-xs mx-auto leading-relaxed">
        Vous serez contacté(e) prochainement pour confirmer les détails de votre livraison.
      </p>

      <!-- Payment info -->
      <div class="bg-surface border border-border rounded-lg p-4 mb-8 flex items-center gap-3 text-left">
        <Banknote :size="22" class="text-secondary flex-shrink-0" />
        <div>
          <p class="text-sm font-medium text-primary">Paiement à la livraison</p>
          <p class="text-xs text-muted">Préparez le montant en espèces lors de la réception.</p>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex flex-col sm:flex-row gap-3 justify-center">
        <RouterLink
          v-if="orderId"
          :to="{ name: 'order-detail', params: { id: orderId } }"
          class="btn-primary"
          id="success-view-order"
        >
          Voir ma commande
        </RouterLink>
        <RouterLink :to="{ name: 'orders' }" class="btn-outline" id="success-my-orders">
          Mes commandes
        </RouterLink>
        <RouterLink :to="{ name: 'shop' }" class="btn-ghost text-muted">
          Continuer mes achats
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { CheckCircle, Banknote } from '@lucide/vue'

const route   = useRoute()
const orderId = computed(() => route.query.id || null)
</script>
