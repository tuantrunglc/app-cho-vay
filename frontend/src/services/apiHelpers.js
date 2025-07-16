// API Helper functions
export const apiHelpers = {
  // Kiểm tra xem user có đang đăng nhập không
  isAuthenticated() {
    const token = localStorage.getItem('token')
    const user = localStorage.getItem('user')
    return !!(token && user)
  },

  // Lấy thông tin user từ localStorage
  getCurrentUser() {
    try {
      const userStr = localStorage.getItem('user')
      return userStr ? JSON.parse(userStr) : null
    } catch (error) {
      console.error('Error parsing user data:', error)
      return null
    }
  },

  // Lấy token từ localStorage
  getToken() {
    return localStorage.getItem('token')
  },

  // Lấy refresh token từ localStorage
  getRefreshToken() {
    return localStorage.getItem('refreshToken')
  },

  // Xóa tất cả dữ liệu authentication
  clearAuthData() {
    localStorage.removeItem('token')
    localStorage.removeItem('refreshToken')
    localStorage.removeItem('user')
  },

  // Format số tiền VND
  formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
      style: 'currency',
      currency: 'VND'
    }).format(amount)
  },

  // Format ngày tháng
  formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('vi-VN')
  },

  // Format datetime
  formatDateTime(dateString) {
    return new Date(dateString).toLocaleString('vi-VN')
  },

  // Xử lý lỗi API response
  handleApiError(error) {
    if (error.errors) {
      // Validation errors
      const errorMessages = Object.values(error.errors).flat()
      return errorMessages.join(', ')
    } else if (error.message) {
      return error.message
    } else {
      return 'Có lỗi xảy ra, vui lòng thử lại'
    }
  },

  // Kiểm tra response có thành công không
  isSuccessResponse(response) {
    return response && response.success === true
  },

  // Lấy data từ response
  getResponseData(response) {
    return this.isSuccessResponse(response) ? response.data : null
  },

  // Tạo query string từ object
  buildQueryString(params) {
    const searchParams = new URLSearchParams()
    
    Object.keys(params).forEach(key => {
      const value = params[key]
      if (value !== null && value !== undefined && value !== '') {
        searchParams.append(key, value)
      }
    })
    
    return searchParams.toString()
  },

  // Validate số điện thoại Việt Nam
  validatePhoneNumber(phone) {
    const phoneRegex = /^0[3|5|7|8|9][0-9]{8}$/
    return phoneRegex.test(phone)
  },

  // Validate email
  validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return emailRegex.test(email)
  },

  // Validate CMND/CCCD
  validateIdCard(idCard) {
    const idCardRegex = /^[0-9]{9,12}$/
    return idCardRegex.test(idCard)
  },

  // Tính tuổi từ ngày sinh
  calculateAge(dateOfBirth) {
    const today = new Date()
    const birthDate = new Date(dateOfBirth)
    let age = today.getFullYear() - birthDate.getFullYear()
    const monthDiff = today.getMonth() - birthDate.getMonth()
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      age--
    }
    
    return age
  },

  // Kiểm tra file có phải là ảnh không
  isImageFile(file) {
    const imageTypes = ['image/jpeg', 'image/jpg', 'image/png']
    return imageTypes.includes(file.type)
  },

  // Kiểm tra kích thước file
  isFileSizeValid(file, maxSizeMB) {
    const maxSizeBytes = maxSizeMB * 1024 * 1024
    return file.size <= maxSizeBytes
  },

  // Tạo preview URL cho file ảnh
  createImagePreview(file) {
    return new Promise((resolve, reject) => {
      if (!this.isImageFile(file)) {
        reject(new Error('File không phải là ảnh'))
        return
      }

      const reader = new FileReader()
      reader.onload = (e) => resolve(e.target.result)
      reader.onerror = (e) => reject(e)
      reader.readAsDataURL(file)
    })
  },

  // Debounce function
  debounce(func, wait) {
    let timeout
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout)
        func(...args)
      }
      clearTimeout(timeout)
      timeout = setTimeout(later, wait)
    }
  },

  // Throttle function
  throttle(func, limit) {
    let inThrottle
    return function() {
      const args = arguments
      const context = this
      if (!inThrottle) {
        func.apply(context, args)
        inThrottle = true
        setTimeout(() => inThrottle = false, limit)
      }
    }
  }
}

export default apiHelpers