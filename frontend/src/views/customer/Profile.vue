<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Hồ sơ cá nhân
        </h1>
        <p class="text-gray-600">
          Quản lý thông tin cá nhân và cài đặt tài khoản
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Sidebar -->
        <div class="lg:col-span-1">
          <div class="card">
            <div class="p-6 text-center">
              <!-- Avatar -->
              <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-3xl font-bold text-primary-600">
                  {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                </span>
              </div>
              
              <h3 class="text-lg font-semibold text-gray-900 mb-1">
                {{ authStore.user?.name }}
              </h3>
              <p class="text-sm text-gray-500 mb-4">
                {{ authStore.user?.phone }}
              </p>
              
              <div class="flex items-center justify-center text-sm text-success-600">
                <CheckCircleIcon class="w-4 h-4 mr-1" />
                Tài khoản đã xác thực
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="card mt-6">
            <div class="p-6">
              <h4 class="font-semibold text-gray-900 mb-4">Thống kê nhanh</h4>
              <div class="space-y-3">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Tổng số đơn vay:</span>
                  <span class="font-semibold">{{ userStats.totalApplications }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Đơn đã duyệt:</span>
                  <span class="font-semibold text-success-600">{{ userStats.approvedApplications }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Khoản vay hiện tại:</span>
                  <span class="font-semibold text-primary-600">{{ userStats.activeLoans }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-600">Tổng đã vay:</span>
                  <span class="font-semibold">{{ formatCurrency(userStats.totalBorrowed) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2">
          <!-- Tab Navigation -->
          <div class="card mb-6">
            <div class="border-b border-gray-200">
              <nav class="flex space-x-8 px-6">
                <button
                  v-for="tab in tabs"
                  :key="tab.id"
                  @click="activeTab = tab.id"
                  class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                  :class="activeTab === tab.id 
                    ? 'border-primary-500 text-primary-600' 
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                >
                  <component :is="tab.icon" class="w-4 h-4 inline mr-2" />
                  {{ tab.name }}
                </button>
              </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
              <!-- Personal Information Tab -->
              <div v-if="activeTab === 'personal'" class="space-y-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Thông tin cá nhân
                  </h3>
                  
                  <form @submit.prevent="updatePersonalInfo" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Họ và tên
                        </label>
                        <input
                          v-model="personalForm.name"
                          type="text"
                          class="input"
                          required
                        />
                      </div>
                      
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Số điện thoại
                        </label>
                        <input
                          v-model="personalForm.phone"
                          type="tel"
                          class="input bg-gray-50"
                          readonly
                        />
                        <p class="text-xs text-gray-500 mt-1">
                          Liên hệ hỗ trợ để thay đổi số điện thoại
                        </p>
                      </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Email
                        </label>
                        <input
                          v-model="personalForm.email"
                          type="email"
                          class="input"
                        />
                      </div>
                      
                      <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                          Ngày sinh
                        </label>
                        <input
                          v-model="personalForm.dateOfBirth"
                          type="date"
                          class="input"
                        />
                      </div>
                    </div>

                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Địa chỉ
                      </label>
                      <textarea
                        v-model="personalForm.address"
                        rows="3"
                        class="input resize-none"
                      ></textarea>
                    </div>

                    <div class="flex justify-end">
                      <button
                        type="submit"
                        class="btn btn-primary"
                        :disabled="isUpdating"
                      >
                        {{ isUpdating ? 'Đang cập nhật...' : 'Cập nhật thông tin' }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Security Tab -->
              <div v-if="activeTab === 'security'" class="space-y-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Bảo mật tài khoản
                  </h3>
                  
                  <form @submit.prevent="changePassword" class="space-y-4">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Mật khẩu hiện tại
                      </label>
                      <input
                        v-model="passwordForm.currentPassword"
                        type="password"
                        class="input"
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
                        class="input"
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
                        class="input"
                        required
                      />
                    </div>

                    <div v-if="passwordError" class="text-danger-600 text-sm">
                      {{ passwordError }}
                    </div>

                    <div class="flex justify-end">
                      <button
                        type="submit"
                        class="btn btn-primary"
                        :disabled="isChangingPassword"
                      >
                        {{ isChangingPassword ? 'Đang thay đổi...' : 'Đổi mật khẩu' }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>

              <!-- Notifications Tab -->
              <div v-if="activeTab === 'notifications'" class="space-y-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Cài đặt thông báo
                  </h3>
                  
                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <h4 class="font-medium text-gray-900">Thông báo qua Email</h4>
                        <p class="text-sm text-gray-600">
                          Nhận thông báo về trạng thái đơn vay qua email
                        </p>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input
                          v-model="notificationSettings.email"
                          type="checkbox"
                          class="sr-only peer"
                        />
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                      </label>
                    </div>

                    <div class="flex items-center justify-between">
                      <div>
                        <h4 class="font-medium text-gray-900">Thông báo qua SMS</h4>
                        <p class="text-sm text-gray-600">
                          Nhận thông báo về thanh toán và nhắc nhở qua SMS
                        </p>
                      </div>
                      <label class="relative inline-flex items-center cursor-pointer">
                        <input
                          v-model="notificationSettings.sms"
                          type="checkbox"
                          class="sr-only peer"
                        />
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                      </label>
                    </div>
                  </div>

                  <div class="flex justify-end mt-6">
                    <button
                      @click="updateNotificationSettings"
                      class="btn btn-primary"
                      :disabled="isUpdatingNotifications"
                    >
                      {{ isUpdatingNotifications ? 'Đang lưu...' : 'Lưu cài đặt' }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'

// Icons
import UserIcon from '@/components/icons/UserIcon.vue'
import ShieldCheckIcon from '@/components/icons/ShieldCheckIcon.vue'
import BellIcon from '@/components/icons/BellIcon.vue'
import CheckCircleIcon from '@/components/icons/CheckCircleIcon.vue'

const authStore = useAuthStore()

// State
const activeTab = ref('personal')
const isUpdating = ref(false)
const isChangingPassword = ref(false)
const isUpdatingNotifications = ref(false)
const passwordError = ref('')

const tabs = [
  { id: 'personal', name: 'Thông tin cá nhân', icon: UserIcon },
  { id: 'security', name: 'Bảo mật', icon: ShieldCheckIcon },
  { id: 'notifications', name: 'Thông báo', icon: BellIcon }
]

const personalForm = reactive({
  name: '',
  phone: '',
  email: '',
  dateOfBirth: '',
  address: ''
})

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const notificationSettings = reactive({
  email: true,
  sms: true
})

const userStats = reactive({
  totalApplications: 3,
  approvedApplications: 2,
  activeLoans: 1,
  totalBorrowed: 150000000
})

// Methods
const updatePersonalInfo = async () => {
  isUpdating.value = true
  
  try {
    const result = await authStore.updateProfile({
      name: personalForm.name,
      email: personalForm.email,
      dateOfBirth: personalForm.dateOfBirth,
      address: personalForm.address
    })
    
    if (result.success) {
      alert('Cập nhật thông tin thành công!')
    } else {
      alert('Có lỗi xảy ra: ' + result.error)
    }
  } catch (error) {
    alert('Có lỗi xảy ra khi cập nhật thông tin')
  } finally {
    isUpdating.value = false
  }
}

const changePassword = async () => {
  passwordError.value = ''
  
  if (passwordForm.newPassword !== passwordForm.confirmPassword) {
    passwordError.value = 'Mật khẩu xác nhận không khớp'
    return
  }
  
  if (passwordForm.newPassword.length < 6) {
    passwordError.value = 'Mật khẩu mới phải có ít nhất 6 ký tự'
    return
  }
  
  isChangingPassword.value = true
  
  try {
    const result = await authStore.changePassword({
      currentPassword: passwordForm.currentPassword,
      newPassword: passwordForm.newPassword
    })
    
    if (result.success) {
      alert('Đổi mật khẩu thành công!')
      passwordForm.currentPassword = ''
      passwordForm.newPassword = ''
      passwordForm.confirmPassword = ''
    } else {
      passwordError.value = result.error
    }
  } catch (error) {
    passwordError.value = 'Có lỗi xảy ra khi đổi mật khẩu'
  } finally {
    isChangingPassword.value = false
  }
}

const updateNotificationSettings = async () => {
  isUpdatingNotifications.value = true
  
  try {
    // Mock API call
    await new Promise(resolve => setTimeout(resolve, 500))
    alert('Cập nhật cài đặt thông báo thành công!')
  } catch (error) {
    alert('Có lỗi xảy ra khi cập nhật cài đặt')
  } finally {
    isUpdatingNotifications.value = false
  }
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const loadUserData = () => {
  // Load personal information
  if (authStore.user) {
    personalForm.name = authStore.user.name || ''
    personalForm.phone = authStore.user.phone || ''
    personalForm.email = authStore.user.email || ''
    personalForm.dateOfBirth = authStore.user.dateOfBirth || ''
    personalForm.address = authStore.user.address || ''
  }
}

onMounted(() => {
  loadUserData()
})
</script>