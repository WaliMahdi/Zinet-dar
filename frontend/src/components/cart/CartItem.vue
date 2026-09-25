<template>
  <div class="flex gap-3 p-4 group">
    <!-- Image placeholder (cart items don't include image from API) -->
    <div class="w-16 h-20 rounded-sm bg-background flex-shrink-0 overflow-hidden">
      <img
        :src="productImage"
        :alt="item.nom"
        class="w-full h-full object-cover"
        @error="$event.target.src = placeholder"
      />
    </div>

    <!-- Info -->
    <div class="flex-1 min-w-0">
      <h4 class="text-sm font-medium text-primary leading-snug line-clamp-2 mb-1">{{ item.nom }}</h4>
      <p class="text-xs text-muted mb-2">{{ formatPrice(item.prix_unitaire) }}</p>

      <div class="flex items-center justify-between">
        <!-- Quantity -->
        <QuantitySelector
          :model-value="item.quantite"
          :min="1"
          :max="99"
          :disabled="isUpdating"
          @update:model-value="handleQuantityChange"
          class="scale-90 origin-left"
        />
        <!-- Line total -->
        <span class="text-sm font-semibold text-primary ml-2">{{ formatPrice(item.montant) }}</span>
      </div>
    </div>

    <!-- Remove -->
    <button
      @click="handleRemove"
      :disabled="isRemoving"
      class="opacity-0 group-hover:opacity-100 p-1 text-muted hover:text-red-500 transition-all mt-0.5"
      aria-label="Supprimer"
    >
      <Loader2 v-if="isRemoving" :size="14" class="animate-spin" />
      <Trash2 v-else :size="14" />
    </button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Trash2, Loader2 } from '@lucide/vue'
import { useCartStore } from '@/stores/cartStore'
import { useToast }     from '@/composables/useToast'
import { formatPrice }  from '@/composables/useFormatPrice'
import { getProductImage } from '@/composables/useProductImage'
import QuantitySelector from '@/components/common/QuantitySelector.vue'
import productPlaceholder from '@/assets/images/product-placeholder.jpg'

const props = defineProps({
  item: { type: Object, required: true },
})

const cartStore = useCartStore()
const toast     = useToast()
const placeholder = productPlaceholder

const productImage = computed(() => getProductImage(props.item))

const isUpdating = ref(false)
const isRemoving = ref(false)

async function handleQuantityChange(newQty) {
  isUpdating.value = true
  try {
    const data = await cartStore.updateQuantity(props.item.produit_id, newQty)
    if (!data?.success) toast.error(data?.message || 'Erreur de mise à jour.')
  } catch (err) {
    toast.error(err.response?.data?.message || 'Stock insuffisant.')
  } finally {
    isUpdating.value = false
  }
}

async function handleRemove() {
  isRemoving.value = true
  try {
    await cartStore.removeFromCart(props.item.produit_id)
    toast.info('Article retiré du panier.')
  } catch {
    toast.error('Impossible de retirer l\'article.')
  } finally {
    isRemoving.value = false
  }
}
</script>
