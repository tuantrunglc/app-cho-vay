import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Layouts
import MobileLayout from '@/layouts/MobileLayout.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'

// User Views
import Home from '@/views/user/Home.vue'
import LoanApplication from '@/views/customer/LoanApplication.vue'
import LookupLoan from '@/views/customer/LookupLoan.vue'
import Profile from '@/views/user/Profile.vue'
import MyLoans from '@/views/user/MyLoans.vue'

// Admin Views
import AdminLogin from '@/views/admin/AdminLogin.vue'
import AdminDashboard from '@/views/admin/Dashboard.vue'
import AdminLoanApproval from '@/views/admin/LoanApproval.vue'
import AdminActiveLoans from '@/views/admin/ActiveLoan.vue'
import AdminCustomers from '@/views/admin/Customers.vue'
import AdminContent from '@/views/admin/ContentManagement.vue'

const routes = [
  // User Routes (Mobile Layout)
  {
    path: '/',
    component: MobileLayout,
    children: [
      {
        path: '',
        name: 'Home',
        component: Home
      },
      {
        path: 'register',
        name: 'LoanApplication',
        component: LoanApplication
      },
      {
        path: 'lookup',
        name: 'LookupLoan',
        component: LookupLoan
      },
      {
        path: 'profile',
        name: 'Profile',
        component: Profile,
        meta: { requiresAuth: true, role: 'customer' }
      },
      {
        path: 'my-loans',
        name: 'MyLoans',
        component: MyLoans,
        meta: { requiresAuth: true, role: 'customer' }
      },
      {
        path: 'support',
        name: 'Support',
        component: () => import('@/views/user/Support.vue')
      },
      {
        path: 'wallet',
        name: 'Wallet',
        component: () => import('@/views/user/Wallet.vue'),
        meta: { requiresAuth: true, role: 'customer' }
      },
      {
        path: 'personal-info',
        name: 'PersonalInfo',
        component: () => import('@/views/user/PersonalInfo.vue'),
        meta: { requiresAuth: true, role: 'customer' }
      },
      {
        path: 'change-password',
        name: 'ChangePassword',
        component: () => import('@/views/user/ChangePassword.vue'),
        meta: { requiresAuth: true, role: 'customer' }
      },
      {
        path: 'notifications',
        name: 'Notifications',
        component: () => import('@/views/user/Notifications.vue'),
        meta: { requiresAuth: true, role: 'customer' }
      },
      {
        path: 'settings',
        name: 'Settings',
        component: () => import('@/views/user/Settings.vue'),
        meta: { requiresAuth: true, role: 'customer' }
      }
    ]
  },
  
  // Admin Login Route (Standalone)
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin
  },
  
  // Admin Routes (Admin Layout)
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'admin' },
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: AdminDashboard
      },
      {
        path: 'loan-approval',
        name: 'AdminLoanApproval',
        component: AdminLoanApproval
      },
      {
        path: 'active-loans',
        name: 'AdminActiveLoans',
        component: AdminActiveLoans
      },
      {
        path: 'customers',
        name: 'AdminCustomers',
        component: AdminCustomers
      },
      {
        path: 'content',
        name: 'AdminContent',
        component: AdminContent
      }
    ]
  },
  
  // Catch all route - redirect to home
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // Check if route requires authentication
  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      // Redirect to appropriate login page
      if (to.path.startsWith('/admin')) {
        next('/admin/login')
      } else {
        next('/')
      }
      return
    }
    
    // Check role-based access
    if (to.meta.role && to.meta.role !== authStore.user?.role) {
      // Redirect based on user role
      if (authStore.isAdmin) {
        next('/admin')
      } else {
        next('/')
      }
      return
    }
  }
  
  // If authenticated admin tries to access admin login, redirect to admin dashboard
  if (to.path === '/admin/login' && authStore.isAuthenticated && authStore.isAdmin) {
    next('/admin')
    return
  }
  
  // If user is authenticated and trying to access home, redirect based on role
  if (to.path === '/' && authStore.isAuthenticated && authStore.isAdmin) {
    next('/admin')
    return
  }
  
  next()
})

export default router