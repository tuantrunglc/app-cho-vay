# AI Integration Guide - Hệ thống Cho vay

## Tổng quan

Tài liệu này cung cấp tất cả thông tin cần thiết để AI có thể tạo code kết nối frontend với backend API của hệ thống cho vay.

## Cấu trúc tài liệu

### 1. API_DOCUMENTATION.md
- **Mục đích**: Tài liệu API chi tiết bằng tiếng Việt
- **Nội dung**: 
  - Mô tả tất cả endpoints
  - Request/Response examples
  - Validation rules
  - Error handling
  - Authentication flow

### 2. openapi.yaml
- **Mục đích**: OpenAPI 3.0 specification
- **Nội dung**:
  - Machine-readable API specification
  - Schema definitions
  - Security requirements
  - Response models

### 3. api-types.ts
- **Mục đích**: TypeScript type definitions
- **Nội dung**:
  - Interface definitions
  - Type safety
  - Constants và enums
  - Validation patterns

### 4. api-client-example.ts
- **Mục đích**: Ví dụ implementation API client
- **Nội dung**:
  - Complete API client class
  - Usage examples
  - React hooks
  - Vue composables
  - Error handling patterns

### 5. postman-collection.json
- **Mục đích**: Postman collection để test API
- **Nội dung**:
  - All API endpoints
  - Sample requests
  - Environment variables
  - Test scripts

## Hướng dẫn cho AI

### Bước 1: Hiểu cấu trúc API
1. Đọc `API_DOCUMENTATION.md` để hiểu business logic
2. Sử dụng `openapi.yaml` để hiểu technical specification
3. Tham khảo `api-types.ts` cho type definitions

### Bước 2: Tạo API Client
1. Sử dụng `api-client-example.ts` làm template
2. Customize theo framework cụ thể (React, Vue, Angular, etc.)
3. Implement error handling và retry logic

### Bước 3: Authentication Flow
```typescript
// 1. Login
const loginResponse = await api.customerLogin({
  phone: "0987654321",
  password: "password123"
});

// 2. Store tokens
localStorage.setItem('auth_token', loginResponse.data.token);
localStorage.setItem('refresh_token', loginResponse.data.refreshToken);

// 3. Set token in client
api.setToken(loginResponse.data.token);

// 4. Make authenticated requests
const profile = await api.getUserProfile();
```

### Bước 4: Error Handling
```typescript
try {
  const response = await api.someMethod();
} catch (error) {
  if (error.response?.status === 422) {
    // Validation errors
    console.log(error.response.data.errors);
  } else if (error.response?.status === 401) {
    // Authentication error - redirect to login
    redirectToLogin();
  } else {
    // System error
    showErrorMessage(error.response?.data?.message);
  }
}
```

### Bước 5: State Management
```typescript
// React Context example
const AuthContext = createContext();

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  
  const login = async (credentials) => {
    const response = await api.customerLogin(credentials);
    if (response.success) {
      setUser(response.data.user);
      setIsAuthenticated(true);
      localStorage.setItem('auth_token', response.data.token);
    }
    return response;
  };
  
  return (
    <AuthContext.Provider value={{ user, isAuthenticated, login }}>
      {children}
    </AuthContext.Provider>
  );
}
```

## Framework-specific Guidelines

### React
- Sử dụng React hooks từ `api-client-example.ts`
- Implement Context API cho authentication
- Sử dụng React Query hoặc SWR cho data fetching
- Handle loading states và error boundaries

### Vue
- Sử dụng Vue composables từ `api-client-example.ts`
- Implement Pinia store cho state management
- Sử dụng Vue Query cho data fetching
- Handle loading states với reactive refs

### Angular
- Tạo Angular services
- Sử dụng HttpClient với interceptors
- Implement guards cho authentication
- Handle errors với global error handler

### Vanilla JavaScript
- Sử dụng fetch API hoặc axios
- Implement simple state management
- Handle DOM updates manually
- Use localStorage cho persistence

## Best Practices

### 1. Security
- Luôn validate input trước khi gửi API
- Store tokens securely
- Implement token refresh logic
- Handle CORS properly

### 2. Performance
- Implement request caching
- Use pagination cho large datasets
- Debounce search inputs
- Lazy load components

### 3. User Experience
- Show loading states
- Handle offline scenarios
- Provide meaningful error messages
- Implement retry mechanisms

### 4. Code Organization
```
src/
├── api/
│   ├── client.ts          # API client
│   ├── types.ts           # Type definitions
│   └── endpoints.ts       # Endpoint constants
├── hooks/                 # React hooks
├── composables/           # Vue composables
├── services/              # Angular services
└── utils/
    ├── auth.ts            # Auth utilities
    ├── storage.ts         # Storage utilities
    └── validation.ts      # Validation utilities
```

## Testing

### Unit Tests
```typescript
describe('API Client', () => {
  it('should login successfully', async () => {
    const mockResponse = { success: true, data: { token: 'test-token' } };
    jest.spyOn(axios, 'post').mockResolvedValue({ data: mockResponse });
    
    const result = await api.customerLogin({
      phone: '0987654321',
      password: 'password123'
    });
    
    expect(result.success).toBe(true);
    expect(result.data.token).toBe('test-token');
  });
});
```

### Integration Tests
- Test complete user flows
- Test error scenarios
- Test authentication flows
- Test API integration

## Environment Configuration

### Development
```typescript
const config = {
  baseURL: 'http://localhost:8000/api',
  timeout: 10000,
};
```

### Production
```typescript
const config = {
  baseURL: 'https://api.yourdomain.com/api',
  timeout: 30000,
};
```

## Common Patterns

### 1. Form Handling
```typescript
const handleSubmit = async (formData) => {
  try {
    setLoading(true);
    const response = await api.customerRegister(formData);
    if (response.success) {
      showSuccess('Đăng ký thành công');
      redirect('/dashboard');
    }
  } catch (error) {
    if (error.response?.status === 422) {
      setErrors(error.response.data.errors);
    } else {
      showError('Đăng ký thất bại');
    }
  } finally {
    setLoading(false);
  }
};
```

### 2. Data Fetching
```typescript
const useLoanApplications = () => {
  const [applications, setApplications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  
  useEffect(() => {
    const fetchApplications = async () => {
      try {
        const response = await api.getMyApplications();
        if (response.success) {
          setApplications(response.data.applications);
        }
      } catch (err) {
        setError(err);
      } finally {
        setLoading(false);
      }
    };
    
    fetchApplications();
  }, []);
  
  return { applications, loading, error };
};
```

### 3. File Upload
```typescript
const handleAvatarUpload = async (file) => {
  try {
    setUploading(true);
    const response = await api.uploadAvatar(file);
    if (response.success) {
      setAvatarUrl(response.data.avatarUrl);
      showSuccess('Cập nhật ảnh đại diện thành công');
    }
  } catch (error) {
    showError('Upload thất bại');
  } finally {
    setUploading(false);
  }
};
```

## Troubleshooting

### Common Issues

1. **CORS Error**
   - Ensure backend CORS is configured
   - Check request headers

2. **Authentication Error**
   - Verify token format
   - Check token expiration
   - Implement token refresh

3. **Validation Error**
   - Check request payload format
   - Verify required fields
   - Check data types

4. **Network Error**
   - Check API endpoint URL
   - Verify network connectivity
   - Check timeout settings

### Debug Tips

1. Use browser dev tools Network tab
2. Log API requests/responses
3. Check console for errors
4. Use Postman for API testing
5. Verify backend logs

## Support

Nếu cần hỗ trợ thêm:
1. Kiểm tra `test-api.http` file cho examples
2. Import `postman-collection.json` vào Postman
3. Tham khảo backend source code
4. Check API logs

## Changelog

### Version 1.0.0
- Initial API documentation
- Complete TypeScript types
- Example implementations
- Postman collection
- Integration guide