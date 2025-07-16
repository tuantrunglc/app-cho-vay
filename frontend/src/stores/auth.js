import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { apiService, apiHelpers } from '@/services'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)
  const isLoading = ref(false)

  // Getters
  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isCustomer = computed(() => user.value?.role === 'customer')

  // Actions
  const login = async (credentials) => {
    isLoading.value = true
    
    try {
      const response = await apiService.login(credentials)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const data = apiHelpers.getResponseData(response)
        user.value = data.user
        token.value = data.token
        
        return { success: true, user: data.user }
      } else {
        throw new Error(response.message || 'Đăng nhập thất bại')
      }
    } catch (error) {
      const errorMessage = apiHelpers.handleApiError(error)
      return { success: false, error: errorMessage }
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    try {
      await apiService.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      // Always clear local state regardless of API call result
      user.value = null
      token.value = null
    }
  }

  const checkAuth = async () => {
    // Check if user is authenticated using helper
    if (apiHelpers.isAuthenticated()) {
      const savedToken = apiHelpers.getToken()
      const savedUser = apiHelpers.getCurrentUser()
      
      if (savedToken && savedUser) {
        token.value = savedToken
        user.value = savedUser
        
        // Optionally verify token with server
        try {
          const response = await apiService.getCurrentUser()
          if (apiHelpers.isSuccessResponse(response)) {
            const userData = apiHelpers.getResponseData(response)
            user.value = userData
            localStorage.setItem('user', JSON.stringify(userData))
          }
        } catch (error) {
          console.error('Token verification failed:', error)
          // Token might be invalid, clear auth state
          user.value = null
          token.value = null
          apiHelpers.clearAuthData()
          return false
        }
        
        return true
      }
    }
    
    return false
  }

  const register = async (userData) => {
    isLoading.value = true
    
    try {
      const response = await apiService.register(userData)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const data = apiHelpers.getResponseData(response)
        user.value = data.user
        token.value = data.token
        
        return { success: true, user: data.user, message: data.message }
      } else {
        throw new Error(response.message || 'Đăng ký thất bại')
      }
    } catch (error) {
      const errorMessage = apiHelpers.handleApiError(error)
      return { success: false, error: errorMessage }
    } finally {
      isLoading.value = false
    }
  }

  const updateProfile = async (profileData) => {
    isLoading.value = true
    
    try {
      const response = await apiService.updateUserProfile(profileData)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const updatedUser = apiHelpers.getResponseData(response)
        user.value = updatedUser
        localStorage.setItem('user', JSON.stringify(updatedUser))
        
        return { success: true, user: updatedUser }
      } else {
        throw new Error(response.message || 'Cập nhật thông tin thất bại')
      }
    } catch (error) {
      const errorMessage = apiHelpers.handleApiError(error)
      return { success: false, error: errorMessage }
    } finally {
      isLoading.value = false
    }
  }

  const changePassword = async (passwordData) => {
    isLoading.value = true
    
    try {
      const response = await apiService.changePassword(passwordData)
      
      if (apiHelpers.isSuccessResponse(response)) {
        return { success: true, message: response.message || 'Đổi mật khẩu thành công' }
      } else {
        throw new Error(response.message || 'Đổi mật khẩu thất bại')
      }
    } catch (error) {
      const errorMessage = apiHelpers.handleApiError(error)
      return { success: false, error: errorMessage }
    } finally {
      isLoading.value = false
    }
  }

  // Initialize auth state
  checkAuth()

  return {
    // State
    user,
    token,
    isLoading,
    
    // Getters
    isAuthenticated,
    isAdmin,
    isCustomer,
    
    // Actions
    login,
    logout,
    register,
    updateProfile,
    changePassword,
    checkAuth
  }
})