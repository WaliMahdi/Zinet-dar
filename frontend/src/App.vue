<template>
  <RouterView v-slot="{ Component, route }">
    <Transition name="page" mode="out-in">
      <component :is="Component" :key="route.path" />
    </Transition>
  </RouterView>
  <CartDrawer />
</template>

<script setup>
import { onMounted } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useCartStore } from '@/stores/cartStore'
import CartDrawer from '@/components/layout/CartDrawer.vue'

const authStore = useAuthStore()
const cartStore = useCartStore()

onMounted(async () => {
  // Restore auth session and fetch cart
  if (authStore.isAuthenticated) {
    await authStore.fetchCurrentUser()
    await cartStore.fetchCart()
  }
})
</script>
