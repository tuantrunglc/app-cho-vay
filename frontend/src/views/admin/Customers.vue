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
              placeholder="Tìm kiếm theo tên, SĐT hoặc email..."
              class="input pl-10"
            />
            <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
          </div>
        </div>
        
        <div class="flex items-center space-x-4">
          <select v-model="statusFilter" class="input">
            <option value="">Tất cả trạng thái</option>
            <option value="active">Hoạt động</option>
            <option value="blocked">Bị khóa</option>
          </select>
          
          <button
            @click="showAddCustomer = true"
            class="btn btn-primary"
          >
            <PlusIcon class="w-4 h-4 mr-2" />
            Thêm khách hàng
          </button>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="card p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-primary-100 rounded-lg flex items-center justify-center">
              <UsersIcon class="w-5 h-5 text-primary-600" />
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
            <div class="w-8 h-8 bg-success-100 rounded-lg flex items-center justify-center">
              <CheckCircleIcon class="w-5 h-5 text-success-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Đang hoạt động</p>
            <p class="text-2xl font-semibold text-gray-900">{{ activeCustomersCount }}</p>
          </div>
        </div>
      </div>

      <div class="card p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-danger-100 rounded-lg flex items-center justify-center">
              <XCircleIcon class="w-5 h-5 text-danger-600" />
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Bị khóa</p>
            <p class="text-2xl font-semibold text-gray-900">{{ blockedCustomersCount }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="card">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Danh sách khách hàng
          <span class="ml-2 text-sm font-normal text-gray-500">
            ({{ filteredCustomers.length }} khách hàng)
          </span>
        </h3>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Khách hàng
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Liên hệ
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                CCCD
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ngày tham gia
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tổng khoản vay
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Dư nợ hiện tại
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Trạng thái
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="customer in filteredCustomers"
              :key="customer.id"
              class="hover:bg-gray-50"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                      <span class="text-sm font-medium text-primary-600">
                        {{ customer.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ customer.name }}</div>
                    <div class="text-sm text-gray-500">ID: {{ customer.id }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ customer.phone }}</div>
                <div class="text-sm text-gray-500">{{ customer.email }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ customer.idCard }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ formatDate(customer.joinDate) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ customer.totalLoans }} khoản
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatCurrency(customer.currentDebt) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                  :class="customer.status === 'active' 
                    ? 'bg-success-100 text-success-800' 
                    : 'bg-danger-100 text-danger-800'"
                >
                  {{ customer.status === 'active' ? 'Hoạt động' : 'Bị khóa' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                  <button
                    @click="viewCustomer(customer)"
                    class="text-primary-600 hover:text-primary-900"
                    title="Xem chi tiết"
                  >
                    <EyeIcon class="w-4 h-4" />
                  </button>
                  
                  <button
                    @click="editCustomer(customer)"
                    class="text-warning-600 hover:text-warning-900"
                    title="Chỉnh sửa"
                  >
                    <PencilIcon class="w-4 h-4" />
                  </button>
                  
                  <button
                    @click="toggleCustomerStatus(customer)"
                    :class="customer.status === 'active' 
                      ? 'text-danger-600 hover:text-danger-900' 
                      : 'text-success-600 hover:text-success-900'"
                    :title="customer.status === 'active' ? 'Khóa tài khoản' : 'Mở khóa tài khoản'"
                  >
                    <LockClosedIcon v-if="customer.status === 'active'" class="w-4 h-4" />
                    <LockOpenIcon v-else class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-if="filteredCustomers.length === 0" class="text-center py-12">
        <UsersIcon class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-900">Không có khách hàng nào</h3>
        <p class="mt-1 text-sm text-gray-500">Chưa có khách hàng nào trong hệ thống</p>
      </div>
    </div>

    <!-- Add Customer Modal -->
    <div v-if="showAddCustomer" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Thêm khách hàng mới</h3>
        </div>
        
        <form @submit.prevent="addCustomer" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Họ và tên *
            </label>
            <input
              v-model="customerForm.name"
              type="text"
              class="input"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số điện thoại *
            </label>
            <input
              v-model="customerForm.phone"
              type="tel"
              class="input"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Email *
            </label>
            <input
              v-model="customerForm.email"
              type="email"
              class="input"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              CCCD *
            </label>
            <input
              v-model="customerForm.idCard"
              type="text"
              class="input"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Địa chỉ
            </label>
            <textarea
              v-model="customerForm.address"
              rows="3"
              class="input resize-none"
            ></textarea>
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              type="button"
              @click="showAddCustomer = false"
              class="flex-1 btn btn-secondary"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 btn btn-primary"
            >
              Thêm khách hàng
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Customer Detail Modal -->
    <div v-if="selectedCustomer" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              Chi tiết khách hàng
            </h3>
            <button
              @click="selectedCustomer = null"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <div class="p-6 space-y-6">
          <!-- Customer Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3">Thông tin cá nhân</h4>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-500">Họ tên:</span>
                  <span class="font-medium">{{ selectedCustomer.name }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Số điện thoại:</span>
                  <span class="font-medium">{{ selectedCustomer.phone }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Email:</span>
                  <span class="font-medium">{{ selectedCustomer.email }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">CCCD:</span>
                  <span class="font-medium">{{ selectedCustomer.idCard }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Địa chỉ:</span>
                  <span class="font-medium">{{ selectedCustomer.address }}</span>
                </div>
              </div>
            </div>

            <div>
              <h4 class="text-sm font-medium text-gray-900 mb-3">Thông tin tài khoản</h4>
              <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                  <span class="text-gray-500">Ngày tham gia:</span>
                  <span class="font-medium">{{ formatDate(selectedCustomer.joinDate) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Tổng khoản vay:</span>
                  <span class="font-medium">{{ selectedCustomer.totalLoans }} khoản</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Dư nợ hiện tại:</span>
                  <span class="font-medium text-danger-600">{{ formatCurrency(selectedCustomer.currentDebt) }}</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-gray-500">Trạng thái:</span>
                  <span
                    class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                    :class="selectedCustomer.status === 'active' 
                      ? 'bg-success-100 text-success-800' 
                      : 'bg-danger-100 text-danger-800'"
                  >
                    {{ selectedCustomer.status === 'active' ? 'Hoạt động' : 'Bị khóa' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Loan History -->
          <div>
            <h4 class="text-sm font-medium text-gray-900 mb-3">Lịch sử vay</h4>
            <div class="bg-gray-50 rounded-lg p-4">
              <p class="text-sm text-gray-500 text-center">
                Tính năng lịch sử vay sẽ được triển khai sớm
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive } from 'vue'
import { useUserStore } from '@/stores/user'

// Icons
import MagnifyingGlassIcon from '@/components/icons/MagnifyingGlassIcon.vue'
import PlusIcon from '@/components/icons/PlusIcon.vue'
import UsersIcon from '@/components/icons/UsersIcon.vue'
import CheckCircleIcon from '@/components/icons/CheckCircleIcon.vue'
import XCircleIcon from '@/components/icons/XCircleIcon.vue'
import EyeIcon from '@/components/icons/EyeIcon.vue'
import PencilIcon from '@/components/icons/PencilIcon.vue'
import LockClosedIcon from '@/components/icons/LockClosedIcon.vue'
import LockOpenIcon from '@/components/icons/LockOpenIcon.vue'
import XMarkIcon from '@/components/icons/XMarkIcon.vue'

const userStore = useUserStore()

// Reactive data
const searchQuery = ref('')
const statusFilter = ref('')
const showAddCustomer = ref(false)
const selectedCustomer = ref(null)

const customerForm = reactive({
  name: '',
  phone: '',
  email: '',
  idCard: '',
  address: ''
})

// Computed properties
const totalCustomers = computed(() => userStore.customers.length)
const activeCustomersCount = computed(() => userStore.activeCustomers.length)
const blockedCustomersCount = computed(() => userStore.blockedCustomers.length)

const filteredCustomers = computed(() => {
  let customers = userStore.customers

  // Status filter
  if (statusFilter.value) {
    customers = customers.filter(customer => customer.status === statusFilter.value)
  }

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    customers = customers.filter(customer => 
      customer.name.toLowerCase().includes(query) ||
      customer.phone.includes(query) ||
      customer.email.toLowerCase().includes(query) ||
      customer.idCard.includes(query)
    )
  }

  return customers
})

// Methods
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount)
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const viewCustomer = (customer) => {
  selectedCustomer.value = customer
}

const editCustomer = (customer) => {
  // Mock edit functionality
  alert(`Chỉnh sửa thông tin khách hàng ${customer.name}`)
}

const toggleCustomerStatus = (customer) => {
  const newStatus = customer.status === 'active' ? 'blocked' : 'active'
  userStore.updateCustomerStatus(customer.id, newStatus)
  
  const action = newStatus === 'blocked' ? 'khóa' : 'mở khóa'
  alert(`Đã ${action} tài khoản khách hàng ${customer.name}`)
}

const addCustomer = () => {
  userStore.addCustomer({
    name: customerForm.name,
    phone: customerForm.phone,
    email: customerForm.email,
    idCard: customerForm.idCard,
    address: customerForm.address
  })

  // Reset form
  Object.keys(customerForm).forEach(key => {
    customerForm[key] = ''
  })

  showAddCustomer.value = false
  alert('Đã thêm khách hàng mới thành công!')
}
</script>