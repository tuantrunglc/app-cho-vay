# App Vay - Frontend

Ứng dụng vay tiền trực tuyến được xây dựng với Vue 3, Vite, và Tailwind CSS.

## Cấu trúc dự án

```
frontend/
├── src/
│   ├── components/          # Các component tái sử dụng
│   │   ├── common/         # Component chung
│   │   ├── forms/          # Form components
│   │   └── icons/          # Icon components
│   ├── layouts/            # Layout components
│   ├── views/              # Các trang chính
│   │   ├── customer/       # Trang khách hàng
│   │   └── admin/          # Trang admin
│   ├── stores/             # Pinia stores
│   ├── router/             # Vue Router config
│   ├── assets/             # Static assets
│   └── styles/             # CSS/SCSS files
├── public/                 # Public assets
└── dist/                   # Build output
```

## Tính năng chính

### Khách hàng
- **Trang chủ**: Hiển thị banner, thông tin sản phẩm
- **Đăng ký vay**: Form đăng ký khoản vay
- **Tra cứu**: Kiểm tra trạng thái đơn vay
- **Hồ sơ**: Quản lý thông tin cá nhân

### Admin
- **Dashboard**: Tổng quan hệ thống
- **Duyệt đơn vay**: Quản lý và duyệt đơn vay
- **Khoản vay hoạt động**: Theo dõi các khoản vay đang hoạt động
- **Quản lý khách hàng**: CRUD khách hàng
- **Quản lý nội dung**: Cấu hình banner, lãi suất, hạn mức

## Công nghệ sử dụng

- **Vue 3**: Framework JavaScript
- **Vite**: Build tool
- **Vue Router**: Routing
- **Pinia**: State management
- **Tailwind CSS**: CSS framework
- **Heroicons**: Icon library

## Cài đặt và chạy

```bash
# Cài đặt dependencies
npm install

# Chạy development server
npm run dev

# Build production
npm run build

# Preview production build
npm run preview
```

## Stores (Pinia)

### AuthStore
- Quản lý authentication
- Login/logout
- User session

### LoanStore
- Quản lý đơn vay
- CRUD loan applications
- Loan status management

### UserStore
- Quản lý thông tin user
- Customer management
- User profiles

### ContentStore
- Quản lý nội dung
- Banner management
- System configuration

## Routing

### Customer Routes
- `/` - Trang chủ
- `/register` - Đăng ký vay
- `/lookup` - Tra cứu đơn vay
- `/profile` - Hồ sơ cá nhân

### Admin Routes
- `/admin` - Dashboard
- `/admin/loan-approval` - Duyệt đơn vay
- `/admin/active-loans` - Khoản vay hoạt động
- `/admin/customers` - Quản lý khách hàng
- `/admin/content` - Quản lý nội dung

## Components

### Common Components
- `AppHeader` - Header chung
- `AppFooter` - Footer chung
- `LoadingSpinner` - Loading indicator
- `Modal` - Modal dialog

### Form Components
- `LoanApplicationForm` - Form đăng ký vay
- `CustomerForm` - Form thông tin khách hàng
- `LookupForm` - Form tra cứu

### Layout Components
- `CustomerLayout` - Layout cho khách hàng
- `AdminLayout` - Layout cho admin

## Styling

Sử dụng Tailwind CSS với custom configuration:

### Colors
- Primary: Blue (#3B82F6)
- Success: Green (#10B981)
- Warning: Yellow (#F59E0B)
- Danger: Red (#EF4444)

### Components
- `.btn` - Button styles
- `.input` - Input styles
- `.card` - Card styles

## API Integration

Các store sử dụng mock data, có thể dễ dàng thay thế bằng API calls thực tế.

## Deployment

```bash
# Build production
npm run build

# Deploy dist/ folder to web server
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)