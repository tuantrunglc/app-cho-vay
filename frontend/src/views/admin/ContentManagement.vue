<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="card p-6">
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Quản lý nội dung</h1>
      <p class="text-gray-600">Cấu hình banner, lãi suất, hạn mức và các thông số hệ thống</p>
    </div>

    <!-- Tabs -->
    <div class="card">
      <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8 px-6">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
            :class="activeTab === tab.key 
              ? 'border-primary-500 text-primary-600' 
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          >
            <component :is="tab.icon" class="w-5 h-5 inline mr-2" />
            {{ tab.label }}
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div class="p-6">
        <!-- Banner Management -->
        <div v-if="activeTab === 'banners'" class="space-y-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Quản lý Banner</h3>
            <button
              @click="showAddBanner = true"
              class="btn btn-primary"
            >
              <PlusIcon class="w-4 h-4 mr-2" />
              Thêm banner
            </button>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="banner in banners"
              :key="banner.id"
              class="border border-gray-200 rounded-lg overflow-hidden"
            >
              <div class="aspect-w-16 aspect-h-9 bg-gray-100">
                <img
                  :src="banner.imageUrl"
                  :alt="banner.title"
                  class="w-full h-40 object-cover"
                  @error="handleImageError"
                />
              </div>
              <div class="p-4">
                <h4 class="font-semibold text-gray-900 mb-1">{{ banner.title }}</h4>
                <p class="text-sm text-gray-600 mb-3">{{ banner.subtitle }}</p>
                <div class="flex items-center justify-between">
                  <span
                    class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                    :class="banner.active 
                      ? 'bg-success-100 text-success-800' 
                      : 'bg-gray-100 text-gray-800'"
                  >
                    {{ banner.active ? 'Hoạt động' : 'Tạm dừng' }}
                  </span>
                  <div class="flex space-x-2">
                    <button
                      @click="editBanner(banner)"
                      class="text-primary-600 hover:text-primary-900"
                    >
                      <PencilIcon class="w-4 h-4" />
                    </button>
                    <button
                      @click="toggleBanner(banner)"
                      :class="banner.active 
                        ? 'text-warning-600 hover:text-warning-900' 
                        : 'text-success-600 hover:text-success-900'"
                    >
                      <EyeSlashIcon v-if="banner.active" class="w-4 h-4" />
                      <EyeIcon v-else class="w-4 h-4" />
                    </button>
                    <button
                      @click="deleteBanner(banner.id)"
                      class="text-danger-600 hover:text-danger-900"
                    >
                      <TrashIcon class="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Interest Rate Management -->
        <div v-if="activeTab === 'interest'" class="space-y-6">
          <h3 class="text-lg font-semibold text-gray-900">Cấu hình lãi suất</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card p-6">
              <h4 class="font-semibold text-gray-900 mb-4">Lãi suất hiện tại</h4>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lãi suất cơ bản (%/tháng)
                  </label>
                  <input
                    v-model="interestConfig.baseRate"
                    type="number"
                    step="0.1"
                    class="input"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lãi suất ưu đãi (%/tháng)
                  </label>
                  <input
                    v-model="interestConfig.promotionalRate"
                    type="number"
                    step="0.1"
                    class="input"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Lãi suất phạt (%/tháng)
                  </label>
                  <input
                    v-model="interestConfig.penaltyRate"
                    type="number"
                    step="0.1"
                    class="input"
                  />
                </div>
                <button
                  @click="updateInterestRates"
                  class="btn btn-primary w-full"
                >
                  Cập nhật lãi suất
                </button>
              </div>
            </div>

            <div class="card p-6">
              <h4 class="font-semibold text-gray-900 mb-4">Lịch sử thay đổi</h4>
              <div class="space-y-3">
                <div
                  v-for="history in interestHistory"
                  :key="history.id"
                  class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0"
                >
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ history.rate }}%/tháng</p>
                    <p class="text-xs text-gray-500">{{ formatDate(history.date) }}</p>
                  </div>
                  <span class="text-xs text-gray-500">{{ history.updatedBy }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Loan Limits Management -->
        <div v-if="activeTab === 'limits'" class="space-y-6">
          <h3 class="text-lg font-semibold text-gray-900">Cấu hình hạn mức vay</h3>
          
          <div class="card p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Hạn mức tối thiểu (VND)
                </label>
                <input
                  v-model="loanLimits.minAmount"
                  type="number"
                  class="input"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Hạn mức tối đa (VND)
                </label>
                <input
                  v-model="loanLimits.maxAmount"
                  type="number"
                  class="input"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Bước nhảy (VND)
                </label>
                <input
                  v-model="loanLimits.stepAmount"
                  type="number"
                  class="input"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Hạn mức mặc định (VND)
                </label>
                <input
                  v-model="loanLimits.defaultAmount"
                  type="number"
                  class="input"
                />
              </div>
            </div>
            
            <div class="mt-6">
              <button
                @click="updateLoanLimits"
                class="btn btn-primary"
              >
                Cập nhật hạn mức
              </button>
            </div>
          </div>
        </div>

        <!-- Loan Terms Management -->
        <div v-if="activeTab === 'terms'" class="space-y-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Cấu hình thời gian vay</h3>
            <button
              @click="showAddTerm = true"
              class="btn btn-primary"
            >
              <PlusIcon class="w-4 h-4 mr-2" />
              Thêm kỳ hạn
            </button>
          </div>

          <div class="card p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
              <div
                v-for="term in loanTerms"
                :key="term.id"
                class="border border-gray-200 rounded-lg p-4 text-center"
              >
                <div class="text-lg font-semibold text-gray-900 mb-1">{{ term.months }}</div>
                <div class="text-sm text-gray-500 mb-3">tháng</div>
                <div class="flex justify-center space-x-2">
                  <button
                    @click="toggleTerm(term)"
                    :class="term.active 
                      ? 'text-success-600 hover:text-success-900' 
                      : 'text-gray-400 hover:text-gray-600'"
                  >
                    <CheckCircleIcon v-if="term.active" class="w-4 h-4" />
                    <XCircleIcon v-else class="w-4 h-4" />
                  </button>
                  <button
                    @click="deleteTerm(term.id)"
                    class="text-danger-600 hover:text-danger-900"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Notifications Management -->
        <div v-if="activeTab === 'notifications'" class="space-y-6">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Quản lý thông báo</h3>
            <button
              @click="showAddNotification = true"
              class="btn btn-primary"
            >
              <PlusIcon class="w-4 h-4 mr-2" />
              Tạo thông báo
            </button>
          </div>

          <div class="space-y-4">
            <div
              v-for="notification in notifications"
              :key="notification.id"
              class="card p-6"
            >
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center mb-2">
                    <h4 class="font-semibold text-gray-900 mr-3">{{ notification.title }}</h4>
                    <span
                      class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                      :class="getNotificationTypeClass(notification.type)"
                    >
                      {{ getNotificationTypeText(notification.type) }}
                    </span>
                  </div>
                  <p class="text-gray-600 mb-3">{{ notification.content }}</p>
                  <div class="flex items-center text-sm text-gray-500">
                    <span>{{ formatDate(notification.createdAt) }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ notification.sentCount }} người nhận</span>
                  </div>
                </div>
                <div class="flex items-center space-x-2 ml-4">
                  <button
                    @click="editNotification(notification)"
                    class="text-primary-600 hover:text-primary-900"
                  >
                    <PencilIcon class="w-4 h-4" />
                  </button>
                  <button
                    @click="deleteNotification(notification.id)"
                    class="text-danger-600 hover:text-danger-900"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Banner Modal -->
    <div v-if="showAddBanner" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Thêm banner mới</h3>
        </div>
        
        <form @submit.prevent="addBanner" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Tiêu đề *
            </label>
            <input
              v-model="bannerForm.title"
              type="text"
              class="input"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Phụ đề
            </label>
            <input
              v-model="bannerForm.subtitle"
              type="text"
              class="input"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Hình ảnh *
            </label>
            <input
              type="file"
              accept="image/png,image/jpg,image/jpeg"
              @change="handleFileUpload"
              class="input"
              required
            />
            <p class="text-xs text-gray-500 mt-1">
              PNG/JPG, tối đa 500KB, kích thước 716x320px
            </p>
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              type="button"
              @click="showAddBanner = false"
              class="flex-1 btn btn-secondary"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 btn btn-primary"
            >
              Thêm banner
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Add Term Modal -->
    <div v-if="showAddTerm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-sm w-full">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Thêm kỳ hạn mới</h3>
        </div>
        
        <form @submit.prevent="addTerm" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số tháng *
            </label>
            <input
              v-model="newTerm"
              type="number"
              min="1"
              max="60"
              class="input"
              required
            />
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              type="button"
              @click="showAddTerm = false"
              class="flex-1 btn btn-secondary"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 btn btn-primary"
            >
              Thêm kỳ hạn
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useContentStore } from '@/stores/content'

// Icons
import PhotoIcon from '@/components/icons/PhotoIcon.vue'
import PercentBadgeIcon from '@/components/icons/PercentBadgeIcon.vue'
import CurrencyDollarIcon from '@/components/icons/CurrencyDollarIcon.vue'
import ClockIcon from '@/components/icons/ClockIcon.vue'
import BellIcon from '@/components/icons/BellIcon.vue'
import PlusIcon from '@/components/icons/PlusIcon.vue'
import PencilIcon from '@/components/icons/PencilIcon.vue'
import EyeIcon from '@/components/icons/EyeIcon.vue'
import EyeSlashIcon from '@/components/icons/EyeSlashIcon.vue'
import TrashIcon from '@/components/icons/TrashIcon.vue'
import CheckCircleIcon from '@/components/icons/CheckCircleIcon.vue'
import XCircleIcon from '@/components/icons/XCircleIcon.vue'

const contentStore = useContentStore()

// Reactive data
const activeTab = ref('banners')
const showAddBanner = ref(false)
const showAddTerm = ref(false)
const showAddNotification = ref(false)
const newTerm = ref('')

const tabs = [
  { key: 'banners', label: 'Banner', icon: PhotoIcon },
  { key: 'interest', label: 'Lãi suất', icon: PercentBadgeIcon },
  { key: 'limits', label: 'Hạn mức vay', icon: CurrencyDollarIcon },
  { key: 'terms', label: 'Thời gian vay', icon: ClockIcon },
  { key: 'notifications', label: 'Thông báo', icon: BellIcon }
]

const bannerForm = reactive({
  title: '',
  subtitle: '',
  imageFile: null
})

const interestConfig = reactive({
  baseRate: 2.0,
  promotionalRate: 1.5,
  penaltyRate: 3.0
})

const loanLimits = reactive({
  minAmount: 10000000,
  maxAmount: 500000000,
  stepAmount: 1000000,
  defaultAmount: 50000000
})

// Mock data
const banners = ref([
  {
    id: 1,
    title: 'Vay nhanh - Lãi thấp',
    subtitle: 'Chỉ từ 2%/tháng',
    imageUrl: 'https://via.placeholder.com/716x320/3B82F6/FFFFFF?text=Banner+1',
    active: true
  },
  {
    id: 2,
    title: 'Duyệt trong 24h',
    subtitle: 'Không cần thế chấp',
    imageUrl: 'https://via.placeholder.com/716x320/10B981/FFFFFF?text=Banner+2',
    active: true
  }
])

const interestHistory = ref([
  { id: 1, rate: 2.0, date: '2024-01-15T10:00:00Z', updatedBy: 'Admin' },
  { id: 2, rate: 2.2, date: '2024-01-01T10:00:00Z', updatedBy: 'Admin' }
])

const loanTerms = ref([
  { id: 1, months: 6, active: true },
  { id: 2, months: 12, active: true },
  { id: 3, months: 18, active: true },
  { id: 4, months: 24, active: true },
  { id: 5, months: 36, active: true },
  { id: 6, months: 48, active: false }
])

const notifications = ref([
  {
    id: 1,
    title: 'Khuyến mãi tháng 1',
    content: 'Giảm 0.5% lãi suất cho khách hàng mới',
    type: 'promotion',
    createdAt: '2024-01-15T10:00:00Z',
    sentCount: 1250
  },
  {
    id: 2,
    title: 'Bảo trì hệ thống',
    content: 'Hệ thống sẽ bảo trì từ 2h-4h sáng ngày 20/1',
    type: 'system',
    createdAt: '2024-01-14T15:00:00Z',
    sentCount: 3500
  }
])

// Methods
const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file size (500KB)
    if (file.size > 500 * 1024) {
      alert('File quá lớn! Vui lòng chọn file dưới 500KB')
      return
    }
    
    // Validate file type
    if (!['image/png', 'image/jpg', 'image/jpeg'].includes(file.type)) {
      alert('Chỉ chấp nhận file PNG/JPG!')
      return
    }
    
    bannerForm.imageFile = file
  }
}

const handleImageError = (event) => {
  event.target.src = 'https://via.placeholder.com/716x320/9CA3AF/FFFFFF?text=No+Image'
}

const addBanner = () => {
  const newBanner = {
    id: Date.now(),
    title: bannerForm.title,
    subtitle: bannerForm.subtitle,
    imageUrl: 'https://via.placeholder.com/716x320/3B82F6/FFFFFF?text=' + encodeURIComponent(bannerForm.title),
    active: true
  }
  
  banners.value.push(newBanner)
  
  // Reset form
  bannerForm.title = ''
  bannerForm.subtitle = ''
  bannerForm.imageFile = null
  showAddBanner.value = false
  
  alert('Đã thêm banner thành công!')
}

const editBanner = (banner) => {
  alert(`Chỉnh sửa banner: ${banner.title}`)
}

const toggleBanner = (banner) => {
  banner.active = !banner.active
  const status = banner.active ? 'kích hoạt' : 'tạm dừng'
  alert(`Đã ${status} banner: ${banner.title}`)
}

const deleteBanner = (id) => {
  if (confirm('Bạn có chắc chắn muốn xóa banner này?')) {
    const index = banners.value.findIndex(b => b.id === id)
    if (index > -1) {
      banners.value.splice(index, 1)
      alert('Đã xóa banner thành công!')
    }
  }
}

const updateInterestRates = () => {
  alert('Đã cập nhật lãi suất thành công!')
}

const updateLoanLimits = () => {
  alert('Đã cập nhật hạn mức vay thành công!')
}

const addTerm = () => {
  const months = parseInt(newTerm.value)
  if (loanTerms.value.some(term => term.months === months)) {
    alert('Kỳ hạn này đã tồn tại!')
    return
  }
  
  loanTerms.value.push({
    id: Date.now(),
    months: months,
    active: true
  })
  
  loanTerms.value.sort((a, b) => a.months - b.months)
  
  newTerm.value = ''
  showAddTerm.value = false
  alert('Đã thêm kỳ hạn thành công!')
}

const toggleTerm = (term) => {
  term.active = !term.active
  const status = term.active ? 'kích hoạt' : 'tạm dừng'
  alert(`Đã ${status} kỳ hạn ${term.months} tháng`)
}

const deleteTerm = (id) => {
  if (confirm('Bạn có chắc chắn muốn xóa kỳ hạn này?')) {
    const index = loanTerms.value.findIndex(t => t.id === id)
    if (index > -1) {
      loanTerms.value.splice(index, 1)
      alert('Đã xóa kỳ hạn thành công!')
    }
  }
}

const getNotificationTypeClass = (type) => {
  const classes = {
    promotion: 'bg-success-100 text-success-800',
    system: 'bg-warning-100 text-warning-800',
    reminder: 'bg-primary-100 text-primary-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getNotificationTypeText = (type) => {
  const texts = {
    promotion: 'Khuyến mãi',
    system: 'Hệ thống',
    reminder: 'Nhắc nhở'
  }
  return texts[type] || 'Khác'
}

const editNotification = (notification) => {
  alert(`Chỉnh sửa thông báo: ${notification.title}`)
}

const deleteNotification = (id) => {
  if (confirm('Bạn có chắc chắn muốn xóa thông báo này?')) {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
      alert('Đã xóa thông báo thành công!')
    }
  }
}
</script>