<template>
  <section class="py-16 md:py-24 bg-background">
    <div class="container-site">
      <div class="flex items-end justify-between mb-10 flex-wrap gap-4">
        <div>
          <p class="section-eyebrow">Favoris</p>
          <h2 class="section-title">Les pièces préférées<br/><em class="font-light not-italic">de nos clients</em></h2>
        </div>
        <RouterLink :to="{ name: 'shop', query: { vedette: '1' } }" class="btn-outline btn-sm flex-shrink-0">
          Voir tout <ArrowRight :size="14" />
        </RouterLink>
      </div>

      <SkeletonCard v-if="loading" :count="4" />
      <ErrorState v-else-if="error" :message="error" @retry="loadProducts" />

      <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        <ProductCard v-for="product in products" :key="product.id" :product="product" />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ArrowRight } from '@lucide/vue'
import { produitService } from '@/services/produitService'
import ProductCard  from '@/components/products/ProductCard.vue'
import SkeletonCard from '@/components/common/SkeletonCard.vue'
import ErrorState   from '@/components/common/ErrorState.vue'

const products = ref([])
const loading  = ref(true)
const error    = ref(null)

async function loadProducts() {
  loading.value = true
  error.value   = null
  try {
    const data = await produitService.getAll({ vedette: true, per_page: 8 })
    // Fallback: if no vedette products, show latest
    if ((data.data || []).length === 0) {
      const fallback = await produitService.getAll({ per_page: 8, sort: 'created_at', order: 'desc' })
      products.value = fallback.data || []
    } else {
      products.value = data.data || []
    }
  } catch {
    error.value = 'Impossible de charger les produits vedettes.'
  } finally {
    loading.value = false
  }
}

onMounted(loadProducts)
</script>
