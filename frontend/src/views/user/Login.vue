<template>
  <div class="p-4 min-h-screen flex flex-col justify-center">
    <div class="max-w-sm mx-auto w-full">
      <!-- Logo/Header -->
      <div class="text-center mb-8">
        <div class="w-20 h-20 bg-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
          <UserIcon class="w-10 h-10 text-white" />
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Đăng nhập</h1>
        <p class="text-gray-600">Truy cập tài khoản của bạn</p>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Số điện thoại
          </label>
          <input
            v-model="loginForm.phone"
            type="tel"
            placeholder="Nhập số điện thoại"
            class="input"
            required
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Mật khẩu
          </label>
          <div class="relative">
            <input
              v-model="loginForm.password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="Nhập mật khẩu"
              class="input pr-10"
              required
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
              <EyeIcon v-if="!showPassword" class="w-5 h-5 text-gray-400" />
              <EyeSlashIcon v-else class="w-5 h-5 text-gray-400" />
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <label class="flex items-center">
            <input
              v-model="loginForm.remember"
              type="checkbox"
              class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
            />
            <span class="ml-2 text-sm text-gray-600">Ghi nhớ đăng nhập</span>
          </label>
          
          <button
            type="button"
            @click="showForgotPassword = true"
            class="text-sm text-primary-600 hover:text-primary-700"
          >
            Quên mật khẩu?
          </button>
        </div>

        <button
          type="submit"
          class="w-full btn btn-primary py-3"
          :disabled="isLoading"
        >
          <span v-if="isLoading">Đang đăng nhập...</span>
          <span v-else>Đăng nhập</span>
        </button>
      </form>

      <!-- Divider -->
      <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
          <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
          <span class="px-2 bg-white text-gray-500">Hoặc</span>
        </div>
      </div>

      <!-- Social Login -->
      <div class="space-y-3">
        <button
          type="button"
          class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
        >
          <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google" class="w-5 h-5 mr-3" />
          Đăng nhập với Google
        </button>
        
        <button
          type="button"
          class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
        >
          <div class="w-5 h-5 bg-blue-600 rounded mr-3 flex items-center justify-center">
            <span class="text-white text-xs font-bold">f</span>
          </div>
          Đăng nhập với Facebook
        </button>
      </div>

      <!-- Register Link -->
      <div class="text-center mt-6">
        <p class="text-sm text-gray-600">
          Chưa có tài khoản?
          <button
            @click="showRegister = true"
            class="text-primary-600 hover:text-primary-700 font-medium"
          >
            Đăng ký ngay
          </button>
        </p>
      </div>

      <!-- Admin Login Link -->
      <div class="text-center mt-4">
        <button
          @click="goToAdminLogin"
          class="text-xs text-gray-500 hover:text-gray-700 flex items-center justify-center mx-auto"
        >
          <ShieldCheckIcon class="w-4 h-4 mr-1" />
          Đăng nhập Admin
        </button>
      </div>
    </div>

    <!-- Forgot Password Modal -->
    <div v-if="showForgotPassword" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quên mật khẩu</h3>
        
        <form @submit.prevent="handleForgotPassword">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số điện thoại
            </label>
            <input
              v-model="forgotPasswordForm.phone"
              type="tel"
              placeholder="Nhập số điện thoại"
              class="input"
              required
            />
          </div>
          
          <div class="flex space-x-3">
            <button
              type="button"
              @click="showForgotPassword = false"
              class="flex-1 btn btn-secondary"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 btn btn-primary"
            >
              Gửi OTP
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Register Modal -->
    <div v-if="showRegister" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Đăng ký tài khoản</h3>
        
        <form @submit.prevent="handleRegister" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Họ và tên
            </label>
            <input
              v-model="registerForm.fullName"
              type="text"
              placeholder="Nhập họ và tên"
              class="input"
              required
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số điện thoại
            </label>
            <input
              v-model="registerForm.phone"
              type="tel"
              placeholder="Nhập số điện thoại"
              class="input"
              required
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              v-model="registerForm.email"
              type="email"
              placeholder="Nhập email"
              class="input"
              required
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Mật khẩu
            </label>
            <input
              v-model="registerForm.password"
              type="password"
              placeholder="Nhập mật khẩu"
              class="input"
              required
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Xác nhận mật khẩu
            </label>
            <input
              v-model="registerForm.confirmPassword"
              type="password"
              placeholder="Nhập lại mật khẩu"
              class="input"
              required
            />
          </div>
          
          <div class="flex items-start">
            <input
              v-model="registerForm.agreeTerms"
              type="checkbox"
              class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 mt-1"
              required
            />
            <span class="ml-2 text-sm text-gray-600">
              Tôi đồng ý với 
              <a href="#" class="text-primary-600 underline">Điều khoản sử dụng</a>
              và 
              <a href="#" class="text-primary-600 underline">Chính sách bảo mật</a>
            </span>
          </div>
          
          <div class="flex space-x-3">
            <button
              type="button"
              @click="showRegister = false"
              class="flex-1 btn btn-secondary"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 btn btn-primary"
            >
              Đăng ký
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
import { useUserStore } from '@/stores/user'
import UserIcon from '@/components/icons/UserIcon.vue'
import EyeIcon from '@/components/icons/EyeIcon.vue'
import EyeSlashIcon from '@/components/icons/EyeSlashIcon.vue'
import ShieldCheckIcon from '@/components/icons/ShieldCheckIcon.vue'

const router = useRouter()
const userStore = useUserStore()

const isLoading = ref(false)
const showPassword = ref(false)
const showForgotPassword = ref(false)
const showRegister = ref(false)

// Login form
const loginForm = reactive({
  phone: '',
  password: '',
  remember: false
})

// Forgot password form
const forgotPasswordForm = reactive({
  phone: ''
})

// Register form
const registerForm = reactive({
  fullName: '',
  phone: '',
  email: '',
  password: '',
  confirmPassword: '',
  agreeTerms: false
})

// Methods
const handleLogin = async () => {
  isLoading.value = true
  
  // Mock login API call
  await new Promise(resolve => setTimeout(resolve, 1000))
  
  // Mock successful login
  userStore.login({
    id: 1,
    name: 'Nguyễn Văn A',
    phone: loginForm.phone,
    email: 'user@example.com'
  })
  
  isLoading.value = false
  
  // Redirect to home
  router.push('/')
}

const handleForgotPassword = async () => {
  // Mock forgot password API call
  await new Promise(resolve => setTimeout(resolve, 500))
  
  alert('OTP đã được gửi đến số điện thoại của bạn!')
  showForgotPassword.value = false
  
  // Reset form
  forgotPasswordForm.phone = ''
}

const handleRegister = async () => {
  if (registerForm.password !== registerForm.confirmPassword) {
    alert('Mật khẩu xác nhận không khớp!')
    return
  }
  
  // Mock register API call
  await new Promise(resolve => setTimeout(resolve, 1000))
  
  alert('Đăng ký thành công! Vui lòng đăng nhập.')
  showRegister.value = false
  
  // Reset form
  Object.keys(registerForm).forEach(key => {
    if (typeof registerForm[key] === 'boolean') {
      registerForm[key] = false
    } else {
      registerForm[key] = ''
    }
  })
}

const goToAdminLogin = () => {
  router.push('/admin/login')
}
</script>