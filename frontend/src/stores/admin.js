import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { adminService, apiHelpers } from '@/services'

export const useAdminStore = defineStore('admin', () => {
  // State
  const dashboardStats = ref(null)
  const loanApplications = ref([])
  const customers = ref([])
  const users = ref([])
  const banners = ref([])
  const notifications = ref([])
  const settings = ref(null)
  const isLoading = ref(false)
  const error = ref(null)

  // Computed
  const pendingLoans = computed(() => 
    loanApplications.value.filter(loan => loan.status === 'pending')
  )

  const approvedLoans = computed(() => 
    loanApplications.value.filter(loan => loan.status === 'approved')
  )

  const rejectedLoans = computed(() => 
    loanApplications.value.filter(loan => loan.status === 'rejected')
  )

  const activeCustomers = computed(() => 
    customers.value.filter(customer => customer.status === 'active')
  )

  const blockedCustomers = computed(() => 
    customers.value.filter(customer => customer.status === 'blocked')
  )

  // Dashboard Actions
  const fetchDashboardStats = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await adminService.getDashboardStats()
      
      if (apiHelpers.isSuccessResponse(response)) {
        dashboardStats.value = apiHelpers.getResponseData(response)
        return { success: true, stats: dashboardStats.value }
      } else {
        throw new Error(response.message || 'Không thể lấy thống kê dashboard')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  // Loan Management Actions
  const fetchLoanApplications = async (params = {}) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await adminService.getLoanApplications(params)
      
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

  const approveLoanApplication = async (id, data = {}) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await adminService.approveLoanApplication(id, data)
      
      if (apiHelpers.isSuccessResponse(response)) {
        // Update local state
        const index = loanApplications.value.findIndex(loan => loan.id === id)
        if (index !== -1) {
          loanApplications.value[index].status = 'approved'
          loanApplications.value[index].approved_at = new Date().toISOString()
        }
        
        return { success: true, message: 'Đơn vay đã được duyệt' }
      } else {
        throw new Error(response.message || 'Không thể duyệt đơn vay')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const rejectLoanApplication = async (id, data = {}) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await adminService.rejectLoanApplication(id, data)
      
      if (apiHelpers.isSuccessResponse(response)) {
        // Update local state
        const index = loanApplications.value.findIndex(loan => loan.id === id)
        if (index !== -1) {
          loanApplications.value[index].status = 'rejected'
          loanApplications.value[index].rejected_at = new Date().toISOString()
          loanApplications.value[index].rejection_reason = data.reason
        }
        
        return { success: true, message: 'Đơn vay đã bị từ chối' }
      } else {
        throw new Error(response.message || 'Không thể từ chối đơn vay')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  // Customer Management Actions
  const fetchCustomers = async (params = {}) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await adminService.getCustomers(params)
      
      if (apiHelpers.isSuccessResponse(response)) {
        const data = apiHelpers.getResponseData(response)
        customers.value = data.customers || data
        return { success: true, customers: customers.value }
      } else {
        throw new Error(response.message || 'Không thể lấy danh sách khách hàng')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const updateCustomerStatus = async (id, status) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await adminService.updateCustomerStatus(id, status)
      
      if (apiHelpers.isSuccessResponse(response)) {
        // Update local state
        const index = customers.value.findIndex(customer => customer.id === id)
        if (index !== -1) {
          customers.value[index].status = status
        }
        
        return { success: true, message: 'Cập nhật trạng thái khách hàng thành công' }
      } else {
        throw new Error(response.message || 'Không thể cập nhật trạng thái khách hàng')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  // Settings Actions
  const fetchSettings = async () => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await adminService.getSettings()
      
      if (apiHelpers.isSuccessResponse(response)) {
        settings.value = apiHelpers.getResponseData(response)
        return { success: true, settings: settings.value }
      } else {
        throw new Error(response.message || 'Không thể lấy cài đặt hệ thống')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const updateSettings = async (data) => {
    isLoading.value = true
    error.value = null
    
    try {
      const response = await adminService.updateSettings(data)
      
      if (apiHelpers.isSuccessResponse(response)) {
        settings.value = apiHelpers.getResponseData(response)
        return { success: true, settings: settings.value }
      } else {
        throw new Error(response.message || 'Không thể cập nhật cài đặt')
      }
    } catch (err) {
      error.value = apiHelpers.handleApiError(err)
      return { success: false, error: error.value }
    } finally {
      isLoading.value = false
    }
  }

  return {
    // State
    dashboardStats,
    loanApplications,
    customers,
    users,
    banners,
    notifications,
    settings,
    isLoading,
    error,
    
    // Computed
    pendingLoans,
    approvedLoans,
    rejectedLoans,
    activeCustomers,
    blockedCustomers,
    
    // Actions
    fetchDashboardStats,
    fetchLoanApplications,
    approveLoanApplication,
    rejectLoanApplication,
    fetchCustomers,
    updateCustomerStatus,
    fetchSettings,
    updateSettings
  }
})