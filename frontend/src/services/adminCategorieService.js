import api from './api'

export const adminCategorieService = {
  getAll() {
    return api.get('/categories').then(res => res.data)
  },

  create(data) {
    return api.post('/categories', data).then(res => res.data)
  },

  update(id, data) {
    return api.post(`/categories/${id}`, data).then(res => res.data)
  },

  delete(id) {
    return api.delete(`/categories/${id}`).then(res => res.data)
  }
}
