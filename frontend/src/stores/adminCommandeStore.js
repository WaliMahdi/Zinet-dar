import { defineStore } from 'pinia'
import { ref } from 'vue'
import { adminCommandeService } from '@/services/adminCommandeService'

export const useAdminCommandeStore = defineStore('adminCommande', () => {
  const commandes = ref([])
  const pagination = ref(null)
  const currentCommande = ref(null)
  const loading = ref(false)
  const error = ref(null)

  async function fetchCommandes(params = { page: 1 }) {
    loading.value = true
    error.value = null
    try {
      const data = await adminCommandeService.getAll(params)
      commandes.value = data.data.data
      pagination.value = {
        current_page: data.data.current_page,
        last_page: data.data.last_page,
        total: data.data.total,
        per_page: data.data.per_page,
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors du chargement des commandes.'
    } finally {
      loading.value = false
    }
  }

  async function fetchCommande(id) {
    loading.value = true
    error.value = null
    try {
      const data = await adminCommandeService.getById(id)
      currentCommande.value = data.data
      return data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors du chargement de la commande.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateStatut(id, statut, note_admin = null) {
    loading.value = true
    error.value = null
    try {
      const data = await adminCommandeService.updateStatut(id, statut, note_admin)
      // Update in list if present
      const index = commandes.value.findIndex(c => c.id === id)
      if (index !== -1) {
        commandes.value[index] = data.data
      }
      if (currentCommande.value?.id === id) {
        currentCommande.value = data.data
      }
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la mise à jour du statut.'
      throw err
    } finally {
      loading.value = false
    }
  }
  
  async function deleteCommande(id) {
    loading.value = true
    error.value = null
    try {
      await adminCommandeService.delete(id)
      commandes.value = commandes.value.filter(c => c.id !== id)
      if (currentCommande.value?.id === id) {
        currentCommande.value = null
      }
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la suppression de la commande.'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    commandes,
    pagination,
    currentCommande,
    loading,
    error,
    fetchCommandes,
    fetchCommande,
    updateStatut,
    deleteCommande
  }
})
