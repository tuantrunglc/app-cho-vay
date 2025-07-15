<template>
  <div class="p-4 space-y-4">
    <!-- Welcome Banner -->
    <div class="text-center py-3">
      <h1 class="text-xl font-bold text-gray-900 mb-1">Vay tiền nhanh chóng</h1>
      <p class="text-sm text-gray-600">Đơn giản, an toàn, lãi suất thấp</p>
    </div>

    <!-- Banner Slider -->
    <div class="relative overflow-hidden rounded-xl shadow-lg">
      <div class="flex transition-transform duration-300 ease-in-out" :style="{ transform: `translateX(-${currentBanner * 100}%)` }">
        <div
          v-for="banner in banners"
          :key="banner.id"
          class="w-full flex-shrink-0"
        >
          <div class="h-32 bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center text-white">
            <div class="text-center px-4">
              <h3 class="text-base font-semibold mb-1">{{ banner.title }}</h3>
              <p class="text-sm opacity-90">{{ banner.subtitle }}</p>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Banner indicators -->
    </div>

    <!-- Loan Amount Slider -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Số tiền vay</h2>
      
      <!-- Amount Display -->
      <div class="text-center mb-4">
        <div class="text-2xl font-bold text-primary-600 mb-1">
          {{ formatCurrency(loanAmount) }}
        </div>
        <div class="text-xs text-gray-500">
          Từ {{ formatCurrency(minAmount) }} đến {{ formatCurrency(maxAmount) }}
        </div>
      </div>

      <!-- Slider -->
      <div class="relative mb-4">
        <input
          v-model="loanAmount"
          type="range"
          :min="minAmount"
          :max="maxAmount"
          :step="stepAmount"
          class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
        />
        <div class="flex justify-between text-xs text-gray-500 mt-1">
          <span>{{ formatCurrency(minAmount) }}</span>
          <span>{{ formatCurrency(maxAmount) }}</span>
        </div>
      </div>

      <!-- Quick Amount Buttons -->
      <div class="grid grid-cols-3 gap-2">
        <button
          v-for="amount in quickAmounts"
          :key="amount"
          @click="loanAmount = amount"
          class="py-2 px-2 text-xs border border-gray-300 rounded-lg transition-colors duration-200"
          :class="loanAmount === amount ? 'border-primary-500 text-primary-600 bg-primary-50' : 'text-gray-700'"
        >
          {{ formatCurrency(amount) }}
        </button>
      </div>
    </div>

    <!-- Loan Term Selection -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Thời gian vay</h2>
      
      <div class="grid grid-cols-3 gap-2">
        <button
          v-for="term in loanTerms"
          :key="term"
          @click="selectedTerm = term"
          class="py-2 px-2 text-center border rounded-lg transition-colors duration-200"
          :class="selectedTerm === term 
            ? 'border-primary-500 bg-primary-50 text-primary-600' 
            : 'border-gray-300 text-gray-700'"
        >
          <div class="text-sm font-semibold">{{ term }}</div>
          <div class="text-xs text-gray-500">tháng</div>
        </button>
      </div>
    </div>

    <!-- Payment Calculation -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <h2 class="text-base font-semibold text-gray-900 mb-3">Thông tin khoản vay</h2>
      
      <div class="space-y-2">
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Số tiền vay:</span>
          <span class="font-semibold">{{ formatCurrency(loanAmount) }}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Thời gian vay:</span>
          <span class="font-semibold">{{ selectedTerm }} tháng</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Lãi suất:</span>
          <span class="font-semibold">{{ interestRate }}%/tháng</span>
        </div>
        <hr class="my-2">
        <div class="flex justify-between">
          <span class="text-gray-900 font-semibold">Trả hàng tháng:</span>
          <span class="font-bold text-primary-600">{{ formatCurrency(monthlyPayment) }}</span>
        </div>
        <div class="flex justify-between text-xs text-gray-500">
          <span>Tổng tiền trả:</span>
          <span>{{ formatCurrency(totalPayment) }}</span>
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="pb-2">
      <button
        @click="submitLoanApplication"
        class="w-full bg-primary-600 text-white py-3 rounded-xl font-semibold shadow-lg hover:bg-primary-700 transition-colors duration-200"
      >
        Nộp đơn vay ngay
      </button>
      
      <p class="text-xs text-gray-500 text-center mt-2 px-2">
        Bằng cách nhấn "Nộp đơn vay ngay", bạn đồng ý với 
        <a href="#" class="text-primary-600 underline">Điều khoản sử dụng</a>
      </p>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <div class="text-center">
          <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckIcon class="w-8 h-8 text-success-600" />
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Đơn vay đã được gửi!</h3>
          <p class="text-gray-600 mb-4">Chúng tôi sẽ xem xét và phản hồi trong vòng 24h</p>
          <button
            @click="showSuccessModal = false"
            class="btn btn-primary w-full"
          >
            Đóng
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLoanStore } from '@/stores/loan'
import CheckIcon from '@/components/icons/CheckIcon.vue'

const loanStore = useLoanStore()

// Banner data
const banners = ref([
  {
    id: 1,
    title: 'Vay nhanh - Lãi thấp',
    subtitle: 'Chỉ từ 2%/tháng'
  },
  {
    id: 2,
    title: 'Duyệt trong 24h',
    subtitle: 'Không cần thế chấp'
  },
  {
    id: 3,
    title: 'Ưu đãi tháng 1',
    subtitle: 'Giảm 0.5% lãi suất'
  }
])

const currentBanner = ref(0)
const showSuccessModal = ref(false)

// Loan configuration
const minAmount = 10000000 // 10 triệu
const maxAmount = 500000000 // 500 triệu
const stepAmount = 1000000 // 1 triệu
const interestRate = 2.0 // 2% per month

const quickAmounts = [
  50000000,  // 50 triệu
  100000000, // 100 triệu
  200000000  // 200 triệu
]

const loanTerms = [6, 12, 18, 24, 36, 48]

// Reactive data
const loanAmount = ref(50000000)
const selectedTerm = ref(12)

// Computed properties
const monthlyPayment = computed(() => {
  const principal = loanAmount.value
  const monthlyInterest = principal * (interestRate / 100)
  const monthlyPrincipal = principal / selectedTerm.value
  return monthlyPrincipal + monthlyInterest
})

const totalPayment = computed(() => {
  return monthlyPayment.value * selectedTerm.value
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const submitLoanApplication = () => {
  // Update store
  loanStore.setLoanAmount(loanAmount.value)
  loanStore.setLoanTerm(selectedTerm.value)
  
  // Submit application (mock)
  const applicationData = {
    customerName: 'Khách hàng mới',
    phone: '0900000000',
    idCard: '000000000000'
  }
  
  loanStore.submitLoanApplication(applicationData)
  showSuccessModal.value = true
}

// Auto-rotate banners
onMounted(() => {
  setInterval(() => {
    currentBanner.value = (currentBanner.value + 1) % banners.value.length
  }, 5000)
})
</script>

<style scoped>
.slider::-webkit-slider-thumb {
  appearance: none;
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #2563eb;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.slider::-moz-range-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #2563eb;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
</style>