import api from './api'

export const categorieService = {
  /**
   * GET /api/categories
   */
  async getAll() {
    const res = await api.get('/categories')
    return res.data
  },

  /**
   * GET /api/categories/{id}
   */
  async getById(id) {
    const res = await api.get(`/categories/${id}`)
    return res.data
  },


  /**
   * GET /api/categories/{id}/produits
   */
  async getProduits(categorieId, params = {}) {
    const res = await api.get(`/categories/${categorieId}/produits`, { params })
    return res.data
  },

}
