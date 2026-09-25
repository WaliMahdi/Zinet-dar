import api from './api'

export const adminBanniereService = {
  getAll(params = {}) {
    return api.get('/bannieres', { params: { ...params, all: true } }).then(res => res.data)
  },

  createBanniere(form) {
    const formData = new FormData()
    formData.append('is_active', form.is_active ? 1 : 0)
    formData.append('section', form.section)
    if (form.image instanceof File) formData.append('image', form.image)

    return api.post('/bannieres', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }).then(res => res.data)
  },

  updateBanniere(id, form) {
    const formData = new FormData()
    formData.append('_method', 'PUT')
    formData.append('is_active', form.is_active ? 1 : 0)
    formData.append('section', form.section)
    if (form.image instanceof File) formData.append('image', form.image)

    return api.post(`/bannieres/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }).then(res => res.data)
  },

  updateStatus(id, isActive) {
    return api.patch(`/bannieres/${id}/status`, { is_active: isActive ? 1 : 0 }).then(res => res.data)
  },

  deleteBanniere(id) {
    return api.delete(`/bannieres/${id}`).then(res => res.data)
  }
}
