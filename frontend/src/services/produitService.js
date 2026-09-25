import api from './api'

export const produitService = {
  /**
   * GET /api/produits
   * Params: q, categorie_id, prix_min, prix_max,
   *         vedette, nouveau, sort, order, per_page, page
   */
  async getAll(params = {}, config = {}) {
    const res = await api.get('/produits', { params, ...config })
    return res.data
  },

  /**
   * GET /api/produits/{id}
   */
  async getById(id) {
    const res = await api.get(`/produits/${id}`)
    return res.data
  },
}
