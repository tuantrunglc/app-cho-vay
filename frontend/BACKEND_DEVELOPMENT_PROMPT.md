# Backend Development Prompt Template - Laravel

## Prompt cho việc phát triển Backend API với Laravel

Bạn có thể sử dụng prompt này để yêu cầu AI phát triển backend cho hệ thống cho vay trực tuyến:

---

## 🎯 PROMPT CHÍNH

```
Tôi cần bạn phát triển một backend API cho hệ thống cho vay trực tuyến sử dụng Laravel 10 và MySQL. 

Dựa trên API specification đã được cung cấp, hãy tạo:

1. **Cấu trúc Laravel project hoàn chỉnh**:
   - Controllers (API Controllers)
   - Models (Eloquent Models)
   - Migrations
   - Seeders
   - Middleware
   - Services
   - Resources (API Resources)
   - Requests (Form Requests)
   - Policies
   - Jobs & Queues

2. **Database Models với Eloquent**:
   - User (Customer & Admin)
   - LoanApplication
   - Loan (Active loans)
   - Notification
   - Banner
   - Document
   - Payment
   - WalletTransaction
   - Relationships và Foreign Keys

3. **Authentication & Authorization**:
   - Laravel Sanctum hoặc JWT implementation
   - Role-based access control với Gates/Policies
   - Password hashing với Hash facade
   - Token refresh mechanism
   - Multi-guard authentication

4. **Core API Endpoints**:
   - Authentication (login, register, logout, refresh)
   - User management (profile, change password)
   - Loan management (apply, calculate, approve, reject)
   - Admin functions (dashboard, customer management, content management)
   - File upload handling với Storage facade
   - Notification system với Laravel Notifications

5. **Laravel Features**:
   - Form Request Validation
   - API Resources cho response formatting
   - Middleware cho authentication/authorization
   - Service Providers
   - Artisan Commands
   - Event & Listeners
   - Jobs & Queues cho background tasks

6. **Services & Integrations**:
   - Mail service với Laravel Mail
   - SMS service integration
   - File storage với Laravel Storage (local/S3)
   - Loan calculation service
   - Payment processing service
   - Notification service

7. **Security Features**:
   - Input validation với Form Requests
   - SQL injection prevention (Eloquent ORM)
   - XSS protection
   - CORS configuration
   - Rate limiting với Laravel Throttle
   - CSRF protection

8. **Database Configuration**:
   - MySQL connection setup
   - Migrations cho database schema
   - Seeders cho test data
   - Database indexes cho performance
   - Foreign key constraints

Yêu cầu kỹ thuật:
- Laravel 10.x framework
- MySQL 8.0+ database
- Laravel Sanctum hoặc JWT cho authentication
- Laravel Storage cho file upload
- Laravel Mail cho email notifications
- Laravel Validation cho input validation
- Laravel Eloquent ORM
- Laravel Queue cho background jobs

Hãy tạo code hoàn chỉnh, có comment chi tiết và follow Laravel best practices.
```

---

## 🔧 PROMPT CHO TỪNG PHẦN CỤ THỂ

### 1. Laravel Models & Migrations

```
Tạo các Eloquent models và migrations cho hệ thống cho vay:

1. **User Model & Migration**:
   - Hỗ trợ cả customer và admin
   - Fields: name, phone, email, password, role, status, verification_status
   - Eloquent relationships và scopes
   - Accessors/Mutators cho data formatting

2. **LoanApplication Model & Migration**:
   - Fields: user_id, amount, term, purpose, status, documents, emergency_contact
   - Belongs to User relationship
   - Status tracking với enums
   - JSON fields cho documents và emergency_contact

3. **Loan Model & Migration** (cho active loans):
   - Fields: loan_application_id, user_id, original_amount, remaining_amount, interest_rate
   - Relationships với User và LoanApplication
   - Payment schedule tracking
   - Overdue calculation với accessors

4. **Notification Model & Migration**:
   - Fields: user_id, title, content, type, read_at, created_at
   - Polymorphic relationships nếu cần
   - Support cho bulk notifications

5. **Document Model & Migration**:
   - Fields: user_id, loan_application_id, type, filename, path, status
   - File metadata và verification status
   - Storage path management

Hãy include:
- Migration files với foreign keys
- Model relationships (hasMany, belongsTo, etc.)
- Validation rules trong Form Requests
- Database indexes cho performance
- Eloquent scopes cho queries thường dùng
```

### 2. Laravel Authentication System

```
Tạo hệ thống authentication hoàn chỉnh với Laravel Sanctum:

1. **Laravel Sanctum Setup**:
   - Personal Access Tokens
   - API token authentication
   - Token abilities và scopes
   - Token expiration management

2. **Authentication Controllers**:
   - AuthController với login/register/logout
   - Separate guards cho admin và customer
   - Password reset functionality
   - Email verification

3. **Middleware**:
   - auth:sanctum middleware
   - Custom role-based middleware
   - Rate limiting middleware
   - CORS middleware

4. **Form Requests**:
   - LoginRequest với validation rules
   - RegisterRequest với custom validation
   - ChangePasswordRequest
   - Input sanitization

5. **Policies & Gates**:
   - User policies cho authorization
   - Role-based gates
   - Resource-based permissions
   - Admin-only actions

6. **Password Security**:
   - Laravel Hash facade
   - Password validation rules
   - Password history tracking
   - Account lockout mechanism

Include:
- Multi-guard authentication setup
- API rate limiting configuration
- Security headers middleware
- CSRF protection for web routes
```

### 3. Laravel Loan Management System

```
Tạo hệ thống quản lý khoản vay với Laravel:

1. **Loan Service Class**:
   - Monthly payment calculation methods
   - Interest calculation (simple interest)
   - Payment schedule generation
   - Total payment và interest calculation
   - Business logic separation

2. **Loan Controllers**:
   - LoanApplicationController cho customer
   - AdminLoanController cho admin
   - RESTful resource controllers
   - API Resource responses

3. **Form Requests**:
   - LoanApplicationRequest với validation
   - LoanApprovalRequest cho admin
   - Custom validation rules
   - Business rule validation

4. **Eloquent Resources**:
   - LoanApplicationResource
   - LoanResource
   - LoanCollection với pagination
   - Conditional field inclusion

5. **Jobs & Queues**:
   - ProcessLoanApplication job
   - SendLoanNotification job
   - CalculateOverdueLoans job
   - Background processing

6. **Events & Listeners**:
   - LoanApplicationSubmitted event
   - LoanApproved/LoanRejected events
   - Notification listeners
   - Audit trail logging

7. **Scopes & Filters**:
   - Eloquent scopes cho status filtering
   - Search functionality
   - Date range filtering
   - Admin dashboard queries

Include:
- Repository pattern nếu cần
- Service layer cho business logic
- Observer pattern cho model events
- Cache optimization cho frequent queries
```

### 4. Laravel File Upload System

```
Tạo hệ thống upload file với Laravel Storage:

1. **Storage Configuration**:
   - Laravel Storage facade setup
   - Multiple disk configuration (local, s3)
   - File visibility settings
   - Storage path organization

2. **File Upload Controller**:
   - DocumentController với upload methods
   - File validation rules
   - Storage disk selection
   - Response với file URLs

3. **Form Requests**:
   - DocumentUploadRequest
   - File type validation (images, PDFs)
   - File size limits
   - MIME type checking

4. **File Services**:
   - DocumentService class
   - Image processing với Intervention Image
   - File compression
   - Metadata extraction

5. **Security Features**:
   - File type verification
   - Secure filename generation
   - Access control với policies
   - Virus scanning integration

6. **Storage Management**:
   - File cleanup jobs
   - Storage optimization
   - Backup strategies
   - CDN integration

7. **API Resources**:
   - DocumentResource với file URLs
   - Conditional file access
   - Signed URLs cho private files

Include:
- Laravel Storage best practices
- File streaming cho large files
- Image optimization
- Storage monitoring
```

### 5. Laravel Admin Dashboard APIs

```
Tạo APIs cho admin dashboard với Laravel:

1. **Dashboard Controller**:
   - AdminDashboardController
   - Statistics aggregation methods
   - Real-time metrics
   - Chart data endpoints

2. **Eloquent Queries**:
   - Complex aggregation queries
   - Subqueries cho statistics
   - Raw queries khi cần thiết
   - Query optimization

3. **API Resources**:
   - DashboardStatsResource
   - CustomerResource với relationships
   - LoanSummaryResource
   - Paginated collections

4. **Caching Strategy**:
   - Redis cache cho statistics
   - Cache tags cho invalidation
   - Query result caching
   - Cache warming jobs

5. **Content Management**:
   - BannerController với CRUD
   - ConfigurationController
   - Bulk operations support
   - File upload integration

6. **Reporting System**:
   - Report generation jobs
   - Excel export với Laravel Excel
   - PDF generation với DomPDF
   - Scheduled reports

7. **Search & Filtering**:
   - Laravel Scout cho full-text search
   - Advanced filtering scopes
   - Sorting capabilities
   - Faceted search

Include:
- Database query optimization
- Response caching
- Background job processing
- Performance monitoring
```

---

## 📋 CHECKLIST HOÀN THÀNH

Khi phát triển backend, hãy đảm bảo:

### ✅ Core Functionality
- [ ] Laravel Sanctum authentication
- [ ] User registration và login
- [ ] Loan application submission
- [ ] Admin approval workflow
- [ ] File upload với Storage
- [ ] Notification system với Laravel Notifications

### ✅ Security
- [ ] Laravel Sanctum tokens
- [ ] Password hashing với Hash facade
- [ ] Form Request validation
- [ ] Rate limiting middleware
- [ ] CORS configuration
- [ ] Security headers

### ✅ Database
- [ ] MySQL connection
- [ ] All Eloquent models
- [ ] Database migrations
- [ ] Seeders cho test data
- [ ] Foreign key relationships

### ✅ API Endpoints
- [ ] All endpoints từ specification
- [ ] API Resources cho responses
- [ ] Proper HTTP status codes
- [ ] Exception handling
- [ ] Laravel pagination

### ✅ Testing
- [ ] PHPUnit tests cho services
- [ ] Feature tests cho APIs
- [ ] Authentication tests
- [ ] Database testing với factories

### ✅ Documentation
- [ ] API documentation
- [ ] Code comments
- [ ] Laravel setup instructions
- [ ] Environment configuration

---

## 🚀 DEPLOYMENT PROMPT

```
Tạo deployment configuration cho Laravel API với:

1. **Docker Setup**:
   - Dockerfile cho Laravel app
   - docker-compose.yml với MySQL, Redis, Nginx
   - Multi-stage build optimization
   - Health checks và monitoring

2. **Environment Configuration**:
   - .env files cho các environments
   - Laravel configuration caching
   - Database connection pooling
   - Queue worker configuration

3. **Production Considerations**:
   - Laravel Octane cho performance
   - Nginx configuration cho Laravel
   - SSL certificate setup
   - Laravel Horizon cho queue monitoring
   - Supervisor cho queue workers

4. **CI/CD Pipeline**:
   - GitHub Actions cho Laravel
   - PHPUnit testing automation
   - Laravel Dusk cho browser testing
   - Automated deployment scripts
   - Database migration automation

5. **Performance Optimization**:
   - OPcache configuration
   - Laravel caching strategies
   - Database query optimization
   - CDN setup cho static assets

Include Laravel deployment best practices và security considerations.
```

---

## 💡 TIPS SỬ DỤNG PROMPT

1. **Chia nhỏ yêu cầu**: Không yêu cầu tất cả cùng lúc, chia thành các phần nhỏ
2. **Cung cấp context**: Attach API specification file khi prompt
3. **Specify technology stack**: Rõ ràng về công nghệ muốn sử dụng
4. **Request examples**: Yêu cầu code examples và usage instructions
5. **Ask for best practices**: Luôn yêu cầu follow best practices
6. **Include error handling**: Đảm bảo có comprehensive error handling
7. **Request documentation**: Yêu cầu comments và documentation

---

## 📝 SAMPLE USAGE

```
Dựa trên API specification đã cung cấp, hãy tạo User model và Authentication system cho hệ thống cho vay Laravel. 

Yêu cầu:
- User Eloquent model với migration
- Support cả customer và admin roles
- Laravel Sanctum authentication
- Password hashing với Hash facade
- AuthController với login/register methods
- Authentication middleware
- Form Request validation
- API Resources cho responses
- Proper exception handling

Hãy include:
- Migration files
- Model relationships
- Controller methods
- Middleware setup
- Route definitions
- Usage examples
```

## 🎯 PROMPT MẪU CHO LARAVEL

```
Tôi cần bạn tạo một hệ thống backend Laravel hoàn chỉnh cho ứng dụng cho vay trực tuyến.

Yêu cầu:
1. Laravel 10 với MySQL
2. Authentication bằng Laravel Sanctum
3. API Resources cho response formatting
4. Form Requests cho validation
5. Eloquent relationships
6. File upload với Storage
7. Queue jobs cho background tasks
8. Notification system

Hãy bắt đầu với:
- Database migrations
- Eloquent models với relationships
- Authentication controllers
- API endpoints cơ bản

Follow Laravel best practices và include comprehensive error handling.
```

Sử dụng các prompt templates này để phát triển Laravel backend một cách có hệ thống và hiệu quả!