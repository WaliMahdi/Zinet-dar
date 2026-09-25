import { defineStore } from 'pinia'
import { ref } from 'vue'
import { commandeService } from '@/services/commandeService'

export const useOrderStore = defineStore('orders', () => {
  const orders      = ref([])
  const currentOrder= ref(null)
  const pagination  = ref(null)
  const loading     = ref(false)
  const error       = ref(null)

  async function fetchOrders(params = {}) {
    loading.value = true
    error.value   = null
    try {
      const data = await commandeService.getMesCommandes(params)
      // data.data.data = array of orders (paginated)
      orders.value    = data.data?.data || []
      pagination.value= {
        current_page:  data.data?.current_page,
        last_page:     data.data?.last_page,
        total:         data.data?.total,
        per_page:      data.data?.per_page,
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Impossible de charger les commandes.'
    } finally {
      loading.value = false
    }
  }

  async function fetchOrder(id) {
    loading.value = true
    error.value   = null
    try {
      const data = await commandeService.getById(id)
      currentOrder.value = data.data
      return data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Commande introuvable.'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    orders, currentOrder, pagination, loading, error,
    fetchOrders, fetchOrder,
  }
})
