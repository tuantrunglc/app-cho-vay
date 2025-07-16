<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Progress Steps -->
      <div class="mb-8">
        <div class="flex items-center justify-center">
          <div class="flex items-center space-x-4">
            <div
              v-for="(step, index) in steps"
              :key="index"
              class="flex items-center"
            >
              <div
                class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors duration-200"
                :class="getStepClass(index)"
              >
                <CheckIcon
                  v-if="index < currentStep"
                  class="w-5 h-5 text-white"
                />
                <span
                  v-else
                  class="text-sm font-semibold"
                  :class="index === currentStep ? 'text-white' : 'text-gray-500'"
                >
                  {{ index + 1 }}
                </span>
              </div>
              <span
                class="ml-2 text-sm font-medium"
                :class="index <= currentStep ? 'text-primary-600' : 'text-gray-500'"
              >
                {{ step }}
              </span>
              <ArrowRightIcon
                v-if="index < steps.length - 1"
                class="w-4 h-4 text-gray-400 ml-4"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Form Card -->
      <div class="card">
        <div class="p-6 border-b border-gray-200">
          <h1 class="text-2xl font-bold text-gray-900">
            {{ steps[currentStep] }}
          </h1>
          <p class="text-gray-600 mt-1">
            {{ getStepDescription(currentStep) }}
          </p>
        </div>

        <form @submit.prevent="handleSubmit" class="p-6">
          <!-- Step 1: Loan Information -->
          <div v-if="currentStep === 0" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Số tiền cần vay *
                </label>
                <input
                  v-model="form.amount"
                  type="number"
                  :min="contentStore.loanLimits.minAmount"
                  :max="contentStore.loanLimits.maxAmount"
                  :step="contentStore.loanLimits.stepAmount"
                  class="input"
                  required
                />
                <p class="text-xs text-gray-500 mt-1">
                  Từ {{ formatCurrency(contentStore.loanLimits.minAmount) }} 
                  đến {{ formatCurrency(contentStore.loanLimits.maxAmount) }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Thời gian vay *
                </label>
                <select v-model="form.term" class="input" required>
                  <option value="">Chọn thời gian vay</option>
                  <option v-for="term in contentStore.activeTerms" :key="term" :value="term">
                    {{ term }} tháng
                  </option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Mục đích vay
              </label>
              <select v-model="form.purpose" class="input">
                <option value="">Chọn mục đích vay</option>
                <option value="business">Kinh doanh</option>
                <option value="personal">Cá nhân</option>
                <option value="education">Học tập</option>
                <option value="medical">Y tế</option>
                <option value="other">Khác</option>
              </select>
            </div>

            <!-- Loan Summary -->
            <div v-if="form.amount && form.term" class="bg-primary-50 rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Thông tin khoản vay</h3>
              <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-600">Số tiền vay:</span>
                  <span class="font-semibold">{{ formatCurrency(form.amount) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Thời gian:</span>
                  <span class="font-semibold">{{ form.term }} tháng</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Lãi suất:</span>
                  <span class="font-semibold">{{ contentStore.interestRates.baseRate }}%/tháng</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-600">Trả hàng tháng:</span>
                  <span class="font-semibold text-primary-600">{{ formatCurrency(monthlyPayment) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Step 2: Personal Information -->
          <div v-if="currentStep === 1" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Họ và tên *
                </label>
                <input
                  v-model="form.fullName"
                  type="text"
                  class="input"
                  placeholder="Nhập họ và tên đầy đủ"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Số điện thoại *
                </label>
                <input
                  v-model="form.phone"
                  type="tel"
                  class="input"
                  placeholder="Nhập số điện thoại"
                  required
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Email
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  class="input"
                  placeholder="Nhập địa chỉ email"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Ngày sinh *
                </label>
                <input
                  v-model="form.dateOfBirth"
                  type="date"
                  class="input"
                  required
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Số CMND/CCCD *
              </label>
              <input
                v-model="form.idCard"
                type="text"
                class="input"
                placeholder="Nhập số CMND/CCCD"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Địa chỉ thường trú *
              </label>
              <textarea
                v-model="form.address"
                rows="3"
                class="input resize-none"
                placeholder="Nhập địa chỉ thường trú"
                required
              ></textarea>
            </div>
          </div>

          <!-- Step 3: Employment Information -->
          <div v-if="currentStep === 2" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Nghề nghiệp *
                </label>
                <select v-model="form.occupation" class="input" required>
                  <option value="">Chọn nghề nghiệp</option>
                  <option value="employee">Nhân viên</option>
                  <option value="business">Kinh doanh</option>
                  <option value="freelancer">Tự do</option>
                  <option value="student">Học sinh/Sinh viên</option>
                  <option value="retired">Nghỉ hưu</option>
                  <option value="other">Khác</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Thu nhập hàng tháng *
                </label>
                <input
                  v-model="form.monthlyIncome"
                  type="number"
                  class="input"
                  placeholder="Nhập thu nhập hàng tháng"
                  required
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Tên công ty/Nơi làm việc
              </label>
              <input
                v-model="form.company"
                type="text"
                class="input"
                placeholder="Nhập tên công ty hoặc nơi làm việc"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Địa chỉ công ty
              </label>
              <textarea
                v-model="form.companyAddress"
                rows="3"
                class="input resize-none"
                placeholder="Nhập địa chỉ công ty"
              ></textarea>
            </div>
          </div>

          <!-- Step 4: Confirmation -->
          <div v-if="currentStep === 3" class="space-y-6">
            <div class="bg-gray-50 rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Xác nhận thông tin</h3>
              
              <!-- Loan Information -->
              <div class="mb-6">
                <h4 class="font-medium text-gray-900 mb-2">Thông tin khoản vay</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Số tiền vay:</span>
                    <span class="font-semibold">{{ formatCurrency(form.amount) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Thời gian vay:</span>
                    <span class="font-semibold">{{ form.term }} tháng</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Mục đích vay:</span>
                    <span class="font-semibold">{{ getPurposeText(form.purpose) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Trả hàng tháng:</span>
                    <span class="font-semibold text-primary-600">{{ formatCurrency(monthlyPayment) }}</span>
                  </div>
                </div>
              </div>

              <!-- Personal Information -->
              <div class="mb-6">
                <h4 class="font-medium text-gray-900 mb-2">Thông tin cá nhân</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-gray-600">Họ tên:</span>
                    <span class="ml-2 font-semibold">{{ form.fullName }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Số điện thoại:</span>
                    <span class="ml-2 font-semibold">{{ form.phone }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Email:</span>
                    <span class="ml-2 font-semibold">{{ form.email || 'Không có' }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">CMND/CCCD:</span>
                    <span class="ml-2 font-semibold">{{ form.idCard }}</span>
                  </div>
                </div>
              </div>

              <!-- Employment Information -->
              <div>
                <h4 class="font-medium text-gray-900 mb-2">Thông tin công việc</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-gray-600">Nghề nghiệp:</span>
                    <span class="ml-2 font-semibold">{{ getOccupationText(form.occupation) }}</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Thu nhập:</span>
                    <span class="ml-2 font-semibold">{{ formatCurrency(form.monthlyIncome) }}/tháng</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Công ty:</span>
                    <span class="ml-2 font-semibold">{{ form.company || 'Không có' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="space-y-4">
              <div class="flex items-start">
                <input
                  v-model="form.agreeTerms"
                  type="checkbox"
                  class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                  required
                />
                <label class="ml-2 text-sm text-gray-700">
                  Tôi đã đọc và đồng ý với 
                  <a href="#" class="text-primary-600 hover:text-primary-700">Điều khoản sử dụng</a>
                  và 
                  <a href="#" class="text-primary-600 hover:text-primary-700">Chính sách bảo mật</a>
                </label>
              </div>

              <div class="flex items-start">
                <input
                  v-model="form.agreeDataProcessing"
                  type="checkbox"
                  class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                  required
                />
                <label class="ml-2 text-sm text-gray-700">
                  Tôi đồng ý cho VayNhanh xử lý thông tin cá nhân của tôi để phục vụ việc thẩm định và cấp tín dụng
                </label>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-between pt-6 border-t border-gray-200">
            <button
              v-if="currentStep > 0"
              type="button"
              @click="previousStep"
              class="btn btn-secondary"
            >
              <ArrowLeftIcon class="w-4 h-4 mr-2" />
              Quay lại
            </button>
            <div v-else></div>

            <button
              v-if="currentStep < steps.length - 1"
              type="button"
              @click="nextStep"
              class="btn btn-primary"
              :disabled="!canProceed"
            >
              Tiếp tục
              <ArrowRightIcon class="w-4 h-4 ml-2" />
            </button>

            <button
              v-else
              type="submit"
              class="btn btn-primary"
              :disabled="!canSubmit || isSubmitting"
            >
              {{ isSubmitting ? 'Đang gửi...' : 'Gửi đơn vay' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full">
        <div class="p-6 text-center">
          <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckCircleIcon class="w-8 h-8 text-success-600" />
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">
            Đơn vay đã được gửi thành công!
          </h3>
          <p class="text-gray-600 mb-6">
            Mã đơn vay của bạn là: <strong class="text-primary-600">{{ applicationId }}</strong>
            <br>
            Chúng tôi sẽ xét duyệt và liên hệ với bạn trong vòng 24 giờ.
          </p>
          <div class="flex space-x-3">
            <router-link
              to="/lookup"
              class="flex-1 btn btn-secondary"
            >
              Tra cứu đơn vay
            </router-link>
            <router-link
              to="/"
              class="flex-1 btn btn-primary"
            >
              Về trang chủ
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useContentStore } from '@/stores/content'
import { useLoanStore } from '@/stores/loan'
import { apiHelpers } from '@/services'

// Icons
import CheckIcon from '@/components/icons/CheckIcon.vue'
import ArrowRightIcon from '@/components/icons/ArrowRightIcon.vue'
import ArrowLeftIcon from '@/components/icons/ArrowLeftIcon.vue'
import CheckCircleIcon from '@/components/icons/CheckCircleIcon.vue'

const route = useRoute()
const router = useRouter()
const contentStore = useContentStore()
const loanStore = useLoanStore()

// State
const currentStep = ref(0)
const isSubmitting = ref(false)
const showSuccessModal = ref(false)
const applicationId = ref('')

const steps = [
  'Thông tin khoản vay',
  'Thông tin cá nhân',
  'Thông tin công việc',
  'Xác nhận'
]

const form = reactive({
  // Loan information
  amount: parseInt(route.query.amount) || contentStore.loanLimits.defaultAmount,
  term: 12,
  purpose: '',
  
  // Personal information
  fullName: '',
  phone: '',
  email: '',
  dateOfBirth: '',
  idCard: '',
  address: '',
  
  // Employment information
  occupation: '',
  monthlyIncome: '',
  company: '',
  companyAddress: '',
  
  // Agreements
  agreeTerms: false,
  agreeDataProcessing: false
})

// Computed properties
const monthlyPayment = computed(() => {
  if (!form.amount || !form.term) return 0
  const principal = form.amount
  const monthlyInterest = principal * (contentStore.interestRates.baseRate / 100)
  const monthlyPrincipal = principal / form.term
  return monthlyPrincipal + monthlyInterest
})

const canProceed = computed(() => {
  switch (currentStep.value) {
    case 0:
      return form.amount && form.term
    case 1:
      return form.fullName && form.phone && form.dateOfBirth && form.idCard && form.address
    case 2:
      return form.occupation && form.monthlyIncome
    default:
      return true
  }
})

const canSubmit = computed(() => {
  return canProceed.value && form.agreeTerms && form.agreeDataProcessing
})

// Methods
const getStepClass = (index) => {
  if (index < currentStep.value) {
    return 'bg-primary-600 border-primary-600'
  } else if (index === currentStep.value) {
    return 'bg-primary-600 border-primary-600'
  } else {
    return 'bg-white border-gray-300'
  }
}

const getStepDescription = (step) => {
  const descriptions = [
    'Nhập số tiền và thời gian vay mong muốn',
    'Cung cấp thông tin cá nhân của bạn',
    'Thông tin về công việc và thu nhập',
    'Kiểm tra lại thông tin trước khi gửi'
  ]
  return descriptions[step]
}

const getPurposeText = (purpose) => {
  const purposes = {
    business: 'Kinh doanh',
    personal: 'Cá nhân',
    education: 'Học tập',
    medical: 'Y tế',
    other: 'Khác'
  }
  return purposes[purpose] || 'Không xác định'
}

const getOccupationText = (occupation) => {
  const occupations = {
    employee: 'Nhân viên',
    business: 'Kinh doanh',
    freelancer: 'Tự do',
    student: 'Học sinh/Sinh viên',
    retired: 'Nghỉ hưu',
    other: 'Khác'
  }
  return occupations[occupation] || 'Không xác định'
}

const formatCurrency = (amount) => {
  return apiHelpers.formatCurrency(amount)
}

const nextStep = () => {
  if (canProceed.value && currentStep.value < steps.length - 1) {
    currentStep.value++
  }
}

const previousStep = () => {
  if (currentStep.value > 0) {
    currentStep.value--
  }
}

const handleSubmit = async () => {
  if (!canSubmit.value) return
  
  isSubmitting.value = true
  
  try {
    // Submit loan application
    const result = await loanStore.submitLoanApplication({
      customerName: form.fullName,
      phone: form.phone,
      email: form.email,
      idCard: form.idCard,
      dateOfBirth: form.dateOfBirth,
      address: form.address,
      amount: form.amount,
      term: form.term,
      purpose: form.purpose,
      occupation: form.occupation,
      monthlyIncome: form.monthlyIncome,
      company: form.company,
      companyAddress: form.companyAddress
    })
    
    if (result.success) {
      applicationId.value = result.application?.id || result.application?.application_id
      showSuccessModal.value = true
    } else {
      alert(result.error || 'Có lỗi xảy ra khi gửi đơn vay. Vui lòng thử lại.')
    }
  } catch (error) {
    console.error('Error submitting loan application:', error)
    alert('Có lỗi xảy ra khi gửi đơn vay. Vui lòng thử lại.')
  } finally {
    isSubmitting.value = false
  }
}

// Initialize form with query params
onMounted(() => {
  if (route.query.amount) {
    form.amount = parseInt(route.query.amount)
  }
})
</script>