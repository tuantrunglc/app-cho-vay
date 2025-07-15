import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

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
      // Mock login - replace with actual API call
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Handle admin login with username
      if (credentials.username && credentials.username === 'admin' && credentials.password === 'admin123') {
        user.value = {
          id: 1,
          username: 'admin',
          name: 'Administrator',
          email: 'admin@company.com',
          role: 'admin'
        }
        token.value = 'mock-admin-token'
      }
      // Handle customer login with phone
      else if (credentials.phone === '0987654321' && credentials.password === 'customer123') {
        user.value = {
          id: 2,
          name: 'Nguyễn Văn A',
          phone: '0987654321',
          role: 'customer'
        }
        token.value = 'mock-customer-token'
      }
      // Handle admin login with phone (backward compatibility)
      else if (credentials.phone === '0123456789' && credentials.password === 'admin123') {
        user.value = {
          id: 1,
          name: 'Admin',
          phone: '0123456789',
          role: 'admin'
        }
        token.value = 'mock-admin-token'
      } else {
        throw new Error('Thông tin đăng nhập không đúng')
      }
      
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
      
      return { success: true }
    } catch (error) {
      return { success: false, error: error.message }
    } finally {
      isLoading.value = false
    }
  }

  const logout = () => {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  const checkAuth = () => {
    const savedToken = localStorage.getItem('token')
    const savedUser = localStorage.getItem('user')
    
    if (savedToken && savedUser) {
      token.value = savedToken
      user.value = JSON.parse(savedUser)
      return true
    }
    
    return false
  }

  const register = async (userData) => {
    isLoading.value = true
    
    try {
      // Mock registration - replace with actual API call
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Create new customer user
      user.value = {
        id: Date.now(),
        name: userData.name,
        phone: userData.phone,
        role: 'customer'
      }
      
      token.value = `mock-token-${Date.now()}`
      
      localStorage.setItem('token', token.value)
      localStorage.setItem('user', JSON.stringify(user.value))
      
      return { success: true }
    } catch (error) {
      return { success: false, error: error.message }
    } finally {
      isLoading.value = false
    }
  }

  const updateProfile = async (profileData) => {
    isLoading.value = true
    
    try {
      // Mock profile update - replace with actual API call
      await new Promise(resolve => setTimeout(resolve, 500))
      
      user.value = { ...user.value, ...profileData }
      localStorage.setItem('user', JSON.stringify(user.value))
      
      return { success: true }
    } catch (error) {
      return { success: false, error: error.message }
    } finally {
      isLoading.value = false
    }
  }

  const changePassword = async (passwordData) => {
    isLoading.value = true
    
    try {
      // Mock password change - replace with actual API call
      await new Promise(resolve => setTimeout(resolve, 500))
      
      // Validate current password
      if (passwordData.currentPassword !== 'current123') {
        throw new Error('Mật khẩu hiện tại không đúng')
      }
      
      return { success: true }
    } catch (error) {
      return { success: false, error: error.message }
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