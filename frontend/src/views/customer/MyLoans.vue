<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Khoản vay của tôi
        </h1>
        <p class="text-gray-600">
          Quản lý và theo dõi các khoản vay của bạn
        </p>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card">
          <div class="p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                <DocumentTextIcon class="w-6 h-6 text-primary-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Tổng đơn vay</p>
                <p class="text-2xl font-bold text-gray-900">{{ summary.totalApplications }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-success-100 rounded-lg flex items-center justify-center">
                <CheckCircleIcon class="w-6 h-6 text-success-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Đang hoạt động</p>
                <p class="text-2xl font-bold text-gray-900">{{ summary.activeLoans }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-warning-100 rounded-lg flex items-center justify-center">
                <CurrencyDollarIcon class="w-6 h-6 text-warning-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Tổng nợ</p>
                <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(summary.totalDebt) }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="p-6">
            <div class="flex items-center">
              <div class="w-12 h-12 bg-danger-100 rounded-lg flex items-center justify-center">
                <ClockIcon class="w-6 h-6 text-danger-600" />
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Quá hạn</p>
                <p class="text-2xl font-bold text-gray-900">{{ summary.overdueLoans }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <div class="card mb-6">
        <div class="border-b border-gray-200">
          <nav class="flex space-x-8 px-6">
            <button
              v-for="filter in filters"
              :key="filter.id"
              @click="activeFilter = filter.id"
              class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
              :class="activeFilter === filter.id 
                ? 'border-primary-500 text-primary-600' 
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            >
              {{ filter.name }}
              <span
                v-if="filter.count > 0"
                class="ml-2 px-2 py-1 text-xs rounded-full"
                :class="activeFilter === filter.id 
                  ? 'bg-primary-100 text-primary-600' 
                  : 'bg-gray-100 text-gray-600'"
              >
                {{ filter.count }}
              </span>
            </button>
          </nav>
        </div>
      </div>

      <!-- Loans List -->
      <div class="space-y-6">
        <div
          v-for="loan in filteredLoans"
          :key="loan.id"
          class="card"
        >
          <div class="p-6">
            <!-- Loan Header -->
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">
                  Khoản vay #{{ loan.applicationId }}
                </h3>
                <p class="text-sm text-gray-500">
                  Ngày vay: {{ formatDate(loan.createdAt) }}
                </p>
              </div>
              <span
                class="px-3 py-1 rounded-full text-xs font-medium"
                :class="getStatusClass(loan.status)"
              >
                {{ getStatusText(loan.status) }}
              </span>
            </div>

            <!-- Loan Details -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
              <div>
                <span class="text-sm text-gray-600">Số tiền vay:</span>
                <p class="font-semibold">{{ formatCurrency(loan.amount) }}</p>
              </div>
              <div>
                <span class="text-sm text-gray-600">Thời gian:</span>
                <p class="font-semibold">{{ loan.term }} tháng</p>
              </div>
              <div>
                <span class="text-sm text-gray-600">Lãi suất:</span>
                <p class="font-semibold">{{ loan.interestRate }}%/tháng</p>
              </div>
              <div>
                <span class="text-sm text-gray-600">Trả hàng tháng:</span>
                <p class="font-semibold text-primary-600">{{ formatCurrency(loan.monthlyPayment) }}</p>
              </div>
            </div>

            <!-- Active Loan Details -->
            <div v-if="loan.status === 'active'" class="bg-primary-50 border border-primary-200 rounded-lg p-4 mb-4">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                  <span class="text-sm text-primary-700">Số tiền còn lại:</span>
                  <p class="font-semibold text-primary-900">{{ formatCurrency(loan.remainingAmount) }}</p>
                </div>
                <div>
                  <span class="text-sm text-primary-700">Đã trả:</span>
                  <p class="font-semibold text-primary-900">{{ loan.paidInstallments }}/{{ loan.term }} kỳ</p>
                </div>
                <div>
                  <span class="text-sm text-primary-700">Kỳ tiếp theo:</span>
                  <p class="font-semibold text-primary-900">{{ formatDate(loan.nextPaymentDate) }}</p>
                </div>
              </div>

              <!-- Progress Bar -->
              <div class="mt-4">
                <div class="flex justify-between text-sm text-primary-700 mb-1">
                  <span>Tiến độ thanh toán</span>
                  <span>{{ Math.round((loan.paidInstallments / loan.term) * 100) }}%</span>
                </div>
                <div class="w-full bg-primary-200 rounded-full h-2">
                  <div
                    class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                    :style="{ width: `${(loan.paidInstallments / loan.term) * 100}%` }"
                  ></div>
                </div>
              </div>

              <!-- Overdue Warning -->
              <div v-if="loan.isOverdue" class="mt-4 p-3 bg-danger-50 border border-danger-200 rounded-lg">
                <div class="flex items-center">
                  <ExclamationTriangleIcon class="w-5 h-5 text-danger-600 mr-2" />
                  <span class="font-medium text-danger-800">Thanh toán quá hạn</span>
                </div>
                <p class="text-sm text-danger-700 mt-1">
                  Vui lòng thanh toán ngay để tránh phí phạt và ảnh hưởng đến tín dụng.
                </p>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-3">
              <button
                v-if="loan.status === 'active'"
                @click="makePayment(loan)"
                class="btn btn-primary btn-sm"
              >
                <CreditCardIcon class="w-4 h-4 mr-2" />
                Thanh toán
              </button>
              
              <button
                v-if="loan.status === 'active'"
                @click="viewPaymentHistory(loan)"
                class="btn btn-secondary btn-sm"
              >
                <ClockIcon class="w-4 h-4 mr-2" />
                Lịch sử thanh toán
              </button>
              
              <button
                @click="viewLoanDetails(loan)"
                class="btn btn-secondary btn-sm"
              >
                <EyeIcon class="w-4 h-4 mr-2" />
                Chi tiết
              </button>
              
              <button
                v-if="loan.status === 'active'"
                @click="downloadContract(loan)"
                class="btn btn-secondary btn-sm"
              >
                <DocumentArrowDownIcon class="w-4 h-4 mr-2" />
                Tải hợp đồng
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredLoans.length === 0" class="text-center py-12">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <DocumentTextIcon class="w-8 h-8 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          Không có khoản vay nào
        </h3>
        <p class="text-gray-600 mb-6">
          {{ getEmptyStateMessage() }}
        </p>
        <router-link
          to="/register"
          class="btn btn-primary"
        >
          Đăng ký vay ngay
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLoanStore } from '@/stores/loan'

// Icons
import DocumentTextIcon from '@/components/icons/DocumentTextIcon.vue'
import CheckCircleIcon from '@/components/icons/CheckCircleIcon.vue'
import CurrencyDollarIcon from '@/components/icons/CurrencyDollarIcon.vue'
import ClockIcon from '@/components/icons/ClockIcon.vue'
import ExclamationTriangleIcon from '@/components/icons/ExclamationTriangleIcon.vue'
import CreditCardIcon from '@/components/icons/CreditCardIcon.vue'
import EyeIcon from '@/components/icons/EyeIcon.vue'
import DocumentArrowDownIcon from '@/components/icons/DocumentArrowDownIcon.vue'

const loanStore = useLoanStore()

// State
const activeFilter = ref('all')
const loans = ref([])

const filters = [
  { id: 'all', name: 'Tất cả', count: 0 },
  { id: 'active', name: 'Đang hoạt động', count: 0 },
  { id: 'pending', name: 'Chờ duyệt', count: 0 },
  { id: 'completed', name: 'Hoàn thành', count: 0 },
  { id: 'overdue', name: 'Quá hạn', count: 0 }
]

const summary = ref({
  totalApplications: 0,
  activeLoans: 0,
  totalDebt: 0,
  overdueLoans: 0
})

// Computed
const filteredLoans = computed(() => {
  if (activeFilter.value === 'all') {
    return loans.value
  } else if (activeFilter.value === 'overdue') {
    return loans.value.filter(loan => loan.isOverdue)
  } else {
    return loans.value.filter(loan => loan.status === activeFilter.value)
  }
})

// Methods
const loadMyLoans = async () => {
  try {
    // Mock data - replace with actual API call
    loans.value = [
      {
        id: 1,
        applicationId: 'LN001',
        amount: 50000000,
        term: 12,
        interestRate: 2.0,
        monthlyPayment: 4583333,
        status: 'active',
        remainingAmount: 35000000,
        paidInstallments: 3,
        nextPaymentDate: '2024-02-15T00:00:00Z',
        isOverdue: false,
        createdAt: '2023-11-15T00:00:00Z'
      },
      {
        id: 2,
        applicationId: 'LN002',
        amount: 30000000,
        term: 6,
        interestRate: 2.0,
        monthlyPayment: 5600000,
        status: 'completed',
        remainingAmount: 0,
        paidInstallments: 6,
        nextPaymentDate: null,
        isOverdue: false,
        createdAt: '2023-08-01T00:00:00Z'
      },
      {
        id: 3,
        applicationId: 'LN003',
        amount: 100000000,
        term: 24,
        interestRate: 2.0,
        monthlyPayment: 6166667,
        status: 'pending',
        remainingAmount: 100000000,
        paidInstallments: 0,
        nextPaymentDate: null,
        isOverdue: false,
        createdAt: '2024-01-10T00:00:00Z'
      }
    ]

    // Update summary
    summary.value = {
      totalApplications: loans.value.length,
      activeLoans: loans.value.filter(loan => loan.status === 'active').length,
      totalDebt: loans.value
        .filter(loan => loan.status === 'active')
        .reduce((sum, loan) => sum + loan.remainingAmount, 0),
      overdueLoans: loans.value.filter(loan => loan.isOverdue).length
    }

    // Update filter counts
    filters.forEach(filter => {
      if (filter.id === 'all') {
        filter.count = loans.value.length
      } else if (filter.id === 'overdue') {
        filter.count = loans.value.filter(loan => loan.isOverdue).length
      } else {
        filter.count = loans.value.filter(loan => loan.status === filter.id).length
      }
    })
  } catch (error) {
    console.error('Error loading loans:', error)
  }
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-warning-100 text-warning-800',
    active: 'bg-primary-100 text-primary-800',
    completed: 'bg-success-100 text-success-800',
    rejected: 'bg-danger-100 text-danger-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Chờ duyệt',
    active: 'Đang hoạt động',
    completed: 'Hoàn thành',
    rejected: 'Từ chối'
  }
  return texts[status] || 'Không xác định'
}

const getEmptyStateMessage = () => {
  const messages = {
    all: 'Bạn chưa có khoản vay nào.',
    active: 'Bạn không có khoản vay nào đang hoạt động.',
    pending: 'Bạn không có đơn vay nào đang chờ duyệt.',
    completed: 'Bạn chưa hoàn thành khoản vay nào.',
    overdue: 'Bạn không có khoản vay nào quá hạn.'
  }
  return messages[activeFilter.value] || 'Không có dữ liệu.'
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit'
  })
}

const makePayment = (loan) => {
  alert(`Chuyển đến trang thanh toán cho khoản vay #${loan.applicationId}`)
}

const viewPaymentHistory = (loan) => {
  alert(`Xem lịch sử thanh toán cho khoản vay #${loan.applicationId}`)
}

const viewLoanDetails = (loan) => {
  alert(`Xem chi tiết khoản vay #${loan.applicationId}`)
}

const downloadContract = (loan) => {
  alert(`Tải hợp đồng cho khoản vay #${loan.applicationId}`)
}

onMounted(() => {
  loadMyLoans()
})
</script>

<style scoped>
.btn-sm {
  @apply px-3 py-1.5 text-sm;
}
</style>