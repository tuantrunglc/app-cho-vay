<template>
  <div class="min-h-screen bg-gray-100 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-sm border-r border-gray-200 flex flex-col">
      <!-- Logo -->
      <div class="p-6 border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-900">Admin Panel</h1>
        <p class="text-sm text-gray-500">Quản trị hệ thống vay</p>
      </div>
      
      <!-- Navigation -->
      <nav class="flex-1 p-4">
        <div class="space-y-2">
          <!-- Quản lý đơn vay -->
          <div>
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
              Quản lý đơn vay
            </h3>
            <div class="space-y-1">
              <router-link
                to="/admin/loan-approval"
                class="nav-link"
                :class="isActive('/admin/loan-approval') ? 'nav-link-active' : 'nav-link-inactive'"
              >
                <DocumentCheckIcon class="w-5 h-5" />
                Duyệt đơn vay mới
              </router-link>
              <router-link
                to="/admin/active-loans"
                class="nav-link"
                :class="isActive('/admin/active-loans') ? 'nav-link-active' : 'nav-link-inactive'"
              >
                <ClipboardDocumentListIcon class="w-5 h-5" />
                Đơn đang hoạt động
              </router-link>
            </div>
          </div>
          
          <!-- Quản lý khách hàng -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
              Quản lý khách hàng
            </h3>
            <router-link
              to="/admin/customers"
              class="nav-link"
              :class="isActive('/admin/customers') ? 'nav-link-active' : 'nav-link-inactive'"
            >
              <UsersIcon class="w-5 h-5" />
              Danh sách khách hàng
            </router-link>
          </div>
          
          <!-- Quản lý nội dung -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
              Quản lý nội dung
            </h3>
            <router-link
              to="/admin/content"
              class="nav-link"
              :class="isActive('/admin/content') ? 'nav-link-active' : 'nav-link-inactive'"
            >
              <CogIcon class="w-5 h-5" />
              Cấu hình hệ thống
            </router-link>
          </div>
        </div>
      </nav>
      
      <!-- User Info -->
      <div class="p-4 border-t border-gray-200">
        <div class="flex items-center space-x-3">
          <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
            <span class="text-white text-sm font-medium">A</span>
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 truncate">Admin User</p>
            <p class="text-xs text-gray-500 truncate">admin@example.com</p>
          </div>
        </div>
        <button class="mt-3 w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors duration-200">
          Đăng xuất
        </button>
      </div>
    </aside>
    
    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ pageTitle }}</h1>
            <p class="text-sm text-gray-500">{{ pageDescription }}</p>
          </div>
          <div class="flex items-center space-x-4">
            <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
              <BellIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
      </header>
      
      <!-- Page Content -->
      <div class="flex-1 overflow-auto p-6">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

// Icons
import DocumentCheckIcon from '@/components/icons/DocumentCheckIcon.vue'
import ClipboardDocumentListIcon from '@/components/icons/ClipboardDocumentListIcon.vue'
import UsersIcon from '@/components/icons/UsersIcon.vue'
import CogIcon from '@/components/icons/CogIcon.vue'
import BellIcon from '@/components/icons/BellIcon.vue'

const route = useRoute()

const isActive = (path) => {
  return route.path === path
}

const pageTitle = computed(() => {
  const titles = {
    '/admin': 'Dashboard',
    '/admin/loan-approval': 'Duyệt đơn vay mới',
    '/admin/active-loans': 'Đơn vay đang hoạt động',
    '/admin/customers': 'Quản lý khách hàng',
    '/admin/content': 'Quản lý nội dung'
  }
  return titles[route.path] || 'Admin Panel'
})

const pageDescription = computed(() => {
  const descriptions = {
    '/admin': 'Tổng quan hệ thống',
    '/admin/loan-approval': 'Xem xét và phê duyệt các đơn vay mới',
    '/admin/active-loans': 'Theo dõi các khoản vay đang hoạt động',
    '/admin/customers': 'Quản lý thông tin khách hàng',
    '/admin/content': 'Cấu hình banner, lãi suất và thông báo'
  }
  return descriptions[route.path] || 'Quản trị hệ thống'
})
</script>

<style scoped>
.nav-link {
  @apply flex items-center space-x-3 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200;
}

.nav-link-active {
  @apply bg-primary-100 text-primary-700;
}

.nav-link-inactive {
  @apply text-gray-600 hover:bg-gray-100 hover:text-gray-900;
}
</style>