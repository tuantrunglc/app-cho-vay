<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <div class="flex items-center space-x-3 mb-4">
      <button @click="$router.go(-1)" class="p-2 hover:bg-gray-100 rounded-lg">
        <ArrowLeftIcon class="w-5 h-5 text-gray-600" />
      </button>
      <h1 class="text-xl font-bold text-gray-900">Cài đặt</h1>
    </div>

    <!-- Notification Settings -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="p-4 border-b border-gray-100">
        <h2 class="text-base font-semibold text-gray-900">Thông báo</h2>
        <p class="text-sm text-gray-500 mt-1">Quản lý các loại thông báo bạn muốn nhận</p>
      </div>
      
      <div class="divide-y divide-gray-100">
        <div class="flex items-center justify-between p-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <BellIcon class="w-5 h-5 text-blue-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Thông báo push</div>
              <div class="text-sm text-gray-500">Nhận thông báo trên thiết bị</div>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input
              v-model="settings.pushNotifications"
              type="checkbox"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
          </label>
        </div>

        <div class="flex items-center justify-between p-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <CurrencyDollarIcon class="w-5 h-5 text-green-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Nhắc nhở thanh toán</div>
              <div class="text-sm text-gray-500">Thông báo trước kỳ hạn thanh toán</div>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input
              v-model="settings.paymentReminders"
              type="checkbox"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
          </label>
        </div>

        <div class="flex items-center justify-between p-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
              <DocumentTextIcon class="w-5 h-5 text-yellow-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Cập nhật khoản vay</div>
              <div class="text-sm text-gray-500">Thông báo về trạng thái khoản vay</div>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input
              v-model="settings.loanUpdates"
              type="checkbox"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
          </label>
        </div>

        <div class="flex items-center justify-between p-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
              <PercentBadgeIcon class="w-5 h-5 text-purple-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Ưu đãi & Khuyến mãi</div>
              <div class="text-sm text-gray-500">Thông báo về các chương trình ưu đãi</div>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input
              v-model="settings.promotions"
              type="checkbox"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
          </label>
        </div>
      </div>
    </div>

    <!-- Security Settings -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="p-4 border-b border-gray-100">
        <h2 class="text-base font-semibold text-gray-900">Bảo mật</h2>
        <p class="text-sm text-gray-500 mt-1">Cài đặt bảo mật tài khoản</p>
      </div>
      
      <div class="divide-y divide-gray-100">
        <div class="flex items-center justify-between p-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
              <LockClosedIcon class="w-5 h-5 text-red-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Xác thực 2 bước</div>
              <div class="text-sm text-gray-500">Tăng cường bảo mật tài khoản</div>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input
              v-model="settings.twoFactorAuth"
              type="checkbox"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
          </label>
        </div>

        <div class="flex items-center justify-between p-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
              <ShieldCheckIcon class="w-5 h-5 text-orange-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Đăng nhập sinh trắc học</div>
              <div class="text-sm text-gray-500">Sử dụng vân tay hoặc Face ID</div>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input
              v-model="settings.biometricLogin"
              type="checkbox"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
          </label>
        </div>
      </div>
    </div>

    <!-- App Settings -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="p-4 border-b border-gray-100">
        <h2 class="text-base font-semibold text-gray-900">Ứng dụng</h2>
        <p class="text-sm text-gray-500 mt-1">Cài đặt giao diện và hiển thị</p>
      </div>
      
      <div class="divide-y divide-gray-100">
        <div class="flex items-center justify-between p-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
              <CogIcon class="w-5 h-5 text-gray-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Chế độ tối</div>
              <div class="text-sm text-gray-500">Giao diện tối cho mắt</div>
            </div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input
              v-model="settings.darkMode"
              type="checkbox"
              class="sr-only peer"
            />
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
          </label>
        </div>

        <div class="p-4">
          <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
              <BellIcon class="w-5 h-5 text-indigo-600" />
            </div>
            <div>
              <div class="font-medium text-gray-900">Âm thanh thông báo</div>
              <div class="text-sm text-gray-500">Chọn âm thanh cho thông báo</div>
            </div>
          </div>
          
          <select
            v-model="settings.notificationSound"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-base"
          >
            <option value="default">Mặc định</option>
            <option value="chime">Chuông</option>
            <option value="bell">Tiếng chuông</option>
            <option value="notification">Thông báo</option>
            <option value="none">Không âm thanh</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Data & Privacy -->
    <div class="bg-white rounded-xl shadow-sm">
      <div class="p-4 border-b border-gray-100">
        <h2 class="text-base font-semibold text-gray-900">Dữ liệu & Quyền riêng tư</h2>
        <p class="text-sm text-gray-500 mt-1">Quản lý dữ liệu cá nhân</p>
      </div>
      
      <div class="divide-y divide-gray-100">
        <button
          @click="exportData"
          class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors duration-200"
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <DocumentArrowDownIcon class="w-5 h-5 text-blue-600" />
            </div>
            <div class="text-left">
              <div class="font-medium text-gray-900">Xuất dữ liệu</div>
              <div class="text-sm text-gray-500">Tải xuống dữ liệu cá nhân</div>
            </div>
          </div>
          <ChevronRightIcon class="w-5 h-5 text-gray-400" />
        </button>

        <button
          @click="showDeleteAccount = true"
          class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors duration-200"
        >
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
              <TrashIcon class="w-5 h-5 text-red-600" />
            </div>
            <div class="text-left">
              <div class="font-medium text-red-600">Xóa tài khoản</div>
              <div class="text-sm text-gray-500">Xóa vĩnh viễn tài khoản</div>
            </div>
          </div>
          <ChevronRightIcon class="w-5 h-5 text-gray-400" />
        </button>
      </div>
    </div>

    <!-- Save Button -->
    <button
      @click="saveSettings"
      class="w-full bg-primary-600 text-white py-3 rounded-xl font-semibold hover:bg-primary-700 transition-colors duration-200"
      :disabled="isSaving"
    >
      {{ isSaving ? 'Đang lưu...' : 'Lưu cài đặt' }}
    </button>

    <!-- Delete Account Modal -->
    <div v-if="showDeleteAccount" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg p-6 max-w-sm w-full">
        <div class="text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <ExclamationTriangleIcon class="w-8 h-8 text-red-600" />
          </div>
          <h3 class="text-lg font-semibold text-gray-900 mb-2">Xóa tài khoản?</h3>
          <p class="text-gray-600 mb-4">
            Hành động này không thể hoàn tác. Tất cả dữ liệu của bạn sẽ bị xóa vĩnh viễn.
          </p>
          <div class="flex space-x-3">
            <button
              @click="showDeleteAccount = false"
              class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              @click="deleteAccount"
              class="flex-1 py-2 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700"
            >
              Xóa tài khoản
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import ArrowLeftIcon from '@/components/icons/ArrowLeftIcon.vue'
import BellIcon from '@/components/icons/BellIcon.vue'
import CurrencyDollarIcon from '@/components/icons/CurrencyDollarIcon.vue'
import DocumentTextIcon from '@/components/icons/DocumentTextIcon.vue'
import PercentBadgeIcon from '@/components/icons/PercentBadgeIcon.vue'
import LockClosedIcon from '@/components/icons/LockClosedIcon.vue'
import ShieldCheckIcon from '@/components/icons/ShieldCheckIcon.vue'
import CogIcon from '@/components/icons/CogIcon.vue'
import DocumentArrowDownIcon from '@/components/icons/DocumentArrowDownIcon.vue'
import TrashIcon from '@/components/icons/TrashIcon.vue'
import ChevronRightIcon from '@/components/icons/ChevronRightIcon.vue'
import ExclamationTriangleIcon from '@/components/icons/ExclamationTriangleIcon.vue'

const router = useRouter()

// State
const isSaving = ref(false)
const showDeleteAccount = ref(false)

// Settings
const settings = reactive({
  // Notifications
  pushNotifications: true,
  paymentReminders: true,
  loanUpdates: true,
  promotions: false,
  
  // Security
  twoFactorAuth: false,
  biometricLogin: true,
  
  // App
  darkMode: false,
  notificationSound: 'default'
})

// Methods
const saveSettings = async () => {
  isSaving.value = true
  
  // Mock API call
  await new Promise(resolve => setTimeout(resolve, 1000))
  
  // Save to localStorage for demo
  localStorage.setItem('userSettings', JSON.stringify(settings))
  
  isSaving.value = false
  
  // Show success message
  alert('Cài đặt đã được lưu thành công!')
}

const exportData = async () => {
  // Mock data export
  const userData = {
    profile: {
      name: 'Nguyễn Văn A',
      email: 'nguyenvana@email.com',
      phone: '0901234567'
    },
    loans: [
      {
        id: 'LN001',
        amount: 10000000,
        term: 12,
        status: 'active'
      }
    ],
    transactions: [
      {
        id: 'TX001',
        type: 'payment',
        amount: 1200000,
        date: '2024-01-15'
      }
    ]
  }
  
  const dataStr = JSON.stringify(userData, null, 2)
  const dataBlob = new Blob([dataStr], { type: 'application/json' })
  const url = URL.createObjectURL(dataBlob)
  
  const link = document.createElement('a')
  link.href = url
  link.download = 'my-data.json'
  link.click()
  
  URL.revokeObjectURL(url)
}

const deleteAccount = async () => {
  // Mock API call
  await new Promise(resolve => setTimeout(resolve, 1000))
  
  // Clear all data
  localStorage.clear()
  
  // Redirect to home
  router.push('/')
  
  alert('Tài khoản đã được xóa thành công!')
}

// Load saved settings on mount
const loadSettings = () => {
  const saved = localStorage.getItem('userSettings')
  if (saved) {
    Object.assign(settings, JSON.parse(saved))
  }
}

loadSettings()
</script>