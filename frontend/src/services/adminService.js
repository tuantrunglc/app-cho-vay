import apiClient from './api.js'

export const adminService = {
  // Dashboard APIs
  async getDashboardStats() {
    return apiClient.get('/admin/dashboard/stats')
  },

  // Loan Management APIs
  async getLoanApplications(params = {}) {
    return apiClient.get('/admin/loans', { params })
  },

  async getLoanApplicationById(id) {
    return apiClient.get(`/admin/loans/${id}`)
  },

  async approveLoanApplication(id, data = {}) {
    return apiClient.post(`/admin/loans/${id}/approve`, data)
  },

  async rejectLoanApplication(id, data = {}) {
    return apiClient.post(`/admin/loans/${id}/reject`, data)
  },

  async updateLoanApplication(id, data) {
    return apiClient.put(`/admin/loans/${id}`, data)
  },

  // Customer Management APIs
  async getCustomers(params = {}) {
    return apiClient.get('/admin/customers', { params })
  },

  async getCustomerById(id) {
    return apiClient.get(`/admin/customers/${id}`)
  },

  async updateCustomerStatus(id, status) {
    return apiClient.patch(`/admin/customers/${id}/status`, { status })
  },

  async deleteCustomer(id) {
    return apiClient.delete(`/admin/customers/${id}`)
  },

  // User Management APIs
  async getUsers(params = {}) {
    return apiClient.get('/admin/users', { params })
  },

  async createUser(data) {
    return apiClient.post('/admin/users', data)
  },

  async updateUser(id, data) {
    return apiClient.put(`/admin/users/${id}`, data)
  },

  async deleteUser(id) {
    return apiClient.delete(`/admin/users/${id}`)
  },

  // Content Management APIs
  async getBanners() {
    return apiClient.get('/admin/banners')
  },

  async createBanner(data) {
    return apiClient.post('/admin/banners', data)
  },

  async updateBanner(id, data) {
    return apiClient.put(`/admin/banners/${id}`, data)
  },

  async deleteBanner(id) {
    return apiClient.delete(`/admin/banners/${id}`)
  },

  async toggleBanner(id) {
    return apiClient.patch(`/admin/banners/${id}/toggle`)
  },

  // Settings APIs
  async getSettings() {
    return apiClient.get('/admin/settings')
  },

  async updateSettings(data) {
    return apiClient.put('/admin/settings', data)
  },

  async updateInterestRates(data) {
    return apiClient.put('/admin/settings/interest-rates', data)
  },

  async updateLoanLimits(data) {
    return apiClient.put('/admin/settings/loan-limits', data)
  },

  // Reports APIs
  async getLoanReport(params = {}) {
    return apiClient.get('/admin/reports/loans', { params })
  },

  async getCustomerReport(params = {}) {
    return apiClient.get('/admin/reports/customers', { params })
  },

  async getFinancialReport(params = {}) {
    return apiClient.get('/admin/reports/financial', { params })
  },

  async exportReport(type, params = {}) {
    return apiClient.get(`/admin/reports/${type}/export`, { 
      params,
      responseType: 'blob'
    })
  },

  // Notifications APIs
  async getNotifications(params = {}) {
    return apiClient.get('/admin/notifications', { params })
  },

  async createNotification(data) {
    return apiClient.post('/admin/notifications', data)
  },

  async updateNotification(id, data) {
    return apiClient.put(`/admin/notifications/${id}`, data)
  },

  async deleteNotification(id) {
    return apiClient.delete(`/admin/notifications/${id}`)
  },

  async sendNotification(id) {
    return apiClient.post(`/admin/notifications/${id}/send`)
  }
}