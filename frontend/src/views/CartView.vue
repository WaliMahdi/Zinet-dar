<template>
  <div class="min-h-screen bg-background">
    <!-- Breadcrumb -->
    <div class="bg-surface py-4 border-b border-border">
      <div class="container-site">
        <Breadcrumb />
      </div>
    </div>
    <div class="bg-surface border-b border-border py-10">
      <div class="container-site">
        <h1 class="section-title">Mon panier</h1>
      </div>
    </div>

    <div class="container-site py-10">
      <!-- Loading -->
      <div v-if="cartStore.loading && cartStore.isEmpty" class="grid md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-4">
          <div v-for="n in 3" :key="n" class="h-24 skeleton rounded-lg"></div>
        </div>
        <div class="h-64 skeleton rounded-lg"></div>
      </div>

      <!-- Empty -->
      <EmptyState
        v-else-if="cartStore.isEmpty"
        icon="cart"
        title="Votre panier est vide"
        description="Explorez notre boutique et ajoutez des articles à votre panier."
      >
        <template #action>
          <RouterLink :to="{ name: 'shop' }" class="btn-primary mt-6">
            Découvrir la boutique
          </RouterLink>
        </template>
      </EmptyState>

      <!-- Cart content -->
      <div v-else class="grid lg:grid-cols-3 gap-8 items-start">
        <!-- Items -->
        <div class="lg:col-span-2">
          <div class="bg-surface rounded-lg border border-border/50 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-border">
              <h2 class="font-medium text-primary text-sm">
                {{ cartStore.totalItems }} article{{ cartStore.totalItems > 1 ? 's' : '' }}
              </h2>
              <button
                @click="handleClearCart"
                :disabled="isClearing"
                class="text-xs text-red-500 hover:text-red-700 flex items-center gap-1 transition-colors"
              >
                <Trash2 :size="12" />
                Vider le panier
              </button>
            </div>
            <CartItem
              v-for="item in cartStore.items"
              :key="item.id"
              :item="item"
              class="border-b border-border/50 last:border-0"
            />
          </div>

          <!-- Continue shopping -->
          <div class="mt-4">
            <RouterLink :to="{ name: 'shop' }" class="btn-ghost btn-sm text-muted">
              <ArrowLeft :size="14" /> Continuer mes achats
            </RouterLink>
          </div>
        </div>

        <!-- Summary -->
        <CartSummary
          :items="cartStore.items"
          :sous-total="cartStore.sousTotal"
          :montant-total="cartStore.totalAmount"
        >
          <template #action>
            <RouterLink :to="{ name: 'checkout' }" class="btn-primary w-full" id="cart-checkout-btn">
              Passer la commande
            </RouterLink>
          </template>
        </CartSummary>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Trash2, ArrowLeft } from '@lucide/vue'
import { useCartStore } from '@/stores/cartStore'
import { useToast }     from '@/composables/useToast'
import CartItem    from '@/components/cart/CartItem.vue'
import CartSummary from '@/components/cart/CartSummary.vue'
import EmptyState  from '@/components/common/EmptyState.vue'
import Breadcrumb  from '@/components/common/Breadcrumb.vue'

const cartStore = useCartStore()
const toast     = useToast()
const isClearing= ref(false)

async function handleClearCart() {
  if (!confirm('Vider le panier ? Cette action est irréversible.')) return
  isClearing.value = true
  try {
    await cartStore.clearCart()
    toast.info('Panier vidé.')
  } catch {
    toast.error('Impossible de vider le panier.')
  } finally {
    isClearing.value = false
  }
}

onMounted(() => { if (!cartStore.isEmpty) cartStore.fetchCart() })
</script>
