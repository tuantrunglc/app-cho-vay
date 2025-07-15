<template>
  <div class="p-4 space-y-4">
    <!-- Header -->
    <div class="text-center py-3">
      <h1 class="text-xl font-bold text-gray-900 mb-1">Ví tiền</h1>
      <p class="text-sm text-gray-600">Quản lý tài chính của bạn</p>
    </div>

    <!-- Balance Card -->
    <div class="card p-6 bg-gradient-to-r from-primary-500 to-primary-600 text-white">
      <div class="flex items-center justify-between mb-4">
        <div>
          <p class="text-primary-100 text-sm">Số dư khả dụng</p>
          <p class="text-2xl font-bold">{{ formattedBalance }}</p>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
          <WalletIcon class="w-6 h-6" />
        </div>
      </div>
      
      <div class="flex space-x-3">
        <button class="flex-1 bg-white/20 hover:bg-white/30 py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200">
          Nạp tiền
        </button>
        <button class="flex-1 bg-white/20 hover:bg-white/30 py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200">
          Rút tiền
        </button>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-4">
      <div class="card p-4 text-center">
        <div class="w-12 h-12 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <ArrowDownIcon class="w-6 h-6 text-success-600" />
        </div>
        <h3 class="font-semibold text-gray-900 mb-1">Nhận tiền</h3>
        <p class="text-sm text-gray-600">Nhận tiền từ khoản vay</p>
      </div>
      
      <div class="card p-4 text-center">
        <div class="w-12 h-12 bg-warning-100 rounded-full flex items-center justify-center mx-auto mb-3">
          <ArrowUpIcon class="w-6 h-6 text-warning-600" />
        </div>
        <h3 class="font-semibold text-gray-900 mb-1">Trả nợ</h3>
        <p class="text-sm text-gray-600">Thanh toán khoản vay</p>
      </div>
    </div>

    <!-- Linked Banks -->
    <div class="card p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Ngân hàng liên kết</h2>
        <button class="text-primary-600 text-sm font-medium hover:text-primary-700">
          + Thêm mới
        </button>
      </div>

      <div class="space-y-3">
        <div
          v-for="bank in linkedBanks"
          :key="bank.id"
          class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:border-primary-300 transition-colors duration-200"
          :class="bank.isDefault ? 'border-primary-300 bg-primary-50' : ''"
        >
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
              <img 
                :src="bank.logo" 
                :alt="bank.name"
                class="w-8 h-8 object-contain"
                @error="handleImageError"
              />
            </div>
            <div>
              <h3 class="font-semibold text-gray-900">{{ bank.name }}</h3>
              <p class="text-sm text-gray-600">{{ bank.accountNumber }}</p>
            </div>
          </div>
          
          <div class="flex items-center space-x-2">
            <span
              v-if="bank.isDefault"
              class="px-2 py-1 bg-primary-100 text-primary-700 text-xs font-medium rounded-full"
            >
              Mặc định
            </span>
            <button class="p-2 text-gray-400 hover:text-gray-600">
              <DotsVerticalIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Giao dịch gần đây</h2>
      
      <div class="space-y-4">
        <div
          v-for="transaction in recentTransactions"
          :key="transaction.id"
          class="flex items-center justify-between py-3 border-b border-gray-100 last:border-b-0"
        >
          <div class="flex items-center space-x-3">
            <div 
              class="w-10 h-10 rounded-full flex items-center justify-center"
              :class="transaction.type === 'in' ? 'bg-success-100' : 'bg-danger-100'"
            >
              <ArrowDownIcon 
                v-if="transaction.type === 'in'"
                class="w-5 h-5 text-success-600"
              />
              <ArrowUpIcon 
                v-else
                class="w-5 h-5 text-danger-600"
              />
            </div>
            <div>
              <p class="font-medium text-gray-900">{{ transaction.description }}</p>
              <p class="text-sm text-gray-500">{{ formatDate(transaction.date) }}</p>
            </div>
          </div>
          
          <div class="text-right">
            <p 
              class="font-semibold"
              :class="transaction.type === 'in' ? 'text-success-600' : 'text-danger-600'"
            >
              {{ transaction.type === 'in' ? '+' : '-' }}{{ formatCurrency(transaction.amount) }}
            </p>
            <p class="text-sm text-gray-500">{{ transaction.status }}</p>
          </div>
        </div>
      </div>
      
      <div v-if="recentTransactions.length === 0" class="text-center py-8">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <DocumentIcon class="w-8 h-8 text-gray-400" />
        </div>
        <p class="text-gray-500">Chưa có giao dịch nào</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useUserStore } from '@/stores/user'
import WalletIcon from '@/components/icons/WalletIcon.vue'
import ArrowDownIcon from '@/components/icons/ArrowDownIcon.vue'
import ArrowUpIcon from '@/components/icons/ArrowUpIcon.vue'
import DotsVerticalIcon from '@/components/icons/DotsVerticalIcon.vue'
import DocumentIcon from '@/components/icons/DocumentIcon.vue'

const userStore = useUserStore()

// Mock transaction data
const recentTransactions = ref([
  {
    id: 1,
    type: 'in',
    description: 'Nhận tiền vay LN001',
    amount: 50000000,
    date: '2024-01-15T10:30:00Z',
    status: 'Hoàn thành'
  },
  {
    id: 2,
    type: 'out',
    description: 'Trả nợ định kỳ',
    amount: 2500000,
    date: '2024-01-10T14:20:00Z',
    status: 'Hoàn thành'
  }
])

// Computed properties
const formattedBalance = computed(() => userStore.formattedBalance)
const linkedBanks = computed(() => userStore.linkedBanks)

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
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const handleImageError = (event) => {
  // Fallback to a placeholder when bank logo fails to load
  event.target.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiByeD0iNCIgZmlsbD0iIzlDQTNBRiIvPgo8cGF0aCBkPSJNMTYgMjJDMTkuMzEzNyAyMiAyMiAxOS4zMTM3IDIyIDE2QzIyIDEyLjY4NjMgMTkuMzEzNyAxMCAxNiAxMEMxMi42ODYzIDEwIDEwIDEyLjY4NjMgMTAgMTZDMTAgMTkuMzEzNyAxMi42ODYzIDIyIDE2IDIyWiIgZmlsbD0id2hpdGUiLz4KPC9zdmc+Cg=='
}
</script>