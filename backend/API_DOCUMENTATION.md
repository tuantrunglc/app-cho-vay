# API Documentation - Hệ thống Cho vay

## Thông tin chung

- **Base URL**: `http://localhost:8000/api`
- **API Version**: v1
- **Authentication**: JWT Bearer Token
- **Content-Type**: `application/json`

## Cấu trúc Response chung

Tất cả API response đều có cấu trúc chung như sau:

```json
{
  "success": true|false,
  "data": {}, // Dữ liệu trả về (chỉ có khi success = true)
  "message": "string", // Thông báo
  "errors": {}, // Chi tiết lỗi (chỉ có khi success = false)
  "code": "string", // Mã lỗi
  "meta": {
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "version": "1.0.0"
  }
}
```

## Mã lỗi chung

- `AUTH_001`: Lỗi xác thực
- `AUTH_002`: Token hết hạn
- `LOAN_001`: Lỗi khoản vay
- `LOAN_004`: Không tìm thấy đơn vay
- `SYS_001`: Lỗi hệ thống
- `SYS_003`: Lỗi upload file

---

## 1. Authentication APIs

### 1.1 Đăng nhập khách hàng

**Endpoint**: `POST /v1/auth/customer/login`

**Request Body**:
```json
{
  "phone": "0987654321",
  "password": "password123"
}
```

**Validation Rules**:
- `phone`: Bắt buộc, định dạng số điện thoại Việt Nam (0[3|5|7|8|9] + 8 số)
- `password`: Bắt buộc, tối thiểu 6 ký tự

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "phone": "0987654321",
      "email": "user@example.com",
      "role": "customer",
      "status": "active",
      "verification_status": "pending"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "refreshToken": "refresh_token_here",
    "expiresIn": 3600
  },
  "meta": {
    "timestamp": "2024-01-01T00:00:00.000000Z",
    "version": "1.0.0"
  }
}
```

**Response Error (401)**:
```json
{
  "success": false,
  "message": "Thông tin đăng nhập không chính xác",
  "code": "AUTH_001"
}
```

### 1.2 Đăng nhập admin

**Endpoint**: `POST /v1/auth/admin/login`

**Request Body**:
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Response**: Tương tự như đăng nhập khách hàng

### 1.3 Đăng ký khách hàng

**Endpoint**: `POST /v1/auth/customer/register`

**Request Body**:
```json
{
  "name": "Nguyễn Văn A",
  "phone": "0987654321",
  "email": "user@example.com",
  "password": "password123",
  "confirmPassword": "password123",
  "idCard": "123456789",
  "address": "123 Đường ABC, Quận 1, TP.HCM",
  "dateOfBirth": "1990-01-01",
  "occupation": "Nhân viên văn phòng",
  "monthlyIncome": 15000000
}
```

**Validation Rules**:
- `name`: Bắt buộc, 2-100 ký tự, chỉ chữ cái và khoảng trắng
- `phone`: Bắt buộc, định dạng số điện thoại VN, unique
- `email`: Tùy chọn, định dạng email, unique
- `password`: Bắt buộc, 6-255 ký tự
- `confirmPassword`: Bắt buộc, phải giống password
- `idCard`: Bắt buộc, 9-12 số, unique
- `address`: Bắt buộc, 10-500 ký tự
- `dateOfBirth`: Bắt buộc, từ 18-80 tuổi
- `occupation`: Bắt buộc, 2-100 ký tự
- `monthlyIncome`: Bắt buộc, 1,000,000 - 1,000,000,000 VND

**Response Success (201)**:
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "phone": "0987654321",
      "role": "customer"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "message": "Đăng ký thành công. Vui lòng xác thực tài khoản."
  }
}
```

### 1.4 Làm mới token

**Endpoint**: `POST /v1/auth/refresh`

**Request Body**:
```json
{
  "refresh_token": "refresh_token_here"
}
```

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "token": "new_jwt_token",
    "refreshToken": "new_refresh_token",
    "expiresIn": 3600
  }
}
```

### 1.5 Đăng xuất

**Endpoint**: `POST /v1/auth/logout`
**Authentication**: Required

**Response Success (200)**:
```json
{
  "success": true,
  "message": "Đăng xuất thành công"
}
```

### 1.6 Lấy thông tin user hiện tại

**Endpoint**: `GET /v1/auth/me`
**Authentication**: Required

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "phone": "0987654321",
    "email": "user@example.com",
    "role": "customer",
    "status": "active",
    "verification_status": "verified",
    "credit_score": 750,
    "avatar_url": "https://example.com/avatar.jpg"
  }
}
```

---

## 2. User Management APIs

### 2.1 Lấy thông tin profile

**Endpoint**: `GET /v1/user/profile`
**Authentication**: Required

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "phone": "0987654321",
    "email": "user@example.com",
    "id_card": "123456789",
    "address": "123 Đường ABC, Quận 1, TP.HCM",
    "date_of_birth": "1990-01-01",
    "occupation": "Nhân viên văn phòng",
    "monthly_income": "15000000.00",
    "role": "customer",
    "status": "active",
    "verification_status": "verified",
    "credit_score": 750,
    "avatar_url": "https://example.com/avatar.jpg",
    "wallet_balance": 0,
    "current_debt": 0,
    "total_loans": 0
  }
}
```

### 2.2 Cập nhật profile

**Endpoint**: `PUT /v1/user/profile`
**Authentication**: Required

**Request Body**:
```json
{
  "name": "Nguyễn Văn A Updated",
  "email": "newemail@example.com",
  "address": "456 Đường XYZ, Quận 2, TP.HCM",
  "occupation": "Kỹ sư phần mềm",
  "monthlyIncome": 20000000
}
```

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    // User object với thông tin đã cập nhật
  },
  "message": "Cập nhật thông tin thành công"
}
```

### 2.3 Đổi mật khẩu

**Endpoint**: `POST /v1/user/change-password`
**Authentication**: Required

**Request Body**:
```json
{
  "currentPassword": "old_password",
  "newPassword": "new_password",
  "confirmPassword": "new_password"
}
```

**Response Success (200)**:
```json
{
  "success": true,
  "message": "Đổi mật khẩu thành công"
}
```

### 2.4 Upload avatar

**Endpoint**: `POST /v1/user/upload-avatar`
**Authentication**: Required
**Content-Type**: `multipart/form-data`

**Request Body**:
```
avatar: [file] (image file: jpg, jpeg, png, max 2MB)
```

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "avatarUrl": "https://example.com/new-avatar.jpg"
  },
  "message": "Cập nhật ảnh đại diện thành công"
}
```

---

## 3. Loan APIs

### 3.1 Lấy cấu hình khoản vay

**Endpoint**: `GET /v1/loans/config`
**Authentication**: Not required

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "min_amount": 1000000,
    "max_amount": 500000000,
    "min_term": 1,
    "max_term": 60,
    "interest_rate": 1.5,
    "processing_fee": 2.0,
    "late_fee": 5.0,
    "available_terms": [1, 3, 6, 12, 18, 24, 36, 48, 60],
    "loan_purposes": [
      "Tiêu dùng cá nhân",
      "Mua xe máy",
      "Mua ô tô",
      "Sửa chữa nhà",
      "Kinh doanh",
      "Khác"
    ]
  }
}
```

### 3.2 Tính toán khoản vay

**Endpoint**: `POST /v1/loans/calculate`
**Authentication**: Not required

**Request Body**:
```json
{
  "amount": 50000000,
  "term": 12
}
```

**Validation Rules**:
- `amount`: Bắt buộc, số, trong khoảng min_amount - max_amount
- `term`: Bắt buộc, số nguyên, trong danh sách available_terms

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "loan_amount": 50000000,
    "term_months": 12,
    "interest_rate": 1.5,
    "monthly_payment": 4500000,
    "total_payment": 54000000,
    "total_interest": 4000000,
    "processing_fee": 1000000,
    "payment_schedule": [
      {
        "month": 1,
        "payment_date": "2024-02-01",
        "principal": 4000000,
        "interest": 500000,
        "total_payment": 4500000,
        "remaining_balance": 46000000
      }
      // ... other months
    ]
  }
}
```

### 3.3 Tra cứu khoản vay theo số điện thoại

**Endpoint**: `GET /v1/loans/lookup/{phone}`
**Authentication**: Not required

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "customer_name": "Nguyễn Văn A",
    "phone": "0987654321",
    "active_loans": [
      {
        "loan_id": "LOAN_001",
        "amount": 50000000,
        "remaining_amount": 30000000,
        "status": "active",
        "next_payment_date": "2024-02-01",
        "next_payment_amount": 4500000
      }
    ],
    "loan_history": [
      {
        "loan_id": "LOAN_002",
        "amount": 20000000,
        "status": "completed",
        "created_at": "2023-01-01",
        "completed_at": "2023-12-01"
      }
    ]
  }
}
```

### 3.4 Nộp đơn vay

**Endpoint**: `POST /v1/loans/apply`
**Authentication**: Required

**Request Body**:
```json
{
  "amount": 50000000,
  "term": 12,
  "purpose": "Mua xe máy",
  "emergencyContact": {
    "name": "Nguyễn Thị B",
    "phone": "0123456789",
    "relationship": "spouse"
  }
}
```

**Validation Rules**:
- `amount`: Bắt buộc, trong khoảng cho phép
- `term`: Bắt buộc, trong danh sách available_terms
- `purpose`: Bắt buộc, string
- `emergencyContact.name`: Bắt buộc, string
- `emergencyContact.phone`: Bắt buộc, định dạng số điện thoại
- `emergencyContact.relationship`: Bắt buộc, enum: spouse, parent, sibling, friend, other

**Response Success (201)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "loan_id": "LOAN_001",
    "amount": 50000000,
    "term": 12,
    "purpose": "Mua xe máy",
    "status": "pending",
    "monthly_payment": 4500000,
    "total_payment": 54000000,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "emergency_contact": {
      "name": "Nguyễn Thị B",
      "phone": "0123456789",
      "relationship": "spouse"
    }
  },
  "message": "Đơn vay đã được gửi thành công"
}
```

### 3.5 Lấy danh sách đơn vay của tôi

**Endpoint**: `GET /v1/loans/my-applications`
**Authentication**: Required

**Query Parameters**:
- `status`: Tùy chọn, filter theo trạng thái (pending, approved, rejected, active, completed)
- `page`: Tùy chọn, số trang (default: 1)
- `limit`: Tùy chọn, số item per page (default: 10)

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "applications": [
      {
        "id": 1,
        "loan_id": "LOAN_001",
        "amount": 50000000,
        "term": 12,
        "purpose": "Mua xe máy",
        "status": "active",
        "monthly_payment": 4500000,
        "remaining_amount": 30000000,
        "next_payment_date": "2024-02-01",
        "created_at": "2024-01-01T00:00:00.000000Z"
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 5,
      "totalItems": 50,
      "itemsPerPage": 10
    }
  }
}
```

### 3.6 Lấy chi tiết đơn vay

**Endpoint**: `GET /v1/loans/applications/{id}`
**Authentication**: Required

**Response Success (200)**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "loan_id": "LOAN_001",
    "amount": 50000000,
    "term": 12,
    "purpose": "Mua xe máy",
    "status": "active",
    "monthly_payment": 4500000,
    "total_payment": 54000000,
    "remaining_amount": 30000000,
    "next_payment_date": "2024-02-01",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "approved_at": "2024-01-02T00:00:00.000000Z",
    "emergency_contact": {
      "name": "Nguyễn Thị B",
      "phone": "0123456789",
      "relationship": "spouse"
    },
    "documents": [
      {
        "id": 1,
        "type": "id_card",
        "name": "CMND mặt trước",
        "url": "https://example.com/doc1.jpg",
        "status": "approved"
      }
    ],
    "payment_history": [
      {
        "id": 1,
        "payment_date": "2024-01-01",
        "amount": 4500000,
        "principal": 4000000,
        "interest": 500000,
        "status": "completed"
      }
    ]
  }
}
```

---

## 4. Health Check API

### 4.1 Kiểm tra trạng thái hệ thống

**Endpoint**: `GET /health`
**Authentication**: Not required

**Response Success (200)**:
```json
{
  "status": "OK",
  "timestamp": "2024-01-01T00:00:00.000000Z",
  "version": "1.0.0"
}
```

---

## 5. Error Handling

### HTTP Status Codes

- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not Found
- `422`: Validation Error
- `500`: Internal Server Error

### Validation Error Response (422)

```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "phone": ["Số điện thoại không đúng định dạng"],
    "password": ["Mật khẩu phải có ít nhất 6 ký tự"]
  }
}
```

---

## 6. Authentication

### JWT Token Usage

Tất cả các API yêu cầu authentication phải gửi JWT token trong header:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

### Token Expiration

- Access token: 1 giờ
- Refresh token: 30 ngày

Khi access token hết hạn, sử dụng refresh token để lấy token mới.

---

## 7. Rate Limiting

- Authentication APIs: 5 requests/minute
- Other APIs: 60 requests/minute

---

## 8. File Upload

### Avatar Upload

- Supported formats: JPG, JPEG, PNG
- Max file size: 2MB
- Recommended dimensions: 400x400px

### Document Upload

- Supported formats: JPG, JPEG, PNG, PDF
- Max file size: 5MB per file
- Max files per upload: 10

---

## 9. Pagination

Các API trả về danh sách sử dụng pagination với format:

```json
{
  "data": {
    "items": [...],
    "pagination": {
      "currentPage": 1,
      "totalPages": 10,
      "totalItems": 100,
      "itemsPerPage": 10
    }
  }
}
```

Query parameters:
- `page`: Số trang (default: 1)
- `limit`: Số item per page (default: 10, max: 100)

---

## 10. Webhooks (Future)

Hệ thống sẽ hỗ trợ webhook để thông báo các sự kiện:

- Loan application status changed
- Payment due reminder
- Payment completed

---

## 11. Testing

Sử dụng file `test-api.http` để test các API endpoints với các ví dụ request/response mẫu.

Base URL cho development: `http://localhost:8000/api`

---

## 12. Changelog

### Version 1.0.0 (2024-01-01)
- Initial API release
- Authentication system
- User management
- Loan application system
- Basic loan calculation