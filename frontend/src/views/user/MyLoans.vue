<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-900">Khoản vay của tôi</h1>
      <button
        @click="refreshLoans"
        class="p-2 text-gray-500 hover:text-gray-700"
      >
        <ArrowPathIcon class="w-5 h-5" />
      </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 gap-3">
      <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="text-center">
          <div class="text-2xl font-bold text-primary-600">{{ activeLoans.length }}</div>
          <div class="text-sm text-gray-500">Đang vay</div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm p-4">
        <div class="text-center">
          <div class="text-2xl font-bold text-green-600">{{ formatCurrency(totalDebt) }}</div>
          <div class="text-sm text-gray-500">Tổng nợ</div>
        </div>
      </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-xl shadow-sm p-1">
      <div class="flex">
        <button
          v-for="tab in filterTabs"
          :key="tab.key"
          @click="activeFilter = tab.key"
          class="flex-1 py-2 px-3 text-sm font-medium rounded-lg transition-colors duration-200"
          :class="activeFilter === tab.key 
            ? 'bg-primary-600 text-white' 
            : 'text-gray-600 hover:text-gray-900'"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Loans List -->
    <div class="space-y-3">
      <div
        v-for="loan in filteredLoans"
        :key="loan.id"
        class="bg-white rounded-xl shadow-sm p-4"
      >
        <!-- Loan Header -->
        <div class="flex items-center justify-between mb-3">
          <div>
            <div class="font-semibold text-gray-900">{{ formatCurrency(loan.amount) }}</div>
            <div class="text-sm text-gray-500">{{ loan.term }} tháng</div>
          </div>
          <div class="text-right">
            <div
              class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
              :class="getStatusClass(loan.status)"
            >
              {{ getStatusText(loan.status) }}
            </div>
            <div class="text-sm text-gray-500 mt-1">{{ formatDate(loan.createdAt) }}</div>
          </div>
        </div>

        <!-- Loan Progress (for active loans) -->
        <div v-if="loan.status === 'active'" class="mb-3">
          <div class="flex justify-between text-sm text-gray-600 mb-1">
            <span>Đã trả: {{ loan.paidMonths }}/{{ loan.term }} tháng</span>
            <span>{{ Math.round((loan.paidMonths / loan.term) * 100) }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="bg-primary-600 h-2 rounded-full transition-all duration-300"
              :style="{ width: `${(loan.paidMonths / loan.term) * 100}%` }"
            ></div>
          </div>
        </div>

        <!-- Loan Details -->
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-gray-600">Lãi suất:</span>
            <span class="font-medium">{{ loan.interestRate }}%/tháng</span>
          </div>
          <div class="flex justify-between">
            <span class="text-gray-600">Trả hàng tháng:</span>
            <span class="font-medium">{{ formatCurrency(loan.monthlyPayment) }}</span>
          </div>
          <div v-if="loan.status === 'active'" class="flex justify-between">
            <span class="text-gray-600">Kỳ thanh toán tiếp theo:</span>
            <span class="font-medium text-orange-600">{{ formatDate(loan.nextPaymentDate) }}</span>
          </div>
          <div v-if="loan.status === 'active'" class="flex justify-between">
            <span class="text-gray-600">Còn lại:</span>
            <span class="font-medium">{{ formatCurrency(loan.remainingAmount) }}</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-2 mt-4">
          <button
            v-if="loan.status === 'active'"
            @click="makePayment(loan)"
            class="flex-1 bg-primary-600 text-white py-2 px-3 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors duration-200"
          >
            Thanh toán
          </button>
          <button
            @click="viewLoanDetails(loan)"
            class="flex-1 border border-gray-300 text-gray-700 py-2 px-3 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors duration-200"
          >
            Chi tiết
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredLoans.length === 0" class="text-center py-12">
      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <DocumentTextIcon class="w-8 h-8 text-gray-400" />
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Không có khoản vay nào</h3>
      <p class="text-gray-500 mb-4">{{ getEmptyStateMessage() }}</p>
      <router-link
        to="/register"
        class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-200"
      >
        Đăng ký vay ngay
      </router-link>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-sm w-full">
        <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Thanh toán khoản vay</h3>
            <button
              @click="showPaymentModal = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <div class="p-4">
          <div v-if="selectedLoan" class="space-y-4">
            <!-- Loan Info -->
            <div class="bg-gray-50 rounded-lg p-3">
              <div class="text-sm text-gray-600 mb-1">Khoản vay</div>
              <div class="font-semibold">{{ formatCurrency(selectedLoan.amount) }}</div>
            </div>

            <!-- Payment Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Số tiền thanh toán
              </label>
              <input
                v-model="paymentAmount"
                type="number"
                :min="selectedLoan.monthlyPayment"
                :max="selectedLoan.remainingAmount"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                :placeholder="formatCurrency(selectedLoan.monthlyPayment)"
              />
              <div class="text-xs text-gray-500 mt-1">
                Tối thiểu: {{ formatCurrency(selectedLoan.monthlyPayment) }}
              </div>
            </div>

            <!-- Payment Method -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Phương thức thanh toán
              </label>
              <select
                v-model="paymentMethod"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              >
                <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                <option value="e_wallet">Ví điện tử</option>
                <option value="credit_card">Thẻ tín dụng</option>
              </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-3 pt-4">
              <button
                @click="showPaymentModal = false"
                class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              >
                Hủy
              </button>
              <button
                @click="processPayment"
                class="flex-1 py-2 px-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
              >
                Thanh toán
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loan Details Modal -->
    <div v-if="showDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-sm w-full max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Chi tiết khoản vay</h3>
            <button
              @click="showDetailsModal = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <div v-if="selectedLoan" class="p-4 space-y-4">
          <!-- Basic Info -->
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Mã khoản vay:</span>
              <span class="font-medium">{{ selectedLoan.id }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Số tiền vay:</span>
              <span class="font-medium">{{ formatCurrency(selectedLoan.amount) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Thời gian vay:</span>
              <span class="font-medium">{{ selectedLoan.term }} tháng</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Lãi suất:</span>
              <span class="font-medium">{{ selectedLoan.interestRate }}%/tháng</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Ngày vay:</span>
              <span class="font-medium">{{ formatDate(selectedLoan.createdAt) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Trạng thái:</span>
              <span
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                :class="getStatusClass(selectedLoan.status)"
              >
                {{ getStatusText(selectedLoan.status) }}
              </span>
            </div>
          </div>

          <!-- Payment History -->
          <div v-if="selectedLoan.paymentHistory && selectedLoan.paymentHistory.length > 0">
            <h4 class="font-medium text-gray-900 mb-3">Lịch sử thanh toán</h4>
            <div class="space-y-2">
              <div
                v-for="payment in selectedLoan.paymentHistory"
                :key="payment.id"
                class="flex justify-between items-center p-2 bg-gray-50 rounded-lg"
              >
                <div>
                  <div class="text-sm font-medium">{{ formatCurrency(payment.amount) }}</div>
                  <div class="text-xs text-gray-500">{{ formatDate(payment.date) }}</div>
                </div>
                <div class="text-xs text-green-600">Đã thanh toán</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLoanStore } from '@/stores/loan'
import { apiHelpers } from '@/services'

// Icons
import ArrowPathIcon from '@/components/icons/ArrowPathIcon.vue'
import DocumentTextIcon from '@/components/icons/DocumentTextIcon.vue'
import XMarkIcon from '@/components/icons/XMarkIcon.vue'

const loanStore = useLoanStore()

// Reactive data
const activeFilter = ref('all')
const showPaymentModal = ref(false)
const showDetailsModal = ref(false)
const selectedLoan = ref(null)
const paymentAmount = ref('')
const paymentMethod = ref('bank_transfer')

const filterTabs = [
  { key: 'all', label: 'Tất cả' },
  { key: 'active', label: 'Đang vay' },
  { key: 'completed', label: 'Hoàn thành' },
  { key: 'overdue', label: 'Quá hạn' }
]

// Use loan store data
const loans = computed(() => loanStore.loanApplications)

// Computed properties
const filteredLoans = computed(() => {
  if (activeFilter.value === 'all') return loans.value
  return loans.value.filter(loan => loan.status === activeFilter.value)
})

const activeLoans = computed(() => {
  return loans.value.filter(loan => loan.status === 'active')
})

const totalDebt = computed(() => {
  return activeLoans.value.reduce((total, loan) => total + (loan.remaining_amount || loan.amount || 0), 0)
})

// Methods
const formatCurrency = (amount) => {
  return apiHelpers.formatCurrency(amount)
}

const formatDate = (date) => {
  return new Intl.DateTimeFormat('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  }).format(date)
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    overdue: 'bg-red-100 text-red-800',
    pending: 'bg-yellow-100 text-yellow-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    active: 'Đang vay',
    completed: 'Hoàn thành',
    overdue: 'Quá hạn',
    pending: 'Chờ duyệt'
  }
  return texts[status] || 'Không xác định'
}

const getEmptyStateMessage = () => {
  const messages = {
    all: 'Bạn chưa có khoản vay nào.',
    active: 'Bạn không có khoản vay đang hoạt động.',
    completed: 'Bạn chưa hoàn thành khoản vay nào.',
    overdue: 'Bạn không có khoản vay quá hạn.'
  }
  return messages[activeFilter.value] || 'Không có dữ liệu.'
}

const refreshLoans = async () => {
  await loanStore.fetchMyLoanApplications()
}

const makePayment = (loan) => {
  selectedLoan.value = loan
  paymentAmount.value = loan.monthlyPayment
  showPaymentModal.value = true
}

const viewLoanDetails = (loan) => {
  selectedLoan.value = loan
  showDetailsModal.value = true
}

const processPayment = async () => {
  // Mock payment processing
  await new Promise(resolve => setTimeout(resolve, 1000))
  
  // Update loan data
  const loan = selectedLoan.value
  const amount = parseInt(paymentAmount.value)
  
  // Add to payment history
  if (!loan.paymentHistory) loan.paymentHistory = []
  loan.paymentHistory.push({
    id: Date.now(),
    amount: amount,
    date: new Date()
  })
  
  // Update loan status
  loan.remainingAmount -= amount
  loan.paidMonths += 1
  
  if (loan.remainingAmount <= 0) {
    loan.status = 'completed'
    loan.remainingAmount = 0
  } else {
    // Update next payment date
    loan.nextPaymentDate = new Date(loan.nextPaymentDate.getTime() + 30 * 24 * 60 * 60 * 1000)
  }
  
  showPaymentModal.value = false
  alert('Thanh toán thành công!')
}

onMounted(() => {
  // Load loans data
  refreshLoans()
})
</script>