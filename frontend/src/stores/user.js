import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useUserStore = defineStore('user', () => {
  // State
  const user = ref(null)
  const walletBalance = ref(2500000) // 2.5 triệu
  const linkedBanks = ref([
    {
      id: 1,
      name: 'Vietcombank',
      logo: '/images/banks/vcb.png',
      accountNumber: '**** **** **** 1234',
      isDefault: true
    },
    {
      id: 2,
      name: 'Techcombank',
      logo: '/images/banks/tcb.png',
      accountNumber: '**** **** **** 5678',
      isDefault: false
    },
    {
      id: 3,
      name: 'BIDV',
      logo: '/images/banks/bidv.png',
      accountNumber: '**** **** **** 9012',
      isDefault: false
    }
  ])
  
  const customers = ref([
    {
      id: 1,
      name: 'Nguyễn Văn A',
      phone: '0901234567',
      email: 'nguyenvana@email.com',
      idCard: '123456789012',
      address: '123 Đường ABC, Quận 1, TP.HCM',
      status: 'active',
      joinDate: '2024-01-10',
      totalLoans: 2,
      currentDebt: 50000000
    },
    {
      id: 2,
      name: 'Trần Thị B',
      phone: '0987654321',
      email: 'tranthib@email.com',
      idCard: '987654321098',
      address: '456 Đường XYZ, Quận 2, TP.HCM',
      status: 'active',
      joinDate: '2024-01-12',
      totalLoans: 1,
      currentDebt: 30000000
    },
    {
      id: 3,
      name: 'Lê Văn C',
      phone: '0912345678',
      email: 'levanc@email.com',
      idCard: '456789123456',
      address: '789 Đường DEF, Quận 3, TP.HCM',
      status: 'blocked',
      joinDate: '2024-01-08',
      totalLoans: 3,
      currentDebt: 0
    }
  ])
  
  // Computed
  const isLoggedIn = computed(() => user.value !== null)
  
  const formattedBalance = computed(() => {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND'
    }).format(walletBalance.value)
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
  const login = (userData) => {
    user.value = userData
  }
  
  const logout = () => {
    user.value = null
  }
  
  const updateWalletBalance = (amount) => {
    walletBalance.value = amount
  }
  
  const setDefaultBank = (bankId) => {
    linkedBanks.value.forEach(bank => {
      bank.isDefault = bank.id === bankId
    })
  }
  
  const addCustomer = (customerData) => {
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
    const customer = customers.value.find(c => c.id === customerId)
    if (customer) {
      customer.status = status
    }
  }
  
  return {
    // State
    user,
    walletBalance,
    linkedBanks,
    customers,
    
    // Computed
    isLoggedIn,
    formattedBalance,
    defaultBank,
    activeCustomers,
    blockedCustomers,
    
    // Actions
    login,
    logout,
    updateWalletBalance,
    setDefaultBank,
    addCustomer,
    updateCustomerStatus
  }
})