<template>
  <div class="p-4 space-y-4">
    <!-- User Info Card -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="flex items-center space-x-4 mb-4">
        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center">
          <span class="text-primary-600 font-bold text-xl">
            {{ authStore.user?.name?.charAt(0).toUpperCase() }}
          </span>
        </div>
        <div>
          <h2 class="text-lg font-semibold text-gray-900">{{ authStore.user?.name }}</h2>
          <p class="text-sm text-gray-600">{{ authStore.user?.phone }}</p>
          <div class="flex items-center mt-1">
            <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
            <span class="text-xs text-green-600">Tài khoản đã xác thực</span>
          </div>
        </div>
      </div>
      
      <!-- Quick Stats -->
      <div class="grid grid-cols-3 gap-4 pt-4 border-t border-gray-100">
        <div class="text-center">
          <div class="text-lg font-bold text-primary-600">{{ userStats.totalLoans }}</div>
          <div class="text-xs text-gray-500">Khoản vay</div>
        </div>
        <div class="text-center">
          <div class="text-lg font-bold text-green-600">{{ formatCurrency(userStats.totalAmount) }}</div>
          <div class="text-xs text-gray-500">Tổng vay</div>
        </div>
        <div class="text-center">
          <div class="text-lg font-bold text-blue-600">{{ userStats.creditScore }}</div>
          <div class="text-xs text-gray-500">Điểm tín dụng</div>
        </div>
      </div>
    </div>

    <!-- Menu Items -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="divide-y divide-gray-100">
        <router-link
          to="/my-loans"
          class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors duration-200"
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <DocumentTextIcon class="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Khoản vay của tôi</div>
              <div class="text-sm text-gray-500">Xem lịch sử và trạng thái vay</div>
            </div>
          </div>
          <ChevronRightIcon class="w-5 h-5 text-gray-400" />
        </router-link>

        <button
          @click="showPersonalInfo = true"
          class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors duration-200"
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <UserIcon class="w-5 h-5 text-green-600" />
            </div>
            <div class="text-left">
              <div class="font-medium text-gray-900">Thông tin cá nhân</div>
              <div class="text-sm text-gray-500">Cập nhật thông tin tài khoản</div>
            </div>
          </div>
          <ChevronRightIcon class="w-5 h-5 text-gray-400" />
        </button>

        <router-link
          to="/change-password"
          class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors duration-200"
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
              <LockClosedIcon class="w-5 h-5 text-yellow-600" />
            </div>
            <div class="text-left">
              <div class="font-medium text-gray-900">Đổi mật khẩu</div>
              <div class="text-sm text-gray-500">Thay đổi mật khẩu đăng nhập</div>
            </div>
          </div>
          <ChevronRightIcon class="w-5 h-5 text-gray-400" />
        </router-link>

        <router-link
          to="/support"
          class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors duration-200"
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
              <ChatBubbleLeftRightIcon class="w-5 h-5 text-purple-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Hỗ trợ khách hàng</div>
              <div class="text-sm text-gray-500">Liên hệ với chúng tôi</div>
            </div>
          </div>
          <ChevronRightIcon class="w-5 h-5 text-gray-400" />
        </router-link>
      </div>
    </div>

    <!-- Settings -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="divide-y divide-gray-100">
        <router-link
          to="/notifications"
          class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors duration-200"
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
              <BellIcon class="w-5 h-5 text-gray-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Thông báo</div>
              <div class="text-sm text-gray-500">Xem tất cả thông báo</div>
            </div>
          </div>
          <div class="flex items-center space-x-2">
            <span v-if="unreadNotifications > 0" class="bg-danger-500 text-white text-xs px-2 py-1 rounded-full">
              {{ unreadNotifications }}
            </span>
            <ChevronRightIcon class="w-5 h-5 text-gray-400" />
          </div>
        </router-link>

        <div class="flex items-center justify-between p-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <BellIcon class="w-5 h-5 text-green-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Thông báo push</div>
              <div class="text-sm text-gray-500">Nhận thông báo về khoản vay</div>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input
              v-model="settings.notifications"
              type="checkbox"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
          </label>
        </div>
      </div>
    </div>

    <!-- Logout Button -->
    <div class="pt-4">
      <button
        @click="handleLogout"
        class="w-full bg-red-50 text-red-600 py-3 rounded-xl font-medium hover:bg-red-100 transition-colors duration-200"
      >
        Đăng xuất
      </button>
    </div>

    <!-- Personal Info Modal -->
    <div v-if="showPersonalInfo" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-sm w-full max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Thông tin cá nhân</h3>
            <button
              @click="showPersonalInfo = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <form @submit.prevent="updatePersonalInfo" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Họ và tên
            </label>
            <input
              v-model="personalInfoForm.name"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email
            </label>
            <input
              v-model="personalInfoForm.email"
              type="email"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Địa chỉ
            </label>
            <textarea
              v-model="personalInfoForm.address"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            ></textarea>
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              type="button"
              @click="showPersonalInfo = false"
              class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 py-2 px-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
            >
              Cập nhật
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Change Password Modal -->
    <div v-if="showChangePassword" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-sm w-full">
        <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Đổi mật khẩu</h3>
            <button
              @click="showChangePassword = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <form @submit.prevent="changePassword" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Mật khẩu hiện tại
            </label>
            <input
              v-model="passwordForm.currentPassword"
              type="password"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Mật khẩu mới
            </label>
            <input
              v-model="passwordForm.newPassword"
              type="password"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Xác nhận mật khẩu mới
            </label>
            <input
              v-model="passwordForm.confirmPassword"
              type="password"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              required
            />
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              type="button"
              @click="showChangePassword = false"
              class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 py-2 px-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
            >
              Đổi mật khẩu
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Icons
import DocumentTextIcon from '@/components/icons/DocumentTextIcon.vue'
import UserIcon from '@/components/icons/UserIcon.vue'
import LockClosedIcon from '@/components/icons/LockClosedIcon.vue'
import ChatBubbleLeftRightIcon from '@/components/icons/ChatBubbleLeftRightIcon.vue'
import BellIcon from '@/components/icons/BellIcon.vue'
import ChevronRightIcon from '@/components/icons/ChevronRightIcon.vue'
import XMarkIcon from '@/components/icons/XMarkIcon.vue'

const router = useRouter()
const authStore = useAuthStore()

// Reactive data
const showPersonalInfo = ref(false)
const showChangePassword = ref(false)

const settings = reactive({
  notifications: true
})

const userStats = reactive({
  totalLoans: 3,
  totalAmount: 150000000,
  creditScore: 750
})

const unreadNotifications = ref(3)

const personalInfoForm = reactive({
  name: authStore.user?.name || '',
  email: authStore.user?.email || '',
  address: ''
})

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const updatePersonalInfo = async () => {
  // Mock API call
  await new Promise(resolve => setTimeout(resolve, 500))
  
  // Update store
  authStore.updateUser({
    ...authStore.user,
    name: personalInfoForm.name,
    email: personalInfoForm.email
  })
  
  showPersonalInfo.value = false
  alert('Cập nhật thông tin thành công!')
}

const changePassword = async () => {
  if (passwordForm.newPassword !== passwordForm.confirmPassword) {
    alert('Mật khẩu xác nhận không khớp!')
    return
  }
  
  // Mock API call
  await new Promise(resolve => setTimeout(resolve, 500))
  
  showChangePassword.value = false
  
  // Reset form
  passwordForm.currentPassword = ''
  passwordForm.newPassword = ''
  passwordForm.confirmPassword = ''
  
  alert('Đổi mật khẩu thành công!')
}

const handleLogout = () => {
  if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
    authStore.logout()
    router.push('/')
  }
}
</script>