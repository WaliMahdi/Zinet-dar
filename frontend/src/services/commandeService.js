import api from './api'

export const commandeService = {
  /**
   * GET /api/mes-commandes
   * Params: page, per_page
   */
  async getMesCommandes(params = {}) {
    const res = await api.get('/mes-commandes', { params })
    return res.data
  },

  /**
   * GET /api/mes-commandes/{id}
   */
  async getById(id) {
    const res = await api.get(`/mes-commandes/${id}`)
    return res.data
  },
}
