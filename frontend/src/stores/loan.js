import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { apiService, apiHelpers, LOAN_CONFIG } from '@/services'

export const useLoanStore = defineStore('loan', () => {
  // State
  const loanAmount = ref(LOAN_CONFIG.DEFAULT_AMOUNT || 50000000) // 50 triệu
  const loanTerm = ref(12) // 12 tháng
  const interestRate = ref(LOAN_CONFIG.DEFAULT_INTEREST_RATE || 1.5) // 1.5% per month
  const loanConfig = ref(null)
  const loanApplications = ref([])
  const activeLoanApplications = ref([])
  const isLoading = ref(false)
  const error = ref(null)
  
  // Computed
  const monthlyPayment = computed(() => {
    if (loanConfig.value) {
      // Use calculation from backend if available
      return 0 // Will be calculated via API
    }
    
    // Fallback calculation
    const principal = loanAmount.value
    const monthlyInterest = principal * (interestRate.value / 100)
    const monthlyPrincipal = principal / loanTerm.value
    return monthlyPrincipal + monthlyInterest
  })
  
  const totalPayment = computed(() => {
    return monthlyPayment.value * loanTerm.value
  })
  
  const totalInterest = computed(() => {
    return totalPayment.value - loanAmount.value
  })
  
  const pendingApplications = computed(() => {
    return loanApplications.value.filter(app => app.status === 'pending')
  })
  
  const cancelledApplications = computed(() => {
    return loanApplications.value.filter(app => app.status === 'cancelled' || app.status === 'rejected')
  })

  const approvedApplications = computed(() => {
    return loanApplications.value.filter(app => app.status === 'approved')
  })

  const activeApplications = computed(() => {
    return loanApplications.value.filter(app => app.status === 'active')
  })
  
  // Actions
  const setLoanAmount = (amount) => {
    loanAmount.value = amount
  }
  
  const setLoanTerm = (term) => {
    loanTerm.value = term
  }

  const fetchLoanConfig = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await apiService.getLoanConfig()
      
      if (apiHelpers.isSuccessResponse(response)) {
        loanConfig.value = apiHelpers.getResponseData(response)
        
        // Update local state with config
        if (loanConfig.value.interest_rate) {
          interestRate.value = loanConfig.value.interest_rate
        }
        
        return { success: true, config: loanConfig.value }
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

  const calculateLoan = async (amount = loanAmount.value, term = loanTerm.value) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await apiService.calculateLoan({ amount, term })
      
      if (apiHelpers.isSuccessResponse(response)) {
        const calculation = apiHelpers.getResponseData(response)
        return { success: true, calculation }
      } else {
        throw new Error(response.message || 'Không thể tính toán khoản vay')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const submitLoanApplication = async (applicationData) => {
    isLoading.value = true
    error.value = null
    
    try {
      const loanData = {
        amount: loanAmount.value,
        term: loanTerm.value,
        ...applicationData
      }
      
      const response = await apiService.applyLoan(loanData)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const newApplication = apiHelpers.getResponseData(response)
        
        // Add to local state
        loanApplications.value.unshift(newApplication)
        
        return { success: true, application: newApplication }
      } else {
        throw new Error(response.message || 'Không thể nộp đơn vay')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const fetchMyLoanApplications = async (params = {}) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await apiService.getMyLoanApplications(params)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const data = apiHelpers.getResponseData(response)
        loanApplications.value = data.applications || data
        
        return { success: true, applications: loanApplications.value }
      } else {
        throw new Error(response.message || 'Không thể lấy danh sách đơn vay')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const fetchLoanApplicationById = async (id) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await apiService.getLoanApplicationById(id)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const application = apiHelpers.getResponseData(response)
        return { success: true, application }
      } else {
        throw new Error(response.message || 'Không thể lấy chi tiết đơn vay')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const lookupLoanByPhone = async (phone) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await apiService.lookupLoanByPhone(phone)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const data = apiHelpers.getResponseData(response)
        return { success: true, data }
      } else {
        throw new Error(response.message || 'Không tìm thấy thông tin khoản vay')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  // Legacy methods for backward compatibility
  const approveLoanApplication = (id) => {
    console.warn('approveLoanApplication is deprecated - use admin APIs')
    const application = loanApplications.value.find(app => app.id === id)
    if (application) {
      application.status = 'approved'
    }
  }
  
  const rejectLoanApplication = (id) => {
    console.warn('rejectLoanApplication is deprecated - use admin APIs')
    const application = loanApplications.value.find(app => app.id === id)
    if (application) {
      application.status = 'rejected'
    }
  }
  
  // Initialize loan config on store creation
  fetchLoanConfig()

  return {
    // State
    loanAmount,
    loanTerm,
    interestRate,
    loanConfig,
    loanApplications,
    activeLoanApplications,
    isLoading,
    error,
    
    // Computed
    monthlyPayment,
    totalPayment,
    totalInterest,
    pendingApplications,
    cancelledApplications,
    approvedApplications,
    activeApplications,
    
    // Actions
    setLoanAmount,
    setLoanTerm,
    fetchLoanConfig,
    calculateLoan,
    submitLoanApplication,
    fetchMyLoanApplications,
    fetchLoanApplicationById,
    lookupLoanByPhone,
    
    // Legacy methods
    approveLoanApplication,
    rejectLoanApplication
  }
})