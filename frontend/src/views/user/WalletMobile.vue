<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <div class="text-center py-3">
      <h1 class="text-xl font-bold text-gray-900 mb-1">Ví tiền</h1>
      <p class="text-sm text-gray-600">Quản lý tài chính của bạn</p>
    </div>

    <!-- Balance Card -->
    <div class="bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl shadow-lg p-4">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-semibold">Số dư hiện tại</h2>
        <WalletIcon class="w-6 h-6" />
      </div>
      
      <div class="text-2xl font-bold mb-2">{{ formatCurrency(balance) }}</div>
      
      <div class="flex items-center text-primary-100">
        <span class="text-xs">Cập nhật lần cuối: {{ lastUpdated }}</span>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-3">
      <button
        @click="showDepositModal = true"
        class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition-shadow duration-200"
      >
        <div class="w-12 h-12 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <PlusIcon class="w-6 h-6 text-success-600" />
        </div>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Nạp tiền</h3>
        <p class="text-xs text-gray-600">Thêm tiền vào ví</p>
      </button>
      
      <button
        @click="showWithdrawModal = true"
        class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition-shadow duration-200"
      >
        <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <ArrowUpIcon class="w-6 h-6 text-warning-600" />
        </div>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Rút tiền</h3>
        <p class="text-xs text-gray-600">Chuyển tiền ra ngoài</p>
      </button>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold text-gray-900">Lịch sử giao dịch</h2>
        <button
          @click="refreshTransactions"
          class="text-primary-600 hover:text-primary-700 text-sm font-medium"
        >
          <ArrowPathIcon class="w-4 h-4" />
        </button>
      </div>
      
      <div class="space-y-3">
        <div
          v-for="transaction in transactions"
          :key="transaction.id"
          class="flex items-center justify-between p-3 border border-gray-200 rounded-lg"
        >
          <div class="flex items-center space-x-3">
            <div
              class="w-10 h-10 rounded-full flex items-center justify-center"
              :class="getTransactionIconClass(transaction.type)"
            >
              <component :is="getTransactionIcon(transaction.type)" class="w-5 h-5" />
            </div>
            
            <div>
              <div class="text-sm font-medium text-gray-900">{{ transaction.description }}</div>
              <div class="text-xs text-gray-500">{{ formatDate(transaction.date) }}</div>
            </div>
          </div>
          
          <div class="text-right">
            <div
              class="text-sm font-semibold"
              :class="transaction.type === 'deposit' ? 'text-success-600' : 'text-danger-600'"
            >
              {{ transaction.type === 'deposit' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
            </div>
            <div
              class="text-xs px-2 py-1 rounded-full mt-1"
              :class="getStatusClass(transaction.status)"
            >
              {{ getStatusText(transaction.status) }}
            </div>
          </div>
        </div>
      </div>
      
      <div v-if="transactions.length === 0" class="text-center py-8">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <DocumentIcon class="w-8 h-8 text-gray-400" />
        </div>
        <h3 class="text-base font-medium text-gray-900 mb-2">Chưa có giao dịch</h3>
        <p class="text-sm text-gray-500">Lịch sử giao dịch sẽ hiển thị tại đây</p>
      </div>
    </div>

    <!-- Deposit Modal -->
    <div v-if="showDepositModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-sm w-full">
        <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Nạp tiền vào ví</h3>
            <button
              @click="showDepositModal = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <form @submit.prevent="processDeposit" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số tiền nạp
            </label>
            <input
              v-model="depositForm.amount"
              type="number"
              min="10000"
              step="1000"
              placeholder="Nhập số tiền"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            />
            <p class="text-xs text-gray-500 mt-1">Tối thiểu 10,000 VND</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Phương thức thanh toán
            </label>
            <select v-model="depositForm.method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base" required>
              <option value="">Chọn phương thức</option>
              <option value="bank_transfer">Chuyển khoản ngân hàng</option>
              <option value="e_wallet">Ví điện tử</option>
              <option value="credit_card">Thẻ tín dụng</option>
            </select>
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              type="button"
              @click="showDepositModal = false"
              class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 py-2 px-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
              :disabled="isProcessing"
            >
              {{ isProcessing ? 'Đang xử lý...' : 'Nạp tiền' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Withdraw Modal -->
    <div v-if="showWithdrawModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-sm w-full">
        <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Rút tiền từ ví</h3>
            <button
              @click="showWithdrawModal = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <form @submit.prevent="processWithdraw" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số tiền rút
            </label>
            <input
              v-model="withdrawForm.amount"
              type="number"
              :min="10000"
              :max="balance"
              step="1000"
              placeholder="Nhập số tiền"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              required
            />
            <p class="text-xs text-gray-500 mt-1">
              Số dư khả dụng: {{ formatCurrency(balance) }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tài khoản nhận
            </label>
            <select v-model="withdrawForm.account" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base" required>
              <option value="">Chọn tài khoản</option>
              <option value="bank_1">Vietcombank - **** 1234</option>
              <option value="bank_2">Techcombank - **** 5678</option>
            </select>
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              type="button"
              @click="showWithdrawModal = false"
              class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 py-2 px-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
              :disabled="isProcessing"
            >
              {{ isProcessing ? 'Đang xử lý...' : 'Rút tiền' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import WalletIcon from '@/components/icons/WalletIcon.vue'
import PlusIcon from '@/components/icons/PlusIcon.vue'
import ArrowUpIcon from '@/components/icons/ArrowUpIcon.vue'
import ArrowPathIcon from '@/components/icons/ArrowPathIcon.vue'
import DocumentIcon from '@/components/icons/DocumentIcon.vue'
import XMarkIcon from '@/components/icons/XMarkIcon.vue'

// Reactive data
const balance = ref(2500000) // 2.5 million VND
const lastUpdated = ref('15:30, 20/01/2024')
const showDepositModal = ref(false)
const showWithdrawModal = ref(false)
const isProcessing = ref(false)

const depositForm = reactive({
  amount: '',
  method: ''
})

const withdrawForm = reactive({
  amount: '',
  account: ''
})

// Mock transaction data
const transactions = ref([
  {
    id: 1,
    type: 'deposit',
    description: 'Nạp tiền từ Vietcombank',
    amount: 1000000,
    date: new Date('2024-01-20T10:30:00'),
    status: 'completed'
  },
  {
    id: 2,
    type: 'withdraw',
    description: 'Rút tiền về Techcombank',
    amount: 500000,
    date: new Date('2024-01-19T14:15:00'),
    status: 'completed'
  },
  {
    id: 3,
    type: 'deposit',
    description: 'Nạp tiền từ ví MoMo',
    amount: 2000000,
    date: new Date('2024-01-18T09:45:00'),
    status: 'pending'
  }
])

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (date) => {
  return new Intl.DateTimeFormat('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date)
}

const getTransactionIcon = (type) => {
  return type === 'deposit' ? PlusIcon : ArrowUpIcon
}

const getTransactionIconClass = (type) => {
  return type === 'deposit' 
    ? 'bg-success-100 text-success-600' 
    : 'bg-warning-100 text-warning-600'
}

const getStatusClass = (status) => {
  const classes = {
    completed: 'bg-success-100 text-success-800',
    pending: 'bg-warning-100 text-warning-800',
    failed: 'bg-danger-100 text-danger-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    completed: 'Hoàn thành',
    pending: 'Đang xử lý',
    failed: 'Thất bại'
  }
  return texts[status] || 'Không xác định'
}

const refreshTransactions = async () => {
  // Mock refresh
  await new Promise(resolve => setTimeout(resolve, 500))
  // In real app, would fetch from API
}

const processDeposit = async () => {
  isProcessing.value = true
  
  // Mock API call
  await new Promise(resolve => setTimeout(resolve, 2000))
  
  // Add new transaction
  const newTransaction = {
    id: Date.now(),
    type: 'deposit',
    description: `Nạp tiền qua ${depositForm.method}`,
    amount: parseInt(depositForm.amount),
    date: new Date(),
    status: 'pending'
  }
  
  transactions.value.unshift(newTransaction)
  
  // Reset form
  depositForm.amount = ''
  depositForm.method = ''
  
  isProcessing.value = false
  showDepositModal.value = false
  
  alert('Yêu cầu nạp tiền đã được gửi thành công!')
}

const processWithdraw = async () => {
  if (parseInt(withdrawForm.amount) > balance.value) {
    alert('Số dư không đủ!')
    return
  }
  
  isProcessing.value = true
  
  // Mock API call
  await new Promise(resolve => setTimeout(resolve, 2000))
  
  // Add new transaction
  const newTransaction = {
    id: Date.now(),
    type: 'withdraw',
    description: `Rút tiền về ${withdrawForm.account}`,
    amount: parseInt(withdrawForm.amount),
    date: new Date(),
    status: 'pending'
  }
  
  transactions.value.unshift(newTransaction)
  
  // Reset form
  withdrawForm.amount = ''
  withdrawForm.account = ''
  
  isProcessing.value = false
  showWithdrawModal.value = false
  
  alert('Yêu cầu rút tiền đã được gửi thành công!')
}

onMounted(() => {
  // Load wallet data
  refreshTransactions()
})
</script>