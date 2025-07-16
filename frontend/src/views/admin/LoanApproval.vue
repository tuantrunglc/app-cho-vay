<template>
  <div class="space-y-6">
    <!-- Search and Filters -->
    <div class="card p-6">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div class="flex-1 max-w-md">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm kiếm theo SĐT hoặc CCCD..."
              class="input pl-10"
            />
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
          </div>
        </div>
        
        <div class="flex items-center space-x-4">
          <select v-model="statusFilter" class="input">
            <option value="">Tất cả trạng thái</option>
            <option value="pending">Chờ duyệt</option>
            <option value="cancelled">Đã hủy</option>
          </select>
          
          <button
            @click="refreshData"
            class="btn btn-secondary"
          >
            <ArrowPathIcon class="w-4 h-4 mr-2" />
            Làm mới
          </button>
        </div>
      </div>
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
            {{ tab.label }}
            <span
              class="ml-2 py-0.5 px-2 rounded-full text-xs"
              :class="activeTab === tab.key 
                ? 'bg-primary-100 text-primary-600' 
                : 'bg-gray-100 text-gray-600'"
            >
              {{ tab.count }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Mã hồ sơ
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Khách hàng
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Số tiền vay
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Thời gian vay
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Xác thực
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Trạng thái
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ngày tạo
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="application in filteredApplications"
              :key="application.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ application.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ application.customerName }}</div>
                  <div class="text-sm text-gray-500">{{ application.phone }}</div>
                  <div class="text-sm text-gray-500">{{ application.idCard }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatCurrency(application.amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ application.term }} tháng
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                  :class="application.verified 
                    ? 'bg-success-100 text-success-800' 
                    : 'bg-warning-100 text-warning-800'"
                >
                  {{ application.verified ? 'Đã xác thực' : 'Chưa xác thực' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                  :class="getStatusClass(application.status)"
                >
                  {{ getStatusText(application.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(application.createdAt) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                  <button
                    @click="viewApplication(application)"
                    class="text-primary-600 hover:text-primary-900"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  
                  <button
                    v-if="application.status === 'pending'"
                    @click="approveApplication(application.id)"
                    class="text-success-600 hover:text-success-900"
                  >
                    <CheckIcon class="w-4 h-4" />
                  </button>
                  
                  <button
                    v-if="application.status === 'pending'"
                    @click="rejectApplication(application.id)"
                    class="text-danger-600 hover:text-danger-900"
                  >
                    <XMarkIcon class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="filteredApplications.length === 0" class="text-center py-12">
        <DocumentIcon class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-900">Không có đơn vay nào</h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ activeTab === 'pending' ? 'Chưa có đơn vay nào chờ duyệt' : 'Chưa có đơn vay nào bị hủy' }}
        </p>
      </div>
    </div>

    <!-- Application Detail Modal -->
    <div v-if="selectedApplication" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              Chi tiết đơn vay {{ selectedApplication.id }}
            </h3>
            <button
              @click="selectedApplication = null"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <div class="p-6 space-y-6">
          <!-- Customer Info -->
          <div>
            <h4 class="text-sm font-medium text-gray-900 mb-3">Thông tin khách hàng</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-500">Họ tên:</span>
                <span class="ml-2 font-medium">{{ selectedApplication.customerName }}</span>
              </div>
              <div>
                <span class="text-gray-500">Số điện thoại:</span>
                <span class="ml-2 font-medium">{{ selectedApplication.phone }}</span>
              </div>
              <div>
                <span class="text-gray-500">CCCD:</span>
                <span class="ml-2 font-medium">{{ selectedApplication.idCard }}</span>
              </div>
              <div>
                <span class="text-gray-500">Trạng thái xác thực:</span>
                <span
                  class="ml-2 inline-flex px-2 py-1 text-xs font-medium rounded-full"
                  :class="selectedApplication.verified 
                    ? 'bg-success-100 text-success-800' 
                    : 'bg-warning-100 text-warning-800'"
                >
                  {{ selectedApplication.verified ? 'Đã xác thực' : 'Chưa xác thực' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Loan Info -->
          <div>
            <h4 class="text-sm font-medium text-gray-900 mb-3">Thông tin khoản vay</h4>
            <div class="grid grid-cols-2 gap-4 text-sm">
              <div>
                <span class="text-gray-500">Số tiền vay:</span>
                <span class="ml-2 font-medium">{{ formatCurrency(selectedApplication.amount) }}</span>
              </div>
              <div>
                <span class="text-gray-500">Thời gian vay:</span>
                <span class="ml-2 font-medium">{{ selectedApplication.term }} tháng</span>
              </div>
              <div>
                <span class="text-gray-500">Lãi suất:</span>
                <span class="ml-2 font-medium">2%/tháng</span>
              </div>
              <div>
                <span class="text-gray-500">Trả hàng tháng:</span>
                <span class="ml-2 font-medium">{{ formatCurrency(calculateMonthlyPayment(selectedApplication)) }}</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div v-if="selectedApplication.status === 'pending'" class="flex space-x-3 pt-4 border-t border-gray-200">
            <button
              @click="approveApplication(selectedApplication.id)"
              class="flex-1 btn btn-success"
            >
              <CheckIcon class="w-4 h-4 mr-2" />
              Phê duyệt
            </button>
            <button
              @click="rejectApplication(selectedApplication.id)"
              class="flex-1 btn btn-danger"
            >
              <XMarkIcon class="w-4 h-4 mr-2" />
              Từ chối
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useLoanStore } from '@/stores/loan'
import { useAdminStore } from '@/stores/admin'
import { apiHelpers } from '@/services'

// Icons
import MagnifyingGlassIcon from '@/components/icons/MagnifyingGlassIcon.vue'
import ArrowPathIcon from '@/components/icons/ArrowPathIcon.vue'
import EyeIcon from '@/components/icons/EyeIcon.vue'
import CheckIcon from '@/components/icons/CheckIcon.vue'
import XMarkIcon from '@/components/icons/XMarkIcon.vue'
import DocumentIcon from '@/components/icons/DocumentIcon.vue'

const loanStore = useLoanStore()
const adminStore = useAdminStore()

// Reactive data
const activeTab = ref('pending')
const searchQuery = ref('')
const statusFilter = ref('')
const selectedApplication = ref(null)

// Computed properties
const tabs = computed(() => [
  {
    key: 'pending',
    label: 'Chờ duyệt',
    count: adminStore.pendingLoans.length
  },
  {
    key: 'rejected',
    label: 'Đã từ chối',
    count: adminStore.rejectedLoans.length
  }
])

const currentApplications = computed(() => {
  if (activeTab.value === 'pending') {
    return adminStore.pendingLoans
  } else {
    return adminStore.rejectedLoans
  }
})

const filteredApplications = computed(() => {
  let applications = currentApplications.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    applications = applications.filter(app => 
      app.phone?.includes(query) || 
      app.id_card?.includes(query) ||
      app.customer_name?.toLowerCase().includes(query) ||
      app.name?.toLowerCase().includes(query)
    )
  }

  return applications
})

// Methods
const formatCurrency = (amount) => {
  return apiHelpers.formatCurrency(amount)
}

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

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-warning-100 text-warning-800',
    approved: 'bg-success-100 text-success-800',
    cancelled: 'bg-danger-100 text-danger-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Chờ duyệt',
    approved: 'Đã duyệt',
    cancelled: 'Đã hủy'
  }
  return texts[status] || 'Không xác định'
}

const calculateMonthlyPayment = (application) => {
  const principal = application.amount
  const monthlyInterest = principal * 0.02 // 2% per month
  const monthlyPrincipal = principal / application.term
  return monthlyPrincipal + monthlyInterest
}

const viewApplication = (application) => {
  selectedApplication.value = application
}

const approveApplication = async (id) => {
  const result = await adminStore.approveLoanApplication(id)
  
  if (result.success) {
    alert('Đơn vay đã được duyệt thành công!')
    selectedApplication.value = null
    refreshData()
  } else {
    alert(result.error || 'Có lỗi xảy ra khi duyệt đơn vay')
  }
}

const rejectApplication = async (id, reason = '') => {
  const result = await adminStore.rejectLoanApplication(id, { reason })
  
  if (result.success) {
    alert('Đơn vay đã bị từ chối!')
    selectedApplication.value = null
    refreshData()
  } else {
    alert(result.error || 'Có lỗi xảy ra khi từ chối đơn vay')
  }
}

const refreshData = async () => {
  await adminStore.fetchLoanApplications()
}

// Initialize data
onMounted(() => {
  refreshData()
})
</script>