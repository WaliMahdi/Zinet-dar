<template>
  <div class="min-h-screen bg-background">
    <!-- Loading -->
    <div v-if="loading" class="container-site py-16">
      <div class="grid md:grid-cols-2 gap-10 lg:gap-16">
        <div class="aspect-[4/3] skeleton rounded-lg"></div>
        <div class="space-y-4">
          <div class="h-6 skeleton rounded w-1/3"></div>
          <div class="h-10 skeleton rounded w-3/4"></div>
          <div class="h-4 skeleton rounded w-1/2"></div>
          <div class="h-24 skeleton rounded"></div>
          <div class="h-12 skeleton rounded"></div>
        </div>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="container-site py-16">
      <ErrorState :message="error" @retry="loadProduct">
        <template #action>
          <RouterLink :to="{ name: 'shop' }" class="btn-outline btn-sm mt-4">Retour à la boutique</RouterLink>
        </template>
      </ErrorState>
    </div>

    <!-- Product detail -->
    <template v-else-if="product">
      <!-- Breadcrumb -->
      <div class="bg-surface border-b border-border py-4">
        <div class="container-site">
          <Breadcrumb :items="breadcrumbItems" />
        </div>
      </div>

      <div class="container-site py-10 md:py-16">
        <div class="grid md:grid-cols-2 gap-10 lg:gap-16">
          <!-- Gallery -->
          <ProductGallery :product="product" />

          <!-- Info -->
          <div class="flex flex-col">
            <!-- Brand & Reference -->
            <div class="flex items-center gap-3 flex-wrap mb-2">
              <span v-if="product.marque" class="text-xs text-muted uppercase tracking-widest">{{ product.marque }}</span>
              <span v-if="product.reference" class="text-xs text-muted">Réf. {{ product.reference }}</span>
            </div>

            <!-- Name -->
            <h1 class="font-display text-3xl md:text-4xl font-medium text-primary mb-4">{{ product.nom }}</h1>

            <!-- Badges -->
            <div class="flex gap-2 flex-wrap mb-5">
              <span v-if="product.nouveau" class="badge-new">Nouveau</span>
              <span v-if="product.remise > 0" class="badge-promo">-{{ Math.round(product.remise) }}%</span>
              <span v-if="product.quantite_stock === 0" class="badge bg-red-100 text-red-700">Épuisé</span>
            </div>

            <!-- Price -->
            <div class="flex items-baseline gap-3 mb-6">
              <span v-if="product.remise > 0" class="price-original text-lg">{{ formatPrice(product.prix) }}</span>
              <span :class="product.remise > 0 ? 'price-discount text-3xl' : 'price-current text-3xl'">
                {{ formatPrice(product.remise > 0 ? product.prix_apres_remise : product.prix) }}
              </span>
            </div>

            <!-- Stock -->
            <div class="flex items-center gap-2 mb-6">
              <div :class="product.quantite_stock > 0 ? 'bg-green-500' : 'bg-red-400'" class="w-2 h-2 rounded-full flex-shrink-0"></div>
              <span class="text-sm text-muted">
                {{ product.quantite_stock > 0
                  ? `En stock${product.quantite_stock <= 5 ? ` — Plus que ${product.quantite_stock}` : ''}`
                  : 'Rupture de stock' }}
              </span>
            </div>

            <!-- Quantity + Add to cart -->
            <div v-if="product.quantite_stock > 0" class="flex gap-3 mb-6 flex-wrap">
              <QuantitySelector
                v-model="quantity"
                :max="product.quantite_stock"
              />
              <button
                @click="handleAddToCart"
                :disabled="isAdding"
                class="btn-primary flex-1"
                id="product-add-cart-btn"
              >
                <Loader2 v-if="isAdding" :size="16" class="animate-spin" />
                <ShoppingBag v-else :size="16" />
                {{ isAdding ? 'Ajout en cours…' : 'Ajouter au panier' }}
              </button>
            </div>

            <div v-else class="mb-6">
              <p class="text-sm text-red-500 font-medium">Ce produit n'est pas disponible actuellement.</p>
            </div>

            <!-- Description -->
            <div v-if="product.description" class="border-t border-border pt-6 mb-5">
              <h3 class="font-display text-base font-medium text-primary mb-3">Description</h3>
              <p class="text-muted text-sm leading-relaxed whitespace-pre-line">{{ product.description }}</p>
            </div>

            <!-- Characteristics -->
            <div v-if="product.caracteristiques" class="mb-5">
              <h3 class="font-display text-base font-medium text-primary mb-3">Caractéristiques</h3>
              <p class="text-muted text-sm leading-relaxed whitespace-pre-line">{{ product.caracteristiques }}</p>
            </div>

            <!-- Warranty -->
            <div v-if="product.garantie" class="flex items-center gap-2 text-sm text-muted mb-5">
              <Shield :size="15" class="text-secondary flex-shrink-0" />
              <span>Garantie : {{ product.garantie }}</span>
            </div>

            <!-- Delivery info -->
            <div class="bg-background rounded-lg p-4 space-y-2.5">
              <div class="flex items-center gap-3 text-sm text-muted">
                <Truck :size="15" class="text-secondary flex-shrink-0" />
                <span>Livraison partout en Tunisie</span>
              </div>
              <div class="flex items-center gap-3 text-sm text-muted">
                <Banknote :size="15" class="text-secondary flex-shrink-0" />
                <span>Paiement en espèces à la livraison</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Related products -->
        <section v-if="relatedProducts.length > 0" class="mt-20">
          <h2 class="font-display text-2xl font-medium text-primary mb-8">Vous aimerez aussi</h2>
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <ProductCard v-for="p in relatedProducts" :key="p.id" :product="p" />
          </div>
        </section>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { ChevronRight, ShoppingBag, Shield, Truck, Banknote, Loader2 } from '@lucide/vue'
import { produitService } from '@/services/produitService'
import { useCartStore }   from '@/stores/cartStore'
import { useAuthStore }   from '@/stores/authStore'
import { useToast }       from '@/composables/useToast'
import { formatPrice }    from '@/composables/useFormatPrice'
import ProductGallery  from '@/components/products/ProductGallery.vue'
import ProductCard     from '@/components/products/ProductCard.vue'
import QuantitySelector from '@/components/common/QuantitySelector.vue'
import ErrorState      from '@/components/common/ErrorState.vue'
import Breadcrumb      from '@/components/common/Breadcrumb.vue'

const route     = useRoute()
const router    = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()
const toast     = useToast()

const product        = ref(null)
const relatedProducts= ref([])
const loading        = ref(true)
const error          = ref(null)
const quantity       = ref(1)
const isAdding       = ref(false)

const breadcrumbItems = computed(() => {
  const items = []
  items.push({ label: 'Boutique', to: { name: 'shop' } })
  if (product.value?.categorie) {
    items.push({ 
      label: product.value.categorie.nom, 
      to: { name: 'shop', query: { categorie_id: product.value.categorie_id } } 
    })
  }
  items.push({ label: product.value?.nom || 'Chargement...' })
  return items
})

async function loadProduct() {
  loading.value  = true
  error.value    = null
  product.value  = null
  quantity.value = 1
  try {
    const data = await produitService.getById(route.params.id)
    product.value = data.data
    // Load related products from same category
    if (product.value?.categorie_id) {
      const rel = await produitService.getAll({
        categorie_id: product.value.categorie_id,
        per_page: 4,
      })
      relatedProducts.value = (rel.data || []).filter(p => p.id !== product.value.id).slice(0, 4)
    }
  } catch (err) {
    if (err.response?.status === 404) {
      error.value = 'Ce produit n\'existe pas ou n\'est plus disponible.'
    } else {
      error.value = 'Impossible de charger le produit.'
    }
  } finally {
    loading.value = false
  }
}

async function handleAddToCart() {
  if (!authStore.isAuthenticated) {
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  isAdding.value = true
  const result = await cartStore.addToCart(product.value.id, quantity.value)
  isAdding.value = false
  if (result?.success) {
    toast.success('Produit ajouté au panier !')
    cartStore.openDrawer()
  } else {
    toast.error(result?.message || 'Impossible d\'ajouter au panier.')
  }
}

onMounted(loadProduct)
watch(() => route.params.id, loadProduct)
</script>
