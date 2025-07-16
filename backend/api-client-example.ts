// API Client Example for Loan Management System
// This file provides examples of how to connect to the backend API

import axios, { AxiosInstance, AxiosResponse } from 'axios';
import {
  ApiConfig,
  CustomerLoginRequest,
  AdminLoginRequest,
  CustomerRegisterRequest,
  RefreshTokenRequest,
  UpdateProfileRequest,
  ChangePasswordRequest,
  LoanCalculateRequest,
  LoanApplicationRequest,
  LoanApplicationsQuery,
  AuthResponse,
  RegisterResponse,
  RefreshResponse,
  UserProfileResponse,
  LoanConfigResponse,
  LoanCalculationResponse,
  LoanLookupResponse,
  LoanApplicationResponse,
  LoanApplicationsResponse,
  LoanApplicationDetailResponse,
  HealthResponse,
  UploadAvatarResponse,
  SuccessResponse,
  API_ENDPOINTS,
  HttpStatus,
} from './api-types';

class LoanApiClient {
  private client: AxiosInstance;
  private token: string | null = null;

  constructor(config: ApiConfig) {
    this.client = axios.create({
      baseURL: config.baseURL,
      timeout: config.timeout || 10000,
      headers: {
        'Content-Type': 'application/json',
        ...config.headers,
      },
    });

    // Request interceptor to add auth token
    this.client.interceptors.request.use((config) => {
      if (this.token) {
        config.headers.Authorization = `Bearer ${this.token}`;
      }
      return config;
    });

    // Response interceptor for error handling
    this.client.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === HttpStatus.UNAUTHORIZED) {
          this.token = null;
          // Redirect to login or refresh token
        }
        return Promise.reject(error);
      }
    );
  }

  // Set authentication token
  setToken(token: string): void {
    this.token = token;
  }

  // Clear authentication token
  clearToken(): void {
    this.token = null;
  }

  // Health Check
  async healthCheck(): Promise<HealthResponse> {
    const response = await this.client.get<HealthResponse>(API_ENDPOINTS.HEALTH);
    return response.data;
  }

  // Authentication Methods
  async customerLogin(data: CustomerLoginRequest): Promise<AuthResponse> {
    const response = await this.client.post<AuthResponse>(
      API_ENDPOINTS.CUSTOMER_LOGIN,
      data
    );
    
    if (response.data.success) {
      this.setToken(response.data.data.token);
    }
    
    return response.data;
  }

  async adminLogin(data: AdminLoginRequest): Promise<AuthResponse> {
    const response = await this.client.post<AuthResponse>(
      API_ENDPOINTS.ADMIN_LOGIN,
      data
    );
    
    if (response.data.success) {
      this.setToken(response.data.data.token);
    }
    
    return response.data;
  }

  async customerRegister(data: CustomerRegisterRequest): Promise<RegisterResponse> {
    const response = await this.client.post<RegisterResponse>(
      API_ENDPOINTS.CUSTOMER_REGISTER,
      data
    );
    
    if (response.data.success) {
      this.setToken(response.data.data.token);
    }
    
    return response.data;
  }

  async refreshToken(data: RefreshTokenRequest): Promise<RefreshResponse> {
    const response = await this.client.post<RefreshResponse>(
      API_ENDPOINTS.REFRESH_TOKEN,
      data
    );
    
    if (response.data.success) {
      this.setToken(response.data.data.token);
    }
    
    return response.data;
  }

  async logout(): Promise<SuccessResponse> {
    const response = await this.client.post<SuccessResponse>(API_ENDPOINTS.LOGOUT);
    this.clearToken();
    return response.data;
  }

  async getCurrentUser(): Promise<UserProfileResponse> {
    const response = await this.client.get<UserProfileResponse>(API_ENDPOINTS.ME);
    return response.data;
  }

  // User Management Methods
  async getUserProfile(): Promise<UserProfileResponse> {
    const response = await this.client.get<UserProfileResponse>(API_ENDPOINTS.USER_PROFILE);
    return response.data;
  }

  async updateProfile(data: UpdateProfileRequest): Promise<UserProfileResponse> {
    const response = await this.client.put<UserProfileResponse>(
      API_ENDPOINTS.USER_PROFILE,
      data
    );
    return response.data;
  }

  async changePassword(data: ChangePasswordRequest): Promise<SuccessResponse> {
    const response = await this.client.post<SuccessResponse>(
      API_ENDPOINTS.CHANGE_PASSWORD,
      data
    );
    return response.data;
  }

  async uploadAvatar(file: File): Promise<UploadAvatarResponse> {
    const formData = new FormData();
    formData.append('avatar', file);

    const response = await this.client.post<UploadAvatarResponse>(
      API_ENDPOINTS.UPLOAD_AVATAR,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      }
    );
    return response.data;
  }

  // Loan Methods
  async getLoanConfig(): Promise<LoanConfigResponse> {
    const response = await this.client.get<LoanConfigResponse>(API_ENDPOINTS.LOAN_CONFIG);
    return response.data;
  }

  async calculateLoan(data: LoanCalculateRequest): Promise<LoanCalculationResponse> {
    const response = await this.client.post<LoanCalculationResponse>(
      API_ENDPOINTS.LOAN_CALCULATE,
      data
    );
    return response.data;
  }

  async lookupLoan(phone: string): Promise<LoanLookupResponse> {
    const response = await this.client.get<LoanLookupResponse>(
      API_ENDPOINTS.LOAN_LOOKUP(phone)
    );
    return response.data;
  }

  async submitLoanApplication(data: LoanApplicationRequest): Promise<LoanApplicationResponse> {
    const response = await this.client.post<LoanApplicationResponse>(
      API_ENDPOINTS.LOAN_APPLY,
      data
    );
    return response.data;
  }

  async getMyApplications(query?: LoanApplicationsQuery): Promise<LoanApplicationsResponse> {
    const response = await this.client.get<LoanApplicationsResponse>(
      API_ENDPOINTS.MY_APPLICATIONS,
      { params: query }
    );
    return response.data;
  }

  async getApplicationDetail(id: string): Promise<LoanApplicationDetailResponse> {
    const response = await this.client.get<LoanApplicationDetailResponse>(
      API_ENDPOINTS.APPLICATION_DETAIL(id)
    );
    return response.data;
  }
}

// Usage Examples
export class ApiUsageExamples {
  private api: LoanApiClient;

  constructor() {
    this.api = new LoanApiClient({
      baseURL: 'http://localhost:8000/api',
      timeout: 10000,
    });
  }

  // Example: Customer Login Flow
  async loginExample() {
    try {
      const loginResponse = await this.api.customerLogin({
        phone: '0987654321',
        password: 'password123',
      });

      if (loginResponse.success) {
        console.log('Login successful:', loginResponse.data.user);
        console.log('Token:', loginResponse.data.token);
        
        // Token is automatically set in the client
        // Now you can make authenticated requests
        const profile = await this.api.getUserProfile();
        console.log('User profile:', profile.data);
      }
    } catch (error) {
      console.error('Login failed:', error);
    }
  }

  // Example: Customer Registration Flow
  async registerExample() {
    try {
      const registerResponse = await this.api.customerRegister({
        name: 'Nguyễn Văn A',
        phone: '0987654321',
        email: 'user@example.com',
        password: 'password123',
        confirmPassword: 'password123',
        idCard: '123456789',
        address: '123 Đường ABC, Quận 1, TP.HCM',
        dateOfBirth: '1990-01-01',
        occupation: 'Nhân viên văn phòng',
        monthlyIncome: 15000000,
      });

      if (registerResponse.success) {
        console.log('Registration successful:', registerResponse.data.user);
      }
    } catch (error) {
      console.error('Registration failed:', error);
    }
  }

  // Example: Loan Application Flow
  async loanApplicationExample() {
    try {
      // First, get loan configuration
      const config = await this.api.getLoanConfig();
      console.log('Loan config:', config.data);

      // Calculate loan details
      const calculation = await this.api.calculateLoan({
        amount: 50000000,
        term: 12,
      });
      console.log('Loan calculation:', calculation.data);

      // Submit loan application
      const application = await this.api.submitLoanApplication({
        amount: 50000000,
        term: 12,
        purpose: 'Mua xe máy',
        emergencyContact: {
          name: 'Nguyễn Thị B',
          phone: '0123456789',
          relationship: 'spouse',
        },
      });

      if (application.success) {
        console.log('Application submitted:', application.data);
      }
    } catch (error) {
      console.error('Loan application failed:', error);
    }
  }

  // Example: Get User Applications
  async getUserApplicationsExample() {
    try {
      const applications = await this.api.getMyApplications({
        status: 'active',
        page: 1,
        limit: 10,
      });

      if (applications.success) {
        console.log('Applications:', applications.data.applications);
        console.log('Pagination:', applications.data.pagination);
      }
    } catch (error) {
      console.error('Failed to get applications:', error);
    }
  }

  // Example: Error Handling
  async errorHandlingExample() {
    try {
      await this.api.customerLogin({
        phone: 'invalid-phone',
        password: 'short',
      });
    } catch (error: any) {
      if (error.response?.status === HttpStatus.VALIDATION_ERROR) {
        console.log('Validation errors:', error.response.data.errors);
      } else if (error.response?.status === HttpStatus.UNAUTHORIZED) {
        console.log('Authentication failed:', error.response.data.message);
      } else {
        console.log('System error:', error.response?.data?.message || error.message);
      }
    }
  }

  // Example: Token Refresh
  async tokenRefreshExample() {
    try {
      const refreshResponse = await this.api.refreshToken({
        refresh_token: 'your-refresh-token-here',
      });

      if (refreshResponse.success) {
        console.log('Token refreshed:', refreshResponse.data.token);
        // New token is automatically set in the client
      }
    } catch (error) {
      console.error('Token refresh failed:', error);
      // Redirect to login
    }
  }

  // Example: File Upload
  async fileUploadExample(file: File) {
    try {
      const uploadResponse = await this.api.uploadAvatar(file);

      if (uploadResponse.success) {
        console.log('Avatar uploaded:', uploadResponse.data.avatarUrl);
      }
    } catch (error) {
      console.error('File upload failed:', error);
    }
  }
}

// React Hook Example (for React applications)
export function useApiClient() {
  const [client] = useState(() => new LoanApiClient({
    baseURL: process.env.REACT_APP_API_URL || 'http://localhost:8000/api',
  }));

  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [user, setUser] = useState(null);

  useEffect(() => {
    // Check if user is logged in on app start
    const token = localStorage.getItem('auth_token');
    if (token) {
      client.setToken(token);
      client.getCurrentUser()
        .then(response => {
          if (response.success) {
            setUser(response.data);
            setIsAuthenticated(true);
          }
        })
        .catch(() => {
          localStorage.removeItem('auth_token');
        });
    }
  }, [client]);

  const login = async (credentials: CustomerLoginRequest) => {
    const response = await client.customerLogin(credentials);
    if (response.success) {
      localStorage.setItem('auth_token', response.data.token);
      localStorage.setItem('refresh_token', response.data.refreshToken);
      setUser(response.data.user);
      setIsAuthenticated(true);
    }
    return response;
  };

  const logout = async () => {
    await client.logout();
    localStorage.removeItem('auth_token');
    localStorage.removeItem('refresh_token');
    setUser(null);
    setIsAuthenticated(false);
  };

  return {
    client,
    isAuthenticated,
    user,
    login,
    logout,
  };
}

// Vue Composable Example (for Vue applications)
export function useApi() {
  const client = new LoanApiClient({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  });

  const isAuthenticated = ref(false);
  const user = ref(null);

  onMounted(() => {
    // Check authentication on mount
    const token = localStorage.getItem('auth_token');
    if (token) {
      client.setToken(token);
      client.getCurrentUser()
        .then(response => {
          if (response.success) {
            user.value = response.data;
            isAuthenticated.value = true;
          }
        })
        .catch(() => {
          localStorage.removeItem('auth_token');
        });
    }
  });

  const login = async (credentials: CustomerLoginRequest) => {
    const response = await client.customerLogin(credentials);
    if (response.success) {
      localStorage.setItem('auth_token', response.data.token);
      localStorage.setItem('refresh_token', response.data.refreshToken);
      user.value = response.data.user;
      isAuthenticated.value = true;
    }
    return response;
  };

  const logout = async () => {
    await client.logout();
    localStorage.removeItem('auth_token');
    localStorage.removeItem('refresh_token');
    user.value = null;
    isAuthenticated.value = false;
  };

  return {
    client,
    isAuthenticated: readonly(isAuthenticated),
    user: readonly(user),
    login,
    logout,
  };
}

export default LoanApiClient;