// API Types for Loan Management System
// Generated from backend API specification

// Base Response Types
export interface BaseResponse {
  success: boolean;
  meta: {
    timestamp: string;
    version: string;
  };
}

export interface SuccessResponse extends BaseResponse {
  message: string;
}

export interface ErrorResponse extends BaseResponse {
  success: false;
  message: string;
  code: string;
}

export interface ValidationErrorResponse extends ErrorResponse {
  errors: Record<string, string[]>;
}

// User Types
export interface User {
  id: number;
  name: string;
  phone: string;
  email?: string;
  role: 'customer' | 'admin';
  status: 'active' | 'inactive' | 'suspended';
  verification_status: 'pending' | 'verified' | 'rejected';
  credit_score?: number;
  avatar_url?: string;
}

export interface UserProfile extends User {
  id_card: string;
  address: string;
  date_of_birth: string;
  occupation: string;
  monthly_income: string;
  wallet_balance: number;
  current_debt: number;
  total_loans: number;
}

// Authentication Types
export interface CustomerLoginRequest {
  phone: string;
  password: string;
}

export interface AdminLoginRequest {
  username: string;
  password: string;
}

export interface CustomerRegisterRequest {
  name: string;
  phone: string;
  email?: string;
  password: string;
  confirmPassword: string;
  idCard: string;
  address: string;
  dateOfBirth: string;
  occupation: string;
  monthlyIncome: number;
}

export interface RefreshTokenRequest {
  refresh_token: string;
}

export interface AuthResponse extends BaseResponse {
  data: {
    user: User;
    token: string;
    refreshToken: string;
    expiresIn: number;
  };
}

export interface RegisterResponse extends BaseResponse {
  data: {
    user: User;
    token: string;
    message: string;
  };
}

export interface RefreshResponse extends BaseResponse {
  data: {
    token: string;
    refreshToken: string;
    expiresIn: number;
  };
}

// User Management Types
export interface UpdateProfileRequest {
  name?: string;
  email?: string;
  address?: string;
  occupation?: string;
  monthlyIncome?: number;
}

export interface ChangePasswordRequest {
  currentPassword: string;
  newPassword: string;
  confirmPassword: string;
}

export interface UploadAvatarResponse extends BaseResponse {
  data: {
    avatarUrl: string;
  };
  message: string;
}

// Loan Types
export interface LoanConfig {
  min_amount: number;
  max_amount: number;
  min_term: number;
  max_term: number;
  interest_rate: number;
  processing_fee: number;
  late_fee: number;
  available_terms: number[];
  loan_purposes: string[];
}

export interface LoanCalculateRequest {
  amount: number;
  term: number;
}

export interface PaymentScheduleItem {
  month: number;
  payment_date: string;
  principal: number;
  interest: number;
  total_payment: number;
  remaining_balance: number;
}

export interface LoanCalculation {
  loan_amount: number;
  term_months: number;
  interest_rate: number;
  monthly_payment: number;
  total_payment: number;
  total_interest: number;
  processing_fee: number;
  payment_schedule: PaymentScheduleItem[];
}

export interface ActiveLoan {
  loan_id: string;
  amount: number;
  remaining_amount: number;
  status: string;
  next_payment_date: string;
  next_payment_amount: number;
}

export interface LoanHistory {
  loan_id: string;
  amount: number;
  status: string;
  created_at: string;
  completed_at?: string;
}

export interface LoanLookup {
  customer_name: string;
  phone: string;
  active_loans: ActiveLoan[];
  loan_history: LoanHistory[];
}

export interface EmergencyContact {
  name: string;
  phone: string;
  relationship: 'spouse' | 'parent' | 'sibling' | 'friend' | 'other';
}

export interface LoanApplicationRequest {
  amount: number;
  term: number;
  purpose: string;
  emergencyContact: EmergencyContact;
}

export interface LoanApplication {
  id: number;
  loan_id: string;
  amount: number;
  term: number;
  purpose: string;
  status: 'pending' | 'approved' | 'rejected' | 'active' | 'completed';
  monthly_payment: number;
  total_payment: number;
  remaining_amount?: number;
  next_payment_date?: string;
  created_at: string;
  approved_at?: string;
  emergency_contact: EmergencyContact;
}

export interface Document {
  id: number;
  type: string;
  name: string;
  url: string;
  status: 'pending' | 'approved' | 'rejected';
}

export interface PaymentHistory {
  id: number;
  payment_date: string;
  amount: number;
  principal: number;
  interest: number;
  status: 'pending' | 'completed' | 'failed';
}

export interface LoanApplicationDetail extends LoanApplication {
  documents: Document[];
  payment_history: PaymentHistory[];
}

export interface Pagination {
  currentPage: number;
  totalPages: number;
  totalItems: number;
  itemsPerPage: number;
}

export interface PaginatedResponse<T> extends BaseResponse {
  data: {
    items: T[];
    pagination: Pagination;
  };
}

// API Response Types
export interface HealthResponse {
  status: string;
  timestamp: string;
  version: string;
}

export interface UserProfileResponse extends BaseResponse {
  data: UserProfile;
}

export interface LoanConfigResponse extends BaseResponse {
  data: LoanConfig;
}

export interface LoanCalculationResponse extends BaseResponse {
  data: LoanCalculation;
}

export interface LoanLookupResponse extends BaseResponse {
  data: LoanLookup;
}

export interface LoanApplicationResponse extends BaseResponse {
  data: LoanApplication;
  message: string;
}

export interface LoanApplicationsResponse extends BaseResponse {
  data: {
    applications: LoanApplication[];
    pagination: Pagination;
  };
}

export interface LoanApplicationDetailResponse extends BaseResponse {
  data: LoanApplicationDetail;
}

// Query Parameters
export interface LoanApplicationsQuery {
  status?: 'pending' | 'approved' | 'rejected' | 'active' | 'completed';
  page?: number;
  limit?: number;
}

// API Client Configuration
export interface ApiConfig {
  baseURL: string;
  timeout?: number;
  headers?: Record<string, string>;
}

// Error Codes
export enum ErrorCodes {
  AUTH_001 = 'AUTH_001', // Authentication error
  AUTH_002 = 'AUTH_002', // Token expired
  LOAN_001 = 'LOAN_001', // Loan error
  LOAN_004 = 'LOAN_004', // Loan not found
  SYS_001 = 'SYS_001',   // System error
  SYS_003 = 'SYS_003',   // File upload error
}

// HTTP Status Codes
export enum HttpStatus {
  OK = 200,
  CREATED = 201,
  BAD_REQUEST = 400,
  UNAUTHORIZED = 401,
  FORBIDDEN = 403,
  NOT_FOUND = 404,
  VALIDATION_ERROR = 422,
  INTERNAL_SERVER_ERROR = 500,
}

// API Endpoints
export const API_ENDPOINTS = {
  // Health
  HEALTH: '/health',
  
  // Authentication
  CUSTOMER_LOGIN: '/v1/auth/customer/login',
  ADMIN_LOGIN: '/v1/auth/admin/login',
  CUSTOMER_REGISTER: '/v1/auth/customer/register',
  REFRESH_TOKEN: '/v1/auth/refresh',
  LOGOUT: '/v1/auth/logout',
  ME: '/v1/auth/me',
  
  // User Management
  USER_PROFILE: '/v1/user/profile',
  CHANGE_PASSWORD: '/v1/user/change-password',
  UPLOAD_AVATAR: '/v1/user/upload-avatar',
  
  // Loans
  LOAN_CONFIG: '/v1/loans/config',
  LOAN_CALCULATE: '/v1/loans/calculate',
  LOAN_LOOKUP: (phone: string) => `/v1/loans/lookup/${phone}`,
  LOAN_APPLY: '/v1/loans/apply',
  MY_APPLICATIONS: '/v1/loans/my-applications',
  APPLICATION_DETAIL: (id: string) => `/v1/loans/applications/${id}`,
} as const;

// Validation Patterns
export const VALIDATION_PATTERNS = {
  PHONE: /^(0[3|5|7|8|9])+([0-9]{8})$/,
  ID_CARD: /^[0-9]{9,12}$/,
  NAME: /^[\p{L}\s]+$/u,
} as const;

// Validation Rules
export const VALIDATION_RULES = {
  NAME: {
    MIN_LENGTH: 2,
    MAX_LENGTH: 100,
  },
  PASSWORD: {
    MIN_LENGTH: 6,
    MAX_LENGTH: 255,
  },
  ADDRESS: {
    MIN_LENGTH: 10,
    MAX_LENGTH: 500,
  },
  OCCUPATION: {
    MIN_LENGTH: 2,
    MAX_LENGTH: 100,
  },
  MONTHLY_INCOME: {
    MIN: 1000000,
    MAX: 1000000000,
  },
  LOAN_AMOUNT: {
    MIN: 1000000,
    MAX: 500000000,
  },
  AGE: {
    MIN: 18,
    MAX: 80,
  },
} as const;

// Default Values
export const DEFAULT_VALUES = {
  PAGINATION: {
    PAGE: 1,
    LIMIT: 10,
    MAX_LIMIT: 100,
  },
  TOKEN_EXPIRY: 3600, // 1 hour in seconds
  REFRESH_TOKEN_EXPIRY: 2592000, // 30 days in seconds
} as const;