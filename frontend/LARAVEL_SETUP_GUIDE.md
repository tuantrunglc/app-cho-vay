# Laravel Backend Setup Guide

## 📋 Yêu cầu Hệ thống

### Server Requirements
- PHP >= 8.1
- MySQL >= 8.0
- Redis (optional, cho cache và queue)
- Composer
- Node.js & NPM (cho asset compilation)

### PHP Extensions
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## 🚀 Cài đặt Laravel Project

### 1. Tạo Laravel Project
```bash
composer create-project laravel/laravel loan-system-backend
cd loan-system-backend
```

### 2. Cấu hình Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Cấu hình Database (.env)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=loan_system
DB_USERNAME=root
DB_PASSWORD=

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# File Storage
FILESYSTEM_DISK=local
# Hoặc cho S3:
# FILESYSTEM_DISK=s3
# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=
# AWS_BUCKET=
```

## 📦 Cài đặt Packages

### 1. Authentication
```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### 2. Additional Packages
```bash
# Image processing
composer require intervention/image

# Excel export
composer require maatwebsite/excel

# PDF generation
composer require barryvdh/laravel-dompdf

# API documentation
composer require darkaonline/l5-swagger

# Queue monitoring
composer require laravel/horizon

# Development tools
composer require --dev laravel/telescope
composer require --dev barryvdh/laravel-debugbar
```

## 🗄️ Database Setup

### 1. Tạo Database
```sql
CREATE DATABASE loan_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Chạy Migrations
```bash
php artisan migrate
```

### 3. Tạo Migrations cho Project
```bash
# User table (đã có sẵn, cần modify)
php artisan make:migration modify_users_table

# Core tables
php artisan make:migration create_loan_applications_table
php artisan make:migration create_loans_table
php artisan make:migration create_payments_table
php artisan make:migration create_documents_table
php artisan make:migration create_banners_table
php artisan make:migration create_notifications_table

# Configuration tables
php artisan make:migration create_loan_configs_table
php artisan make:migration create_interest_rates_table
```

## 🏗️ Cấu trúc Project

### 1. Tạo Models
```bash
php artisan make:model User # Đã có sẵn
php artisan make:model LoanApplication
php artisan make:model Loan
php artisan make:model Payment
php artisan make:model Document
php artisan make:model Banner
php artisan make:model Notification
```

### 2. Tạo Controllers
```bash
# API Controllers
php artisan make:controller Api/AuthController
php artisan make:controller Api/UserController
php artisan make:controller Api/LoanApplicationController
php artisan make:controller Api/LoanController
php artisan make:controller Api/PaymentController
php artisan make:controller Api/DocumentController
php artisan make:controller Api/NotificationController

# Admin Controllers
php artisan make:controller Api/Admin/DashboardController
php artisan make:controller Api/Admin/LoanManagementController
php artisan make:controller Api/Admin/CustomerController
php artisan make:controller Api/Admin/ContentController
```

### 3. Tạo Form Requests
```bash
php artisan make:request Auth/LoginRequest
php artisan make:request Auth/RegisterRequest
php artisan make:request LoanApplicationRequest
php artisan make:request DocumentUploadRequest
php artisan make:request Admin/LoanApprovalRequest
```

### 4. Tạo API Resources
```bash
php artisan make:resource UserResource
php artisan make:resource LoanApplicationResource
php artisan make:resource LoanResource
php artisan make:resource PaymentResource
php artisan make:resource DocumentResource
php artisan make:resource NotificationResource
```

### 5. Tạo Middleware
```bash
php artisan make:middleware AdminMiddleware
php artisan make:middleware CustomerMiddleware
php artisan make:middleware RoleMiddleware
```

### 6. Tạo Services
```bash
php artisan make:class Services/LoanCalculationService
php artisan make:class Services/NotificationService
php artisan make:class Services/DocumentService
php artisan make:class Services/PaymentService
```

### 7. Tạo Jobs
```bash
php artisan make:job ProcessLoanApplication
php artisan make:job SendLoanNotification
php artisan make:job CalculateOverdueLoans
php artisan make:job ProcessPayment
```

### 8. Tạo Policies
```bash
php artisan make:policy LoanApplicationPolicy
php artisan make:policy LoanPolicy
php artisan make:policy DocumentPolicy
```

## ⚙️ Configuration

### 1. Sanctum Configuration (config/sanctum.php)
```php
<?php

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
    ))),

    'guard' => ['web'],

    'expiration' => 60 * 24, // 24 hours

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],
];
```

### 2. CORS Configuration (config/cors.php)
```php
<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:3000', 'http://localhost:5173'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
```

### 3. Queue Configuration
```bash
# Publish horizon config
php artisan horizon:install

# Start horizon
php artisan horizon
```

## 🛣️ Routes Setup

### 1. API Routes (routes/api.php)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LoanApplicationController;

// Public routes
Route::post('/auth/customer/login', [AuthController::class, 'customerLogin']);
Route::post('/auth/admin/login', [AuthController::class, 'adminLogin']);
Route::post('/auth/customer/register', [AuthController::class, 'customerRegister']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::put('/user/profile', [UserController::class, 'updateProfile']);
    
    // Customer routes
    Route::middleware('role:customer')->group(function () {
        Route::apiResource('loan-applications', LoanApplicationController::class);
        Route::get('/my-loans', [LoanController::class, 'myLoans']);
    });
    
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/loan-applications', [LoanManagementController::class, 'index']);
        Route::post('/loan-applications/{id}/approve', [LoanManagementController::class, 'approve']);
        Route::post('/loan-applications/{id}/reject', [LoanManagementController::class, 'reject']);
    });
});
```

## 🔧 Development Tools

### 1. Telescope (Development)
```bash
php artisan telescope:install
php artisan migrate
```

### 2. Debugbar (Development)
```bash
# Automatically registered in development
```

### 3. API Documentation
```bash
php artisan l5-swagger:generate
```

## 🧪 Testing Setup

### 1. Database Testing
```bash
# Create test database
CREATE DATABASE loan_system_test;

# Configure .env.testing
cp .env .env.testing
# Update database name to loan_system_test
```

### 2. Factory Setup
```bash
php artisan make:factory UserFactory # Đã có sẵn
php artisan make:factory LoanApplicationFactory
php artisan make:factory LoanFactory
php artisan make:factory PaymentFactory
```

### 3. Seeder Setup
```bash
php artisan make:seeder UserSeeder
php artisan make:seeder LoanApplicationSeeder
php artisan make:seeder BannerSeeder
```

### 4. Test Files
```bash
php artisan make:test AuthTest
php artisan make:test LoanApplicationTest
php artisan make:test AdminLoanManagementTest
```

## 🚀 Deployment

### 1. Production Optimization
```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 2. Queue Workers
```bash
# Start queue worker
php artisan queue:work

# Or use Horizon
php artisan horizon
```

### 3. Scheduled Tasks
```bash
# Add to crontab
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 📝 Environment Variables

### Development (.env)
```env
APP_NAME="Loan System"
APP_ENV=local
APP_KEY=base64:generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=loan_system
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:5173
```

### Production (.env)
```env
APP_NAME="Loan System"
APP_ENV=production
APP_KEY=base64:your-production-key
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=loan_system_prod
DB_USERNAME=your-db-user
DB_PASSWORD=your-secure-password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=your-redis-host
REDIS_PASSWORD=your-redis-password
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls

SANCTUM_STATEFUL_DOMAINS=your-frontend-domain.com
```

## 🔍 Troubleshooting

### Common Issues

1. **Storage Permission**
```bash
chmod -R 775 storage bootstrap/cache
```

2. **Sanctum CORS Issues**
```bash
php artisan config:clear
php artisan cache:clear
```

3. **Queue Not Processing**
```bash
php artisan queue:restart
php artisan horizon:terminate
```

4. **Database Connection**
```bash
php artisan migrate:status
php artisan db:show
```

## 📚 Useful Commands

```bash
# Clear all caches
php artisan optimize:clear

# Generate IDE helper files
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate

# Database operations
php artisan migrate:fresh --seed
php artisan db:seed

# Queue operations
php artisan queue:work --tries=3
php artisan queue:failed
php artisan queue:retry all

# Storage operations
php artisan storage:link
```

Hướng dẫn này cung cấp foundation hoàn chỉnh để setup Laravel backend cho hệ thống cho vay trực tuyến!