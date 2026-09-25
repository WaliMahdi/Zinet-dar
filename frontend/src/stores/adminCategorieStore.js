import { defineStore } from 'pinia'
import { ref } from 'vue'
import { adminCategorieService } from '@/services/adminCategorieService'

export const useAdminCategorieStore = defineStore('adminCategorie', () => {
  const categories = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchCategories() {
    loading.value = true
    error.value = null
    try {
      const data = await adminCategorieService.getAll()
      categories.value = data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors du chargement des catégories.'
    } finally {
      loading.value = false
    }
  }

  async function createCategorie(data) {
    loading.value = true
    error.value = null
    try {
      const result = await adminCategorieService.create(data)
      categories.value.push(result.data)
      return result
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la création.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function updateCategorie(id, data) {
    loading.value = true
    error.value = null
    try {
      const result = await adminCategorieService.update(id, data)
      const index = categories.value.findIndex(c => c.id === id)
      if (index !== -1) {
        categories.value[index] = result.data
      }
      return result
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la modification.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function deleteCategorie(id) {
    loading.value = true
    error.value = null
    try {
      await adminCategorieService.delete(id)
      categories.value = categories.value.filter(c => c.id !== id)
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la suppression.'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    categories,
    loading,
    error,
    fetchCategories,
    createCategorie,
    updateCategorie,
    deleteCategorie
  }
})
