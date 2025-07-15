<template>
  <div class="mobile-app min-h-screen bg-gray-50">
    <!-- Status Bar Spacer -->
    <div class="h-safe-top bg-primary-600"></div>
    
    <!-- Header -->
    <header class="bg-primary-600 text-white px-4 py-3 shadow-lg">
      <div class="flex items-center justify-between">
        <!-- Logo -->
        <div class="flex items-center">
          <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center mr-3">
            <span class="text-primary-600 font-bold text-lg">V</span>
          </div>
          <span class="text-lg font-bold">App Vay</span>
        </div>
        
        <!-- User Info / Login -->
        <div class="flex items-center space-x-3">
          <div v-if="authStore.isAuthenticated" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
              <span class="text-white font-medium text-sm">
                {{ authStore.user?.name?.charAt(0).toUpperCase() }}
              </span>
            </div>
            <span class="text-sm font-medium">{{ authStore.user?.name }}</span>
          </div>
          <button
            v-else
            @click="showLoginModal = true"
            class="text-sm font-medium bg-primary-500 px-3 py-1 rounded-full"
          >
            Đăng nhập
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 pb-20">
      <router-view />
    </main>
    
    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 px-4 py-2 safe-bottom">
      <div class="flex justify-around max-w-md mx-auto">
        <router-link
          v-for="item in navItems"
          :key="item.name"
          :to="item.path"
          class="flex flex-col items-center py-2 px-3 rounded-lg transition-colors duration-200 min-w-0"
          :class="isActive(item.path) ? 'text-primary-600 bg-primary-50' : 'text-gray-500'"
        >
          <component :is="item.icon" class="w-6 h-6 mb-1 flex-shrink-0" />
          <span class="text-xs font-medium truncate">{{ item.label }}</span>
        </router-link>
      </div>
    </nav>

    <!-- Login Modal -->
    <div v-if="showLoginModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-sm w-full">
        <div class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Đăng nhập</h3>
            <button
              @click="showLoginModal = false"
              class="text-gray-400 hover:text-gray-600"
            >
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <form @submit.prevent="handleLogin" class="p-4 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số điện thoại
            </label>
            <input
              v-model="loginForm.phone"
              type="tel"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              placeholder="Nhập số điện thoại"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Mật khẩu
            </label>
            <input
              v-model="loginForm.password"
              type="password"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
              placeholder="Nhập mật khẩu"
              required
            />
          </div>

          <div v-if="loginError" class="text-red-600 text-sm">
            {{ loginError }}
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              type="button"
              @click="showLoginModal = false"
              class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 py-2 px-4 bg-primary-600 text-white rounded-lg hover:bg-primary-700"
              :disabled="authStore.isLoading"
            >
              {{ authStore.isLoading ? 'Đang đăng nhập...' : 'Đăng nhập' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Icons
import HomeIcon from '@/components/icons/HomeIcon.vue'
import SupportIcon from '@/components/icons/SupportIcon.vue'
import WalletIcon from '@/components/icons/WalletIcon.vue'
import UserIcon from '@/components/icons/UserIcon.vue'
import XMarkIcon from '@/components/icons/XMarkIcon.vue'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// Reactive data
const showLoginModal = ref(false)
const loginError = ref('')

const loginForm = reactive({
  phone: '',
  password: ''
})

const navItems = [
  {
    name: 'home',
    path: '/',
    label: 'Trang chủ',
    icon: HomeIcon
  },
  {
    name: 'register',
    path: '/register',
    label: 'Vay tiền',
    icon: WalletIcon
  },
  {
    name: 'lookup',
    path: '/lookup',
    label: 'Tra cứu',
    icon: SupportIcon
  },
  {
    name: 'profile',
    path: authStore.isAuthenticated ? '/profile' : '/login',
    label: authStore.isAuthenticated ? 'Tài khoản' : 'Đăng nhập',
    icon: UserIcon
  }
]

const isActive = (path) => {
  if (path === '/login' && authStore.isAuthenticated) {
    return route.path === '/profile' || route.path === '/my-loans'
  }
  return route.path === path
}

// Methods
const handleLogin = async () => {
  loginError.value = ''
  
  const result = await authStore.login(loginForm)
  
  if (result.success) {
    showLoginModal.value = false
    loginForm.phone = ''
    loginForm.password = ''
    
    // Redirect based on user role
    if (authStore.isAdmin) {
      router.push('/admin')
    } else {
      router.push('/profile')
    }
  } else {
    loginError.value = result.error
  }
}
</script>

<style scoped>
.mobile-app {
  max-width: 428px;
  margin: 0 auto;
  position: relative;
}

.h-safe-top {
  height: env(safe-area-inset-top, 0px);
}

.safe-bottom {
  padding-bottom: env(safe-area-inset-bottom, 0px);
}

/* Custom scrollbar for mobile */
::-webkit-scrollbar {
  width: 0px;
  background: transparent;
}
</style>