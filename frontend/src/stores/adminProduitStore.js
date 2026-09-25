import { defineStore } from 'pinia'
import { ref } from 'vue'
import { adminProduitService } from '@/services/adminProduitService'

export const useAdminProduitStore = defineStore('adminProduit', () => {
  const produits = ref([])
  const pagination = ref(null)
  const loading = ref(false)
  const error = ref(null)

  async function fetchProduits(params = { page: 1 }) {
    loading.value = true
    error.value = null
    try {
      const response = await adminProduitService.getAll(params)
      produits.value = response.data || []
      if (response.pagination) {
        pagination.value = {
          current_page: response.pagination.current_page,
          last_page: response.pagination.last_page,
          total: response.pagination.total,
          per_page: response.pagination.per_page,
        }
      } else {
        pagination.value = null
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors du chargement des produits.'
      produits.value = []
      pagination.value = null
    } finally {
      loading.value = false
    }
  }

  async function createProduit(data) {
    loading.value = true
    error.value = null
    try {
      return await adminProduitService.create(data)
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la création.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateProduit(id, data) {
    loading.value = true
    error.value = null
    try {
      return await adminProduitService.update(id, data)
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la modification.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function deleteProduit(id) {
    loading.value = true
    error.value = null
    try {
      await adminProduitService.delete(id)
      produits.value = produits.value.filter(p => p.id !== id)
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la suppression.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateStatut(id, actif) {
    loading.value = true
    error.value = null
    try {
      const data = await adminProduitService.updateStatut(id, actif)
      const index = produits.value.findIndex(p => p.id === id)
      if (index !== -1) {
        produits.value[index] = data.data
      }
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors du changement de statut.'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    produits,
    pagination,
    loading,
    error,
    fetchProduits,
    createProduit,
    updateProduit,
    deleteProduit,
    updateStatut
  }
})
