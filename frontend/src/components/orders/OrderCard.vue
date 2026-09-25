<template>
  <RouterLink
    :to="{ name: 'order-detail', params: { id: order.id } }"
    class="block bg-surface border border-border/50 rounded-lg p-5 hover:shadow-warm hover:-translate-y-0.5 transition-all duration-200"
  >
    <div class="flex items-start justify-between gap-3 flex-wrap">
      <div>
        <p class="text-xs text-muted mb-1">Commande #{{ order.id }}</p>
        <p class="font-medium text-primary text-sm">{{ formatDate(order.created_at) }}</p>
      </div>
      <OrderStatusBadge :statut="order.statut" />
    </div>

    <div class="mt-3 text-sm text-muted">
      <span>{{ order.details?.length || 0 }} article{{ (order.details?.length || 0) > 1 ? 's' : '' }}</span>
      <span class="mx-2">·</span>
      <span class="font-semibold text-primary">{{ formatPrice(order.montant_total) }}</span>
    </div>
  </RouterLink>
</template>

<script setup>
import { formatDate }  from '@/utils/helpers'
import { formatPrice } from '@/composables/useFormatPrice'
import OrderStatusBadge from './OrderStatusBadge.vue'

defineProps({ order: { type: Object, required: true } })
</script>
