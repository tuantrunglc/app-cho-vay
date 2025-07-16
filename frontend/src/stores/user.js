import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { apiService, apiHelpers } from '@/services'

export const useUserStore = defineStore('user', () => {
  // State
  const user = ref(null)
  const profile = ref(null)
  const walletBalance = ref(0)
  const linkedBanks = ref([])
  const customers = ref([])
  const isLoading = ref(false)
  const error = ref(null)
  
  // Computed
  const isLoggedIn = computed(() => user.value !== null)
  
  const formattedBalance = computed(() => {
    return apiHelpers.formatCurrency(walletBalance.value)
  })
  
  const defaultBank = computed(() => {
    return linkedBanks.value.find(bank => bank.isDefault)
  })
  
  const activeCustomers = computed(() => {
    return customers.value.filter(customer => customer.status === 'active')
  })
  
  const blockedCustomers = computed(() => {
    return customers.value.filter(customer => customer.status === 'blocked')
  })
  
  // Actions
  const fetchUserProfile = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await apiService.getUserProfile()
      
      if (apiHelpers.isSuccessResponse(response)) {
        profile.value = apiHelpers.getResponseData(response)
        user.value = profile.value // Keep user in sync
        
        return { success: true, profile: profile.value }
      } else {
        throw new Error(response.message || 'Không thể lấy thông tin profile')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const updateUserProfile = async (profileData) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await apiService.updateUserProfile(profileData)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const updatedProfile = apiHelpers.getResponseData(response)
        profile.value = updatedProfile
        user.value = updatedProfile
        
        return { success: true, profile: updatedProfile }
      } else {
        throw new Error(response.message || 'Không thể cập nhật profile')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const uploadAvatar = async (file, onProgress) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await apiService.uploadAvatar(file, onProgress)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const data = apiHelpers.getResponseData(response)
        
        // Update profile with new avatar
        if (profile.value) {
          profile.value.avatar = data.avatar_url || data.url
        }
        
        return { success: true, avatarUrl: data.avatar_url || data.url }
      } else {
        throw new Error(response.message || 'Không thể upload avatar')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  // Legacy methods for backward compatibility
  const login = (userData) => {
    user.value = userData
    profile.value = userData
  }
  
  const logout = () => {
    user.value = null
    profile.value = null
    walletBalance.value = 0
    linkedBanks.value = []
    customers.value = []
  }
  
  const updateWalletBalance = (amount) => {
    walletBalance.value = amount
  }
  
  const setDefaultBank = (bankId) => {
    linkedBanks.value.forEach(bank => {
      bank.isDefault = bank.id === bankId
    })
  }
  
  // Mock methods for admin functionality (to be replaced with admin APIs)
  const addCustomer = (customerData) => {
    console.warn('addCustomer is deprecated - use admin APIs')
    const newCustomer = {
      id: customers.value.length + 1,
      ...customerData,
      status: 'active',
      joinDate: new Date().toISOString().split('T')[0],
      totalLoans: 0,
      currentDebt: 0
    }
    customers.value.unshift(newCustomer)
    return newCustomer
  }
  
  const updateCustomerStatus = (customerId, status) => {
    console.warn('updateCustomerStatus is deprecated - use admin APIs')
    const customer = customers.value.find(c => c.id === customerId)
    if (customer) {
      customer.status = status
    }
  }
  
  return {
    // State
    user,
    profile,
    walletBalance,
    linkedBanks,
    customers,
    isLoading,
    error,
    
    // Computed
    isLoggedIn,
    formattedBalance,
    defaultBank,
    activeCustomers,
    blockedCustomers,
    
    // Actions
    fetchUserProfile,
    updateUserProfile,
    uploadAvatar,
    
    // Legacy methods
    login,
    logout,
    updateWalletBalance,
    setDefaultBank,
    addCustomer,
    updateCustomerStatus
  }
})