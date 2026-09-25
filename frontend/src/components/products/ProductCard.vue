<template>
  <article
    class="group relative bg-surface rounded-lg border border-border/50 overflow-hidden
           hover:shadow-warm hover:-translate-y-0.5 transition-all duration-300 ease-warm
           flex flex-col"
  >
    <!-- Image -->
    <RouterLink :to="{ name: 'product-detail', params: { id: product.id } }" class="block relative overflow-hidden aspect-[3/4] bg-background flex-shrink-0">
      <img
        :src="productImage"
        :alt="product.nom"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-warm"
        loading="lazy"
        @error="onImageError"
      />
      <!-- Badges -->
      <div class="absolute top-2.5 left-2.5 flex flex-col gap-1.5">
        <span v-if="product.nouveau" class="badge-new">Nouveau</span>
        <span v-if="discountLabel" class="badge-promo">{{ discountLabel }}</span>
      </div>
      <!-- Out of stock overlay -->
      <div v-if="product.quantite_stock === 0" class="absolute inset-0 bg-white/60 flex items-center justify-center">
        <span class="badge bg-primary/80 text-white px-3 py-1 text-xs uppercase tracking-widest">Épuisé</span>
      </div>
    </RouterLink>

    <!-- Content -->
    <div class="p-4 flex flex-col flex-1">
      <!-- Brand -->
      <p v-if="product.marque" class="text-2xs text-muted uppercase tracking-widest mb-1">{{ product.marque }}</p>
      <!-- Name -->
      <RouterLink :to="{ name: 'product-detail', params: { id: product.id } }">
        <h3 class="font-body text-sm font-medium text-primary leading-snug hover:text-secondary transition-colors line-clamp-2 mb-2">
          {{ product.nom }}
        </h3>
      </RouterLink>

      <!-- Price -->
      <div class="flex items-center gap-2 flex-wrap mt-auto pt-2">
        <span v-if="hasDiscount" class="price-original text-xs">{{ formatPrice(product.prix) }}</span>
        <span :class="hasDiscount ? 'price-discount text-base' : 'price-current text-base'">
          {{ formatPrice(effectivePrice) }}
        </span>
      </div>

      <!-- Stock -->
      <p v-if="product.quantite_stock > 0 && product.quantite_stock <= 5" class="badge-low mt-2 w-fit">
        Plus que {{ product.quantite_stock }} en stock
      </p>

      <!-- Add to cart -->
      <button
        v-if="product.quantite_stock > 0"
        @click.stop="handleAddToCart"
        :disabled="isAdding"
        class="btn-primary btn-sm w-full mt-3"
        :id="`add-cart-${product.id}`"
      >
        <Loader2 v-if="isAdding" :size="14" class="animate-spin" />
        <ShoppingBag v-else :size="14" />
        {{ isAdding ? 'Ajout…' : 'Ajouter au panier' }}
      </button>
      <RouterLink
        v-else
        :to="{ name: 'product-detail', params: { id: product.id } }"
        class="btn-outline btn-sm w-full mt-3 text-center"
      >
        Voir le produit
      </RouterLink>
    </div>
  </article>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { ShoppingBag, Loader2 } from '@lucide/vue'
import { useCartStore }      from '@/stores/cartStore'
import { useAuthStore }      from '@/stores/authStore'
import { useToast }          from '@/composables/useToast'
import { getProductImage }   from '@/composables/useProductImage'
import { formatPrice, formatDiscount } from '@/composables/useFormatPrice'
import productPlaceholder    from '@/assets/images/product-placeholder.jpg'

const props = defineProps({
  product: { type: Object, required: true },
})

const cartStore = useCartStore()
const authStore = useAuthStore()
const router    = useRouter()
const toast     = useToast()

const isAdding   = ref(false)
const imgSrc     = ref(getProductImage(props.product))

const productImage = computed(() => imgSrc.value)
const hasDiscount  = computed(() => props.product.remise > 0 && props.product.prix_apres_remise !== null)
const effectivePrice = computed(() => hasDiscount.value ? props.product.prix_apres_remise : props.product.prix)
const discountLabel  = computed(() => hasDiscount.value ? formatDiscount(props.product.remise) : null)

function onImageError() {
  imgSrc.value = productPlaceholder
}

async function handleAddToCart() {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: `/boutique/${props.product.id}` } })
    return
  }
  isAdding.value = true
  const result = await cartStore.addToCart(props.product.id, 1)
  isAdding.value = false
  if (result?.success) {
    toast.success('Produit ajouté au panier !')
    cartStore.openDrawer()
  } else if (!result?.needsAuth) {
    toast.error(result?.message || 'Erreur lors de l\'ajout.')
  }
}
</script>
