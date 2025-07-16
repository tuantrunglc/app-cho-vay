# API Service Documentation

## Tổng quan

Service này được xây dựng để thực hiện các API calls sử dụng axios và lấy base URL từ environment variable `VITE_API_URL` trong file `.env`.

## Cấu trúc

```
src/services/
├── api.js            # Axios client configuration
├── apiService.js     # Main API service class
├── apiConstants.js   # API endpoints, error codes, constants
├── apiHelpers.js     # Utility functions
├── index.js          # Export file
└── README.md         # Documentation
```

## Cấu hình

### Environment Variable

Trong file `.env`:
```
VITE_API_URL=http://localhost:8000/api
```

### Features

- ✅ Tự động thêm base URL từ environment variable
- ✅ Request/Response interceptors
- ✅ Tự động thêm Authorization header
- ✅ Automatic token refresh khi hết hạn
- ✅ Error handling với error codes
- ✅ Timeout configuration
- ✅ File upload support
- ✅ API constants và helper functions
- ✅ Validation utilities

## Cách sử dụng

### 1. Import service

```javascript
import apiService from '@/services/apiService.js'
// hoặc
import { apiService } from '@/services'
```

### 2. Sử dụng trong Vue component

```javascript
<script>
import { ref } from 'vue'
import apiService from '@/services/apiService.js'

export default {
  setup() {
    const data = ref(null)
    const loading = ref(false)
    const error = ref(null)

    const fetchData = async () => {
      loading.value = true
      try {
        const response = await apiService.get('/endpoint')
        data.value = response
      } catch (err) {
        error.value = err.message
      } finally {
        loading.value = false
      }
    }

    return { data, loading, error, fetchData }
  }
}
</script>
```

### 3. Sử dụng trong Pinia store

```javascript
import { defineStore } from 'pinia'
import apiService from '@/services/apiService.js'

export const useDataStore = defineStore('data', {
  state: () => ({
    items: [],
    loading: false,
    error: null
  }),

  actions: {
    async fetchItems() {
      this.loading = true
      this.error = null
      
      try {
        const response = await apiService.get('/items')
        this.items = response.data || response
      } catch (error) {
        this.error = error.message
        console.error('Fetch items error:', error)
      } finally {
        this.loading = false
      }
    }
  }
})
```

## API Methods

### Authentication
- `login(credentials)` - Đăng nhập (customer/admin)
- `register(userData)` - Đăng ký customer
- `logout()` - Đăng xuất
- `refreshToken()` - Làm mới token
- `getCurrentUser()` - Lấy thông tin user hiện tại

### User Management
- `getUserProfile()` - Lấy thông tin profile
- `updateUserProfile(userData)` - Cập nhật profile
- `changePassword(passwordData)` - Đổi mật khẩu
- `uploadAvatar(file, onProgress)` - Upload avatar

### Loan Management
- `getLoanConfig()` - Lấy cấu hình khoản vay
- `calculateLoan(loanData)` - Tính toán khoản vay
- `lookupLoanByPhone(phone)` - Tra cứu khoản vay
- `applyLoan(loanData)` - Nộp đơn vay
- `getMyLoanApplications(params)` - Lấy danh sách đơn vay
- `getLoanApplicationById(id)` - Lấy chi tiết đơn vay
- `getLoans(params)` - Alias cho getMyLoanApplications
- `getLoanById(id)` - Alias cho getLoanApplicationById
- `createLoan(loanData)` - Alias cho applyLoan

### Generic Methods
- `get(endpoint, params)` - GET request
- `post(endpoint, data)` - POST request
- `put(endpoint, data)` - PUT request
- `patch(endpoint, data)` - PATCH request
- `delete(endpoint)` - DELETE request
- `uploadFile(endpoint, file, onProgress)` - Upload single file
- `uploadMultipleFiles(endpoint, files, onProgress)` - Upload multiple files
- `healthCheck()` - Kiểm tra trạng thái hệ thống

## Error Handling

Service tự động xử lý các lỗi phổ biến:

- **401 Unauthorized**: Tự động xóa token và redirect về login
- **403 Forbidden**: Log error message
- **404 Not Found**: Log error message
- **500 Server Error**: Log error message
- **Network Error**: Hiển thị thông báo không thể kết nối

## Authentication

Service tự động:
- Thêm token từ localStorage vào Authorization header
- Xóa token khi gặp lỗi 401
- Redirect về trang login khi unauthorized

## Ví dụ sử dụng

### Đăng nhập
```javascript
try {
  const response = await apiService.login({
    email: 'user@example.com',
    password: 'password123'
  })
  
  // Lưu token
  localStorage.setItem('token', response.token)
  
  // Redirect hoặc update UI
} catch (error) {
  console.error('Login failed:', error.message)
}
```

### Lấy dữ liệu với params
```javascript
try {
  const response = await apiService.getLoans({
    page: 1,
    limit: 10,
    status: 'active'
  })
  
  console.log('Loans:', response.data)
} catch (error) {
  console.error('Fetch loans failed:', error.message)
}
```

### Upload file
```javascript
try {
  const response = await apiService.uploadFile(
    '/upload',
    file,
    (progressEvent) => {
      const progress = (progressEvent.loaded / progressEvent.total) * 100
      console.log(`Upload progress: ${progress}%`)
    }
  )
  
  console.log('File uploaded:', response)
} catch (error) {
  console.error('Upload failed:', error.message)
}
```

## Customization

Bạn có thể customize axios client trong file `api.js`:

```javascript
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  timeout: 10000, // Thay đổi timeout
  headers: {
    'Content-Type': 'application/json',
    // Thêm headers khác
  }
})
```

## Testing

Để test API service, bạn có thể sử dụng component example:

```javascript
import ApiUsageExample from '@/examples/ApiUsageExample.vue'
```

Hoặc test trực tiếp trong browser console:

```javascript
import apiService from './src/services/apiService.js'

// Test API call
apiService.get('/test').then(console.log).catch(console.error)
```