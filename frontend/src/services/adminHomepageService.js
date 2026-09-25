import api from './api'

export const adminHomepageService = {
  getAll() {
    return api.get('/homepage-images').then(res => res.data)
  },

  create(data) {
    return api.post('/homepage-images', data, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }).then(res => res.data)
  },

  delete(id) {
    return api.delete(`/homepage-images/${id}`).then(res => res.data)
  }
}
