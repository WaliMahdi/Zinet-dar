<template>
  <div>
    <div
      v-if="products.length > 0"
      class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6"
    >
      <ProductCard
        v-for="product in products"
        :key="product.id"
        :product="product"
        class="animate-fade-in"
      />
    </div>
    <slot v-else name="empty">
      <EmptyState
        icon="search"
        title="Aucun produit disponible"
        description="Essayez de modifier vos filtres ou votre recherche."
      />
    </slot>
  </div>
</template>

<script setup>
import ProductCard from './ProductCard.vue'
import EmptyState  from '@/components/common/EmptyState.vue'

defineProps({
  products: { type: Array, default: () => [] },
})
</script>
