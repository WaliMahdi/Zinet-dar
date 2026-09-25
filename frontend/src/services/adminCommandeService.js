import api from './api'

export const adminCommandeService = {
  /**
   * Fetch all orders with optional filters.
   */
  getAll(params) {
    return api.get('/admin/commandes', { params }).then(res => res.data)
  },

  /**
   * Fetch details for a specific order.
   */
  getById(id) {
    return api.get(`/admin/commandes/${id}`).then(res => res.data)
  },

  /**
   * Update the status of an order.
   */
  updateStatut(id, statut, note_admin = null) {
    return api.patch(`/admin/commandes/${id}/statut`, { statut, note_admin }).then(res => res.data)
  },

  /**
   * Update the internal admin note of an order.
   */
  updateNote(id, note_admin) {
    return api.patch(`/admin/commandes/${id}/note`, { note_admin }).then(res => res.data)
  },

  /**
   * Delete an order.
   */
  delete(id) {
    return api.delete(`/admin/commandes/${id}`).then(res => res.data)
  }
}
