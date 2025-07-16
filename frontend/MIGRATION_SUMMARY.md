# API Migration Summary

## Tóm tắt thay đổi

Đã thực hiện migration từ mock APIs sang real backend APIs dựa trên tài liệu `backend/API_DOCUMENTATION.md`.

## Files đã thay đổi

### 1. `src/services/api.js`
- **Thay đổi**: Cập nhật base URL từ `localhost:8080` sang `localhost:8000`
- **Thêm mới**: Auto refresh token khi access token hết hạn
- **Thêm mới**: Import constants từ `apiConstants.js`
- **Cải thiện**: Error handling với error codes cụ thể

### 2. `src/services/apiService.js`
- **Thay đổi**: Tất cả endpoints được cập nhật theo API documentation
- **Thêm mới**: Các methods mới:
  - `refreshToken()` - Làm mới token
  - `getCurrentUser()` - Lấy thông tin user hiện tại
  - `changePassword()` - Đổi mật khẩu
  - `uploadAvatar()` - Upload avatar
  - `getLoanConfig()` - Lấy cấu hình khoản vay
  - `calculateLoan()` - Tính toán khoản vay
  - `lookupLoanByPhone()` - Tra cứu khoản vay
  - `applyLoan()` - Nộp đơn vay
  - `getMyLoanApplications()` - Lấy danh sách đơn vay
  - `getLoanApplicationById()` - Lấy chi tiết đơn vay
  - `uploadMultipleFiles()` - Upload nhiều files
  - `healthCheck()` - Kiểm tra trạng thái hệ thống
- **Cải thiện**: Tự động lưu/xóa token trong localStorage
- **Backward compatibility**: Giữ lại các methods cũ với alias

### 3. `src/services/apiConstants.js` (Mới)
- **Chức năng**: Định nghĩa tất cả constants cho API
- **Nội dung**:
  - API endpoints
  - Error codes
  - HTTP status codes
  - User roles và status
  - Loan status và purposes
  - File upload limits
  - Validation rules
  - Pagination defaults

### 4. `src/services/apiHelpers.js` (Mới)
- **Chức năng**: Utility functions hỗ trợ xử lý API
- **Nội dung**:
  - Authentication helpers
  - Data formatting (currency, date)
  - Validation functions (phone, email, ID card)
  - Error handling utilities
  - File handling utilities
  - Debounce/throttle functions

### 5. `src/services/index.js`
- **Thay đổi**: Cập nhật exports để include các files mới
- **Thêm mới**: Export constants và helpers

### 6. `src/services/README.md`
- **Cập nhật**: Documentation phản ánh các thay đổi mới
- **Thêm mới**: Hướng dẫn sử dụng constants và helpers

### 7. `src/examples/ApiUsageExample.vue`
- **Cập nhật**: Import từ services mới
- **Sẵn sàng**: Để mở rộng demo các API mới

## Mapping API Endpoints

### Authentication
| Old Mock | New Backend | Method |
|----------|-------------|---------|
| `/auth/login` | `/v1/auth/customer/login` hoặc `/v1/auth/admin/login` | `login()` |
| `/auth/register` | `/v1/auth/customer/register` | `register()` |
| `/auth/logout` | `/v1/auth/logout` | `logout()` |
| - | `/v1/auth/refresh` | `refreshToken()` |
| - | `/v1/auth/me` | `getCurrentUser()` |

### User Management
| Old Mock | New Backend | Method |
|----------|-------------|---------|
| `/user/profile` | `/v1/user/profile` | `getUserProfile()` |
| `/user/profile` | `/v1/user/profile` | `updateUserProfile()` |
| - | `/v1/user/change-password` | `changePassword()` |
| - | `/v1/user/upload-avatar` | `uploadAvatar()` |

### Loan Management
| Old Mock | New Backend | Method |
|----------|-------------|---------|
| `/loans` | `/v1/loans/my-applications` | `getMyLoanApplications()` |
| `/loans/{id}` | `/v1/loans/applications/{id}` | `getLoanApplicationById()` |
| `/loans` | `/v1/loans/apply` | `applyLoan()` |
| - | `/v1/loans/config` | `getLoanConfig()` |
| - | `/v1/loans/calculate` | `calculateLoan()` |
| - | `/v1/loans/lookup/{phone}` | `lookupLoanByPhone()` |

### System
| Old Mock | New Backend | Method |
|----------|-------------|---------|
| - | `/health` | `healthCheck()` |

## Tính năng mới

### 1. Automatic Token Refresh
- Tự động refresh token khi access token hết hạn
- Retry request với token mới
- Fallback logout nếu refresh thất bại

### 2. Enhanced Error Handling
- Error codes cụ thể từ backend
- Validation error handling
- Network error handling
- Centralized error processing

### 3. Authentication Management
- Tự động lưu/xóa token
- Phân biệt customer/admin login
- Authentication status helpers

### 4. Validation Utilities
- Phone number validation (VN format)
- Email validation
- ID card validation
- File validation (type, size)

### 5. Data Formatting
- Currency formatting (VND)
- Date/datetime formatting
- Age calculation

### 6. File Upload Support
- Single file upload với progress
- Multiple files upload
- File type và size validation

## Breaking Changes

### Không có breaking changes
- Tất cả methods cũ vẫn hoạt động
- Backward compatibility được đảm bảo
- Chỉ thêm tính năng mới

## Migration Guide

### Cho developers hiện tại:
1. **Không cần thay đổi code hiện tại** - tất cả vẫn hoạt động
2. **Khuyến khích sử dụng**: Constants thay vì hardcode endpoints
3. **Khuyến khích sử dụng**: Helper functions cho validation và formatting
4. **Cập nhật**: Environment variable `VITE_API_URL` thành `http://localhost:8000/api`

### Ví dụ migration tùy chọn:

#### Trước:
```javascript
const response = await apiService.get('/loans')
```

#### Sau (khuyến khích):
```javascript
import { apiService, API_ENDPOINTS } from '@/services'
const response = await apiService.get(API_ENDPOINTS.LOANS.MY_APPLICATIONS)
```

#### Hoặc sử dụng method mới:
```javascript
const response = await apiService.getMyLoanApplications()
```

## Testing

### Để test các API mới:
1. Khởi động backend server trên port 8000
2. Sử dụng `ApiUsageExample.vue` component để test
3. Hoặc test trực tiếp trong browser console:

```javascript
import { apiService } from '@/services'
await apiService.healthCheck()
```

## Environment Setup

Đảm bảo file `.env` có:
```
VITE_API_URL=http://localhost:8000/api
```

## Next Steps

1. **Test tất cả APIs** với backend thực tế
2. **Cập nhật UI components** để sử dụng các tính năng mới
3. **Implement error handling** trong components
4. **Sử dụng validation helpers** trong forms
5. **Implement file upload** features