import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '@/services/authService'

export const useAuthStore = defineStore('auth', () => {
  // ── State ─────────────────────────────────────────────────────────────────
  const user  = ref(JSON.parse(localStorage.getItem('zinet_user') || 'null'))
  const token = ref(localStorage.getItem('zinet_token') || null)
  const loading = ref(false)
  const error   = ref(null)

  // ── Getters ───────────────────────────────────────────────────────────────
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin' || user.value?.is_admin || user.value?.email === 'sedielectro@gmail.com')
  const isClient = computed(() => isAuthenticated.value && !isAdmin.value)
  const fullName = computed(() => {
    if (!user.value) return ''
    if (user.value.first_name || user.value.last_name) {
      return `${user.value.first_name || ''} ${user.value.last_name || ''}`.trim()
    }
    return user.value.username || user.value.name || user.value.email
  })

  // ── Helpers ───────────────────────────────────────────────────────────────
  function persist(newToken, newUser) {
    token.value = newToken
    user.value  = newUser
    localStorage.setItem('zinet_token', newToken)
    localStorage.setItem('zinet_user', JSON.stringify(newUser))
  }

  function clear() {
    token.value = null
    user.value  = null
    localStorage.removeItem('zinet_token')
    localStorage.removeItem('zinet_user')
  }

  // ── Actions ───────────────────────────────────────────────────────────────
  async function register(email, password) {
    loading.value = true
    error.value   = null
    try {
      const data = await authService.register({ email, password })
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de l\'inscription.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function verifyEmail(email, verification_code) {
    loading.value = true
    error.value   = null
    try {
      const data = await authService.verifyEmail({ email, verification_code })
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Code de vérification invalide.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function resendVerification(email) {
    loading.value = true
    error.value   = null
    try {
      const data = await authService.resendVerification(email)
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de l\'envoi.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function login(email, password) {
    loading.value = true
    error.value   = null
    try {
      const data = await authService.login(email, password)
      persist(data.token, data.user)
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Identifiants incorrects.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    loading.value = true
    try {
      if (token.value) await authService.logout()
    } catch {
      // Ignore API errors on logout
    } finally {
      clear()
      loading.value = false
    }
  }

  async function fetchCurrentUser() {
    if (!token.value) return null
    try {
      const data = await authService.getProfile()
      user.value = data.data
      localStorage.setItem('zinet_user', JSON.stringify(data.data))
      return data.data
    } catch {
      clear()
      return null
    }
  }

  async function updateProfile(profileData) {
    loading.value = true
    error.value   = null
    try {
      const data = await authService.updateProfile(profileData)
      user.value = data.data
      localStorage.setItem('zinet_user', JSON.stringify(data.data))
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Erreur lors de la mise à jour.'
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    user, token, loading, error,
    isAuthenticated, isAdmin, isClient, fullName,
    register, verifyEmail, resendVerification,
    login, logout, fetchCurrentUser, updateProfile,
  }
})
