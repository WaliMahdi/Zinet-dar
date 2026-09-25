import api from './api'

export const adminSettingsService = {
  getLogo() {
    return api.get('/settings/logo').then(res => res.data)
  },

  updateLogo(file) {
    const formData = new FormData()
    formData.append('logo', file)
    
    return api.post('/settings/logo', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }).then(res => res.data)
  }
}
