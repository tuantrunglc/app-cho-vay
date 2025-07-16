import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { apiService, apiHelpers } from '@/services'

export const useContentStore = defineStore('content', () => {
  // State
  const banners = ref([
    {
      id: 1,
      title: 'Vay nhanh - Lãi thấp',
      subtitle: 'Chỉ từ 2%/tháng',
      imageUrl: 'https://via.placeholder.com/716x320/3B82F6/FFFFFF?text=Banner+1',
      active: true,
      createdAt: '2024-01-01T00:00:00Z'
    },
    {
      id: 2,
      title: 'Duyệt trong 24h',
      subtitle: 'Không cần thế chấp',
      imageUrl: 'https://via.placeholder.com/716x320/10B981/FFFFFF?text=Banner+2',
      active: true,
      createdAt: '2024-01-02T00:00:00Z'
    },
    {
      id: 3,
      title: 'Ưu đãi tháng 1',
      subtitle: 'Giảm 0.5% lãi suất',
      imageUrl: 'https://via.placeholder.com/716x320/F59E0B/FFFFFF?text=Banner+3',
      active: true,
      createdAt: '2024-01-03T00:00:00Z'
    }
  ])

  const interestRates = ref(null)
  const loanLimits = ref(null)
  const loanTerms = ref([])
  const isLoading = ref(false)
  const error = ref(null)

  const notifications = ref([
    {
      id: 1,
      title: 'Khuyến mãi tháng 1',
      content: 'Giảm 0.5% lãi suất cho khách hàng mới trong tháng 1/2024',
      type: 'promotion',
      active: true,
      createdAt: '2024-01-15T10:00:00Z',
      sentCount: 1250
    },
    {
      id: 2,
      title: 'Bảo trì hệ thống',
      content: 'Hệ thống sẽ được bảo trì từ 2h-4h sáng ngày 20/1/2024',
      type: 'system',
      active: true,
      createdAt: '2024-01-14T15:00:00Z',
      sentCount: 3500
    }
  ])

  // Getters
  const activeBanners = computed(() => 
    banners.value.filter(banner => banner.active)
  )

  const activeTerms = computed(() => 
    loanTerms.value.filter(term => term.active).map(term => term.months)
  )

  const activeNotifications = computed(() =>
    notifications.value.filter(notification => notification.active)
  )

  // Actions
  const fetchLoanConfig = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await apiService.getLoanConfig()
      
      if (apiHelpers.isSuccessResponse(response)) {
        const config = apiHelpers.getResponseData(response)
        
        // Update state with config from API
        if (config.interest_rates) {
          interestRates.value = config.interest_rates
        }
        
        if (config.loan_limits) {
          loanLimits.value = config.loan_limits
        }
        
        if (config.available_terms) {
          loanTerms.value = config.available_terms.map((term, index) => ({
            id: index + 1,
            months: term,
            active: true
          }))
        }
        
        return { success: true, config }
      } else {
        throw new Error(response.message || 'Không thể lấy cấu hình khoản vay')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const addBanner = (bannerData) => {
    const newBanner = {
      id: Date.now(),
      ...bannerData,
      active: true,
      createdAt: new Date().toISOString()
    }
    banners.value.push(newBanner)
    return newBanner
  }

  const updateBanner = (id, updates) => {
    const index = banners.value.findIndex(banner => banner.id === id)
    if (index !== -1) {
      banners.value[index] = { ...banners.value[index], ...updates }
      return banners.value[index]
    }
    return null
  }

  const deleteBanner = (id) => {
    const index = banners.value.findIndex(banner => banner.id === id)
    if (index !== -1) {
      banners.value.splice(index, 1)
      return true
    }
    return false
  }

  const toggleBanner = (id) => {
    const banner = banners.value.find(b => b.id === id)
    if (banner) {
      banner.active = !banner.active
      return banner
    }
    return null
  }

  const updateInterestRates = (rates) => {
    interestRates.value = {
      ...rates,
      lastUpdated: new Date().toISOString(),
      updatedBy: 'Admin'
    }
  }

  const updateLoanLimits = (limits) => {
    loanLimits.value = {
      ...limits,
      lastUpdated: new Date().toISOString()
    }
  }

  const addLoanTerm = (months) => {
    const exists = loanTerms.value.some(term => term.months === months)
    if (!exists) {
      const newTerm = {
        id: Date.now(),
        months: months,
        active: true
      }
      loanTerms.value.push(newTerm)
      loanTerms.value.sort((a, b) => a.months - b.months)
      return newTerm
    }
    return null
  }

  const toggleLoanTerm = (id) => {
    const term = loanTerms.value.find(t => t.id === id)
    if (term) {
      term.active = !term.active
      return term
    }
    return null
  }

  const deleteLoanTerm = (id) => {
    const index = loanTerms.value.findIndex(term => term.id === id)
    if (index !== -1) {
      loanTerms.value.splice(index, 1)
      return true
    }
    return false
  }

  const addNotification = (notificationData) => {
    const newNotification = {
      id: Date.now(),
      ...notificationData,
      active: true,
      createdAt: new Date().toISOString(),
      sentCount: 0
    }
    notifications.value.unshift(newNotification)
    return newNotification
  }

  const updateNotification = (id, updates) => {
    const index = notifications.value.findIndex(notification => notification.id === id)
    if (index !== -1) {
      notifications.value[index] = { ...notifications.value[index], ...updates }
      return notifications.value[index]
    }
    return null
  }

  const deleteNotification = (id) => {
    const index = notifications.value.findIndex(notification => notification.id === id)
    if (index !== -1) {
      notifications.value.splice(index, 1)
      return true
    }
    return false
  }

  const getQuickAmounts = () => {
    if (!loanLimits.value) return []
    
    const { min_amount, max_amount } = loanLimits.value
    const step = (max_amount - min_amount) / 4
    return [
      min_amount + step,
      min_amount + step * 2,
      min_amount + step * 3
    ]
  }

  // Initialize config on store creation
  fetchLoanConfig()

  return {
    // State
    banners,
    interestRates,
    loanLimits,
    loanTerms,
    notifications,
    isLoading,
    error,
    
    // Getters
    activeBanners,
    activeTerms,
    activeNotifications,
    
    // Actions
    fetchLoanConfig,
    addBanner,
    updateBanner,
    deleteBanner,
    toggleBanner,
    updateInterestRates,
    updateLoanLimits,
    addLoanTerm,
    toggleLoanTerm,
    deleteLoanTerm,
    addNotification,
    updateNotification,
    deleteNotification,
    getQuickAmounts
  }
})