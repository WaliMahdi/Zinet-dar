<template>
  <div class="min-h-screen bg-background">
    <!-- Loading -->
    <div v-if="orderStore.loading" class="container-site py-16">
      <div class="h-8 skeleton rounded w-48 mb-6"></div>
      <div class="space-y-3">
        <div v-for="n in 4" :key="n" class="h-16 skeleton rounded-lg"></div>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="orderStore.error" class="container-site py-16">
      <ErrorState :message="orderStore.error" @retry="loadOrder">
        <template #action>
          <RouterLink :to="{ name: 'orders' }" class="btn-outline btn-sm mt-4">Mes commandes</RouterLink>
        </template>
      </ErrorState>
    </div>

    <template v-else-if="order">
      <!-- Header -->
      <div class="bg-surface border-b border-border py-4">
        <div class="container-site">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
      </div>
      <div class="bg-surface border-b border-border py-8">
        <div class="container-site">
          <div class="flex items-start justify-between flex-wrap gap-3">
            <div>
              <h1 class="section-title text-2xl">Commande #{{ order.id }}</h1>
              <p class="text-muted text-sm mt-1">{{ formatDate(order.created_at) }}</p>
            </div>
            <OrderStatusBadge :statut="order.statut" />
          </div>
        </div>
      </div>

      <div class="container-site py-10">
        <div class="grid lg:grid-cols-3 gap-8 items-start">

          <!-- Products -->
          <div class="lg:col-span-2 space-y-4">
            <div class="bg-surface rounded-lg border border-border/50 overflow-hidden">
              <div class="px-5 py-4 border-b border-border">
                <h2 class="font-medium text-primary text-sm">Articles commandés</h2>
              </div>
              <div class="divide-y divide-border/50">
                <div v-for="detail in order.details" :key="detail.id" class="flex gap-4 p-5">
                  <div class="w-14 h-14 rounded-sm bg-background flex-shrink-0 flex items-center justify-center overflow-hidden">
                    <img
                      v-if="detail.produit && getProductImage(detail.produit) !== placeholder"
                      :src="getProductImage(detail.produit)"
                      :alt="detail.nom_produit"
                      class="w-full h-full object-cover"
                      @error="$event.target.src = placeholder"
                    />
                    <Package v-else :size="20" class="text-muted" />
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-primary">{{ detail.nom_produit }}</p>
                    <p v-if="detail.reference_produit" class="text-xs text-muted">Réf. {{ detail.reference_produit }}</p>
                    <div class="flex items-center justify-between mt-1">
                      <p class="text-xs text-muted">{{ formatPrice(detail.prix_unitaire) }} × {{ detail.quantite }}</p>
                      <p class="text-sm font-semibold text-primary">{{ formatPrice(detail.montant) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Order summary -->
          <div class="space-y-4">
            <!-- Totals -->
            <div class="bg-surface rounded-lg border border-border/50 p-5">
              <h3 class="font-display text-base font-medium text-primary mb-4">Récapitulatif</h3>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between text-muted">
                  <span>Sous-total</span>
                  <span>{{ formatPrice(order.sous_total) }}</span>
                </div>
                <div class="flex justify-between font-semibold text-primary pt-2 border-t border-border">
                  <span>Total</span>
                  <span class="font-display text-lg">{{ formatPrice(order.montant_total) }}</span>
                </div>
              </div>
              <div class="mt-3 flex items-center gap-2 text-xs text-muted">
                <Banknote :size="13" class="text-secondary" />
                <span>Paiement en espèces à la livraison</span>
              </div>
            </div>

            <!-- Delivery info -->
            <div class="bg-surface rounded-lg border border-border/50 p-5">
              <h3 class="font-display text-base font-medium text-primary mb-4">Livraison</h3>
              <div class="space-y-2 text-sm text-muted">
                <div class="flex items-start gap-2">
                  <User :size="14" class="flex-shrink-0 mt-0.5" />
                  <span>{{ order.nom_client }}</span>
                </div>
                <div class="flex items-start gap-2">
                  <Phone :size="14" class="flex-shrink-0 mt-0.5" />
                  <span>{{ order.telephone }}</span>
                </div>
                <div class="flex items-start gap-2">
                  <MapPin :size="14" class="flex-shrink-0 mt-0.5" />
                  <span>{{ order.adresse }}</span>
                </div>
                <div v-if="order.note_client" class="flex items-start gap-2 pt-2 border-t border-border">
                  <MessageSquare :size="14" class="flex-shrink-0 mt-0.5" />
                  <span>{{ order.note_client }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ChevronRight, Package, Banknote, User, Phone, MapPin, MessageSquare } from '@lucide/vue'
import { useOrderStore } from '@/stores/orderStore'
import { formatPrice }   from '@/composables/useFormatPrice'
import { formatDate }    from '@/utils/helpers'
import { getProductImage } from '@/composables/useProductImage'
import OrderStatusBadge from '@/components/orders/OrderStatusBadge.vue'
import ErrorState       from '@/components/common/ErrorState.vue'
import Breadcrumb       from '@/components/common/Breadcrumb.vue'
import productPlaceholder from '@/assets/images/product-placeholder.jpg'

const route      = useRoute()
const orderStore = useOrderStore()
const order      = computed(() => orderStore.currentOrder)
const placeholder = productPlaceholder

const breadcrumbItems = computed(() => {
  const items = []
  items.push({ label: 'Mes commandes', to: { name: 'orders' } })
  items.push({ label: order.value ? `#${order.value.id}` : 'Chargement...' })
  return items
})

async function loadOrder() {
  await orderStore.fetchOrder(route.params.id)
}

onMounted(loadOrder)
</script>
