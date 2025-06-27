# Tóm tắt OrderController - Quản lý Orders

## 🎯 Tổng quan

OrderController cung cấp đầy đủ chức năng CRUD cho việc quản lý orders dựa theo distributor, với các tính năng lọc, tìm kiếm, thống kê và export dữ liệu.

## 📋 Các Method chính

### 1. **index()** - Danh sách tất cả orders
```php
public function index(Request $request): View
```
**Chức năng:**
- Hiển thị danh sách tất cả orders với pagination
- Lọc theo distributor, cấp độ, thời gian, giá trị
- Tìm kiếm theo bill_code
- Thống kê tổng quan

**Filters hỗ trợ:**
- `distributor_id` - Lọc theo distributor
- `distributor_level` - Lọc theo cấp độ
- `start_date`, `end_date` - Lọc theo khoảng thời gian
- `min_amount`, `max_amount` - Lọc theo khoảng giá
- `bill_code` - Tìm kiếm theo mã hóa đơn

### 2. **byDistributor()** - Orders theo distributor
```php
public function byDistributor($distributorId): View
```
**Chức năng:**
- Hiển thị tất cả orders của một distributor cụ thể
- Tính toán tổng doanh số, số lượng orders, giá trị trung bình

### 3. **byLevel()** - Orders theo cấp độ
```php
public function byLevel($level): View
```
**Chức năng:**
- Hiển thị tất cả orders theo cấp độ distributor
- Thống kê theo cấp độ

### 4. **show()** - Chi tiết order
```php
public function show($id): View
```
**Chức năng:**
- Hiển thị chi tiết một order cụ thể
- Bao gồm thông tin distributor

### 5. **create()** - Form tạo mới
```php
public function create(): View
```
**Chức năng:**
- Hiển thị form tạo order mới
- Danh sách distributors để chọn

### 6. **store()** - Lưu order mới
```php
public function store(Request $request): RedirectResponse
```
**Validation:**
- `distributor_id` - Bắt buộc, phải tồn tại trong bảng distributors
- `amount` - Bắt buộc, số dương
- `sale_time` - Bắt buộc, định dạng ngày
- `bill_code` - Bắt buộc, unique
- `notes` - Tùy chọn

**Tính năng đặc biệt:**
- Tự động set `distributor_level` dựa trên level của distributor được chọn

### 7. **edit()** - Form chỉnh sửa
```php
public function edit($id): View
```
**Chức năng:**
- Hiển thị form chỉnh sửa order
- Pre-fill dữ liệu hiện tại

### 8. **update()** - Cập nhật order
```php
public function update(Request $request, $id): RedirectResponse
```
**Validation:** Tương tự store() nhưng bill_code có thể trùng với chính nó

### 9. **destroy()** - Xóa order
```php
public function destroy($id): RedirectResponse
```
**Chức năng:**
- Xóa order theo ID
- Redirect về danh sách với thông báo thành công

### 10. **search()** - Tìm kiếm nâng cao
```php
public function search(Request $request): View
```
**Tìm kiếm theo:**
- `q` - Tìm trong bill_code, notes, tên distributor, mã distributor
- `distributor_id` - Lọc theo distributor
- `distributor_level` - Lọc theo cấp độ
- `start_date`, `end_date` - Lọc theo thời gian

### 11. **statistics()** - Thống kê chi tiết
```php
public function statistics(): View
```
**Thống kê bao gồm:**
- Tổng quan: tổng orders, tổng doanh số, giá trị trung bình
- Theo thời gian: hôm nay, tháng này, năm này
- Theo cấp độ: thống kê cho từng level
- Top distributors: 10 distributor có doanh số cao nhất
- Thống kê theo ngày trong tháng

### 12. **export()** - Xuất dữ liệu
```php
public function export(Request $request)
```
**Chức năng:**
- Export orders ra file CSV
- Hỗ trợ các filter tương tự index()
- Bao gồm thông tin distributor

## 🛣️ Routes tương ứng

```php
// Trang chủ quản lý orders
GET /orders

// Hiển thị orders theo distributor
GET /orders/by-distributor/{distributorId}

// Hiển thị orders theo cấp độ
GET /orders/by-level/{level}

// Hiển thị chi tiết order
GET /orders/{id}

// Form tạo mới order
GET /orders/create
POST /orders/store

// Form chỉnh sửa order
GET /orders/{id}/edit
PUT /orders/{id}/update

// Xóa order
DELETE /orders/{id}/delete

// Tìm kiếm orders
GET /orders/search

// Thống kê orders
GET /orders/statistics

// Export orders
GET /orders/export
```

## 🎯 Tính năng đặc biệt

### 1. **Tự động set distributor_level**
- Khi tạo/cập nhật order, hệ thống tự động lấy level của distributor
- Đảm bảo tính nhất quán dữ liệu

### 2. **Lọc và tìm kiếm linh hoạt**
- Hỗ trợ nhiều tiêu chí lọc cùng lúc
- Tìm kiếm full-text trong nhiều trường
- Pagination cho hiệu suất tốt

### 3. **Thống kê toàn diện**
- Thống kê theo nhiều chiều khác nhau
- Phân tích hiệu suất theo cấp độ
- Theo dõi xu hướng theo thời gian

### 4. **Export dữ liệu**
- Xuất ra CSV với đầy đủ thông tin
- Hỗ trợ filter khi export
- Format dữ liệu chuẩn

## 📊 Dữ liệu trả về cho Views

### Cho index():
```php
[
    'orders' => $orders,                    // Danh sách orders với pagination
    'distributors' => $distributors,        // Danh sách distributors cho filter
    'distributorLevels' => $distributorLevels, // Danh sách cấp độ
    'totalOrders' => $totalOrders,          // Tổng số orders
    'totalAmount' => $totalAmount,          // Tổng doanh số
    'todayOrders' => $todayOrders,          // Orders hôm nay
    'todayAmount' => $todayAmount           // Doanh số hôm nay
]
```

### Cho statistics():
```php
[
    'totalOrders' => $totalOrders,          // Tổng orders
    'totalAmount' => $totalAmount,          // Tổng doanh số
    'averageOrderValue' => $averageOrderValue, // Giá trị trung bình
    'todayOrders' => $todayOrders,          // Orders hôm nay
    'todayAmount' => $todayAmount,          // Doanh số hôm nay
    'thisMonthOrders' => $thisMonthOrders,  // Orders tháng này
    'thisMonthAmount' => $thisMonthAmount,  // Doanh số tháng này
    'thisYearOrders' => $thisYearOrders,    // Orders năm này
    'thisYearAmount' => $thisYearAmount,    // Doanh số năm này
    'levelStats' => $levelStats,            // Thống kê theo cấp độ
    'topDistributors' => $topDistributors,  // Top distributors
    'dailyStats' => $dailyStats             // Thống kê theo ngày
]
```

## 🔧 Cách sử dụng

### Truy cập danh sách orders:
```
http://localhost/orders
```

### Lọc theo distributor:
```
http://localhost/orders?distributor_id=1
```

### Lọc theo cấp độ:
```
http://localhost/orders?distributor_level=2
```

### Lọc theo thời gian:
```
http://localhost/orders?start_date=2024-01-01&end_date=2024-12-31
```

### Xem orders của distributor cụ thể:
```
http://localhost/orders/by-distributor/1
```

### Xem orders theo cấp độ:
```
http://localhost/orders/by-level/3
```

### Thống kê:
```
http://localhost/orders/statistics
```

### Export dữ liệu:
```
http://localhost/orders/export?distributor_level=2&start_date=2024-01-01
```

## 📋 Lưu ý quan trọng

1. **Validation đầy đủ** cho tất cả input
2. **Tự động set distributor_level** khi tạo/cập nhật
3. **Pagination** cho hiệu suất tốt với dữ liệu lớn
4. **Eager loading** để tránh N+1 query
5. **Soft delete** có thể được thêm nếu cần
6. **Audit trail** có thể được thêm để theo dõi thay đổi 