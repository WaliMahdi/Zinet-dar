<template>
  <!-- Overlay -->
  <Transition name="fade">
    <div
      v-if="cartStore.isDrawerOpen"
      @click="cartStore.closeDrawer()"
      class="fixed inset-0 bg-primary/50 backdrop-blur-sm z-50"
    />
  </Transition>

  <!-- Drawer -->
  <Transition name="slide-left">
    <aside
      v-if="cartStore.isDrawerOpen"
      class="fixed top-0 right-0 h-full w-full sm:w-96 bg-surface z-50 shadow-warm-xl flex flex-col"
      role="dialog"
      aria-modal="true"
      aria-label="Panier"
    >
      <!-- Header -->
      <div class="flex items-center justify-between px-5 py-4 border-b border-border bg-surface">
        <div class="flex items-center gap-2">
          <ShoppingBag :size="18" class="text-secondary" />
          <h2 class="font-display text-lg font-medium text-primary">Mon panier</h2>
          <span
            v-if="cartStore.totalItems > 0"
            class="badge bg-secondary/10 text-secondary text-xs px-2"
          >
            {{ cartStore.totalItems }} article{{ cartStore.totalItems > 1 ? 's' : '' }}
          </span>
        </div>
        <button @click="cartStore.closeDrawer()" class="btn-icon text-muted" aria-label="Fermer le panier">
          <X :size="18" />
        </button>
      </div>

      <!-- Loading overlay -->
      <div v-if="cartStore.loading" class="absolute inset-0 bg-surface/80 backdrop-blur-sm z-10 flex items-center justify-center">
        <Loader2 :size="32" class="text-secondary animate-spin" />
      </div>

      <!-- Empty state -->
      <div v-if="cartStore.isEmpty && !cartStore.loading" class="flex-1 flex flex-col items-center justify-center px-6 text-center">
        <div class="w-16 h-16 rounded-full bg-secondary/10 flex items-center justify-center mb-4">
          <ShoppingBag :size="28" class="text-secondary/60" />
        </div>
        <h3 class="font-display text-xl font-medium text-primary mb-2">Votre panier est vide</h3>
        <p class="text-muted text-sm mb-6">Découvrez notre collection et ajoutez des articles à votre panier.</p>
        <RouterLink :to="{ name: 'shop' }" @click="cartStore.closeDrawer()" class="btn-primary btn-sm">
          Voir la boutique
        </RouterLink>
      </div>

      <!-- Cart items -->
      <div v-else class="flex-1 overflow-y-auto py-2">
        <CartItem
          v-for="item in cartStore.items"
          :key="item.id"
          :item="item"
          class="border-b border-border/50 last:border-0"
        />
      </div>

      <!-- Footer summary -->
      <div v-if="!cartStore.isEmpty" class="border-t border-border p-5 space-y-4 bg-surface">
        <!-- Totals -->
        <div class="space-y-2">
          <div class="flex justify-between text-sm text-muted">
            <span>Sous-total</span>
            <span>{{ formatPrice(cartStore.sousTotal) }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm font-semibold text-primary">Total</span>
            <span class="font-display text-xl font-medium text-primary">{{ formatPrice(cartStore.totalAmount) }}</span>
          </div>
          <p class="text-xs text-muted flex items-center gap-1">
            <Truck :size="12" /> Livraison gratuite partout en Tunisie
          </p>
        </div>

        <!-- CTA -->
        <div class="grid gap-2">
          <RouterLink
            :to="{ name: 'checkout' }"
            @click="cartStore.closeDrawer()"
            class="btn-primary w-full"
            id="checkout-btn-drawer"
          >
            Commander — {{ formatPrice(cartStore.totalAmount) }}
          </RouterLink>
          <RouterLink
            :to="{ name: 'cart' }"
            @click="cartStore.closeDrawer()"
            class="btn-outline w-full btn-sm"
          >
            Voir le panier
          </RouterLink>
        </div>

        <!-- Payment badge -->
        <div class="flex items-center justify-center gap-2 text-xs text-muted">
          <Banknote :size="14" />
          <span>Paiement en espèces à la livraison</span>
        </div>
      </div>
    </aside>
  </Transition>
</template>

<script setup>
import { ShoppingBag, X, Loader2, Truck, Banknote } from '@lucide/vue'
import { useCartStore } from '@/stores/cartStore'
import { formatPrice }  from '@/composables/useFormatPrice'
import CartItem from '@/components/cart/CartItem.vue'

const cartStore = useCartStore()
</script>
