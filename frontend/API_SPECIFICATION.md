# API Specification - Hệ thống Cho vay Trực tuyến

## Tổng quan

Đây là tài liệu đặc tả API cho hệ thống cho vay trực tuyến, bao gồm các chức năng cho **Customer** (khách hàng) và **Admin** (quản trị viên). Backend được xây dựng bằng **Laravel** với **MySQL** database.

### Base URL
```
http://localhost:8000/api
```

### Authentication
- Sử dụng Laravel Sanctum hoặc JWT Bearer Token
- Header: `Authorization: Bearer <token>`
- Token có thời hạn và cần refresh khi hết hạn
- CSRF protection cho web routes

### Response Format (Laravel API Resource)
```json
{
  "success": true,
  "data": {},
  "message": "Success message",
  "meta": {
    "timestamp": "2024-01-15T10:30:00Z",
    "version": "1.0.0"
  }
}
```

### Error Format (Laravel Exception Handler)
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error message"]
  },
  "code": 422,
  "meta": {
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

### Pagination Format (Laravel Paginator)
```json
{
  "success": true,
  "data": [],
  "links": {
    "first": "http://localhost:8000/api/endpoint?page=1",
    "last": "http://localhost:8000/api/endpoint?page=10",
    "prev": null,
    "next": "http://localhost:8000/api/endpoint?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 10,
    "per_page": 15,
    "to": 15,
    "total": 150
  }
}
```

---

## 1. AUTHENTICATION APIs

### 1.1 Customer Login
**POST** `/auth/customer/login`

**Request Body:**
```json
{
  "phone": "0987654321",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "phone": "0987654321",
      "email": "nguyenvana@email.com",
      "role": "customer",
      "status": "active",
      "createdAt": "2024-01-10T00:00:00Z"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "refreshToken": "refresh_token_here",
    "expiresIn": 3600
  }
}
```

### 1.2 Admin Login
**POST** `/auth/admin/login`

**Request Body:**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "username": "admin",
      "name": "Administrator",
      "email": "admin@company.com",
      "role": "admin",
      "permissions": ["loan_approval", "customer_management", "content_management"]
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "refreshToken": "refresh_token_here",
    "expiresIn": 3600
  }
}
```

### 1.3 Customer Registration
**POST** `/auth/customer/register`

**Request Body:**
```json
{
  "name": "Nguyễn Văn A",
  "phone": "0987654321",
  "email": "nguyenvana@email.com",
  "password": "password123",
  "confirmPassword": "password123",
  "idCard": "123456789012",
  "address": "123 Đường ABC, Quận 1, TP.HCM",
  "dateOfBirth": "1990-01-01",
  "occupation": "Nhân viên văn phòng",
  "monthlyIncome": 15000000
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 2,
      "name": "Nguyễn Văn A",
      "phone": "0987654321",
      "email": "nguyenvana@email.com",
      "role": "customer",
      "status": "pending_verification"
    },
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "message": "Đăng ký thành công. Vui lòng xác thực tài khoản."
  }
}
```

### 1.4 Logout
**POST** `/auth/logout`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "message": "Đăng xuất thành công"
}
```

### 1.5 Refresh Token
**POST** `/auth/refresh`

**Request Body:**
```json
{
  "refreshToken": "refresh_token_here"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "new_access_token",
    "refreshToken": "new_refresh_token",
    "expiresIn": 3600
  }
}
```

---

## 2. USER MANAGEMENT APIs

### 2.1 Get User Profile
**GET** `/user/profile`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "phone": "0987654321",
    "email": "nguyenvana@email.com",
    "idCard": "123456789012",
    "address": "123 Đường ABC, Quận 1, TP.HCM",
    "dateOfBirth": "1990-01-01",
    "occupation": "Nhân viên văn phòng",
    "monthlyIncome": 15000000,
    "status": "active",
    "verificationStatus": "verified",
    "createdAt": "2024-01-10T00:00:00Z",
    "updatedAt": "2024-01-15T10:30:00Z"
  }
}
```

### 2.2 Update User Profile
**PUT** `/user/profile`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
  "name": "Nguyễn Văn A",
  "email": "newemail@email.com",
  "address": "456 Đường XYZ, Quận 2, TP.HCM",
  "occupation": "Kỹ sư phần mềm",
  "monthlyIncome": 20000000
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "email": "newemail@email.com",
    "address": "456 Đường XYZ, Quận 2, TP.HCM",
    "occupation": "Kỹ sư phần mềm",
    "monthlyIncome": 20000000,
    "updatedAt": "2024-01-15T10:30:00Z"
  },
  "message": "Cập nhật thông tin thành công"
}
```

### 2.3 Change Password
**POST** `/user/change-password`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
  "currentPassword": "oldpassword123",
  "newPassword": "newpassword123",
  "confirmPassword": "newpassword123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Đổi mật khẩu thành công"
}
```

### 2.4 Upload Avatar
**POST** `/user/avatar`

**Headers:** 
- `Authorization: Bearer <token>`
- `Content-Type: multipart/form-data`

**Request Body:** FormData with file field named "avatar"

**Response:**
```json
{
  "success": true,
  "data": {
    "avatarUrl": "https://storage.example.com/avatars/user_1_avatar.jpg"
  },
  "message": "Cập nhật ảnh đại diện thành công"
}
```

---

## 3. LOAN MANAGEMENT APIs

### 3.1 Get Loan Configuration
**GET** `/loans/config`

**Response:**
```json
{
  "success": true,
  "data": {
    "interestRates": {
      "baseRate": 2.0,
      "promotionalRate": 1.5,
      "penaltyRate": 3.0
    },
    "loanLimits": {
      "minAmount": 10000000,
      "maxAmount": 500000000,
      "stepAmount": 1000000,
      "defaultAmount": 50000000
    },
    "loanTerms": [6, 12, 18, 24, 36, 48],
    "quickAmounts": [50000000, 100000000, 200000000]
  }
}
```

### 3.2 Calculate Loan
**POST** `/loans/calculate`

**Request Body:**
```json
{
  "amount": 100000000,
  "term": 12,
  "interestRate": 2.0
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "loanAmount": 100000000,
    "term": 12,
    "interestRate": 2.0,
    "monthlyPayment": 10333333,
    "totalPayment": 124000000,
    "totalInterest": 24000000,
    "paymentSchedule": [
      {
        "month": 1,
        "principalPayment": 8333333,
        "interestPayment": 2000000,
        "totalPayment": 10333333,
        "remainingBalance": 91666667
      }
      // ... more months
    ]
  }
}
```

### 3.3 Submit Loan Application
**POST** `/loans/applications`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
  "amount": 100000000,
  "term": 12,
  "purpose": "Mua xe",
  "documents": {
    "idCardFront": "base64_image_data",
    "idCardBack": "base64_image_data",
    "incomeProof": "base64_image_data",
    "bankStatement": "base64_image_data"
  },
  "emergencyContact": {
    "name": "Nguyễn Thị B",
    "phone": "0901234567",
    "relationship": "Vợ"
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "LN001",
    "customerId": 1,
    "amount": 100000000,
    "term": 12,
    "purpose": "Mua xe",
    "status": "pending",
    "verificationStatus": "pending",
    "createdAt": "2024-01-15T10:30:00Z",
    "estimatedApprovalTime": "24 hours"
  },
  "message": "Đơn vay đã được gửi thành công"
}
```

### 3.4 Get User's Loan Applications
**GET** `/loans/applications/my`

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `status` (optional): pending, approved, rejected, active, completed
- `page` (optional): default 1
- `limit` (optional): default 10

**Response:**
```json
{
  "success": true,
  "data": {
    "applications": [
      {
        "id": "LN001",
        "amount": 100000000,
        "term": 12,
        "purpose": "Mua xe",
        "status": "pending",
        "verificationStatus": "verified",
        "createdAt": "2024-01-15T10:30:00Z",
        "updatedAt": "2024-01-15T10:30:00Z"
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 1,
      "totalItems": 1,
      "itemsPerPage": 10
    }
  }
}
```

### 3.5 Get Loan Application Details
**GET** `/loans/applications/{id}`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "LN001",
    "customerId": 1,
    "customerName": "Nguyễn Văn A",
    "amount": 100000000,
    "term": 12,
    "purpose": "Mua xe",
    "status": "pending",
    "verificationStatus": "verified",
    "interestRate": 2.0,
    "monthlyPayment": 10333333,
    "documents": [
      {
        "type": "id_card_front",
        "url": "https://storage.example.com/documents/id_front.jpg",
        "status": "verified"
      }
    ],
    "emergencyContact": {
      "name": "Nguyễn Thị B",
      "phone": "0901234567",
      "relationship": "Vợ"
    },
    "createdAt": "2024-01-15T10:30:00Z",
    "updatedAt": "2024-01-15T10:30:00Z"
  }
}
```

### 3.6 Lookup Loan by Phone
**GET** `/loans/lookup/{phone}`

**Response:**
```json
{
  "success": true,
  "data": {
    "customer": {
      "name": "Nguyễn Văn A",
      "phone": "0987654321"
    },
    "loans": [
      {
        "id": "LN001",
        "amount": 100000000,
        "status": "active",
        "remainingAmount": 80000000,
        "nextPaymentDate": "2024-02-15",
        "nextPaymentAmount": 10333333
      }
    ]
  }
}
```

---

## 4. ADMIN - LOAN MANAGEMENT APIs

### 4.1 Get All Loan Applications (Admin)
**GET** `/admin/loans/applications`

**Headers:** `Authorization: Bearer <admin_token>`

**Query Parameters:**
- `status` (optional): pending, approved, rejected, active, completed
- `verificationStatus` (optional): pending, verified, rejected
- `page` (optional): default 1
- `limit` (optional): default 10
- `search` (optional): search by customer name, phone, or loan ID

**Response:**
```json
{
  "success": true,
  "data": {
    "applications": [
      {
        "id": "LN001",
        "customerId": 1,
        "customerName": "Nguyễn Văn A",
        "customerPhone": "0987654321",
        "amount": 100000000,
        "term": 12,
        "purpose": "Mua xe",
        "status": "pending",
        "verificationStatus": "verified",
        "createdAt": "2024-01-15T10:30:00Z",
        "priority": "normal"
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 5,
      "totalItems": 50,
      "itemsPerPage": 10
    },
    "statistics": {
      "pending": 15,
      "approved": 20,
      "rejected": 10,
      "active": 25
    }
  }
}
```

### 4.2 Approve Loan Application
**POST** `/admin/loans/applications/{id}/approve`

**Headers:** `Authorization: Bearer <admin_token>`

**Request Body:**
```json
{
  "approvedAmount": 100000000,
  "approvedTerm": 12,
  "interestRate": 2.0,
  "notes": "Đơn vay được duyệt với điều kiện đầy đủ",
  "conditions": [
    "Cung cấp thêm giấy tờ chứng minh thu nhập",
    "Ký hợp đồng trong vòng 7 ngày"
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "LN001",
    "status": "approved",
    "approvedAmount": 100000000,
    "approvedTerm": 12,
    "interestRate": 2.0,
    "approvedBy": "admin",
    "approvedAt": "2024-01-15T10:30:00Z",
    "notes": "Đơn vay được duyệt với điều kiện đầy đủ"
  },
  "message": "Duyệt đơn vay thành công"
}
```

### 4.3 Reject Loan Application
**POST** `/admin/loans/applications/{id}/reject`

**Headers:** `Authorization: Bearer <admin_token>`

**Request Body:**
```json
{
  "reason": "Thu nhập không đủ điều kiện",
  "notes": "Khách hàng cần có thu nhập tối thiểu 20 triệu/tháng",
  "suggestions": [
    "Tăng thu nhập chứng minh được",
    "Giảm số tiền vay xuống 50 triệu"
  ]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "LN001",
    "status": "rejected",
    "reason": "Thu nhập không đủ điều kiện",
    "rejectedBy": "admin",
    "rejectedAt": "2024-01-15T10:30:00Z"
  },
  "message": "Từ chối đơn vay thành công"
}
```

### 4.4 Get Active Loans (Admin)
**GET** `/admin/loans/active`

**Headers:** `Authorization: Bearer <admin_token>`

**Query Parameters:**
- `page` (optional): default 1
- `limit` (optional): default 10
- `search` (optional): search by customer name, phone, or loan ID
- `overdue` (optional): true/false - filter overdue loans

**Response:**
```json
{
  "success": true,
  "data": {
    "loans": [
      {
        "id": "LN001",
        "customerId": 1,
        "customerName": "Nguyễn Văn A",
        "customerPhone": "0987654321",
        "originalAmount": 100000000,
        "remainingAmount": 80000000,
        "term": 12,
        "remainingTerm": 10,
        "interestRate": 2.0,
        "monthlyPayment": 10333333,
        "nextPaymentDate": "2024-02-15",
        "status": "active",
        "isOverdue": false,
        "daysPastDue": 0,
        "startDate": "2024-01-15T00:00:00Z"
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 3,
      "totalItems": 25,
      "itemsPerPage": 10
    },
    "statistics": {
      "totalActiveLoans": 25,
      "totalOutstandingAmount": 2000000000,
      "overdueLoans": 3,
      "overdueAmount": 150000000
    }
  }
}
```

---

## 5. CUSTOMER MANAGEMENT APIs (Admin)

### 5.1 Get All Customers
**GET** `/admin/customers`

**Headers:** `Authorization: Bearer <admin_token>`

**Query Parameters:**
- `status` (optional): active, blocked, pending_verification
- `page` (optional): default 1
- `limit` (optional): default 10
- `search` (optional): search by name, phone, email

**Response:**
```json
{
  "success": true,
  "data": {
    "customers": [
      {
        "id": 1,
        "name": "Nguyễn Văn A",
        "phone": "0987654321",
        "email": "nguyenvana@email.com",
        "idCard": "123456789012",
        "status": "active",
        "verificationStatus": "verified",
        "totalLoans": 2,
        "currentDebt": 80000000,
        "creditScore": 750,
        "joinDate": "2024-01-10T00:00:00Z",
        "lastActivity": "2024-01-15T10:30:00Z"
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 10,
      "totalItems": 100,
      "itemsPerPage": 10
    },
    "statistics": {
      "totalCustomers": 100,
      "activeCustomers": 85,
      "blockedCustomers": 10,
      "pendingVerification": 5
    }
  }
}
```

### 5.2 Get Customer Details
**GET** `/admin/customers/{id}`

**Headers:** `Authorization: Bearer <admin_token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Nguyễn Văn A",
    "phone": "0987654321",
    "email": "nguyenvana@email.com",
    "idCard": "123456789012",
    "address": "123 Đường ABC, Quận 1, TP.HCM",
    "dateOfBirth": "1990-01-01",
    "occupation": "Nhân viên văn phòng",
    "monthlyIncome": 15000000,
    "status": "active",
    "verificationStatus": "verified",
    "creditScore": 750,
    "joinDate": "2024-01-10T00:00:00Z",
    "loanHistory": [
      {
        "id": "LN001",
        "amount": 100000000,
        "status": "active",
        "startDate": "2024-01-15T00:00:00Z",
        "remainingAmount": 80000000
      }
    ],
    "documents": [
      {
        "type": "id_card_front",
        "url": "https://storage.example.com/documents/id_front.jpg",
        "status": "verified",
        "uploadedAt": "2024-01-10T00:00:00Z"
      }
    ]
  }
}
```

### 5.3 Update Customer Status
**PUT** `/admin/customers/{id}/status`

**Headers:** `Authorization: Bearer <admin_token>`

**Request Body:**
```json
{
  "status": "blocked",
  "reason": "Quá hạn thanh toán nhiều lần",
  "notes": "Khách hàng cần liên hệ để giải quyết nợ quá hạn"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "status": "blocked",
    "updatedBy": "admin",
    "updatedAt": "2024-01-15T10:30:00Z"
  },
  "message": "Cập nhật trạng thái khách hàng thành công"
}
```

---

## 6. CONTENT MANAGEMENT APIs (Admin)

### 6.1 Get Banners
**GET** `/admin/content/banners`

**Headers:** `Authorization: Bearer <admin_token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "banners": [
      {
        "id": 1,
        "title": "Vay nhanh - Lãi thấp",
        "subtitle": "Chỉ từ 2%/tháng",
        "imageUrl": "https://storage.example.com/banners/banner1.jpg",
        "active": true,
        "order": 1,
        "createdAt": "2024-01-01T00:00:00Z"
      }
    ]
  }
}
```

### 6.2 Create Banner
**POST** `/admin/content/banners`

**Headers:** 
- `Authorization: Bearer <admin_token>`
- `Content-Type: multipart/form-data`

**Request Body:** FormData with:
- `title`: "Banner title"
- `subtitle`: "Banner subtitle"
- `image`: File
- `active`: true/false
- `order`: 1

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "title": "Banner title",
    "subtitle": "Banner subtitle",
    "imageUrl": "https://storage.example.com/banners/banner2.jpg",
    "active": true,
    "order": 1,
    "createdAt": "2024-01-15T10:30:00Z"
  },
  "message": "Tạo banner thành công"
}
```

### 6.3 Update Interest Rates
**PUT** `/admin/content/interest-rates`

**Headers:** `Authorization: Bearer <admin_token>`

**Request Body:**
```json
{
  "baseRate": 2.0,
  "promotionalRate": 1.5,
  "penaltyRate": 3.0
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "baseRate": 2.0,
    "promotionalRate": 1.5,
    "penaltyRate": 3.0,
    "lastUpdated": "2024-01-15T10:30:00Z",
    "updatedBy": "admin"
  },
  "message": "Cập nhật lãi suất thành công"
}
```

### 6.4 Update Loan Limits
**PUT** `/admin/content/loan-limits`

**Headers:** `Authorization: Bearer <admin_token>`

**Request Body:**
```json
{
  "minAmount": 10000000,
  "maxAmount": 500000000,
  "stepAmount": 1000000,
  "defaultAmount": 50000000
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "minAmount": 10000000,
    "maxAmount": 500000000,
    "stepAmount": 1000000,
    "defaultAmount": 50000000,
    "lastUpdated": "2024-01-15T10:30:00Z"
  },
  "message": "Cập nhật hạn mức vay thành công"
}
```

---

## 7. NOTIFICATION APIs

### 7.1 Get User Notifications
**GET** `/notifications`

**Headers:** `Authorization: Bearer <token>`

**Query Parameters:**
- `page` (optional): default 1
- `limit` (optional): default 10
- `unread` (optional): true/false

**Response:**
```json
{
  "success": true,
  "data": {
    "notifications": [
      {
        "id": 1,
        "title": "Đơn vay được duyệt",
        "content": "Đơn vay LN001 của bạn đã được duyệt với số tiền 100,000,000 VND",
        "type": "loan_approved",
        "read": false,
        "createdAt": "2024-01-15T10:30:00Z"
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 2,
      "totalItems": 15,
      "itemsPerPage": 10
    },
    "unreadCount": 5
  }
}
```

### 7.2 Mark Notification as Read
**PUT** `/notifications/{id}/read`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "message": "Đánh dấu đã đọc thành công"
}
```

### 7.3 Send Notification (Admin)
**POST** `/admin/notifications/send`

**Headers:** `Authorization: Bearer <admin_token>`

**Request Body:**
```json
{
  "title": "Thông báo bảo trì hệ thống",
  "content": "Hệ thống sẽ được bảo trì từ 2h-4h sáng ngày 20/1/2024",
  "type": "system",
  "recipients": "all", // or "customers" or specific user IDs array
  "sendImmediately": true,
  "scheduledAt": null
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Thông báo bảo trì hệ thống",
    "sentCount": 150,
    "sentAt": "2024-01-15T10:30:00Z"
  },
  "message": "Gửi thông báo thành công"
}
```

---

## 8. WALLET & PAYMENT APIs

### 8.1 Get Wallet Balance
**GET** `/wallet/balance`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "balance": 2500000,
    "formattedBalance": "2,500,000 VND",
    "lastUpdated": "2024-01-15T10:30:00Z"
  }
}
```

### 8.2 Get Linked Banks
**GET** `/wallet/banks`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "banks": [
      {
        "id": 1,
        "name": "Vietcombank",
        "logo": "https://storage.example.com/banks/vcb.png",
        "accountNumber": "**** **** **** 1234",
        "isDefault": true,
        "status": "active"
      }
    ]
  }
}
```

### 8.3 Add Bank Account
**POST** `/wallet/banks`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
  "bankCode": "VCB",
  "accountNumber": "1234567890123456",
  "accountName": "NGUYEN VAN A",
  "isDefault": false
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "name": "Vietcombank",
    "accountNumber": "**** **** **** 3456",
    "isDefault": false,
    "status": "pending_verification"
  },
  "message": "Thêm tài khoản ngân hàng thành công"
}
```

### 8.4 Make Payment
**POST** `/payments/loan/{loanId}`

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
  "amount": 10333333,
  "paymentMethod": "bank_transfer",
  "bankId": 1,
  "notes": "Thanh toán kỳ hạn tháng 2"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "paymentId": "PAY001",
    "loanId": "LN001",
    "amount": 10333333,
    "status": "processing",
    "paymentMethod": "bank_transfer",
    "createdAt": "2024-01-15T10:30:00Z",
    "estimatedCompletionTime": "2024-01-15T12:30:00Z"
  },
  "message": "Thanh toán đang được xử lý"
}
```

---

## 9. DASHBOARD & STATISTICS APIs

### 9.1 Admin Dashboard Statistics
**GET** `/admin/dashboard/stats`

**Headers:** `Authorization: Bearer <admin_token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "overview": {
      "pendingLoans": 15,
      "activeLoans": 25,
      "totalCustomers": 100,
      "totalOutstandingAmount": 2000000000
    },
    "recentApplications": [
      {
        "id": "LN001",
        "customerName": "Nguyễn Văn A",
        "amount": 100000000,
        "status": "pending",
        "createdAt": "2024-01-15T10:30:00Z"
      }
    ],
    "monthlyStats": {
      "newLoans": 20,
      "approvedLoans": 18,
      "rejectedLoans": 2,
      "totalDisbursed": 1800000000
    },
    "overdueLoans": [
      {
        "id": "LN002",
        "customerName": "Trần Thị B",
        "amount": 50000000,
        "daysPastDue": 5,
        "overdueAmount": 10333333
      }
    ]
  }
}
```

### 9.2 Customer Dashboard
**GET** `/customer/dashboard`

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "activeLoans": [
      {
        "id": "LN001",
        "amount": 100000000,
        "remainingAmount": 80000000,
        "nextPaymentDate": "2024-02-15",
        "nextPaymentAmount": 10333333,
        "status": "active"
      }
    ],
    "recentApplications": [
      {
        "id": "LN002",
        "amount": 50000000,
        "status": "pending",
        "createdAt": "2024-01-14T10:30:00Z"
      }
    ],
    "walletBalance": 2500000,
    "creditScore": 750,
    "notifications": {
      "unreadCount": 3,
      "latest": [
        {
          "id": 1,
          "title": "Nhắc nhở thanh toán",
          "content": "Bạn có khoản thanh toán đến hạn vào ngày 15/02/2024",
          "createdAt": "2024-01-13T10:30:00Z"
        }
      ]
    }
  }
}
```

---

## 10. FILE UPLOAD APIs

### 10.1 Upload Document
**POST** `/upload/document`

**Headers:** 
- `Authorization: Bearer <token>`
- `Content-Type: multipart/form-data`

**Request Body:** FormData with:
- `file`: File
- `type`: "id_card_front" | "id_card_back" | "income_proof" | "bank_statement"
- `loanApplicationId`: "LN001" (optional)

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "DOC001",
    "filename": "id_card_front.jpg",
    "url": "https://storage.example.com/documents/id_card_front.jpg",
    "type": "id_card_front",
    "size": 1024000,
    "uploadedAt": "2024-01-15T10:30:00Z"
  },
  "message": "Upload tài liệu thành công"
}
```

---

## ERROR CODES

### Authentication Errors
- `AUTH_001`: Invalid credentials
- `AUTH_002`: Token expired
- `AUTH_003`: Token invalid
- `AUTH_004`: Insufficient permissions
- `AUTH_005`: Account locked/blocked

### Validation Errors
- `VAL_001`: Required field missing
- `VAL_002`: Invalid format
- `VAL_003`: Value out of range
- `VAL_004`: Duplicate value

### Business Logic Errors
- `LOAN_001`: Loan amount exceeds limit
- `LOAN_002`: Invalid loan term
- `LOAN_003`: Customer not eligible
- `LOAN_004`: Loan application not found
- `LOAN_005`: Loan already processed

### System Errors
- `SYS_001`: Database error
- `SYS_002`: External service unavailable
- `SYS_003`: File upload failed
- `SYS_004`: Rate limit exceeded

---

## RATE LIMITING

- **General APIs**: 100 requests per minute per IP
- **Authentication APIs**: 10 requests per minute per IP
- **File Upload APIs**: 20 requests per minute per user
- **Admin APIs**: 200 requests per minute per admin user

---

## SECURITY CONSIDERATIONS

1. **HTTPS Only**: All APIs must be accessed via HTTPS
2. **JWT Tokens**: Use secure JWT tokens with appropriate expiration
3. **Input Validation**: Validate all input data
4. **SQL Injection Prevention**: Use parameterized queries
5. **File Upload Security**: Validate file types and scan for malware
6. **Rate Limiting**: Implement rate limiting to prevent abuse
7. **Audit Logging**: Log all admin actions and sensitive operations

---

## TESTING ENDPOINTS

### Health Check
**GET** `/health`

**Response:**
```json
{
  "success": true,
  "data": {
    "status": "healthy",
    "timestamp": "2024-01-15T10:30:00Z",
    "version": "1.0.0"
  }
}
```

### API Version
**GET** `/version`

**Response:**
```json
{
  "success": true,
  "data": {
    "version": "1.0.0",
    "buildDate": "2024-01-15T00:00:00Z",
    "environment": "production"
  }
}
```

---

Tài liệu này cung cấp đầy đủ thông tin để phát triển backend API cho hệ thống cho vay trực tuyến. Mỗi endpoint đều có mô tả chi tiết về request/response format, authentication requirements, và error handling.