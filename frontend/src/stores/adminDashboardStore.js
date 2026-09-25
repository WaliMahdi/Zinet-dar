import { defineStore } from 'pinia'
import { ref } from 'vue'
import { adminDashboardService } from '@/services/adminDashboardService'

export const useAdminDashboardStore = defineStore('adminDashboard', () => {
  const stats = ref(null)
  const loading = ref(false)
  const error = ref(null)

  async function fetchStats() {
    loading.value = true
    error.value = null
    try {
      const data = await adminDashboardService.getStatistics()
      stats.value = data.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors du chargement des statistiques.'
    } finally {
      loading.value = false
    }
  }

  return {
    stats,
    loading,
    error,
    fetchStats
  }
})
