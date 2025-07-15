<template>
  <div class="space-y-6">
    <!-- Search and Filters -->
    <div class="card p-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex-1 max-w-md">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm kiếm theo SĐT hoặc CCCD..."
              class="input pl-10"
            />
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
          </div>
        </div>
        
        <div class="flex items-center space-x-4">
          <select v-model="sortBy" class="input">
            <option value="approvedAt">Ngày duyệt</option>
            <option value="nextPayment">Ngày trả tiếp theo</option>
            <option value="amount">Số tiền vay</option>
            <option value="remainingAmount">Số dư còn lại</option>
          </select>
          
          <button
            @click="refreshData"
            class="btn btn-secondary"
          >
            <ArrowPathIcon class="w-4 h-4 mr-2" />
            Làm mới
          </button>
        </div>
      </div>
    </div>

    <!-- Active Loans Table -->
    <div class="card">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Danh sách khoản vay đang hoạt động
          <span class="ml-2 text-sm font-normal text-gray-500">
            ({{ filteredLoans.length }} khoản vay)
          </span>
        </h3>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Mã hồ sơ
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Khách hàng
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Số tiền vay
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Số dư còn lại
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Thời gian vay
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ngày trả tiếp theo
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Trạng thái
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="loan in filteredLoans"
              :key="loan.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ loan.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ loan.customerName }}</div>
                  <div class="text-sm text-gray-500">{{ loan.phone }}</div>
                  <div class="text-sm text-gray-500">{{ loan.idCard }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatCurrency(loan.amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ formatCurrency(loan.remainingAmount) }}</div>
                <div class="text-xs text-gray-500">
                  {{ Math.round((loan.remainingAmount / loan.amount) * 100) }}% còn lại
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ loan.term }} tháng
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ formatDate(loan.nextPayment) }}</div>
                <div 
                  class="text-xs"
                  :class="getPaymentStatusClass(loan.nextPayment)"
                >
                  {{ getPaymentStatusText(loan.nextPayment) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-success-100 text-success-800">
                  Hoạt động
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                  <button
                    @click="viewLoanDetail(loan)"
                    class="text-primary-600 hover:text-primary-900"
                    title="Xem chi tiết"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  
                  <button
                    @click="recordPayment(loan)"
                    class="text-success-600 hover:text-success-900"
                    title="Ghi nhận thanh toán"
                  >
                    <CurrencyDollarIcon class="w-4 h-4" />
                  </button>
                  
                  <button
                    @click="sendReminder(loan)"
                    class="text-warning-600 hover:text-warning-900"
                    title="Gửi nhắc nhở"
                  >
                    <BellIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="filteredLoans.length === 0" class="text-center py-12">
        <ClipboardDocumentListIcon class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-900">Không có khoản vay nào</h3>
        <p class="mt-1 text-sm text-gray-500">Chưa có khoản vay nào đang hoạt động</p>
      </div>
    </div>

    <!-- Loan Detail Modal -->
    <div v-if="selectedLoan" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              Chi tiết khoản vay {{ selectedLoan.id }}
            </h3>
            <button
              @click="selectedLoan = null"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <div class="p-6 space-y-6">
          <!-- Customer & Loan Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3">Thông tin khách hàng</h4>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-500">Họ tên:</span>
                  <span class="font-medium">{{ selectedLoan.customerName }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Số điện thoại:</span>
                  <span class="font-medium">{{ selectedLoan.phone }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">CCCD:</span>
                  <span class="font-medium">{{ selectedLoan.idCard }}</span>
                </div>
              </div>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3">Thông tin khoản vay</h4>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-500">Số tiền vay:</span>
                  <span class="font-medium">{{ formatCurrency(selectedLoan.amount) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Số dư còn lại:</span>
                  <span class="font-medium text-danger-600">{{ formatCurrency(selectedLoan.remainingAmount) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Thời gian vay:</span>
                  <span class="font-medium">{{ selectedLoan.term }} tháng</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Ngày duyệt:</span>
                  <span class="font-medium">{{ formatDate(selectedLoan.approvedAt) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Ngày trả tiếp theo:</span>
                  <span class="font-medium">{{ formatDate(selectedLoan.nextPayment) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment History -->
          <div>
            <h4 class="text-sm font-medium text-gray-900 mb-3">Lịch sử thanh toán</h4>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm text-gray-500 text-center">
                Tính năng lịch sử thanh toán sẽ được triển khai sớm
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Ghi nhận thanh toán</h3>
        </div>
        
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số tiền thanh toán
            </label>
            <input
              v-model="paymentAmount"
              type="number"
              placeholder="Nhập số tiền"
              class="input"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Ghi chú
            </label>
            <textarea
              v-model="paymentNote"
              rows="3"
              placeholder="Ghi chú về khoản thanh toán"
              class="input resize-none"
            ></textarea>
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              @click="showPaymentModal = false"
              class="flex-1 btn btn-secondary"
            >
              Hủy
            </button>
            <button
              @click="confirmPayment"
              class="flex-1 btn btn-success"
            >
              Xác nhận
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useLoanStore } from '@/stores/loan'

// Icons
import MagnifyingGlassIcon from '@/components/icons/MagnifyingGlassIcon.vue'
import ArrowPathIcon from '@/components/icons/ArrowPathIcon.vue'
import EyeIcon from '@/components/icons/EyeIcon.vue'
import CurrencyDollarIcon from '@/components/icons/CurrencyDollarIcon.vue'
import BellIcon from '@/components/icons/BellIcon.vue'
import ClipboardDocumentListIcon from '@/components/icons/ClipboardDocumentListIcon.vue'
import XMarkIcon from '@/components/icons/XMarkIcon.vue'

const loanStore = useLoanStore()

// Reactive data
const searchQuery = ref('')
const sortBy = ref('approvedAt')
const selectedLoan = ref(null)
const showPaymentModal = ref(false)
const paymentAmount = ref('')
const paymentNote = ref('')

// Computed properties
const filteredLoans = computed(() => {
  let loans = [...loanStore.activeLoanApplications]

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    loans = loans.filter(loan => 
      loan.phone.includes(query) || 
      loan.idCard.includes(query) ||
      loan.customerName.toLowerCase().includes(query)
    )
  }

  // Sort
  loans.sort((a, b) => {
    if (sortBy.value === 'approvedAt') {
      return new Date(b.approvedAt) - new Date(a.approvedAt)
    } else if (sortBy.value === 'nextPayment') {
      return new Date(a.nextPayment) - new Date(b.nextPayment)
    } else if (sortBy.value === 'amount') {
      return b.amount - a.amount
    } else if (sortBy.value === 'remainingAmount') {
      return b.remainingAmount - a.remainingAmount
    }
    return 0
  })

  return loans
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const getPaymentStatusClass = (nextPaymentDate) => {
  const today = new Date()
  const paymentDate = new Date(nextPaymentDate)
  const diffDays = Math.ceil((paymentDate - today) / (1000 * 60 * 60 * 24))

  if (diffDays < 0) return 'text-danger-600'
  if (diffDays <= 3) return 'text-warning-600'
  return 'text-success-600'
}

const getPaymentStatusText = (nextPaymentDate) => {
  const today = new Date()
  const paymentDate = new Date(nextPaymentDate)
  const diffDays = Math.ceil((paymentDate - today) / (1000 * 60 * 60 * 24))

  if (diffDays < 0) return `Quá hạn ${Math.abs(diffDays)} ngày`
  if (diffDays === 0) return 'Đến hạn hôm nay'
  if (diffDays <= 3) return `Còn ${diffDays} ngày`
  return `Còn ${diffDays} ngày`
}

const viewLoanDetail = (loan) => {
  selectedLoan.value = loan
}

const recordPayment = (loan) => {
  selectedLoan.value = loan
  showPaymentModal.value = true
}

const confirmPayment = () => {
  // Mock payment recording
  alert(`Đã ghi nhận thanh toán ${formatCurrency(paymentAmount.value)} cho khoản vay ${selectedLoan.value.id}`)
  
  // Reset form
  paymentAmount.value = ''
  paymentNote.value = ''
  showPaymentModal.value = false
  selectedLoan.value = null
}

const sendReminder = (loan) => {
  // Mock reminder functionality
  alert(`Đã gửi nhắc nhở thanh toán đến ${loan.phone}`)
}

const refreshData = () => {
  // Mock refresh functionality
  console.log('Refreshing active loans data...')
}
</script>