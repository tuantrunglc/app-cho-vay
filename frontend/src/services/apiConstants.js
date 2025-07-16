// API Constants
export const API_ENDPOINTS = {
  // Auth endpoints
  AUTH: {
    CUSTOMER_LOGIN: '/v1/auth/customer/login',
    ADMIN_LOGIN: '/v1/auth/admin/login',
    CUSTOMER_REGISTER: '/v1/auth/customer/register',
    REFRESH: '/v1/auth/refresh',
    LOGOUT: '/v1/auth/logout',
    ME: '/v1/auth/me'
  },

  // User endpoints
  USER: {
    PROFILE: '/v1/user/profile',
    CHANGE_PASSWORD: '/v1/user/change-password',
    UPLOAD_AVATAR: '/v1/user/upload-avatar'
  },

  // Loan endpoints
  LOANS: {
    CONFIG: '/v1/loans/config',
    CALCULATE: '/v1/loans/calculate',
    LOOKUP: '/v1/loans/lookup',
    APPLY: '/v1/loans/apply',
    MY_APPLICATIONS: '/v1/loans/my-applications',
    APPLICATIONS: '/v1/loans/applications'
  },

  // Health check
  HEALTH: '/health'
}

// API Error Codes
export const API_ERROR_CODES = {
  AUTH_001: 'AUTH_001', // Lỗi xác thực
  AUTH_002: 'AUTH_002', // Token hết hạn
  LOAN_001: 'LOAN_001', // Lỗi khoản vay
  LOAN_004: 'LOAN_004', // Không tìm thấy đơn vay
  SYS_001: 'SYS_001',   // Lỗi hệ thống
  SYS_003: 'SYS_003'    // Lỗi upload file
}

// HTTP Status Codes
export const HTTP_STATUS = {
  OK: 200,
  CREATED: 201,
  BAD_REQUEST: 400,
  UNAUTHORIZED: 401,
  FORBIDDEN: 403,
  NOT_FOUND: 404,
  VALIDATION_ERROR: 422,
  INTERNAL_SERVER_ERROR: 500
}

// User Roles
export const USER_ROLES = {
  CUSTOMER: 'customer',
  ADMIN: 'admin'
}

// User Status
export const USER_STATUS = {
  ACTIVE: 'active',
  INACTIVE: 'inactive',
  SUSPENDED: 'suspended'
}

// Verification Status
export const VERIFICATION_STATUS = {
  PENDING: 'pending',
  VERIFIED: 'verified',
  REJECTED: 'rejected'
}

// Loan Status
export const LOAN_STATUS = {
  PENDING: 'pending',
  APPROVED: 'approved',
  REJECTED: 'rejected',
  ACTIVE: 'active',
  COMPLETED: 'completed',
  OVERDUE: 'overdue',
  CANCELLED: 'cancelled'
}

// Loan Purposes
export const LOAN_PURPOSES = [
  'Tiêu dùng cá nhân',
  'Mua xe máy',
  'Mua ô tô',
  'Sửa chữa nhà',
  'Kinh doanh',
  'Khác'
]

// Emergency Contact Relationships
export const EMERGENCY_RELATIONSHIPS = {
  SPOUSE: 'spouse',
  PARENT: 'parent',
  SIBLING: 'sibling',
  FRIEND: 'friend',
  OTHER: 'other'
}

// File Upload Limits
export const FILE_LIMITS = {
  AVATAR: {
    MAX_SIZE_MB: 2,
    ALLOWED_TYPES: ['image/jpeg', 'image/jpg', 'image/png']
  },
  DOCUMENT: {
    MAX_SIZE_MB: 5,
    MAX_FILES: 10,
    ALLOWED_TYPES: ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf']
  }
}

// Pagination Defaults
export const PAGINATION = {
  DEFAULT_PAGE: 1,
  DEFAULT_LIMIT: 10,
  MAX_LIMIT: 100
}

// Validation Rules
export const VALIDATION_RULES = {
  NAME: {
    MIN_LENGTH: 2,
    MAX_LENGTH: 100
  },
  PASSWORD: {
    MIN_LENGTH: 6,
    MAX_LENGTH: 255
  },
  ID_CARD: {
    MIN_LENGTH: 9,
    MAX_LENGTH: 12
  },
  ADDRESS: {
    MIN_LENGTH: 10,
    MAX_LENGTH: 500
  },
  OCCUPATION: {
    MIN_LENGTH: 2,
    MAX_LENGTH: 100
  },
  MONTHLY_INCOME: {
    MIN: 1000000,      // 1 triệu VND
    MAX: 1000000000    // 1 tỷ VND
  },
  AGE: {
    MIN: 18,
    MAX: 80
  }
}

// Loan Configuration Defaults
export const LOAN_CONFIG = {
  MIN_AMOUNT: 1000000,      // 1 triệu VND
  MAX_AMOUNT: 500000000,    // 500 triệu VND
  MIN_TERM: 1,              // 1 tháng
  MAX_TERM: 60,             // 60 tháng
  DEFAULT_INTEREST_RATE: 1.5, // 1.5% per month
  DEFAULT_PROCESSING_FEE: 2.0, // 2%
  DEFAULT_LATE_FEE: 5.0,    // 5%
  AVAILABLE_TERMS: [1, 3, 6, 12, 18, 24, 36, 48, 60]
}

// Request Timeouts
export const TIMEOUTS = {
  DEFAULT: 10000,     // 10 seconds
  UPLOAD: 30000,      // 30 seconds for file uploads
  LONG_RUNNING: 60000 // 60 seconds for long operations
}

// Rate Limiting
export const RATE_LIMITS = {
  AUTH_REQUESTS_PER_MINUTE: 5,
  GENERAL_REQUESTS_PER_MINUTE: 60
}

// Token Expiration
export const TOKEN_EXPIRATION = {
  ACCESS_TOKEN_HOURS: 1,
  REFRESH_TOKEN_DAYS: 30
}

export default {
  API_ENDPOINTS,
  API_ERROR_CODES,
  HTTP_STATUS,
  USER_ROLES,
  USER_STATUS,
  VERIFICATION_STATUS,
  LOAN_STATUS,
  LOAN_PURPOSES,
  EMERGENCY_RELATIONSHIPS,
  FILE_LIMITS,
  PAGINATION,
  VALIDATION_RULES,
  LOAN_CONFIG,
  TIMEOUTS,
  RATE_LIMITS,
  TOKEN_EXPIRATION
}