import api from './api'

export const adminDashboardService = {
  /**
   * Fetch dashboard statistics.
   */
  getStatistics() {
    return api.get('/admin/dashboard').then(res => res.data)
  }
}
