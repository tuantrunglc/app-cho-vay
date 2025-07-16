import apiClient from './api.js'

// Service chính để thực hiện các API calls
class ApiService {
  // Auth APIs
  async login(credentials) {
    try {
      const response = await apiClient.post('/auth/login', credentials)
      return response
    } catch (error) {
      throw error
    }
  }

  async register(userData) {
    try {
      const response = await apiClient.post('/auth/register', userData)
      return response
    } catch (error) {
      throw error
    }
  }

  async logout() {
    try {
      const response = await apiClient.post('/auth/logout')
      return response
    } catch (error) {
      throw error
    }
  }

  // User APIs
  async getUserProfile() {
    try {
      const response = await apiClient.get('/user/profile')
      return response
    } catch (error) {
      throw error
    }
  }

  async updateUserProfile(userData) {
    try {
      const response = await apiClient.put('/user/profile', userData)
      return response
    } catch (error) {
      throw error
    }
  }

  // Loan APIs
  async getLoans(params = {}) {
    try {
      const response = await apiClient.get('/loans', { params })
      return response
    } catch (error) {
      throw error
    }
  }

  async getLoanById(id) {
    try {
      const response = await apiClient.get(`/loans/${id}`)
      return response
    } catch (error) {
      throw error
    }
  }

  async createLoan(loanData) {
    try {
      const response = await apiClient.post('/loans', loanData)
      return response
    } catch (error) {
      throw error
    }
  }

  async updateLoan(id, loanData) {
    try {
      const response = await apiClient.put(`/loans/${id}`, loanData)
      return response
    } catch (error) {
      throw error
    }
  }

  async deleteLoan(id) {
    try {
      const response = await apiClient.delete(`/loans/${id}`)
      return response
    } catch (error) {
      throw error
    }
  }

  // Generic methods cho các API calls khác
  async get(endpoint, params = {}) {
    try {
      const response = await apiClient.get(endpoint, { params })
      return response
    } catch (error) {
      throw error
    }
  }

  async post(endpoint, data = {}) {
    try {
      const response = await apiClient.post(endpoint, data)
      return response
    } catch (error) {
      throw error
    }
  }

  async put(endpoint, data = {}) {
    try {
      const response = await apiClient.put(endpoint, data)
      return response
    } catch (error) {
      throw error
    }
  }

  async patch(endpoint, data = {}) {
    try {
      const response = await apiClient.patch(endpoint, data)
      return response
    } catch (error) {
      throw error
    }
  }

  async delete(endpoint) {
    try {
      const response = await apiClient.delete(endpoint)
      return response
    } catch (error) {
      throw error
    }
  }

  // Upload file
  async uploadFile(endpoint, file, onUploadProgress = null) {
    try {
      const formData = new FormData()
      formData.append('file', file)

      const config = {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }

      if (onUploadProgress) {
        config.onUploadProgress = onUploadProgress
      }

      const response = await apiClient.post(endpoint, formData, config)
      return response
    } catch (error) {
      throw error
    }
  }
}

// Export singleton instance
export default new ApiService()