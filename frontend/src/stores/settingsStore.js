import { defineStore } from 'pinia'
import api from '@/services/api'

export const useSettingsStore = defineStore('settings', {
  state: () => ({
    logoUrl: null,
    loading: false,
    fetched: false
  }),

  actions: {
    async fetchLogo() {
      if (this.fetched || this.loading) return
      
      this.loading = true
      try {
        const res = await api.get('/settings/logo')
        if (res.data && res.data.value) {
          this.logoUrl = res.data.value
        }
        this.fetched = true
      } catch (error) {
        console.error('Erreur chargement du logo', error)
      } finally {
        this.loading = false
      }
    }
  }
})
