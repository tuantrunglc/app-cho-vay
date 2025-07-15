<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center space-x-3">
        <button @click="$router.go(-1)" class="p-2 hover:bg-gray-100 rounded-lg">
          <ArrowLeftIcon class="w-5 h-5 text-gray-600" />
        </button>
        <h1 class="text-xl font-bold text-gray-900">Thông báo</h1>
      </div>
      
      <button
        v-if="unreadCount > 0"
        @click="markAllAsRead"
        class="text-sm text-primary-600 hover:text-primary-700 font-medium"
      >
        Đánh dấu đã đọc
      </button>
    </div>

    <!-- Filter Tabs -->
    <div class="flex space-x-1 bg-gray-100 rounded-lg p-1">
      <button
        v-for="tab in filterTabs"
        :key="tab.value"
        @click="activeFilter = tab.value"
        class="flex-1 py-2 px-3 text-sm font-medium rounded-md transition-colors duration-200"
        :class="activeFilter === tab.value 
          ? 'bg-white text-gray-900 shadow-sm' 
          : 'text-gray-600 hover:text-gray-900'"
      >
        {{ tab.label }}
        <span v-if="tab.count > 0" class="ml-1 text-xs bg-primary-100 text-primary-600 px-1.5 py-0.5 rounded-full">
          {{ tab.count }}
        </span>
      </button>
    </div>

    <!-- Notifications List -->
    <div class="space-y-3">
      <div
        v-for="notification in filteredNotifications"
        :key="notification.id"
        class="bg-white rounded-xl shadow-sm border-l-4 transition-colors duration-200"
        :class="[
          notification.read ? 'border-gray-200' : 'border-primary-500',
          !notification.read ? 'bg-primary-50/30' : ''
        ]"
      >
        <div class="p-4">
          <div class="flex items-start space-x-3">
            <!-- Icon -->
            <div
              class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
              :class="getNotificationIconClass(notification.type)"
            >
              <component :is="getNotificationIcon(notification.type)" class="w-5 h-5" />
            </div>
            
            <!-- Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <h3 class="text-sm font-semibold text-gray-900 mb-1">
                    {{ notification.title }}
                  </h3>
                  <p class="text-sm text-gray-600 mb-2">
                    {{ notification.message }}
                  </p>
                  
                  <!-- Action Button -->
                  <button
                    v-if="notification.actionText"
                    @click="handleNotificationAction(notification)"
                    class="text-sm text-primary-600 hover:text-primary-700 font-medium"
                  >
                    {{ notification.actionText }}
                  </button>
                </div>
                
                <!-- Menu -->
                <div class="relative">
                  <button
                    @click="toggleMenu(notification.id)"
                    class="p-1 hover:bg-gray-100 rounded-full"
                  >
                    <DotsVerticalIcon class="w-4 h-4 text-gray-400" />
                  </button>
                  
                  <div
                    v-if="activeMenu === notification.id"
                    class="absolute right-0 top-8 bg-white rounded-lg shadow-lg border py-1 z-10 min-w-[120px]"
                  >
                    <button
                      @click="toggleRead(notification)"
                      class="w-full px-3 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                    >
                      {{ notification.read ? 'Đánh dấu chưa đọc' : 'Đánh dấu đã đọc' }}
                    </button>
                    <button
                      @click="deleteNotification(notification.id)"
                      class="w-full px-3 py-2 text-left text-sm text-danger-600 hover:bg-gray-50"
                    >
                      Xóa thông báo
                    </button>
                  </div>
                </div>
              </div>
              
              <!-- Time -->
              <div class="flex items-center justify-between mt-2">
                <span class="text-xs text-gray-500">
                  {{ formatTime(notification.createdAt) }}
                </span>
                
                <!-- Priority Badge -->
                <span
                  v-if="notification.priority === 'high'"
                  class="text-xs bg-danger-100 text-danger-800 px-2 py-1 rounded-full"
                >
                  Quan trọng
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredNotifications.length === 0" class="text-center py-12">
      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <BellIcon class="w-8 h-8 text-gray-400" />
      </div>
      <h3 class="text-base font-medium text-gray-900 mb-2">
        {{ activeFilter === 'all' ? 'Chưa có thông báo' : 'Không có thông báo nào' }}
      </h3>
      <p class="text-sm text-gray-500">
        {{ activeFilter === 'all' 
          ? 'Thông báo sẽ hiển thị tại đây' 
          : `Không có thông báo ${filterTabs.find(t => t.value === activeFilter)?.label.toLowerCase()}` 
        }}
      </p>
    </div>

    <!-- Load More -->
    <div v-if="hasMore && filteredNotifications.length > 0" class="text-center py-4">
      <button
        @click="loadMore"
        class="text-primary-600 hover:text-primary-700 font-medium"
        :disabled="isLoading"
      >
        {{ isLoading ? 'Đang tải...' : 'Xem thêm' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import ArrowLeftIcon from '@/components/icons/ArrowLeftIcon.vue'
import BellIcon from '@/components/icons/BellIcon.vue'
import CheckCircleIcon from '@/components/icons/CheckCircleIcon.vue'
import ExclamationTriangleIcon from '@/components/icons/ExclamationTriangleIcon.vue'
import CurrencyDollarIcon from '@/components/icons/CurrencyDollarIcon.vue'
import DocumentTextIcon from '@/components/icons/DocumentTextIcon.vue'
import DotsVerticalIcon from '@/components/icons/DotsVerticalIcon.vue'

const router = useRouter()

// State
const activeFilter = ref('all')
const activeMenu = ref(null)
const isLoading = ref(false)
const hasMore = ref(true)

// Mock notifications data
const notifications = ref([
  {
    id: 1,
    type: 'loan_approved',
    title: 'Khoản vay được duyệt',
    message: 'Khoản vay 10,000,000 VND của bạn đã được phê duyệt. Tiền sẽ được chuyển vào tài khoản trong 24h.',
    actionText: 'Xem chi tiết',
    priority: 'high',
    read: false,
    createdAt: new Date('2024-01-20T10:30:00')
  },
  {
    id: 2,
    type: 'payment_reminder',
    title: 'Nhắc nhở thanh toán',
    message: 'Kỳ thanh toán tiếp theo của bạn là 25/01/2024. Số tiền: 1,200,000 VND.',
    actionText: 'Thanh toán ngay',
    priority: 'normal',
    read: false,
    createdAt: new Date('2024-01-19T14:15:00')
  },
  {
    id: 3,
    type: 'payment_success',
    title: 'Thanh toán thành công',
    message: 'Bạn đã thanh toán thành công 1,200,000 VND cho kỳ hạn tháng 12/2023.',
    actionText: null,
    priority: 'normal',
    read: true,
    createdAt: new Date('2024-01-18T09:45:00')
  },
  {
    id: 4,
    type: 'system',
    title: 'Cập nhật hệ thống',
    message: 'Hệ thống sẽ bảo trì từ 2:00 - 4:00 sáng ngày 22/01/2024.',
    actionText: null,
    priority: 'normal',
    read: true,
    createdAt: new Date('2024-01-17T16:20:00')
  },
  {
    id: 5,
    type: 'loan_overdue',
    title: 'Khoản vay quá hạn',
    message: 'Khoản vay của bạn đã quá hạn 3 ngày. Vui lòng thanh toán sớm để tránh phí phạt.',
    actionText: 'Thanh toán ngay',
    priority: 'high',
    read: false,
    createdAt: new Date('2024-01-16T11:00:00')
  }
])

// Computed
const unreadCount = computed(() => {
  return notifications.value.filter(n => !n.read).length
})

const filterTabs = computed(() => [
  { 
    value: 'all', 
    label: 'Tất cả', 
    count: notifications.value.length 
  },
  { 
    value: 'unread', 
    label: 'Chưa đọc', 
    count: unreadCount.value 
  },
  { 
    value: 'loan', 
    label: 'Khoản vay', 
    count: notifications.value.filter(n => ['loan_approved', 'loan_overdue'].includes(n.type)).length 
  },
  { 
    value: 'payment', 
    label: 'Thanh toán', 
    count: notifications.value.filter(n => ['payment_reminder', 'payment_success'].includes(n.type)).length 
  }
])

const filteredNotifications = computed(() => {
  let filtered = notifications.value
  
  switch (activeFilter.value) {
    case 'unread':
      filtered = filtered.filter(n => !n.read)
      break
    case 'loan':
      filtered = filtered.filter(n => ['loan_approved', 'loan_overdue'].includes(n.type))
      break
    case 'payment':
      filtered = filtered.filter(n => ['payment_reminder', 'payment_success'].includes(n.type))
      break
  }
  
  return filtered.sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
})

// Methods
const getNotificationIcon = (type) => {
  const iconMap = {
    loan_approved: CheckCircleIcon,
    loan_overdue: ExclamationTriangleIcon,
    payment_reminder: CurrencyDollarIcon,
    payment_success: CheckCircleIcon,
    system: DocumentTextIcon
  }
  return iconMap[type] || BellIcon
}

const getNotificationIconClass = (type) => {
  const classMap = {
    loan_approved: 'bg-success-100 text-success-600',
    loan_overdue: 'bg-danger-100 text-danger-600',
    payment_reminder: 'bg-warning-100 text-warning-600',
    payment_success: 'bg-success-100 text-success-600',
    system: 'bg-blue-100 text-blue-600'
  }
  return classMap[type] || 'bg-gray-100 text-gray-600'
}

const formatTime = (date) => {
  const now = new Date()
  const diff = now - date
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 60) return `${minutes} phút trước`
  if (hours < 24) return `${hours} giờ trước`
  if (days < 7) return `${days} ngày trước`
  
  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  }).format(date)
}

const toggleMenu = (id) => {
  activeMenu.value = activeMenu.value === id ? null : id
}

const toggleRead = (notification) => {
  notification.read = !notification.read
  activeMenu.value = null
}

const deleteNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
  activeMenu.value = null
}

const markAllAsRead = () => {
  notifications.value.forEach(n => {
    n.read = true
  })
}

const handleNotificationAction = (notification) => {
  // Mark as read when action is clicked
  notification.read = true
  
  // Handle different actions
  switch (notification.type) {
    case 'loan_approved':
      router.push('/my-loans')
      break
    case 'payment_reminder':
    case 'loan_overdue':
      router.push('/my-loans')
      break
    default:
      break
  }
}

const loadMore = async () => {
  isLoading.value = true
  
  // Mock API call
  await new Promise(resolve => setTimeout(resolve, 1000))
  
  // In real app, would load more notifications
  hasMore.value = false
  isLoading.value = false
}

// Close menu when clicking outside
onMounted(() => {
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
      activeMenu.value = null
    }
  })
})
</script>