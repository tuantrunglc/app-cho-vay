<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <div class="text-center py-3">
      <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-3">
        <ChatIcon class="w-8 h-8 text-primary-600" />
      </div>
      <h1 class="text-xl font-bold text-gray-900 mb-1">Hỗ trợ khách hàng</h1>
      <p class="text-sm text-gray-600">Chúng tôi luôn sẵn sàng hỗ trợ bạn 24/7</p>
    </div>

    <!-- Quick Contact -->
    <div class="grid grid-cols-2 gap-3">
      <a
        href="tel:1900123456"
        class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition-shadow duration-200"
      >
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <PhoneIcon class="w-6 h-6 text-green-600" />
        </div>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Hotline</h3>
        <p class="text-xs text-gray-600">1900 123 456</p>
      </a>
      
      <button
        @click="openChat"
        class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition-shadow duration-200"
      >
        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <ChatBubbleLeftRightIcon class="w-6 h-6 text-blue-600" />
        </div>
        <h3 class="text-sm font-semibold text-gray-900 mb-1">Live Chat</h3>
        <p class="text-xs text-gray-600">Trò chuyện trực tuyến</p>
      </button>
    </div>

    <!-- FAQ Section -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <h2 class="text-base font-semibold text-gray-900 mb-4">Câu hỏi thường gặp</h2>
      
      <div class="space-y-3">
        <div
          v-for="(faq, index) in faqs"
          :key="index"
          class="border border-gray-200 rounded-lg"
        >
          <button
            @click="toggleFaq(index)"
            class="w-full px-3 py-3 text-left flex items-center justify-between hover:bg-gray-50 transition-colors duration-200"
          >
            <span class="text-sm font-medium text-gray-900">{{ faq.question }}</span>
            <ChevronDownIcon 
              class="w-4 h-4 text-gray-500 transition-transform duration-200 flex-shrink-0 ml-2"
              :class="openFaqIndex === index ? 'transform rotate-180' : ''"
            />
          </button>
          
          <div
            v-if="openFaqIndex === index"
            class="px-3 pb-3 text-gray-600 text-sm border-t border-gray-100 leading-relaxed"
          >
            {{ faq.answer }}
          </div>
        </div>
      </div>
    </div>

    <!-- Contact Form -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <h2 class="text-base font-semibold text-gray-900 mb-4">Gửi yêu cầu hỗ trợ</h2>
      
      <form @submit.prevent="submitSupportRequest" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Loại yêu cầu
          </label>
          <select v-model="supportForm.type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base">
            <option value="">Chọn loại yêu cầu</option>
            <option value="loan">Vấn đề về khoản vay</option>
            <option value="payment">Vấn đề thanh toán</option>
            <option value="account">Vấn đề tài khoản</option>
            <option value="other">Khác</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Tiêu đề
          </label>
          <input
            v-model="supportForm.subject"
            type="text"
            placeholder="Nhập tiêu đề yêu cầu"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Mô tả chi tiết
          </label>
          <textarea
            v-model="supportForm.description"
            rows="4"
            placeholder="Mô tả chi tiết vấn đề của bạn"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base resize-none"
            required
          ></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Số điện thoại liên hệ
          </label>
          <input
            v-model="supportForm.phone"
            type="tel"
            placeholder="Nhập số điện thoại"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
            required
          />
        </div>

        <button
          type="submit"
          class="w-full bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-700 transition-colors duration-200"
          :disabled="isSubmitting"
        >
          <span v-if="isSubmitting">Đang gửi...</span>
          <span v-else>Gửi yêu cầu</span>
        </button>
      </form>
    </div>

    <!-- Operating Hours -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <h2 class="text-base font-semibold text-gray-900 mb-4">Giờ làm việc</h2>
      
      <div class="space-y-3">
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Thứ 2 - Thứ 6:</span>
          <span class="font-medium">8:00 - 18:00</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Thứ 7:</span>
          <span class="font-medium">8:00 - 12:00</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">Chủ nhật:</span>
          <span class="font-medium">Nghỉ</span>
        </div>
      </div>
      
      <div class="mt-3 p-3 bg-primary-50 rounded-lg">
        <p class="text-xs text-primary-700">
          <strong>Lưu ý:</strong> Hotline hỗ trợ khẩn cấp hoạt động 24/7
        </p>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <div class="text-center">
          <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckIcon class="w-8 h-8 text-success-600" />
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Gửi thành công!</h3>
          <p class="text-gray-600 mb-4">Chúng tôi sẽ phản hồi trong vòng 24h</p>
          <button
            @click="showSuccessModal = false"
            class="w-full bg-primary-600 text-white py-2 rounded-lg hover:bg-primary-700 transition-colors duration-200"
          >
            Đóng
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import PhoneIcon from '@/components/icons/PhoneIcon.vue'
import ChatIcon from '@/components/icons/ChatIcon.vue'
import ChatBubbleLeftRightIcon from '@/components/icons/ChatBubbleLeftRightIcon.vue'
import ChevronDownIcon from '@/components/icons/ChevronDownIcon.vue'
import CheckIcon from '@/components/icons/CheckIcon.vue'

// FAQ data
const faqs = ref([
  {
    question: 'Làm thế nào để vay tiền?',
    answer: 'Bạn chỉ cần chọn số tiền và thời gian vay trên trang chủ, sau đó nhấn "Nộp đơn vay ngay". Chúng tôi sẽ xem xét và phản hồi trong vòng 24h.'
  },
  {
    question: 'Lãi suất vay là bao nhiêu?',
    answer: 'Lãi suất hiện tại là 2%/tháng, được tính theo phương pháp lãi đơn. Lãi suất có thể thay đổi tùy theo chính sách của công ty.'
  },
  {
    question: 'Tôi có thể vay tối đa bao nhiêu?',
    answer: 'Hạn mức vay từ 10 triệu đến 500 triệu VND, tùy thuộc vào khả năng tài chính và lịch sử tín dụng của bạn.'
  },
  {
    question: 'Thời gian vay tối đa là bao lâu?',
    answer: 'Bạn có thể chọn thời gian vay từ 6 đến 48 tháng, với các mức: 6, 12, 18, 24, 36, 48 tháng.'
  },
  {
    question: 'Làm thế nào để trả nợ?',
    answer: 'Bạn có thể trả nợ qua ví điện tử trong ứng dụng hoặc chuyển khoản trực tiếp đến tài khoản ngân hàng của chúng tôi.'
  }
])

const openFaqIndex = ref(null)
const showSuccessModal = ref(false)
const isSubmitting = ref(false)

// Support form
const supportForm = reactive({
  type: '',
  subject: '',
  description: '',
  phone: ''
})

// Methods
const toggleFaq = (index) => {
  openFaqIndex.value = openFaqIndex.value === index ? null : index
}

const openChat = () => {
  // Mock chat functionality
  alert('Tính năng chat sẽ được triển khai sớm!')
}

const submitSupportRequest = async () => {
  isSubmitting.value = true
  
  // Mock API call
  await new Promise(resolve => setTimeout(resolve, 1000))
  
  // Reset form
  Object.keys(supportForm).forEach(key => {
    supportForm[key] = ''
  })
  
  isSubmitting.value = false
  showSuccessModal.value = true
}
</script>