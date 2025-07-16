import apiClient from './api.js'
import { API_ENDPOINTS } from './apiConstants.js'

// Service chính để thực hiện các API calls
class ApiService {
  // Auth APIs
  async login(credentials) {
    try {
      // Phân biệt login cho customer và admin
      const endpoint = credentials.username ? API_ENDPOINTS.AUTH.ADMIN_LOGIN : API_ENDPOINTS.AUTH.CUSTOMER_LOGIN
      const response = await apiClient.post(endpoint, credentials)
      
      // Lưu token vào localStorage nếu login thành công
      if (response.success && response.data.token) {
        localStorage.setItem('token', response.data.token)
        localStorage.setItem('refreshToken', response.data.refreshToken)
        localStorage.setItem('user', JSON.stringify(response.data.user))
      }
      
      return response
    } catch (error) {
      throw error
    }
  }

  async register(userData) {
    try {
      const response = await apiClient.post(API_ENDPOINTS.AUTH.CUSTOMER_REGISTER, userData)
      
      // Lưu token vào localStorage nếu register thành công
      if (response.success && response.data.token) {
        localStorage.setItem('token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
      }
      
      return response
    } catch (error) {
      throw error
    }
  }

  async logout() {
    try {
      const response = await apiClient.post(API_ENDPOINTS.AUTH.LOGOUT)
      
      // Xóa token khỏi localStorage
      localStorage.removeItem('token')
      localStorage.removeItem('refreshToken')
      localStorage.removeItem('user')
      
      return response
    } catch (error) {
      throw error
    }
  }

  async refreshToken() {
    try {
      const refreshToken = localStorage.getItem('refreshToken')
      if (!refreshToken) {
        throw new Error('No refresh token available')
      }

      const response = await apiClient.post(API_ENDPOINTS.AUTH.REFRESH, {
        refresh_token: refreshToken
      })

      if (response.success && response.data.token) {
        localStorage.setItem('token', response.data.token)
        localStorage.setItem('refreshToken', response.data.refreshToken)
      }

      return response
    } catch (error) {
      throw error
    }
  }

  async getCurrentUser() {
    try {
      const response = await apiClient.get(API_ENDPOINTS.AUTH.ME)
      return response
    } catch (error) {
      throw error
    }
  }

  // User APIs
  async getUserProfile() {
    try {
      const response = await apiClient.get(API_ENDPOINTS.USER.PROFILE)
      return response
    } catch (error) {
      throw error
    }
  }

  async updateUserProfile(userData) {
    try {
      const response = await apiClient.put(API_ENDPOINTS.USER.PROFILE, userData)
      return response
    } catch (error) {
      throw error
    }
  }

  async changePassword(passwordData) {
    try {
      const response = await apiClient.post(API_ENDPOINTS.USER.CHANGE_PASSWORD, passwordData)
      return response
    } catch (error) {
      throw error
    }
  }

  async uploadAvatar(file, onUploadProgress = null) {
    try {
      const formData = new FormData()
      formData.append('avatar', file)

      const config = {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }

      if (onUploadProgress) {
        config.onUploadProgress = onUploadProgress
      }

      const response = await apiClient.post(API_ENDPOINTS.USER.UPLOAD_AVATAR, formData, config)
      return response
    } catch (error) {
      throw error
    }
  }

  // Loan APIs
  async getLoanConfig() {
    try {
      const response = await apiClient.get(API_ENDPOINTS.LOANS.CONFIG)
      return response
    } catch (error) {
      throw error
    }
  }

  async calculateLoan(loanData) {
    try {
      const response = await apiClient.post(API_ENDPOINTS.LOANS.CALCULATE, loanData)
      return response
    } catch (error) {
      throw error
    }
  }

  async lookupLoanByPhone(phone) {
    try {
      const response = await apiClient.get(`${API_ENDPOINTS.LOANS.LOOKUP}/${phone}`)
      return response
    } catch (error) {
      throw error
    }
  }

  async applyLoan(loanData) {
    try {
      const response = await apiClient.post(API_ENDPOINTS.LOANS.APPLY, loanData)
      return response
    } catch (error) {
      throw error
    }
  }

  async getMyLoanApplications(params = {}) {
    try {
      const response = await apiClient.get(API_ENDPOINTS.LOANS.MY_APPLICATIONS, { params })
      return response
    } catch (error) {
      throw error
    }
  }

  async getLoanApplicationById(id) {
    try {
      const response = await apiClient.get(`${API_ENDPOINTS.LOANS.APPLICATIONS}/${id}`)
      return response
    } catch (error) {
      throw error
    }
  }

  // Backward compatibility - map old methods to new ones
  async getLoans(params = {}) {
    return this.getMyLoanApplications(params)
  }

  async getLoanById(id) {
    return this.getLoanApplicationById(id)
  }

  async createLoan(loanData) {
    return this.applyLoan(loanData)
  }

  async updateLoan(id, loanData) {
    // This functionality might not be available in the backend
    // or might require admin privileges
    throw new Error('Update loan functionality not available')
  }

  async deleteLoan(id) {
    // This functionality might not be available in the backend
    // or might require admin privileges
    throw new Error('Delete loan functionality not available')
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

  // Health check
  async healthCheck() {
    try {
      const response = await apiClient.get(API_ENDPOINTS.HEALTH)
      return response
    } catch (error) {
      throw error
    }
  }

  // Upload file - generic method
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

  // Upload multiple files
  async uploadMultipleFiles(endpoint, files, onUploadProgress = null) {
    try {
      const formData = new FormData()
      
      // Add multiple files to FormData
      files.forEach((file, index) => {
        formData.append(`files[${index}]`, file)
      })

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