<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-4">
      <button @click="$router.go(-1)" class="p-2 hover:bg-gray-100 rounded-lg">
        <ArrowLeftIcon class="w-5 h-5 text-gray-600" />
      </button>
      <h1 class="text-xl font-bold text-gray-900">Đổi mật khẩu</h1>
    </div>

    <!-- Security Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
      <div class="flex items-start space-x-3">
        <ShieldCheckIcon class="w-5 h-5 text-blue-600 mt-0.5" />
        <div>
          <h3 class="text-sm font-semibold text-blue-900 mb-1">Bảo mật tài khoản</h3>
          <p class="text-xs text-blue-700">
            Để đảm bảo an toàn, vui lòng sử dụng mật khẩu mạnh có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số.
          </p>
        </div>
      </div>
    </div>

    <!-- Change Password Form -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <form @submit.prevent="changePassword" class="space-y-4">
        <!-- Current Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Mật khẩu hiện tại *
          </label>
          <div class="relative">
            <input
              v-model="form.currentPassword"
              :type="showCurrentPassword ? 'text' : 'password'"
              class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              placeholder="Nhập mật khẩu hiện tại"
              required
            />
            <button
              type="button"
              @click="showCurrentPassword = !showCurrentPassword"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <EyeIcon v-if="!showCurrentPassword" class="w-5 h-5" />
              <EyeSlashIcon v-else class="w-5 h-5" />
            </button>
          </div>
        </div>

        <!-- New Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Mật khẩu mới *
          </label>
          <div class="relative">
            <input
              v-model="form.newPassword"
              :type="showNewPassword ? 'text' : 'password'"
              class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              placeholder="Nhập mật khẩu mới"
              required
              @input="checkPasswordStrength"
            />
            <button
              type="button"
              @click="showNewPassword = !showNewPassword"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <EyeIcon v-if="!showNewPassword" class="w-5 h-5" />
              <EyeSlashIcon v-else class="w-5 h-5" />
            </button>
          </div>
          
          <!-- Password Strength Indicator -->
          <div v-if="form.newPassword" class="mt-2">
            <div class="flex items-center space-x-2 mb-2">
              <div class="flex-1 bg-gray-200 rounded-full h-2">
                <div
                  class="h-2 rounded-full transition-all duration-300"
                  :class="passwordStrengthClass"
                  :style="{ width: passwordStrengthWidth }"
                ></div>
              </div>
              <span class="text-xs font-medium" :class="passwordStrengthTextClass">
                {{ passwordStrengthText }}
              </span>
            </div>
            
            <!-- Password Requirements -->
            <div class="space-y-1">
              <div class="flex items-center space-x-2">
                <CheckIcon v-if="passwordChecks.length" class="w-3 h-3 text-success-600" />
                <XMarkIcon v-else class="w-3 h-3 text-gray-400" />
                <span class="text-xs" :class="passwordChecks.length ? 'text-success-600' : 'text-gray-500'">
                  Ít nhất 8 ký tự
                </span>
              </div>
              
              <div class="flex items-center space-x-2">
                <CheckIcon v-if="passwordChecks.uppercase" class="w-3 h-3 text-success-600" />
                <XMarkIcon v-else class="w-3 h-3 text-gray-400" />
                <span class="text-xs" :class="passwordChecks.uppercase ? 'text-success-600' : 'text-gray-500'">
                  Có chữ hoa
                </span>
              </div>
              
              <div class="flex items-center space-x-2">
                <CheckIcon v-if="passwordChecks.lowercase" class="w-3 h-3 text-success-600" />
                <XMarkIcon v-else class="w-3 h-3 text-gray-400" />
                <span class="text-xs" :class="passwordChecks.lowercase ? 'text-success-600' : 'text-gray-500'">
                  Có chữ thường
                </span>
              </div>
              
              <div class="flex items-center space-x-2">
                <CheckIcon v-if="passwordChecks.number" class="w-3 h-3 text-success-600" />
                <XMarkIcon v-else class="w-3 h-3 text-gray-400" />
                <span class="text-xs" :class="passwordChecks.number ? 'text-success-600' : 'text-gray-500'">
                  Có số
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Confirm Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Xác nhận mật khẩu mới *
          </label>
          <div class="relative">
            <input
              v-model="form.confirmPassword"
              :type="showConfirmPassword ? 'text' : 'password'"
              class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
              placeholder="Nhập lại mật khẩu mới"
              required
            />
            <button
              type="button"
              @click="showConfirmPassword = !showConfirmPassword"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <EyeIcon v-if="!showConfirmPassword" class="w-5 h-5" />
              <EyeSlashIcon v-else class="w-5 h-5" />
            </button>
          </div>
          
          <!-- Password Match Indicator -->
          <div v-if="form.confirmPassword" class="mt-2">
            <div v-if="form.newPassword === form.confirmPassword" class="flex items-center space-x-2 text-success-600">
              <CheckIcon class="w-4 h-4" />
              <span class="text-sm">Mật khẩu khớp</span>
            </div>
            <div v-else class="flex items-center space-x-2 text-danger-600">
              <XMarkIcon class="w-4 h-4" />
              <span class="text-sm">Mật khẩu không khớp</span>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="w-full bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-700 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="!canSubmit || isChanging"
        >
          {{ isChanging ? 'Đang đổi mật khẩu...' : 'Đổi mật khẩu' }}
        </button>
      </form>
    </div>

    <!-- Security Tips -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <h3 class="text-base font-semibold text-gray-900 mb-3">Mẹo bảo mật</h3>
      
      <div class="space-y-3">
        <div class="flex items-start space-x-3">
          <div class="w-2 h-2 bg-primary-600 rounded-full mt-2"></div>
          <p class="text-sm text-gray-600">Không chia sẻ mật khẩu với bất kỳ ai</p>
        </div>
        
        <div class="flex items-start space-x-3">
          <div class="w-2 h-2 bg-primary-600 rounded-full mt-2"></div>
          <p class="text-sm text-gray-600">Thay đổi mật khẩu định kỳ (3-6 tháng)</p>
        </div>
        
        <div class="flex items-start space-x-3">
          <div class="w-2 h-2 bg-primary-600 rounded-full mt-2"></div>
          <p class="text-sm text-gray-600">Sử dụng mật khẩu khác nhau cho các tài khoản</p>
        </div>
        
        <div class="flex items-start space-x-3">
          <div class="w-2 h-2 bg-primary-600 rounded-full mt-2"></div>
          <p class="text-sm text-gray-600">Đăng xuất sau khi sử dụng trên thiết bị chung</p>
        </div>
      </div>
    </div>

    <!-- Success Modal -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <div class="text-center">
          <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckIcon class="w-8 h-8 text-success-600" />
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Đổi mật khẩu thành công!</h3>
          <p class="text-gray-600 mb-4">Mật khẩu của bạn đã được cập nhật</p>
          <button
            @click="handleSuccess"
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
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import ArrowLeftIcon from '@/components/icons/ArrowLeftIcon.vue'
import ShieldCheckIcon from '@/components/icons/ShieldCheckIcon.vue'
import EyeIcon from '@/components/icons/EyeIcon.vue'
import EyeSlashIcon from '@/components/icons/EyeSlashIcon.vue'
import CheckIcon from '@/components/icons/CheckIcon.vue'
import XMarkIcon from '@/components/icons/XMarkIcon.vue'

const router = useRouter()

// Form state
const form = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)
const isChanging = ref(false)
const showSuccessModal = ref(false)

// Password strength checking
const passwordChecks = reactive({
  length: false,
  uppercase: false,
  lowercase: false,
  number: false
})

const passwordStrength = ref(0)

const checkPasswordStrength = () => {
  const password = form.newPassword
  
  passwordChecks.length = password.length >= 8
  passwordChecks.uppercase = /[A-Z]/.test(password)
  passwordChecks.lowercase = /[a-z]/.test(password)
  passwordChecks.number = /\d/.test(password)
  
  const checks = Object.values(passwordChecks).filter(Boolean).length
  passwordStrength.value = checks
}

const passwordStrengthWidth = computed(() => {
  return `${(passwordStrength.value / 4) * 100}%`
})

const passwordStrengthClass = computed(() => {
  if (passwordStrength.value <= 1) return 'bg-danger-500'
  if (passwordStrength.value <= 2) return 'bg-warning-500'
  if (passwordStrength.value <= 3) return 'bg-yellow-500'
  return 'bg-success-500'
})

const passwordStrengthTextClass = computed(() => {
  if (passwordStrength.value <= 1) return 'text-danger-600'
  if (passwordStrength.value <= 2) return 'text-warning-600'
  if (passwordStrength.value <= 3) return 'text-yellow-600'
  return 'text-success-600'
})

const passwordStrengthText = computed(() => {
  if (passwordStrength.value <= 1) return 'Yếu'
  if (passwordStrength.value <= 2) return 'Trung bình'
  if (passwordStrength.value <= 3) return 'Khá'
  return 'Mạnh'
})

const canSubmit = computed(() => {
  return form.currentPassword && 
         form.newPassword && 
         form.confirmPassword &&
         form.newPassword === form.confirmPassword &&
         passwordStrength.value >= 3
})

// Methods
const changePassword = async () => {
  if (!canSubmit.value) return
  
  isChanging.value = true
  
  try {
    // Mock API call
    await new Promise(resolve => setTimeout(resolve, 2000))
    
    // Reset form
    form.currentPassword = ''
    form.newPassword = ''
    form.confirmPassword = ''
    passwordStrength.value = 0
    
    showSuccessModal.value = true
  } catch (error) {
    alert('Có lỗi xảy ra. Vui lòng thử lại!')
  } finally {
    isChanging.value = false
  }
}

const handleSuccess = () => {
  showSuccessModal.value = false
  router.push('/profile')
}
</script>