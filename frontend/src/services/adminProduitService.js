import api from './api'

export const adminProduitService = {
  getAll(params) {
    return api.get('/produits', { params: { ...params, for_admin: true } }).then(res => res.data)
  },

  getById(id) {
    return api.get(`/produits/${id}`, { params: { for_admin: true } }).then(res => res.data)
  },

  create(data) {
    return api.post('/produits', data)
      .then(res => res.data)
  },

  update(id, data) {
    return api.post(`/produits/${id}`, data)
      .then(res => res.data)
  },

  delete(id) {
    return api.delete(`/produits/${id}`).then(res => res.data)
  },

  deleteImage(produitId, imageId) {
    return api.delete(`/produits/${produitId}/images/${imageId}`).then(res => res.data)
  },

  updateStock(id, quantite_stock) {
    return api.patch(`/produits/${id}/stock`, { quantite_stock }).then(res => res.data)
  },

  updateStatut(id, actif) {
    return api.patch(`/produits/${id}/statut`, { actif }).then(res => res.data)
  }
}
