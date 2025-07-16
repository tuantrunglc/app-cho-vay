<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">
          Tra cứu đơn vay
        </h1>
        <p class="text-lg text-gray-600">
          Nhập thông tin để kiểm tra trạng thái đơn vay của bạn
        </p>
      </div>

      <!-- Lookup Form -->
      <div class="card mb-8">
        <div class="p-6">
          <form @submit.prevent="handleLookup" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Số điện thoại *
              </label>
              <input
                v-model="lookupForm.phone"
                type="tel"
                class="input"
                placeholder="Nhập số điện thoại đã đăng ký"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Số CMND/CCCD *
              </label>
              <input
                v-model="lookupForm.idCard"
                type="text"
                class="input"
                placeholder="Nhập số CMND/CCCD"
                required
              />
            </div>

            <div v-if="error" class="text-danger-600 text-sm">
              {{ error }}
            </div>

            <button
              type="submit"
              class="btn btn-primary w-full"
              :disabled="isLoading"
            >
              {{ isLoading ? 'Đang tra cứu...' : 'Tra cứu đơn vay' }}
            </button>
          </form>
        </div>
      </div>

      <!-- Lookup Results -->
      <div v-if="loanApplications.length > 0" class="space-y-4">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
          Kết quả tra cứu ({{ loanApplications.length }} đơn vay)
        </h2>

        <div
          v-for="loan in loanApplications"
          :key="loan.id"
          class="card"
        >
          <div class="p-6">
            <!-- Loan Header -->
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">
                  Đơn vay #{{ loan.applicationId }}
                </h3>
                <p class="text-sm text-gray-500">
                  Ngày đăng ký: {{ formatDate(loan.createdAt) }}
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <span class="text-sm text-gray-600">Số tiền vay:</span>
                <span class="ml-2 font-semibold">{{ formatCurrency(loan.amount) }}</span>
              </div>
              <div>
                <span class="text-sm text-gray-600">Thời gian vay:</span>
                <span class="ml-2 font-semibold">{{ loan.term }} tháng</span>
              </div>
              <div>
                <span class="text-sm text-gray-600">Lãi suất:</span>
                <span class="ml-2 font-semibold">{{ loan.interestRate }}%/tháng</span>
              </div>
              <div>
                <span class="text-sm text-gray-600">Trả hàng tháng:</span>
                <span class="ml-2 font-semibold text-primary-600">{{ formatCurrency(loan.monthlyPayment) }}</span>
              </div>
            </div>

            <!-- Status Details -->
            <div v-if="loan.status === 'approved'" class="bg-success-50 border border-success-200 rounded-lg p-4">
              <div class="flex items-center mb-2">
                <CheckCircleIcon class="w-5 h-5 text-success-600 mr-2" />
                <span class="font-medium text-success-800">Đơn vay đã được phê duyệt</span>
              </div>
              <p class="text-sm text-success-700">
                Tiền sẽ được chuyển vào tài khoản của bạn trong vòng 24 giờ.
                Vui lòng kiểm tra tài khoản ngân hàng.
              </p>
            </div>

            <div v-else-if="loan.status === 'rejected'" class="bg-danger-50 border border-danger-200 rounded-lg p-4">
              <div class="flex items-center mb-2">
                <XCircleIcon class="w-5 h-5 text-danger-600 mr-2" />
                <span class="font-medium text-danger-800">Đơn vay bị từ chối</span>
              </div>
              <p class="text-sm text-danger-700">
                {{ loan.rejectionReason || 'Hồ sơ không đáp ứng yêu cầu của chúng tôi.' }}
              </p>
            </div>

            <div v-else-if="loan.status === 'pending'" class="bg-warning-50 border border-warning-200 rounded-lg p-4">
              <div class="flex items-center mb-2">
                <ClockIcon class="w-5 h-5 text-warning-600 mr-2" />
                <span class="font-medium text-warning-800">Đang xét duyệt</span>
              </div>
              <p class="text-sm text-warning-700">
                Đơn vay của bạn đang được xem xét. Chúng tôi sẽ liên hệ với bạn trong vòng 24 giờ.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- No Results -->
      <div v-else-if="hasSearched && !isLoading" class="text-center py-12">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <DocumentTextIcon class="w-8 h-8 text-gray-400" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          Không tìm thấy đơn vay
        </h3>
        <p class="text-gray-600">
          Không tìm thấy đơn vay nào với thông tin bạn cung cấp.
          Vui lòng kiểm tra lại số điện thoại và CMND/CCCD.
        </p>
      </div>

      <!-- Help Section -->
      <div class="card mt-8">
        <div class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Cần hỗ trợ?
          </h3>
          <div class="space-y-3 text-sm text-gray-600">
            <div class="flex items-center">
              <PhoneIcon class="w-4 h-4 mr-2" />
              <span>Hotline: <strong class="text-primary-600">1900 1234</strong></span>
            </div>
            <div class="flex items-center">
              <EnvelopeIcon class="w-4 h-4 mr-2" />
              <span>Email: <strong class="text-primary-600">support@vaynhanh.com</strong></span>
            </div>
            <div class="flex items-center">
              <ClockIcon class="w-4 h-4 mr-2" />
              <span>Thời gian hỗ trợ: 8:00 - 22:00 (Thứ 2 - Chủ nhật)</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useLoanStore } from '@/stores/loan'

// Icons
import CheckCircleIcon from '@/components/icons/CheckCircleIcon.vue'
import XCircleIcon from '@/components/icons/XCircleIcon.vue'
import ClockIcon from '@/components/icons/ClockIcon.vue'
import DocumentTextIcon from '@/components/icons/DocumentTextIcon.vue'
import PhoneIcon from '@/components/icons/PhoneIcon.vue'
import EnvelopeIcon from '@/components/icons/EnvelopeIcon.vue'

const loanStore = useLoanStore()

// State
const isLoading = ref(false)
const hasSearched = ref(false)
const error = ref('')
const loanApplications = ref([])

const lookupForm = reactive({
  phone: '',
  idCard: ''
})

// Methods
const handleLookup = async () => {
  error.value = ''
  isLoading.value = true
  hasSearched.value = true
  
  try {
    const result = await loanStore.lookupLoanByPhone(lookupForm.phone)
    
    if (result.success) {
      loanApplications.value = result.data?.applications || [result.data] || []
    } else {
      error.value = result.error || 'Không tìm thấy thông tin khoản vay'
      loanApplications.value = []
    }
  } catch (err) {
    error.value = 'Có lỗi xảy ra khi tra cứu'
    loanApplications.value = []
  } finally {
    isLoading.value = false
  }
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-warning-100 text-warning-800',
    approved: 'bg-success-100 text-success-800',
    rejected: 'bg-danger-100 text-danger-800',
    active: 'bg-primary-100 text-primary-800',
    completed: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Chờ duyệt',
    approved: 'Đã duyệt',
    rejected: 'Từ chối',
    active: 'Đang hoạt động',
    completed: 'Hoàn thành'
  }
  return texts[status] || 'Không xác định'
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
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>