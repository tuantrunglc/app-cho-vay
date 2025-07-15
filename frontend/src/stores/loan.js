import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useLoanStore = defineStore('loan', () => {
  // State
  const loanAmount = ref(50000000) // 50 triệu
  const loanTerm = ref(12) // 12 tháng
  const interestRate = ref(0.02) // 2% per month (lãi đơn)
  
  // Mock data cho đơn vay
  const loanApplications = ref([
    {
      id: 'LN001',
      customerName: 'Nguyễn Văn A',
      phone: '0901234567',
      idCard: '123456789012',
      amount: 100000000,
      term: 24,
      status: 'pending',
      verified: true,
      createdAt: '2024-01-15T10:30:00Z'
    },
    {
      id: 'LN002',
      customerName: 'Trần Thị B',
      phone: '0987654321',
      idCard: '987654321098',
      amount: 50000000,
      term: 12,
      status: 'pending',
      verified: false,
      createdAt: '2024-01-15T14:20:00Z'
    },
    {
      id: 'LN003',
      customerName: 'Lê Văn C',
      phone: '0912345678',
      idCard: '456789123456',
      amount: 200000000,
      term: 36,
      status: 'cancelled',
      verified: true,
      createdAt: '2024-01-14T09:15:00Z'
    }
  ])
  
  const activeLoanApplications = ref([
    {
      id: 'LN004',
      customerName: 'Phạm Thị D',
      phone: '0923456789',
      idCard: '789123456789',
      amount: 150000000,
      term: 18,
      status: 'active',
      verified: true,
      approvedAt: '2024-01-10T16:45:00Z',
      nextPayment: '2024-02-10',
      remainingAmount: 140000000
    }
  ])
  
  // Computed
  const monthlyPayment = computed(() => {
    const principal = loanAmount.value
    const monthlyInterest = principal * interestRate.value
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
    return loanApplications.value.filter(app => app.status === 'cancelled')
  })
  
  // Actions
  const setLoanAmount = (amount) => {
    loanAmount.value = amount
  }
  
  const setLoanTerm = (term) => {
    loanTerm.value = term
  }
  
  const submitLoanApplication = (customerData) => {
    const newApplication = {
      id: `LN${String(loanApplications.value.length + 1).padStart(3, '0')}`,
      ...customerData,
      amount: loanAmount.value,
      term: loanTerm.value,
      status: 'pending',
      verified: false,
      createdAt: new Date().toISOString()
    }
    
    loanApplications.value.unshift(newApplication)
    return newApplication
  }
  
  const approveLoanApplication = (id) => {
    const application = loanApplications.value.find(app => app.id === id)
    if (application) {
      application.status = 'approved'
      // Move to active loans
      activeLoanApplications.value.unshift({
        ...application,
        status: 'active',
        approvedAt: new Date().toISOString(),
        nextPayment: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        remainingAmount: application.amount
      })
      // Remove from pending
      const index = loanApplications.value.findIndex(app => app.id === id)
      loanApplications.value.splice(index, 1)
    }
  }
  
  const rejectLoanApplication = (id) => {
    const application = loanApplications.value.find(app => app.id === id)
    if (application) {
      application.status = 'cancelled'
    }
  }
  
  return {
    // State
    loanAmount,
    loanTerm,
    interestRate,
    loanApplications,
    activeLoanApplications,
    
    // Computed
    monthlyPayment,
    totalPayment,
    totalInterest,
    pendingApplications,
    cancelledApplications,
    
    // Actions
    setLoanAmount,
    setLoanTerm,
    submitLoanApplication,
    approveLoanApplication,
    rejectLoanApplication
  }
})