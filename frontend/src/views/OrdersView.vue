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
        <h1 class="section-title">Mes commandes</h1>
      </div>
    </div>

    <div class="container-site py-10 max-w-3xl">
      <!-- Loading -->
      <div v-if="orderStore.loading" class="space-y-4">
        <div v-for="n in 4" :key="n" class="h-20 skeleton rounded-lg"></div>
      </div>

      <!-- Error -->
      <ErrorState v-else-if="orderStore.error" :message="orderStore.error" @retry="loadOrders" />

      <!-- Empty -->
      <EmptyState
        v-else-if="orderStore.orders.length === 0"
        icon="package"
        title="Aucune commande"
        description="Vous n'avez pas encore passé de commande."
      >
        <template #action>
          <RouterLink :to="{ name: 'shop' }" class="btn-primary mt-6">Découvrir la boutique</RouterLink>
        </template>
      </EmptyState>

      <!-- Orders list -->
      <div v-else class="space-y-3">
        <OrderCard v-for="order in orderStore.orders" :key="order.id" :order="order" />
        <Pagination
          v-if="orderStore.pagination"
          :current-page="orderStore.pagination.current_page"
          :last-page="orderStore.pagination.last_page"
          @change="onPageChange"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useOrderStore } from '@/stores/orderStore'
import OrderCard   from '@/components/orders/OrderCard.vue'
import EmptyState  from '@/components/common/EmptyState.vue'
import ErrorState  from '@/components/common/ErrorState.vue'
import Pagination  from '@/components/common/Pagination.vue'
import Breadcrumb  from '@/components/common/Breadcrumb.vue'

const orderStore = useOrderStore()

async function loadOrders(page = 1) {
  await orderStore.fetchOrders({ page, per_page: 10 })
}

function onPageChange(page) {
  loadOrders(page)
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => loadOrders())
</script>
