import api from './api'

export const authService = {
  /**
   * POST /api/auth/register
   */
  async register(data) {
    const res = await api.post('/auth/register', data)
    return res.data
  },

  /**
   * POST /api/auth/verify-email
   */
  async verifyEmail(data) {
    const res = await api.post('/auth/verify-email', data)
    return res.data
  },

  /**
   * POST /api/auth/resend-verification
   */
  async resendVerification(email) {
    const res = await api.post('/auth/resend-verification', { email })
    return res.data
  },

  /**
   * POST /api/auth/login
   * Returns: { success, token, user }
   */
  async login(email, password) {
    const res = await api.post('/auth/login', { email, password })
    return res.data
  },

  /**
   * POST /api/auth/logout
   */
  async logout() {
    const res = await api.post('/auth/logout')
    return res.data
  },

  /**
   * GET /api/auth/profile
   */
  async getProfile() {
    const res = await api.get('/auth/profile')
    return res.data
  },

  /**
   * PUT /api/auth/profile
   */
  async updateProfile(data) {
    const res = await api.patch('/auth/profile', data)
    return res.data
  },
}
