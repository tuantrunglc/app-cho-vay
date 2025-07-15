<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <router-link to="/" class="flex items-center">
              <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center mr-3">
                <span class="text-white font-bold text-lg">V</span>
              </div>
              <span class="text-xl font-bold text-gray-900">VayNhanh</span>
            </router-link>
          </div>

          <!-- Navigation -->
          <nav class="hidden md:flex space-x-8">
            <router-link
              to="/"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path === '/' }"
            >
              Trang chủ
            </router-link>
            <router-link
              to="/register"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path === '/register' }"
            >
              Đăng ký vay
            </router-link>
            <router-link
              to="/lookup"
              class="nav-link"
              :class="{ 'nav-link-active': $route.path === '/lookup' }"
            >
              Tra cứu
            </router-link>
          </nav>

          <!-- User Menu -->
          <div class="flex items-center space-x-4">
            <div v-if="!authStore.isAuthenticated" class="flex items-center space-x-3">
              <button
                @click="showLoginModal = true"
                class="text-gray-700 hover:text-primary-600 font-medium"
              >
                Đăng nhập
              </button>
              <router-link
                to="/register"
                class="btn btn-primary"
              >
                Đăng ký vay
              </router-link>
            </div>

            <div v-else class="relative">
              <button
                @click="showUserMenu = !showUserMenu"
                class="flex items-center space-x-2 text-gray-700 hover:text-primary-600"
              >
                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                  <span class="text-primary-600 font-medium text-sm">
                    {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                  </span>
                </div>
                <span class="font-medium">{{ authStore.user?.name }}</span>
                <ChevronDownIcon class="w-4 h-4" />
              </button>

              <!-- User Dropdown -->
              <div
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
              >
                <router-link
                  to="/profile"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                  @click="showUserMenu = false"
                >
                  <UserIcon class="w-4 h-4 inline mr-2" />
                  Hồ sơ cá nhân
                </router-link>
                <router-link
                  to="/my-loans"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                  @click="showUserMenu = false"
                >
                  <DocumentTextIcon class="w-4 h-4 inline mr-2" />
                  Khoản vay của tôi
                </router-link>
                <hr class="my-1">
                <button
                  @click="logout"
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                >
                  <ArrowRightOnRectangleIcon class="w-4 h-4 inline mr-2" />
                  Đăng xuất
                </button>
              </div>
            </div>

            <!-- Mobile menu button -->
            <button
              @click="showMobileMenu = !showMobileMenu"
              class="md:hidden p-2 rounded-md text-gray-700 hover:text-primary-600 hover:bg-gray-100"
            >
              <Bars3Icon v-if="!showMobileMenu" class="w-6 h-6" />
              <XMarkIcon v-else class="w-6 h-6" />
            </button>
          </div>
        </div>

        <!-- Mobile Navigation -->
        <div v-if="showMobileMenu" class="md:hidden py-4 border-t border-gray-200">
          <div class="space-y-2">
            <router-link
              to="/"
              class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md"
              @click="showMobileMenu = false"
            >
              Trang chủ
            </router-link>
            <router-link
              to="/register"
              class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md"
              @click="showMobileMenu = false"
            >
              Đăng ký vay
            </router-link>
            <router-link
              to="/lookup"
              class="block px-3 py-2 text-gray-700 hover:text-primary-600 hover:bg-gray-50 rounded-md"
              @click="showMobileMenu = false"
            >
              Tra cứu
            </router-link>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <!-- Company Info -->
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center mb-4">
              <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center mr-3">
                <span class="text-white font-bold text-lg">V</span>
              </div>
              <span class="text-xl font-bold">VayNhanh</span>
            </div>
            <p class="text-gray-300 mb-4">
              Giải pháp vay tiền trực tuyến nhanh chóng, an toàn và tiện lợi. 
              Duyệt trong 24h, lãi suất cạnh tranh.
            </p>
            <div class="flex space-x-4">
              <a href="#" class="text-gray-300 hover:text-white">
                <span class="sr-only">Facebook</span>
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </a>
              <a href="#" class="text-gray-300 hover:text-white">
                <span class="sr-only">Zalo</span>
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.568 8.16c-.169-.224-.487-.4-.686-.4-.2 0-.518.176-.686.4l-1.396 1.86-1.396-1.86c-.169-.224-.487-.4-.686-.4-.2 0-.518.176-.686.4-.169.225-.169.576 0 .8L13.2 11.2l-2.168 2.88c-.169.225-.169.576 0 .8.169.225.487.4.686.4.2 0 .518-.176.686-.4L14 13.04l1.596 1.84c.169.224.487.4.686.4.2 0 .518-.176.686-.4.169-.224.169-.575 0-.8L15.8 11.2l2.168-2.88c.169-.224.169-.575 0-.8z"/>
                </svg>
              </a>
            </div>
          </div>

          <!-- Quick Links -->
          <div>
            <h3 class="text-lg font-semibold mb-4">Liên kết nhanh</h3>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-300 hover:text-white">Về chúng tôi</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white">Sản phẩm</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white">Hướng dẫn vay</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white">Câu hỏi thường gặp</a></li>
            </ul>
          </div>

          <!-- Contact Info -->
          <div>
            <h3 class="text-lg font-semibold mb-4">Liên hệ</h3>
            <ul class="space-y-2 text-gray-300">
              <li class="flex items-center">
                <PhoneIcon class="w-4 h-4 mr-2" />
                1900 1234
              </li>
              <li class="flex items-center">
                <EnvelopeIcon class="w-4 h-4 mr-2" />
                support@vaynhanh.com
              </li>
              <li class="flex items-center">
                <MapPinIcon class="w-4 h-4 mr-2" />
                123 Đường ABC, Quận 1, TP.HCM
              </li>
            </ul>
          </div>
        </div>

        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-300">
          <p>&copy; 2024 VayNhanh. Tất cả quyền được bảo lưu.</p>
        </div>
      </div>
    </footer>

    <!-- Login Modal -->
    <div v-if="showLoginModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
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
        
        <form @submit.prevent="handleLogin" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Số điện thoại
            </label>
            <input
              v-model="loginForm.phone"
              type="tel"
              class="input"
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
              class="input"
              placeholder="Nhập mật khẩu"
              required
            />
          </div>

          <div v-if="loginError" class="text-danger-600 text-sm">
            {{ loginError }}
          </div>

          <div class="flex space-x-3 pt-4">
            <button
              type="button"
              @click="showLoginModal = false"
              class="flex-1 btn btn-secondary"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="flex-1 btn btn-primary"
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
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Icons
import ChevronDownIcon from '@/components/icons/ChevronDownIcon.vue'
import UserIcon from '@/components/icons/UserIcon.vue'
import DocumentTextIcon from '@/components/icons/DocumentTextIcon.vue'
import ArrowRightOnRectangleIcon from '@/components/icons/ArrowRightOnRectangleIcon.vue'
import Bars3Icon from '@/components/icons/Bars3Icon.vue'
import XMarkIcon from '@/components/icons/XMarkIcon.vue'
import PhoneIcon from '@/components/icons/PhoneIcon.vue'
import EnvelopeIcon from '@/components/icons/EnvelopeIcon.vue'
import MapPinIcon from '@/components/icons/MapPinIcon.vue'

const router = useRouter()
const authStore = useAuthStore()

// Reactive data
const showUserMenu = ref(false)
const showMobileMenu = ref(false)
const showLoginModal = ref(false)
const loginError = ref('')

const loginForm = reactive({
  phone: '',
  password: ''
})

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

const logout = () => {
  authStore.logout()
  showUserMenu.value = false
  router.push('/')
}

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.nav-link {
  @apply text-gray-700 hover:text-primary-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200;
}

.nav-link-active {
  @apply text-primary-600 bg-primary-50;
}
</style>