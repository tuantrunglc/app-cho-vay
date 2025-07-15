<template>
  <div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="card p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
              <DocumentCheckIcon class="w-5 h-5 text-primary-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Đơn chờ duyệt</p>
            <p class="text-2xl font-semibold text-gray-900">{{ pendingLoans }}</p>
          </div>
        </div>
      </div>

      <div class="card p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-success-100 rounded-lg flex items-center justify-center">
              <ClipboardDocumentListIcon class="w-5 h-5 text-success-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Đơn đang hoạt động</p>
            <p class="text-2xl font-semibold text-gray-900">{{ activeLoans }}</p>
          </div>
        </div>
      </div>

      <div class="card p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-warning-100 rounded-lg flex items-center justify-center">
              <UsersIcon class="w-5 h-5 text-warning-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Tổng khách hàng</p>
            <p class="text-2xl font-semibold text-gray-900">{{ totalCustomers }}</p>
          </div>
        </div>
      </div>

      <div class="card p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-danger-100 rounded-lg flex items-center justify-center">
              <CurrencyDollarIcon class="w-5 h-5 text-danger-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Tổng dư nợ</p>
            <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(totalDebt) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Loan Applications Chart -->
      <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Đơn vay theo tháng</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
          <p class="text-gray-500">Biểu đồ sẽ được hiển thị ở đây</p>
        </div>
      </div>

      <!-- Revenue Chart -->
      <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Doanh thu theo tháng</h3>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
          <p class="text-gray-500">Biểu đồ sẽ được hiển thị ở đây</p>
        </div>
      </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Recent Loan Applications -->
      <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Đơn vay mới nhất</h3>
          <router-link
            to="/admin/loan-approval"
            class="text-primary-600 hover:text-primary-700 text-sm font-medium"
          >
            Xem tất cả
          </router-link>
        </div>

        <div class="space-y-4">
          <div
            v-for="application in recentApplications"
            :key="application.id"
            class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0"
          >
            <div>
              <p class="font-medium text-gray-900">{{ application.customerName }}</p>
              <p class="text-sm text-gray-500">{{ application.phone }}</p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-gray-900">{{ formatCurrency(application.amount) }}</p>
              <span
                class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                :class="getStatusClass(application.status)"
              >
                {{ getStatusText(application.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- System Status -->
      <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Trạng thái hệ thống</h3>
        
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-success-500 rounded-full mr-3"></div>
              <span class="text-gray-700">API Server</span>
            </div>
            <span class="text-success-600 text-sm font-medium">Hoạt động</span>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-success-500 rounded-full mr-3"></div>
              <span class="text-gray-700">Database</span>
            </div>
            <span class="text-success-600 text-sm font-medium">Hoạt động</span>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-warning-500 rounded-full mr-3"></div>
              <span class="text-gray-700">Payment Gateway</span>
            </div>
            <span class="text-warning-600 text-sm font-medium">Chậm</span>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-success-500 rounded-full mr-3"></div>
              <span class="text-gray-700">SMS Service</span>
            </div>
            <span class="text-success-600 text-sm font-medium">Hoạt động</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="card p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Thao tác nhanh</h3>
      
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <router-link
          to="/admin/loan-approval"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors duration-200"
        >
          <DocumentCheckIcon class="w-8 h-8 text-primary-600 mb-2" />
          <span class="text-sm font-medium text-gray-900">Duyệt đơn vay</span>
        </router-link>

        <router-link
          to="/admin/customers"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors duration-200"
        >
          <UsersIcon class="w-8 h-8 text-primary-600 mb-2" />
          <span class="text-sm font-medium text-gray-900">Quản lý KH</span>
        </router-link>

        <router-link
          to="/admin/content"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors duration-200"
        >
          <CogIcon class="w-8 h-8 text-primary-600 mb-2" />
          <span class="text-sm font-medium text-gray-900">Cấu hình</span>
        </router-link>

        <button
          @click="exportReport"
          class="flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors duration-200"
        >
          <DocumentArrowDownIcon class="w-8 h-8 text-primary-600 mb-2" />
          <span class="text-sm font-medium text-gray-900">Xuất báo cáo</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useLoanStore } from '@/stores/loan'
import { useUserStore } from '@/stores/user'

// Icons
import DocumentCheckIcon from '@/components/icons/DocumentCheckIcon.vue'
import ClipboardDocumentListIcon from '@/components/icons/ClipboardDocumentListIcon.vue'
import UsersIcon from '@/components/icons/UsersIcon.vue'
import CogIcon from '@/components/icons/CogIcon.vue'
import CurrencyDollarIcon from '@/components/icons/CurrencyDollarIcon.vue'
import DocumentArrowDownIcon from '@/components/icons/DocumentArrowDownIcon.vue'

const loanStore = useLoanStore()
const userStore = useUserStore()

// Computed properties
const pendingLoans = computed(() => loanStore.pendingApplications.length)
const activeLoans = computed(() => loanStore.activeLoanApplications.length)
const totalCustomers = computed(() => userStore.customers.length)
const totalDebt = computed(() => {
  return loanStore.activeLoanApplications.reduce((total, loan) => {
    return total + (loan.remainingAmount || 0)
  }, 0)
})

const recentApplications = computed(() => {
  return [...loanStore.loanApplications]
    .sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt))
    .slice(0, 5)
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const getStatusClass = (status) => {
  const classes = {
    pending: 'bg-warning-100 text-warning-800',
    approved: 'bg-success-100 text-success-800',
    cancelled: 'bg-danger-100 text-danger-800',
    active: 'bg-primary-100 text-primary-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    pending: 'Chờ duyệt',
    approved: 'Đã duyệt',
    cancelled: 'Đã hủy',
    active: 'Hoạt động'
  }
  return texts[status] || 'Không xác định'
}

const exportReport = () => {
  // Mock export functionality
  alert('Tính năng xuất báo cáo sẽ được triển khai sớm!')
}
</script>