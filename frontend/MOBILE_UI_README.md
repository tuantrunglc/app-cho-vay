# App Vay - Mobile UI Documentation

## Tổng quan
Giao diện di động của App Vay được thiết kế tối ưu cho trải nghiệm người dùng trên điện thoại di động, với các tính năng:

- **Responsive Design**: Tối ưu cho màn hình di động (max-width: 428px)
- **PWA Support**: Hỗ trợ Progressive Web App
- **Touch-friendly**: Các nút bấm có kích thước tối thiểu 44px
- **Safe Area**: Hỗ trợ safe area cho các thiết bị có notch

## Cấu trúc Layout

### MobileLayout.vue
- **Header**: Logo và thông tin user/đăng nhập
- **Main Content**: Nội dung chính của từng trang
- **Bottom Navigation**: Thanh điều hướng dưới cùng với 4 tab chính

### Navigation Structure
```
├── Trang chủ (/)
├── Vay tiền (/register)
├── Tra cứu (/lookup)
└── Tài khoản (/profile hoặc /login)
```

## Các View User

### 1. Home.vue
- **Banner Slider**: Hiển thị các ưu đãi và thông tin
- **Loan Calculator**: Tính toán khoản vay với slider và quick buttons
- **Loan Terms**: Chọn thời gian vay
- **Payment Info**: Hiển thị thông tin thanh toán
- **Submit Button**: Nộp đơn vay

### 2. Profile.vue
- **User Info Card**: Thông tin cá nhân và thống kê
- **Menu Items**: Các chức năng chính
  - Khoản vay của tôi
  - Thông tin cá nhân
  - Đổi mật khẩu
  - Hỗ trợ khách hàng
- **Settings**: Cài đặt thông báo
- **Logout**: Đăng xuất

### 3. MyLoans.vue
- **Summary Cards**: Tổng quan khoản vay
- **Filter Tabs**: Lọc theo trạng thái
- **Loans List**: Danh sách khoản vay với:
  - Progress bar cho khoản vay đang hoạt động
  - Thông tin chi tiết
  - Nút thanh toán và xem chi tiết
- **Payment Modal**: Form thanh toán
- **Details Modal**: Chi tiết khoản vay và lịch sử

### 4. Support.vue
- **Quick Contact**: Hotline và Live Chat
- **FAQ Section**: Câu hỏi thường gặp có thể mở rộng
- **Contact Form**: Form gửi yêu cầu hỗ trợ
- **Operating Hours**: Giờ làm việc

### 5. Wallet.vue
- **Balance Card**: Hiển thị số dư hiện tại
- **Quick Actions**: Nạp tiền và rút tiền
- **Transaction History**: Lịch sử giao dịch
- **Deposit/Withdraw Modals**: Form nạp/rút tiền

## Tính năng PWA

### Manifest.json
```json
{
  "name": "App Vay - Vay tiền nhanh chóng",
  "short_name": "App Vay",
  "display": "standalone",
  "orientation": "portrait",
  "theme_color": "#2563eb",
  "shortcuts": [
    {
      "name": "Vay tiền ngay",
      "url": "/register"
    },
    {
      "name": "Tra cứu khoản vay",
      "url": "/lookup"
    }
  ]
}
```

## Styling và CSS

### Tailwind Configuration
- **Mobile-first approach**
- **Custom screens**: mobile, tablet, desktop
- **Safe area support**: padding cho notch
- **Touch-friendly sizing**: min-height và min-width 44px

### Key CSS Classes
```css
.mobile-app {
  max-width: 428px;
  margin: 0 auto;
  background: #f9fafb;
}

.h-safe-top {
  height: env(safe-area-inset-top, 0px);
}

.safe-bottom {
  padding-bottom: env(safe-area-inset-bottom, 0px);
}
```

## Components và Icons

### Icon Components
Tất cả icons được tạo thành components Vue riêng biệt trong `src/components/icons/`:
- HomeIcon, WalletIcon, SupportIcon, UserIcon
- ChevronRightIcon, ChatBubbleLeftRightIcon
- ArrowPathIcon, DocumentTextIcon, etc.

### Reusable Patterns
- **Card Layout**: `bg-white rounded-xl shadow-sm p-4`
- **Button Primary**: `bg-primary-600 text-white py-3 rounded-xl`
- **Modal**: Fixed overlay với form centered
- **Input**: `text-base` để tránh zoom trên iOS

## Performance Optimizations

### Mobile Specific
- **Font size 16px** cho input để tránh zoom trên iOS
- **Tap highlight disabled**: `-webkit-tap-highlight-color: transparent`
- **Smooth scrolling**: `scroll-behavior: smooth`
- **Custom scrollbar**: Ẩn scrollbar trên mobile

### Loading và UX
- **Loading states** cho các form submission
- **Optimistic updates** cho transaction history
- **Error handling** với user-friendly messages

## Cách sử dụng

### Development
```bash
cd frontend
npm install
npm run dev
```

### Build for Production
```bash
npm run build
```

### Testing trên Mobile
1. Sử dụng Chrome DevTools Device Mode
2. Test trên thiết bị thật qua network
3. Kiểm tra PWA features

## Browser Support
- **iOS Safari**: 12+
- **Chrome Mobile**: 70+
- **Samsung Internet**: 10+
- **Firefox Mobile**: 68+

## Notes
- Giao diện được thiết kế mobile-first
- Tất cả interactions đều touch-friendly
- Hỗ trợ dark mode có thể được thêm sau
- Offline support có thể được implement với service worker