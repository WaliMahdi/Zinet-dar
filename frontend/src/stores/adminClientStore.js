import { defineStore } from 'pinia'
import { ref } from 'vue'
import { adminClientService } from '@/services/adminClientService'

export const useAdminClientStore = defineStore('adminClient', () => {
  const clients = ref([])
  const currentClient = ref(null)
  const pagination = ref(null)
  const loading = ref(false)
  const error = ref(null)

  async function fetchClients(params = { page: 1 }) {
    loading.value = true
    error.value = null
    try {
      const data = await adminClientService.getAll(params)
      clients.value = data.data.data
      pagination.value = {
        current_page: data.data.current_page,
        last_page: data.data.last_page,
        total: data.data.total,
        per_page: data.data.per_page,
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors du chargement des clients.'
    } finally {
      loading.value = false
    }
  }

  async function fetchClient(id) {
    loading.value = true
    error.value = null
    try {
      const data = await adminClientService.getById(id)
      currentClient.value = data.data
      return data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors du chargement du client.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function deleteClient(id) {
    loading.value = true
    error.value = null
    try {
      await adminClientService.delete(id)
      clients.value = clients.value.filter(c => c.id !== id)
      if (currentClient.value?.id === id) {
        currentClient.value = null
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la suppression.'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    clients,
    currentClient,
    pagination,
    loading,
    error,
    fetchClients,
    fetchClient,
    deleteClient
  }
})
