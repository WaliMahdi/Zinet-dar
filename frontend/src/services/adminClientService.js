import api from './api'

export const adminClientService = {
  getAll(params) {
    return api.get('/admin/clients', { params }).then(res => res.data)
  },

  getById(id) {
    return api.get(`/admin/clients/${id}`).then(res => res.data)
  },

  delete(id) {
    return api.delete(`/admin/clients/${id}`).then(res => res.data)
  }
}
