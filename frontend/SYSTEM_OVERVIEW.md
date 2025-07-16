# Tổng quan Hệ thống Cho vay Trực tuyến

## 📋 Mô tả Hệ thống

Hệ thống cho vay trực tuyến là một ứng dụng web cho phép khách hàng đăng ký vay tiền online và admin quản lý các đơn vay. Hệ thống được xây dựng với kiến trúc frontend-backend tách biệt.

## 🏗️ Kiến trúc Hệ thống

```
┌─────────────────┐    HTTP/HTTPS    ┌─────────────────┐
│                 │ ◄──────────────► │                 │
│   Frontend      │                  │   Backend API   │
│   (Vue.js)      │                  │   (Node.js)     │
│                 │                  │                 │
└─────────────────┘                  └─────────────────┘
                                              │
                                              │ MongoDB
                                              ▼
                                     ┌─────────────────┐
                                     │                 │
                                     │    Database     │
                                     │   (MongoDB)     │
                                     │                 │
                                     └─────────────────┘
```

## 👥 Người dùng Hệ thống

### 1. **Customer (Khách hàng)**
- Đăng ký tài khoản
- Đăng nhập vào hệ thống
- Nộp đơn xin vay
- Theo dõi trạng thái đơn vay
- Quản lý thông tin cá nhân
- Thanh toán khoản vay
- Nhận thông báo

### 2. **Admin (Quản trị viên)**
- Đăng nhập hệ thống admin
- Duyệt/từ chối đơn vay
- Quản lý khách hàng
- Quản lý nội dung (banner, lãi suất)
- Xem báo cáo thống kê
- Gửi thông báo
- Quản lý khoản vay đang hoạt động

## 🔄 Quy trình Nghiệp vụ

### Quy trình Vay vốn
```
1. Khách hàng đăng ký tài khoản
   ↓
2. Khách hàng nộp đơn xin vay (với giấy tờ)
   ↓
3. Admin xem xét và duyệt/từ chối
   ↓
4. Nếu được duyệt → Tạo khoản vay active
   ↓
5. Khách hàng thanh toán theo kỳ hạn
   ↓
6. Hoàn thành khoản vay
```

### Quy trình Quản lý
```
1. Admin đăng nhập hệ thống
   ↓
2. Xem dashboard với thống kê tổng quan
   ↓
3. Quản lý đơn vay chờ duyệt
   ↓
4. Quản lý khách hàng và khoản vay active
   ↓
5. Cập nhật nội dung hệ thống
```

## 📱 Giao diện Người dùng

### Customer Interface (Mobile-first)
- **Home**: Trang chủ với banner, calculator
- **Loan Application**: Form đăng ký vay
- **Loan Lookup**: Tra cứu khoản vay
- **Profile**: Thông tin cá nhân
- **My Loans**: Khoản vay của tôi
- **Wallet**: Ví điện tử
- **Notifications**: Thông báo
- **Support**: Hỗ trợ

### Admin Interface (Desktop)
- **Dashboard**: Thống kê tổng quan
- **Loan Approval**: Duyệt đơn vay
- **Active Loans**: Khoản vay đang hoạt động
- **Customers**: Quản lý khách hàng
- **Content Management**: Quản lý nội dung

## 🗄️ Cấu trúc Database (MySQL)

### Core Tables & Eloquent Models

#### users
```sql
CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  phone VARCHAR(20) UNIQUE NOT NULL,
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer', 'admin') DEFAULT 'customer',
  status ENUM('active', 'blocked', 'pending_verification') DEFAULT 'pending_verification',
  verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
  id_card VARCHAR(20),
  address TEXT,
  date_of_birth DATE,
  occupation VARCHAR(255),
  monthly_income DECIMAL(15,2),
  email_verified_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX idx_phone (phone),
  INDEX idx_email (email),
  INDEX idx_role_status (role, status)
);
```

#### loan_applications
```sql
CREATE TABLE loan_applications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  term INT NOT NULL COMMENT 'months',
  purpose VARCHAR(500),
  status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
  verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
  documents JSON,
  emergency_contact JSON,
  admin_notes TEXT,
  approved_by BIGINT UNSIGNED NULL,
  approved_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_user_status (user_id, status),
  INDEX idx_status_created (status, created_at)
);
```

#### loans
```sql
CREATE TABLE loans (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  loan_application_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  original_amount DECIMAL(15,2) NOT NULL,
  remaining_amount DECIMAL(15,2) NOT NULL,
  term INT NOT NULL,
  remaining_term INT NOT NULL,
  interest_rate DECIMAL(5,2) NOT NULL,
  monthly_payment DECIMAL(15,2) NOT NULL,
  status ENUM('active', 'completed', 'overdue', 'defaulted') DEFAULT 'active',
  start_date DATE NOT NULL,
  next_payment_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (loan_application_id) REFERENCES loan_applications(id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_status (user_id, status),
  INDEX idx_next_payment (next_payment_date, status)
);
```

#### payments
```sql
CREATE TABLE payments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  loan_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  payment_date DATE NOT NULL,
  method ENUM('bank_transfer', 'cash', 'online') DEFAULT 'bank_transfer',
  status ENUM('completed', 'pending', 'failed') DEFAULT 'pending',
  transaction_id VARCHAR(255),
  notes TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
  INDEX idx_loan_date (loan_id, payment_date),
  INDEX idx_status (status)
);
```

#### notifications
```sql
CREATE TABLE notifications (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL COMMENT 'NULL for broadcast',
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  type ENUM('loan_approved', 'payment_reminder', 'system', 'promotion') NOT NULL,
  read_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user_read (user_id, read_at),
  INDEX idx_type_created (type, created_at)
);
```

#### banners
```sql
CREATE TABLE banners (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  subtitle VARCHAR(500),
  image_path VARCHAR(500) NOT NULL,
  active BOOLEAN DEFAULT TRUE,
  order_index INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  INDEX idx_active_order (active, order_index)
);
```

#### documents
```sql
CREATE TABLE documents (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  loan_application_id BIGINT UNSIGNED NULL,
  type ENUM('id_card_front', 'id_card_back', 'income_proof', 'bank_statement') NOT NULL,
  filename VARCHAR(255) NOT NULL,
  path VARCHAR(500) NOT NULL,
  size INT NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (loan_application_id) REFERENCES loan_applications(id) ON DELETE CASCADE,
  INDEX idx_user_type (user_id, type),
  INDEX idx_application (loan_application_id)
);
```

## 🔐 Bảo mật

### Authentication
- JWT tokens với access token (15 phút) và refresh token (7 ngày)
- Password hashing với bcrypt
- Role-based access control

### Authorization
- Customer chỉ truy cập được data của mình
- Admin có quyền truy cập tất cả data
- API endpoints được bảo vệ bằng middleware

### Data Security
- Input validation và sanitization
- SQL injection prevention
- XSS protection
- File upload security
- Rate limiting

## 📊 Tính năng Chính

### Customer Features
✅ **Đăng ký/Đăng nhập**: Phone + password authentication  
✅ **Đăng ký vay**: Form với upload giấy tờ  
✅ **Tra cứu khoản vay**: Lookup by phone number  
✅ **Quản lý profile**: Cập nhật thông tin cá nhân  
✅ **Theo dõi khoản vay**: Xem trạng thái và lịch thanh toán  
✅ **Ví điện tử**: Quản lý số dư và ngân hàng liên kết  
✅ **Thông báo**: Nhận thông báo từ hệ thống  
✅ **Hỗ trợ**: Liên hệ support  

### Admin Features
✅ **Dashboard**: Thống kê tổng quan hệ thống  
✅ **Duyệt đơn vay**: Approve/reject loan applications  
✅ **Quản lý khoản vay**: Theo dõi active loans  
✅ **Quản lý khách hàng**: Customer management  
✅ **Quản lý nội dung**: Banner, lãi suất, thông báo  
✅ **Báo cáo**: Statistics và reports  

## 🛠️ Công nghệ Sử dụng

### Frontend
- **Framework**: Vue.js 3 với Composition API
- **Build Tool**: Vite
- **State Management**: Pinia
- **Routing**: Vue Router 4
- **Styling**: Tailwind CSS
- **HTTP Client**: Axios
- **Icons**: Heroicons

### Backend
- **Framework**: Laravel 10
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum
- **File Upload**: Laravel Storage
- **Email**: Laravel Mail
- **Validation**: Form Requests
- **Security**: Laravel built-in security features
- **Queue**: Laravel Queue với Redis
- **Cache**: Redis

### DevOps
- **Containerization**: Docker
- **Web Server**: Nginx
- **Process Manager**: Laravel Octane (optional)
- **Queue Worker**: Laravel Horizon
- **Monitoring**: Laravel Telescope
- **CI/CD**: GitHub Actions

## 📈 Scalability Considerations

### Performance
- Database indexing cho queries thường dùng
- Caching cho static content
- Image optimization cho uploads
- API response pagination
- Rate limiting để tránh abuse

### Monitoring
- Application logging
- Error tracking
- Performance monitoring
- Database monitoring
- User activity tracking

## 🚀 Deployment Architecture

```
Internet
    │
    ▼
┌─────────────┐
│   Nginx     │ ◄── SSL Termination, Static Files
│ (Reverse    │
│   Proxy)    │
└─────────────┘
    │
    ▼
┌─────────────┐
│  Laravel    │ ◄── API Server (PHP-FPM/Octane)
│  Backend    │
└─────────────┘
    │
    ├─────────────────┐
    ▼                 ▼
┌─────────────┐  ┌─────────────┐
│   MySQL     │  │    Redis    │
│  Database   │  │ (Cache/Queue)│
└─────────────┘  └─────────────┘
```

## 📋 Development Roadmap

### Phase 1: Core Features ✅
- User authentication
- Loan application system
- Basic admin functions
- File upload

### Phase 2: Advanced Features
- Payment integration
- SMS notifications
- Advanced reporting
- Mobile app

### Phase 3: Enhancements
- AI-based loan approval
- Credit scoring
- Advanced analytics
- Third-party integrations

## 🔍 Testing Strategy

### Frontend Testing
- Unit tests cho components
- Integration tests cho stores
- E2E tests cho user flows

### Backend Testing (Laravel)
- PHPUnit tests cho services
- Feature tests cho APIs
- Database testing với factories
- Browser testing với Laravel Dusk

## 📞 Support & Maintenance

### Monitoring
- Application health checks
- Database performance monitoring
- Error tracking và alerting
- User activity monitoring

### Backup & Recovery
- Database backup strategy
- File storage backup
- Disaster recovery plan
- Data retention policies

---

Hệ thống này được thiết kế để đáp ứng nhu cầu cho vay trực tuyến với tính bảo mật cao, hiệu suất tốt và khả năng mở rộng trong tương lai.