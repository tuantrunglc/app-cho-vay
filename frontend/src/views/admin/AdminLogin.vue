<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center p-4">
    <div class="max-w-md w-full">
      <!-- Admin Login Card -->
      <div class="bg-white rounded-2xl shadow-2xl p-8">
        <!-- Header -->
        <div class="text-center mb-8">
          <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
            <ShieldCheckIcon class="w-8 h-8 text-white" />
          </div>
          <h1 class="text-2xl font-bold text-gray-900 mb-2">Admin Portal</h1>
          <p class="text-gray-600">Đăng nhập hệ thống quản trị</p>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
          <div class="flex items-center">
            <ExclamationTriangleIcon class="w-5 h-5 text-red-500 mr-2" />
            <span class="text-red-700 text-sm">{{ errorMessage }}</span>
          </div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="handleAdminLogin" class="space-y-6">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Tên đăng nhập
            </label>
            <div class="relative">
              <input
                v-model="loginForm.username"
                type="text"
                placeholder="Nhập tên đăng nhập admin"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 pl-10"
                required
              />
              <UserIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Mật khẩu
            </label>
            <div class="relative">
              <input
                v-model="loginForm.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Nhập mật khẩu admin"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 pl-10 pr-10"
                required
              />
              <LockClosedIcon class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                <EyeIcon v-if="!showPassword" class="w-5 h-5" />
                <EyeSlashIcon v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input
                v-model="loginForm.remember"
                type="checkbox"
                class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
              />
              <span class="ml-2 text-sm text-gray-600">Ghi nhớ đăng nhập</span>
            </label>
            
            <button
              type="button"
              @click="showForgotPassword = true"
              class="text-sm text-purple-600 hover:text-purple-700 font-medium"
            >
              Quên mật khẩu?
            </button>
          </div>

          <button
            type="submit"
            class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-3 px-4 rounded-lg font-semibold shadow-lg hover:from-purple-700 hover:to-blue-700 transition-all duration-200 transform hover:scale-[1.02]"
            :disabled="isLoading"
          >
            <span v-if="isLoading" class="flex items-center justify-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Đang đăng nhập...
            </span>
            <span v-else>Đăng nhập Admin</span>
          </button>
        </form>

        <!-- Security Notice -->
        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
          <div class="flex items-start">
            <ExclamationTriangleIcon class="w-5 h-5 text-amber-500 mr-2 mt-0.5" />
            <div class="text-sm text-amber-700">
              <p class="font-medium mb-1">Lưu ý bảo mật:</p>
              <p>Chỉ nhân viên được ủy quyền mới có thể truy cập hệ thống này. Mọi hoạt động đều được ghi lại và giám sát.</p>
            </div>
          </div>
        </div>

        <!-- Back to User Site -->
        <div class="text-center mt-6">
          <button
            @click="goToUserSite"
            class="text-sm text-gray-500 hover:text-gray-700 flex items-center justify-center mx-auto"
          >
            <ArrowLeftIcon class="w-4 h-4 mr-1" />
            Quay lại trang khách hàng
          </button>
        </div>
      </div>
    </div>

    <!-- Forgot Password Modal -->
    <div v-if="showForgotPassword" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Khôi phục mật khẩu Admin</h3>
        
        <form @submit.prevent="handleForgotPassword">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tên đăng nhập
            </label>
            <input
              v-model="forgotPasswordForm.username"
              type="text"
              placeholder="Nhập tên đăng nhập admin"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
              required
            />
          </div>
          
          <div class="flex space-x-3">
            <button
              type="button"
              @click="showForgotPassword = false"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200"
            >
              Gửi yêu cầu
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import ShieldCheckIcon from '@/components/icons/ShieldCheckIcon.vue'
import UserIcon from '@/components/icons/UserIcon.vue'
import LockClosedIcon from '@/components/icons/LockClosedIcon.vue'
import EyeIcon from '@/components/icons/EyeIcon.vue'
import EyeSlashIcon from '@/components/icons/EyeSlashIcon.vue'
import ExclamationTriangleIcon from '@/components/icons/ExclamationTriangleIcon.vue'
import ArrowLeftIcon from '@/components/icons/ArrowLeftIcon.vue'

const router = useRouter()
const authStore = useAuthStore()

const isLoading = ref(false)
const showPassword = ref(false)
const showForgotPassword = ref(false)
const errorMessage = ref('')

// Login form
const loginForm = reactive({
  username: '',
  password: '',
  remember: false
})

// Forgot password form
const forgotPasswordForm = reactive({
  username: ''
})

// Methods
const handleAdminLogin = async () => {
  isLoading.value = true
  errorMessage.value = ''
  
  try {
    const result = await authStore.login({
      username: loginForm.username,
      password: loginForm.password
    })
    
    if (result.success) {
      // Redirect to admin dashboard
      router.push('/admin')
    } else {
      errorMessage.value = result.error || 'Tên đăng nhập hoặc mật khẩu không chính xác!'
    }
  } catch (error) {
    errorMessage.value = 'Có lỗi xảy ra. Vui lòng thử lại!'
  } finally {
    isLoading.value = false
  }
}

const handleForgotPassword = async () => {
  // Mock forgot password API call
  await new Promise(resolve => setTimeout(resolve, 500))
  
  alert('Yêu cầu khôi phục mật khẩu đã được gửi đến IT Admin!')
  showForgotPassword.value = false
  
  // Reset form
  forgotPasswordForm.username = ''
}

const goToUserSite = () => {
  router.push('/')
}
</script>

<style scoped>
/* Custom styles for admin login */
.bg-gradient-to-br {
  background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
}

/* Animation for loading spinner */
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>