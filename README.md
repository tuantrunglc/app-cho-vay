# Hệ Thống Cho Vay Nhanh

Hệ thống quản lý cho vay trực tuyến với Laravel backend và React frontend.

## Tính Năng Chính

### Khách Hàng
- Đăng ký/Đăng nhập tài khoản
- Tính toán khoản vay
- Nộp đơn xin vay
- Theo dõi trạng thái đơn vay
- Quản lý hồ sơ cá nhân
- Tra cứu thông tin vay

### Admin
- Quản lý người dùng
- Duyệt/Từ chối đơn vay
- Quản lý khoản vay
- Báo cáo thống kê
- Cấu hình hệ thống

## Công Nghệ Sử Dụng

### Backend
- Laravel 10
- MySQL 8.0
- Redis
- JWT Authentication
- Docker

### Frontend
- React 18
- TypeScript
- Tailwind CSS
- Axios
- React Router

## Cài Đặt

### Yêu Cầu Hệ Thống
- Docker & Docker Compose
- Node.js 18+
- PHP 8.2+
- Composer

### Backend Setup

1. **Clone repository**
```bash
git clone <repository-url>
cd app-cho-vay/backend
```

2. **Cấu hình environment**
```bash
cp .env.example .env
```

3. **Chỉnh sửa file .env**
```env
APP_NAME="Cho Vay Nhanh"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=loan_app
DB_USERNAME=loan_user
DB_PASSWORD=loan_password

REDIS_HOST=redis

JWT_SECRET=your-jwt-secret-key
```

4. **Khởi động Docker containers**
```bash
docker-compose up -d --build
```

5. **Cài đặt dependencies**
```bash
docker-compose exec app composer install
```

6. **Generate application key**
```bash
docker-compose exec app php artisan key:generate
```

7. **Generate JWT secret**
```bash
docker-compose exec app php artisan jwt:secret
```

8. **Chạy migrations**
```bash
docker-compose exec app php artisan migrate
```

9. **Seed database**
```bash
docker-compose exec app php artisan db:seed
```

### Frontend Setup

1. **Chuyển đến thư mục frontend**
```bash
cd ../frontend
```

2. **Cài đặt dependencies**
```bash
npm install
```

3. **Cấu hình environment**
```bash
cp .env.example .env.local
```

4. **Chỉnh sửa file .env.local**
```env
REACT_APP_API_URL=http://localhost:8000/api/v1
REACT_APP_APP_NAME=Cho Vay Nhanh
```

5. **Khởi động development server**
```bash
npm start
```

## API Documentation

### Authentication Endpoints

#### Customer Login
```http
POST /api/v1/auth/customer/login
Content-Type: application/json

{
  "phone": "0987654321",
  "password": "password123"
}
```

#### Customer Register
```http
POST /api/v1/auth/customer/register
Content-Type: application/json

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

#### Admin Login
```http
POST /api/v1/auth/admin/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123"
}
```

### Loan Endpoints

#### Calculate Loan
```http
POST /api/v1/loans/calculate
Content-Type: application/json

{
  "amount": 50000000,
  "term": 12
}
```

#### Submit Loan Application
```http
POST /api/v1/loans/apply
Authorization: Bearer <token>
Content-Type: application/json

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

## Tài Khoản Mặc Định

### Admin Accounts
- **Super Admin**: username: `admin`, password: `admin123`
- **Loan Officer**: username: `loan_officer`, password: `loan123`
- **Customer Service**: username: `cs_staff`, password: `cs123`

## Cấu Hình Hệ Thống

### Loan Settings
- Số tiền vay tối thiểu: 10,000,000 VND
- Số tiền vay tối đa: 500,000,000 VND
- Bước nhảy: 1,000,000 VND
- Lãi suất cơ bản: 2.0%/tháng
- Thời hạn vay: 6, 12, 18, 24, 36, 48 tháng

### File Upload
- Kích thước tối đa: 5MB
- Định dạng cho phép: JPG, JPEG, PNG, PDF

## Cấu Trúc Database

### Bảng Chính
- `users` - Thông tin người dùng
- `loan_applications` - Đơn xin vay
- `loans` - Khoản vay đã duyệt
- `payments` - Thanh toán
- `documents` - Tài liệu
- `system_settings` - Cấu hình hệ thống
- `banners` - Banner quảng cáo

## Development

### Chạy Tests
```bash
# Backend tests
docker-compose exec app php artisan test

# Frontend tests
cd frontend && npm test
```

### Code Style
```bash
# Backend - PHP CS Fixer
docker-compose exec app ./vendor/bin/pint

# Frontend - Prettier
cd frontend && npm run format
```

### Build Production
```bash
# Backend
docker-compose -f docker-compose.prod.yml up -d --build

# Frontend
cd frontend && npm run build
```

## Troubleshooting

### Common Issues

1. **JWT Secret not set**
```bash
docker-compose exec app php artisan jwt:secret
```

2. **Permission denied**
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

3. **Database connection failed**
- Kiểm tra Docker containers đang chạy
- Kiểm tra cấu hình database trong .env

4. **CORS errors**
- Kiểm tra cấu hình CORS trong config/cors.php
- Đảm bảo frontend URL được thêm vào allowed_origins

## Support

Nếu gặp vấn đề, vui lòng tạo issue trên GitHub hoặc liên hệ team phát triển.

## License

MIT License