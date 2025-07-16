import axios from 'axios'

// Tạo instance axios với base URL từ environment variable
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8080/api',
  timeout: 10000, // 10 seconds timeout
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor - thêm token vào header nếu có
apiClient.interceptors.request.use(
  (config) => {
    // Lấy token từ localStorage hoặc store nếu có
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor - xử lý response và error
apiClient.interceptors.response.use(
  (response) => {
    return response.data
  },
  (error) => {
    // Xử lý các lỗi phổ biến
    if (error.response) {
      // Server trả về error status
      const { status, data } = error.response
      
      switch (status) {
        case 401:
          // Unauthorized - xóa token và redirect về login
          localStorage.removeItem('token')
          window.location.href = '/login'
          break
        case 403:
          console.error('Forbidden: Bạn không có quyền truy cập')
          break
        case 404:
          console.error('Not Found: API endpoint không tồn tại')
          break
        case 500:
          console.error('Server Error: Lỗi server nội bộ')
          break
        default:
          console.error(`Error ${status}:`, data?.message || 'Có lỗi xảy ra')
      }
      
      return Promise.reject(error.response.data)
    } else if (error.request) {
      // Network error
      console.error('Network Error: Không thể kết nối đến server')
      return Promise.reject({ message: 'Không thể kết nối đến server' })
    } else {
      // Other error
      console.error('Error:', error.message)
      return Promise.reject({ message: error.message })
    }
  }
)

export default apiClient