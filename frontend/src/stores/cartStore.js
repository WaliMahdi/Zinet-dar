import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { panierService } from '@/services/panierService'
import { useAuthStore } from '@/stores/authStore'

export const useCartStore = defineStore('cart', () => {
  // ── State ─────────────────────────────────────────────────────────────────
  const items       = ref([])   // PanierDetailResource[]
  const sousTotal   = ref(0)
  const montantTotal= ref(0)
  const loading     = ref(false)
  const error       = ref(null)
  const isDrawerOpen= ref(false)

  // ── Getters ───────────────────────────────────────────────────────────────
  const totalItems  = computed(() => items.value.reduce((sum, i) => sum + i.quantite, 0))
  const totalAmount = computed(() => parseFloat(montantTotal.value) || 0)
  const isEmpty     = computed(() => items.value.length === 0)

  // ── Helpers ───────────────────────────────────────────────────────────────
  function syncFromResponse(panier) {
    items.value        = panier.items || []
    sousTotal.value    = parseFloat(panier.sous_total) || 0
    montantTotal.value = parseFloat(panier.montant_total) || 0
  }

  function requireAuth() {
    const auth = useAuthStore()
    return auth.isAuthenticated
  }

  // ── Actions ───────────────────────────────────────────────────────────────
  async function fetchCart() {
    if (!requireAuth()) return
    loading.value = true
    error.value   = null
    try {
      const data = await panierService.get()
      syncFromResponse(data.panier)
    } catch (err) {
      error.value = err.response?.data?.message || 'Impossible de charger le panier.'
    } finally {
      loading.value = false
    }
  }

  async function addToCart(produit_id, quantite = 1) {
    if (!requireAuth()) return { needsAuth: true }
    loading.value = true
    error.value   = null
    try {
      const data = await panierService.ajouter(produit_id, quantite)
      if (data.success) {
        syncFromResponse(data.panier)
        return { success: true }
      }
      error.value = data.message
      return { success: false, message: data.message }
    } catch (err) {
      const msg = err.response?.data?.message || 'Impossible d\'ajouter au panier.'
      error.value = msg
      return { success: false, message: msg }
    } finally {
      loading.value = false
    }
  }

  async function updateQuantity(produit_id, quantite) {
    loading.value = true
    error.value   = null
    try {
      const data = await panierService.updateQuantite(produit_id, quantite)
      if (data.success) syncFromResponse(data.panier)
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Impossible de mettre à jour la quantité.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function removeFromCart(produit_id) {
    loading.value = true
    error.value   = null
    try {
      const data = await panierService.retirer(produit_id)
      if (data.success) syncFromResponse(data.panier)
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Impossible de supprimer l\'article.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function clearCart() {
    loading.value = true
    error.value   = null
    try {
      const data = await panierService.vider()
      if (data.success) syncFromResponse(data.panier)
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Impossible de vider le panier.'
      throw err
    } finally {
      loading.value = false
    }
  }

  async function checkout(checkoutData) {
    loading.value = true
    error.value   = null
    try {
      const data = await panierService.commander(checkoutData)
      if (data.success) {
        // Reset cart state
        items.value        = []
        sousTotal.value    = 0
        montantTotal.value = 0
      }
      return data
    } catch (err) {
      error.value = err.response?.data?.message || 'Impossible de passer la commande.'
      throw err
    } finally {
      loading.value = false
    }
  }

  function openDrawer()  { isDrawerOpen.value = true }
  function closeDrawer() { isDrawerOpen.value = false }

  return {
    items, sousTotal, montantTotal, loading, error, isDrawerOpen,
    totalItems, totalAmount, isEmpty,
    fetchCart, addToCart, updateQuantity, removeFromCart, clearCart, checkout,
    openDrawer, closeDrawer,
  }
})
