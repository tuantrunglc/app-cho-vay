<template>
  <div>
    <!-- Hero Section with Banner -->
    <section class="relative bg-gradient-to-r from-primary-600 to-primary-800 text-white">
      <div class="absolute inset-0 bg-black opacity-20"></div>
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
          <h1 class="text-4xl md:text-6xl font-bold mb-6">
            Vay tiền nhanh chóng
            <span class="block text-primary-200">chỉ trong 24 giờ</span>
          </h1>
          <p class="text-xl md:text-2xl mb-8 text-primary-100">
            Lãi suất từ 2%/tháng • Không cần thế chấp • Duyệt online
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <router-link
              to="/register"
              class="btn btn-white text-primary-600 hover:bg-gray-50 text-lg px-8 py-4"
            >
              Đăng ký vay ngay
            </router-link>
            <router-link
              to="/lookup"
              class="btn btn-outline-white text-lg px-8 py-4"
            >
              Tra cứu đơn vay
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Tại sao chọn VayNhanh?
          </h2>
          <p class="text-lg text-gray-600">
            Giải pháp vay tiền trực tuyến hiện đại, an toàn và tiện lợi
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <ClockIcon class="w-8 h-8 text-primary-600" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Duyệt nhanh 24h</h3>
            <p class="text-gray-600">
              Hệ thống tự động duyệt đơn vay trong vòng 24 giờ, 
              giải ngân ngay sau khi được phê duyệt
            </p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <ShieldCheckIcon class="w-8 h-8 text-success-600" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">An toàn bảo mật</h3>
            <p class="text-gray-600">
              Thông tin khách hàng được mã hóa và bảo mật tuyệt đối, 
              tuân thủ các tiêu chuẩn bảo mật quốc tế
            </p>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 bg-warning-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <CurrencyDollarIcon class="w-8 h-8 text-warning-600" />
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Lãi suất cạnh tranh</h3>
            <p class="text-gray-600">
              Lãi suất từ 2%/tháng, minh bạch và cạnh tranh nhất thị trường, 
              không phí ẩn
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Loan Calculator Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Tính toán khoản vay
          </h2>
          <p class="text-lg text-gray-600">
            Tính toán số tiền cần trả hàng tháng một cách dễ dàng
          </p>
        </div>

        <div class="card p-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Calculator Form -->
            <div class="space-y-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Số tiền vay
                </label>
                <input
                  v-model="calculator.amount"
                  type="range"
                  :min="contentStore.loanLimits.minAmount"
                  :max="contentStore.loanLimits.maxAmount"
                  :step="contentStore.loanLimits.stepAmount"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                />
                <div class="flex justify-between text-sm text-gray-500 mt-1">
                  <span>{{ formatCurrency(contentStore.loanLimits.minAmount) }}</span>
                  <span class="font-semibold text-primary-600">{{ formatCurrency(calculator.amount) }}</span>
                  <span>{{ formatCurrency(contentStore.loanLimits.maxAmount) }}</span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Thời gian vay
                </label>
                <select v-model="calculator.term" class="input">
                  <option v-for="term in contentStore.activeTerms" :key="term" :value="term">
                    {{ term }} tháng
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Lãi suất
                </label>
                <div class="input bg-gray-50">
                  {{ contentStore.interestRates.baseRate }}%/tháng
                </div>
              </div>
            </div>

            <!-- Calculator Result -->
            <div class="bg-primary-50 rounded-lg p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Kết quả tính toán</h3>
              
              <div class="space-y-3">
                <div class="flex justify-between">
                  <span class="text-gray-600">Số tiền vay:</span>
                  <span class="font-semibold">{{ formatCurrency(calculator.amount) }}</span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-gray-600">Thời gian vay:</span>
                  <span class="font-semibold">{{ calculator.term }} tháng</span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-gray-600">Lãi suất:</span>
                  <span class="font-semibold">{{ contentStore.interestRates.baseRate }}%/tháng</span>
                </div>
                
                <hr class="border-primary-200">
                
                <div class="flex justify-between text-lg">
                  <span class="text-gray-900 font-semibold">Trả hàng tháng:</span>
                  <span class="font-bold text-primary-600">{{ formatCurrency(monthlyPayment) }}</span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-gray-600">Tổng tiền trả:</span>
                  <span class="font-semibold">{{ formatCurrency(totalPayment) }}</span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-gray-600">Tổng lãi:</span>
                  <span class="font-semibold text-warning-600">{{ formatCurrency(totalInterest) }}</span>
                </div>
              </div>

              <router-link
                to="/register"
                class="btn btn-primary w-full mt-6"
              >
                Đăng ký vay với số tiền này
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- How it works Section -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Quy trình vay đơn giản
          </h2>
          <p class="text-lg text-gray-600">
            Chỉ 3 bước đơn giản để có tiền trong tay
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center relative">
            <div class="w-16 h-16 bg-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
              1
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Đăng ký online</h3>
            <p class="text-gray-600">
              Điền thông tin cá nhân và số tiền cần vay trên website. 
              Chỉ mất 5 phút để hoàn thành.
            </p>
            <!-- Arrow -->
            <div class="hidden md:block absolute top-8 -right-4 text-primary-300">
              <ArrowRightIcon class="w-8 h-8" />
            </div>
          </div>

          <div class="text-center relative">
            <div class="w-16 h-16 bg-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
              2
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Xét duyệt tự động</h3>
            <p class="text-gray-600">
              Hệ thống AI tự động xét duyệt hồ sơ trong vòng 24 giờ. 
              Bạn sẽ nhận được thông báo qua SMS.
            </p>
            <!-- Arrow -->
            <div class="hidden md:block absolute top-8 -right-4 text-primary-300">
              <ArrowRightIcon class="w-8 h-8" />
            </div>
          </div>

          <div class="text-center">
            <div class="w-16 h-16 bg-primary-600 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
              3
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Nhận tiền ngay</h3>
            <p class="text-gray-600">
              Sau khi được duyệt, tiền sẽ được chuyển vào tài khoản 
              ngân hàng của bạn trong vòng 30 phút.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Quick Amounts Section -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Chọn nhanh số tiền cần vay
          </h2>
          <p class="text-lg text-gray-600">
            Các mức vay phổ biến được khách hàng lựa chọn nhiều nhất
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div
            v-for="amount in quickAmounts"
            :key="amount"
            class="card p-6 text-center hover:shadow-lg transition-shadow duration-200 cursor-pointer hover:border-primary-300"
            @click="selectQuickAmount(amount)"
          >
            <div class="text-2xl font-bold text-primary-600 mb-2">
              {{ formatCurrency(amount) }}
            </div>
            <div class="text-sm text-gray-500 mb-3">
              Trả {{ formatCurrency(calculateQuickPayment(amount)) }}/tháng
            </div>
            <button class="btn btn-primary btn-sm w-full">
              Chọn ngay
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-gray-900 mb-4">
            Câu hỏi thường gặp
          </h2>
          <p class="text-lg text-gray-600">
            Những thắc mắc phổ biến của khách hàng
          </p>
        </div>

        <div class="space-y-4">
          <div
            v-for="(faq, index) in faqs"
            :key="index"
            class="card"
          >
            <button
              @click="toggleFaq(index)"
              class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50"
            >
              <span class="font-semibold text-gray-900">{{ faq.question }}</span>
              <ChevronDownIcon
                class="w-5 h-5 text-gray-500 transition-transform duration-200"
                :class="{ 'transform rotate-180': faq.open }"
              />
            </button>
            <div
              v-if="faq.open"
              class="px-6 pb-4 text-gray-600"
            >
              {{ faq.answer }}
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-primary-600 text-white">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-bold mb-4">
          Sẵn sàng vay tiền ngay hôm nay?
        </h2>
        <p class="text-xl text-primary-100 mb-8">
          Hàng nghìn khách hàng đã tin tưởng và sử dụng dịch vụ của chúng tôi
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            to="/register"
            class="btn btn-white text-primary-600 hover:bg-gray-50 text-lg px-8 py-4"
          >
            Đăng ký vay ngay
          </router-link>
          <a
            href="tel:19001234"
            class="btn btn-outline-white text-lg px-8 py-4"
          >
            Gọi tư vấn: 1900 1234
          </a>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useContentStore } from '@/stores/content'

// Icons
import ClockIcon from '@/components/icons/ClockIcon.vue'
import ShieldCheckIcon from '@/components/icons/ShieldCheckIcon.vue'
import CurrencyDollarIcon from '@/components/icons/CurrencyDollarIcon.vue'
import ArrowRightIcon from '@/components/icons/ArrowRightIcon.vue'
import ChevronDownIcon from '@/components/icons/ChevronDownIcon.vue'

const router = useRouter()
const contentStore = useContentStore()

// Calculator state
const calculator = reactive({
  amount: contentStore.loanLimits.defaultAmount,
  term: 12
})

// FAQ state
const faqs = ref([
  {
    question: 'Điều kiện để vay tiền là gì?',
    answer: 'Bạn cần từ 18-60 tuổi, có CMND/CCCD hợp lệ, có thu nhập ổn định và không nằm trong danh sách đen của các tổ chức tín dụng.',
    open: false
  },
  {
    question: 'Thời gian duyệt hồ sơ bao lâu?',
    answer: 'Hệ thống sẽ tự động xét duyệt hồ sơ trong vòng 24 giờ. Trong trường hợp cần bổ sung thêm giấy tờ, chúng tôi sẽ liên hệ với bạn.',
    open: false
  },
  {
    question: 'Có cần thế chấp tài sản không?',
    answer: 'Không cần thế chấp tài sản. Chúng tôi chỉ cần bạn cung cấp thông tin cá nhân và chứng minh thu nhập.',
    open: false
  },
  {
    question: 'Lãi suất được tính như thế nào?',
    answer: 'Lãi suất được tính theo tháng, từ 2%/tháng tùy thuộc vào hồ sơ và thời gian vay. Không có phí ẩn.',
    open: false
  },
  {
    question: 'Có thể trả trước hạn được không?',
    answer: 'Có, bạn có thể trả trước hạn bất kỳ lúc nào mà không mất phí phạt. Lãi suất sẽ được tính theo thời gian thực tế sử dụng.',
    open: false
  }
])

// Computed properties
const monthlyPayment = computed(() => {
  const principal = calculator.amount
  const monthlyInterest = principal * (contentStore.interestRates.baseRate / 100)
  const monthlyPrincipal = principal / calculator.term
  return monthlyPrincipal + monthlyInterest
})

const totalPayment = computed(() => {
  return monthlyPayment.value * calculator.term
})

const totalInterest = computed(() => {
  return totalPayment.value - calculator.amount
})

const quickAmounts = computed(() => {
  return [
    50000000,   // 50 triệu
    100000000,  // 100 triệu
    200000000,  // 200 triệu
    300000000   // 300 triệu
  ]
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const calculateQuickPayment = (amount) => {
  const monthlyInterest = amount * (contentStore.interestRates.baseRate / 100)
  const monthlyPrincipal = amount / 12 // Default 12 months
  return monthlyPrincipal + monthlyInterest
}

const selectQuickAmount = (amount) => {
  router.push({
    path: '/register',
    query: { amount: amount }
  })
}

const toggleFaq = (index) => {
  faqs.value[index].open = !faqs.value[index].open
}
</script>

<style scoped>
.slider::-webkit-slider-thumb {
  appearance: none;
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #3B82F6;
  cursor: pointer;
}

.slider::-moz-range-thumb {
  height: 20px;
  width: 20px;
  border-radius: 50%;
  background: #3B82F6;
  cursor: pointer;
  border: none;
}

.btn-white {
  @apply bg-white text-gray-900 border border-transparent hover:bg-gray-50;
}

.btn-outline-white {
  @apply bg-transparent text-white border border-white hover:bg-white hover:text-primary-600;
}

.btn-sm {
  @apply px-3 py-1.5 text-sm;
}
</style>