# 📋 Tài liệu Nghiệp vụ Chi tiết – Hệ thống Quản lý Phòng Trọ

> **Công nghệ:** Laravel 10 (PHP) · MySQL · VietQR · MoMo Payment  
> **Cập nhật lần cuối:** 2026-04-08

---

## 1. 👥 Phân quyền & Kiểm soát truy cập

### 1.1 Vai trò người dùng (bảng `users`, cột `role`)

| Vai trò | Mô tả |
|---|---|
| `super_admin` | Toàn quyền hệ thống |
| `landlord` | Chủ nhà – quản lý phòng/hợp đồng/hóa đơn của riêng mình |
| `staff` | Nhân viên – duyệt phòng, xử lý bảo trì, xem báo cáo |
| `tenant` | Người thuê – gửi yêu cầu thuê, thanh toán, đánh giá |

### 1.2 Gates (Chính sách phân quyền – `AuthServiceProvider`)

| Gate | Ai có quyền |
|---|---|
| `access-admin` | `super_admin` + `landlord` + `staff` |
| `manage-rooms` | `landlord` |
| `approve-rooms` | `super_admin` + `staff` |
| `manage-contracts` | `landlord` |
| `manage-invoices` | `landlord` |
| `manage-utilities` | `landlord` |
| `manage-rent-requests` | `landlord` + `staff` + `super_admin` |
| `manage-maintenance` | `landlord` + `staff` + `super_admin` |
| `manage-settings` | `super_admin` + `landlord` |
| `manage-commissions` | `super_admin` + `landlord` |
| `manage-users` | `super_admin` |
| `manage-system` | `super_admin` |
| `view-reports` | `super_admin` + `staff` + `landlord` |
| `rent-rooms` | `tenant` |
| `view-own-invoices` | `tenant` |

### 1.3 Middleware `admin`
- Route `/admin/*` được bảo vệ bởi `AdminMiddleware`.
- Kiểm tra: người dùng phải đăng nhập VÀ có Gate `access-admin`.
- Nếu không đủ điều kiện → HTTP 403.

### 1.4 Dashboard theo vai trò
Dashboard tự động phân nhánh khi đăng nhập:

```
super_admin → superAdminDashboard()  : tổng landlord, phòng, tenant, doanh thu, hoa hồng
staff       → staffDashboard()       : phòng chờ duyệt, yêu cầu thuê, bảo trì pending
landlord    → landlordDashboard()    : phòng trống/đã thuê, thu nhập tháng, hoa hồng chờ
tenant      → redirect rooms.index  : về trang tìm phòng
```

---

## 2. 🏠 Nghiệp vụ Quản lý Phòng

### 2.1 Đăng phòng (`RoomController@store`)

**Validation bắt buộc:**
- `name`, `price`, `electricity_price`, `water_price`, `service_fee`
- `province_name`, `district_name`, `ward_name`
- `images.*` : ảnh tối đa 5MB/ảnh

**Logic xử lý:**
```
1. Parse tiện nghi: amenities_text ("wifi, máy lạnh") → split(",") → array JSON
2. Xác định approval_status ban đầu:
   - Nếu là landlord  → approval_status = 'pending'  (cần duyệt)
   - Nếu là super_admin → approval_status = 'approved' (tự duyệt)
3. Tạo bản ghi Room
4. Upload ảnh vào storage/public/rooms/
   - Ảnh đầu tiên trong batch → is_primary = true
   - Các ảnh còn lại          → is_primary = false
5. Flash message khác nhau tùy role:
   - landlord    → "Thêm phòng thành công! Phòng đang chờ duyệt."
   - super_admin → "Thêm phòng thành công!"
```

### 2.2 Phê duyệt phòng (`Admin\RoomController@approve/reject`)

**Gate yêu cầu:** `approve-rooms` (chỉ `super_admin` + `staff`)

```
approve → update approval_status = 'approved'
reject  → update approval_status = 'rejected'
```

> ⚠️ Landlord KHÔNG thể tự duyệt phòng của mình.

### 2.3 Lọc phòng phía người dùng (`RoomController@index`)

Trang chủ chỉ hiển thị phòng thỏa mãn **cả hai điều kiện**:
```
status = 'available'  AND  approval_status = 'approved'
```

**Tính năng lọc:**
- Tìm kiếm theo `name` hoặc `description` (LIKE)
- Lọc theo `max_price` (giá ≤ X)
- Lọc theo `province_name`, `district_name`, `ward_name`

**Logic tỉnh mặc định:**
```
Nếu không truyền param 'province':
  → Lấy province_name của user đang đăng nhập
  → Nếu không có → lấy Setting['default_province'] (mặc định: 'Tỉnh Nghệ An')
Nếu province = '' (chuỗi rỗng) → hiển thị tất cả tỉnh
```

### 2.4 Xem bản đồ (`RoomController@mapRooms`)
- Trả về JSON tất cả phòng `available + approved + có GPS` (tối đa 2.000 bản ghi).
- Mỗi phòng gồm: id, name, price, lat, lng, address, ảnh đại diện, URL chi tiết.

### 2.5 Xem chi tiết phòng (`RoomController@show`)
- Load: images, landlord, reviews (kèm user).
- Kiểm tra `hasActiveRequest`: người dùng đã có yêu cầu `pending` cho phòng này chưa (để ẩn nút "Thuê phòng").
- Kiểm tra `userReview`: người dùng đã từng đánh giá phòng này chưa (để hiển thị form chỉnh sửa).

### 2.6 Xóa phòng (`Admin\RoomController@destroy`)
```
1. Xóa từng file ảnh khỏi disk 'public' (Storage::delete)
2. Xóa bản ghi Room (cascade xóa room_images qua FK)
```

---

## 3. 📝 Nghiệp vụ Yêu cầu Thuê phòng

### 3.1 Tenant gửi yêu cầu (`RentRequestController@store`)

**Điều kiện cần:**
```
1. Phòng phải có status = 'available'
   → Nếu không: redirect về trang phòng, báo lỗi
2. User chưa có yêu cầu 'pending' cho phòng này
   → Nếu có rồi: báo "Bạn đã gửi yêu cầu thuê phòng này rồi."
```

**Nếu hợp lệ:**
```
1. Tạo RentRequest { user_id, room_id, note, status='pending', requested_at=now() }
2. Gửi notification NewRentRequest đến tất cả users có role='admin'
   (⚠️ Lưu ý: query đang lọc role='admin' nhưng hệ thống dùng role='super_admin' → có thể là bug)
3. Redirect về danh sách phòng + flash success
```

### 3.2 Admin duyệt yêu cầu (`Admin\RentRequestController@approve`)

**Guard check:** Nếu là landlord → chỉ được duyệt yêu cầu thuê phòng của mình.

**Điều kiện:** Yêu cầu phải đang ở trạng thái `pending`.

**Khi duyệt (một transaction logic):**
```
1. Validate: start_date (bắt buộc), end_date (nullable, phải sau start_date), deposit, notes
2. Tạo Contract {
     user_id         = rentRequest.user_id,
     room_id         = rentRequest.room_id,
     rent_request_id = rentRequest.id,       ← liên kết về yêu cầu gốc
     start_date, end_date, deposit,
     monthly_rent    = rentRequest.room.price, ← lấy giá phòng tại thời điểm duyệt
     notes,
     status          = 'active'
   }
3. room.update(status = 'rented')
4. rentRequest.update(status = 'approved')
5. Gửi notification RentRequestApproved đến tenant
```

### 3.3 Admin từ chối yêu cầu (`Admin\RentRequestController@reject`)
```
1. Kiểm tra status = 'pending' (không xử lý lại yêu cầu đã xong)
2. rentRequest.update(status = 'rejected')
3. Gửi notification RentRequestRejected đến tenant
```

---

## 4. 📄 Nghiệp vụ Hợp đồng

### 4.1 Tạo hợp đồng trực tiếp (`Admin\ContractController@store`)

Ngoài luồng từ yêu cầu thuê, admin/landlord có thể tạo hợp đồng thủ công:

**Validation:**
- `user_id` (phải là tenant tồn tại trong DB)
- `room_id` (phòng phải `available`)
- `start_date`, `end_date`, `deposit`, `monthly_rent`

**Logic:**
```
1. Kiểm tra phòng phải available → nếu đã rented, báo lỗi
2. Tạo Contract { status = 'active' }
3. room.update(status = 'rented')
```

### 4.2 Kết thúc hợp đồng (`Admin\ContractController@endContract`)
```
1. contract.update(status = 'ended')
2. contract.room.update(status = 'available')
→ Phòng về trạng thái trống, có thể thuê lại
```

---

## 5. 💡 Nghiệp vụ Điện – Nước (`Utility`)

### Nhập chỉ số (`Admin\UtilityController@store`)

**Validation:**
- `electricity_new >= electricity_old` (chỉ số mới phải ≥ chỉ số cũ)
- `water_new >= water_old`

**Logic upsert (updateOrCreate):**
```
Key duy nhất: { room_id, month, year }
→ Nếu đã có bản ghi tháng đó → CẬP NHẬT
→ Nếu chưa có → TẠO MỚI

Tính toán và lưu sẵn:
electricity_amount = (electricity_new - electricity_old) × electricity_price
water_amount       = (water_new - water_old) × water_price
```

**Đơn giá mặc định** lấy từ `Setting`:
- `default_electricity_price` = 3500 VNĐ/kWh
- `default_water_price` = 15000 VNĐ/m³

**Chỉ lấy phòng đang `rented`** khi tạo mới (không nhập điện nước cho phòng trống).

---

## 6. 🧾 Nghiệp vụ Hóa đơn

### 6.1 Tạo hóa đơn (`Admin\InvoiceController@store`)

**Validation:**
- `contract_id`: hợp đồng phải tồn tại và đang `active`
- `month` (1–12), `year` (≥ 2020)
- `room_fee`, `electricity_fee`, `water_fee`, `service_fee` ≥ 0
- `due_date`: tùy chọn, nếu không nhập → mặc định `now() + 15 ngày`

**Logic:**
```
1. Kiểm tra landlord chỉ được tạo hóa đơn cho phòng của mình
2. Tính: total_amount = room_fee + electricity_fee + water_fee + service_fee
3. Tạo Invoice {
     transaction_id = 'INV-' + random(8 ký tự viết hoa),
     status = 'unpaid'
   }
4. Gửi notification InvoiceCreated đến tenant (contract.user)
```

**API lấy dữ liệu điện nước (`getUtilityData`):**
- Gọi `GET /admin/invoices/utility-data?room_id=X&month=Y&year=Z`
- Trả về JSON bản ghi Utility tương ứng (để auto-fill form tạo hóa đơn)

### 6.2 Xác nhận thanh toán (`confirmPayment`)

**Điều kiện:**
```
- Hóa đơn chưa được thanh toán (isPaid() = false)
  → Nếu đã paid: báo lỗi "Hóa đơn đã được thanh toán trước đó!"
```

**Khi xác nhận thành công:**
```
1. invoice.update {
     status         = 'paid',
     payment_method = request.payment_method,
     payment_ref    = request.payment_ref,
     paid_at        = now()
   }
2. Tính hoa hồng: rate = 5%
   commission_amount = total_amount × 5 / 100
3. AdminCommission.create {
     landlord_id = invoice.room.landlord_id,
     invoice_id  = invoice.id,
     amount      = commission_amount,
     rate        = 5,
     status      = 'pending'
   }
```

### 6.3 Hủy hóa đơn (`cancel`)
```
- Nếu invoice.isPaid() → báo lỗi "Không thể hủy hóa đơn đã thanh toán!"
- Nếu chưa paid → update status = 'cancelled'
```

### 6.4 Quy tắc hóa đơn quá hạn (`isOverdue()`)
```php
return status != 'paid'
    && status != 'cancelled'
    && due_date != null
    && due_date.isPast()   ← ngày hết hạn đã qua
```

### 6.5 Thanh toán điện tử

**VietQR** – tạo URL ảnh QR:
```
https://img.vietqr.io/image/{bank_id}-{account_no}-compact2.png
  ?amount={total_amount}
  &addInfo=Thanh toan hoa don phong {room.name} thang {month}/{year}
  &accountName={site_name}
```

**MoMo** – tạo deep-link:
```
https://nhantien.momo.vn/{momo_number}
  ?amount={total_amount}
  &note=Thanh toan phong {room.name} thang {month}/{year}
```

### 6.6 Tenant xem hóa đơn của mình (`InvoiceController`)
```
Lấy hóa đơn qua: Invoice → Contract → user_id = auth().id
Kiểm tra ownership ở invoice.show: invoice.contract.user_id == auth().id
```

---

## 7. 💰 Nghiệp vụ Hoa hồng Admin (`AdminCommission`)

- Mỗi khi xác nhận thanh toán hóa đơn → 1 bản ghi hoa hồng được tạo.
- **Tỷ lệ cố định: 5%** (hardcode trong `confirmPayment`).
- Trạng thái ban đầu: `pending` (chờ thu).
- Super Admin có thể xem và quản lý các khoản hoa hồng này qua `CommissionController`.

**Thống kê trên Dashboard (Super Admin):**
```
totalRevenue       = SUM(Invoice.total_amount WHERE status='paid')
totalCommissions   = SUM(AdminCommission.amount WHERE status='paid')
pendingCommissions = SUM(AdminCommission.amount WHERE status='pending')
```

---

## 8. 🔧 Nghiệp vụ Yêu cầu Bảo trì

### 8.1 Tenant gửi yêu cầu (`MaintenanceController@store`)

**Điều kiện bắt buộc:**
```
User phải có Contract { user_id = auth().id, room_id = request.room_id, status = 'active' }
→ Chỉ người đang THỰC SỰ thuê phòng mới được gửi yêu cầu bảo trì
```

**Validation:**
- `title`, `description` bắt buộc
- `priority`: `low | medium | high | urgent`
- `images`: tối đa 5 ảnh, mỗi ảnh ≤ 3MB, định dạng jpeg/png/jpg/webp

**Danh sách phòng trong form:** Lấy từ các active contract của user → pluck room.

### 8.2 Admin cập nhật trạng thái (`Admin\MaintenanceController@updateStatus`)

```
Validation: status ∈ ['pending', 'in_progress', 'done', 'rejected']

Logic đặc biệt khi status = 'done':
→ Tự động ghi resolved_at = now()
```

**Stats hiển thị trên trang danh sách:**
```
pending     : số yêu cầu chưa xử lý
in_progress : số đang xử lý
done        : số đã hoàn thành
(Landlord chỉ thấy yêu cầu của phòng mình)
```

---

## 9. ⭐ Nghiệp vụ Đánh giá Phòng

### Gửi / Cập nhật đánh giá (`RoomReviewController@store`)

**Validation:**
- `rating`: 1–5 sao (bắt buộc)
- `title`: tùy chọn, tối đa 100 ký tự
- `comment`: bắt buộc, tối thiểu 10 ký tự, tối đa 1000 ký tự

**Logic upsert:**
```
Tìm review cũ: { room_id, user_id = auth().id }
→ Nếu đã có: CẬP NHẬT (rating, title, comment) → "Đã cập nhật đánh giá của bạn!"
→ Nếu chưa có: TẠO MỚI → "Cảm ơn bạn đã đánh giá!"
```
> Mỗi user chỉ có **duy nhất 1 đánh giá** cho mỗi phòng.

### Xóa đánh giá
```
Chỉ được xóa nếu: review.user_id == auth().id  HOẶC  auth().user.isAdmin()
```

### Điểm trung bình
```php
averageRating() = round(reviews()->avg('rating') ?? 0, 1)
// Hiển thị theo dạng: 4.5/5.0
```

---

## 10. 📊 Nghiệp vụ Báo cáo (`ReportController`)

Báo cáo cung cấp các chỉ số:

| Chỉ số | Cách tính |
|---|---|
| **Doanh thu 12 tháng** | `SUM(Invoice.total_amount)` theo từng tháng, lọc `status='paid'` |
| **Tỷ lệ lấp đầy** | `(số phòng rented / tổng phòng) × 100` |
| **Công nợ** | Danh sách hóa đơn `status='unpaid'` + tổng số tiền |
| **Hợp đồng sắp hết hạn** | `active contracts` có `end_date` trong vòng **30 ngày tới** |
| **Doanh thu tháng/năm hiện tại** | Filter tháng/năm hiện tại |

> Landlord chỉ thấy dữ liệu phòng của mình; Super Admin/Staff thấy toàn hệ thống.

---

## 11. ⚙️ Cài đặt Hệ thống (`Setting`)

Bảng `settings` dạng key-value, truy xuất qua `Setting::get('key', 'default')`:

| Key | Mô tả | Mặc định |
|---|---|---|
| `site_name` | Tên hiển thị hệ thống | `'Nha Tro'` |
| `vietqr_bank_id` | Mã ngân hàng VietQR | `'MB'` |
| `vietqr_account_no` | Số tài khoản | `''` |
| `momo_number` | SĐT ví MoMo | `''` |
| `default_electricity_price` | Đơn giá điện mặc định | `3500` VNĐ/kWh |
| `default_water_price` | Đơn giá nước mặc định | `15000` VNĐ/m³ |
| `default_province` | Tỉnh lọc mặc định | `'Tỉnh Nghệ An'` |

---

## 12. 🗺️ Địa danh & 🔔 Thông báo & 🖼️ Slider

### Địa danh
- 3 bảng: `provinces` → `districts` (FK: province_id) → `wards` (FK: district_id)
- Controller `RegionController` cung cấp API JSON trả về danh sách theo cấp (dùng cho select dropdown)
- Phòng lưu tên string (`province_name`, `district_name`, `ward_name`) + `address_detail`

### Thông báo (Notification)
| Event | Notification Class | Gửi đến |
|---|---|---|
| Tạo hóa đơn | `InvoiceCreated` | Tenant |
| Duyệt yêu cầu thuê | `RentRequestApproved` | Tenant |
| Từ chối yêu cầu thuê | `RentRequestRejected` | Tenant |
| Có yêu cầu thuê mới | `NewRentRequest` | Admin |
| Hợp đồng sắp hết hạn | `ContractExpiring` | (import trong ReportController) |

### Slider
- Admin quản lý banner/slider ảnh hiển thị trang chủ (`SliderController`).

---

## 🔄 Sơ đồ Luồng Nghiệp vụ Đầy đủ

```
┌──────────────── LUỒNG CHÍNH ────────────────────────────────────────────┐
│                                                                          │
│  [Landlord/SuperAdmin tạo phòng]                                         │
│     ↓ landlord → approval='pending'                                      │
│     ↓ super_admin → approval='approved' (auto)                           │
│                                                                          │
│  [Staff/SuperAdmin duyệt phòng] → approval='approved'                   │
│     ↓ Phòng hiện trên trang chủ (available + approved)                   │
│                                                                          │
│  [Tenant tìm phòng → Gửi RentRequest]                                   │
│     ↓ Kiểm tra: phòng available? + chưa có pending request?             │
│     ↓ Tạo RentRequest { status='pending' }                              │
│     ↓ Notify → Admins                                                    │
│                                                                          │
│  [Landlord/Admin duyệt RentRequest]                                      │
│     ↓ Tạo Contract { status='active', monthly_rent=room.price }         │
│     ↓ Room.status → 'rented'                                             │
│     ↓ RentRequest.status → 'approved'                                    │
│     ↓ Notify → Tenant (RentRequestApproved)                              │
│                                                                          │
│  [Hàng tháng: Nhập chỉ số điện nước (updateOrCreate)]                   │
│     ↓ electricity_amount & water_amount được tính sẵn                    │
│                                                                          │
│  [Tạo Hóa đơn tháng]                                                    │
│     ↓ total = room_fee + elec_fee + water_fee + service_fee             │
│     ↓ transaction_id = 'INV-XXXXXXXX'                                   │
│     ↓ due_date = now() + 15 ngày                                         │
│     ↓ status = 'unpaid'                                                  │
│     ↓ Notify → Tenant (InvoiceCreated)                                   │
│                                                                          │
│  [Tenant thanh toán: tiền mặt / VietQR / MoMo]                         │
│                                                                          │
│  [Admin xác nhận thanh toán]                                             │
│     ↓ Invoice.status → 'paid', paid_at = now()                          │
│     ↓ Tự động ghi AdminCommission { amount = total×5%, status='pending' }│
│                                                                          │
│  [Kết thúc hợp đồng]                                                    │
│     ↓ Contract.status → 'ended'                                          │
│     ↓ Room.status → 'available'                                          │
│                                                                          │
├──────────────── LUỒNG PHỤ ──────────────────────────────────────────────┤
│                                                                          │
│  [Tenant gửi Bảo trì] ← phải có active contract cho phòng đó           │
│     ↓ pending → in_progress → done (resolved_at=now()) | rejected       │
│                                                                          │
│  [Tenant đánh giá phòng] ← upsert 1 review/phòng                       │
│     ↓ averageRating = avg(rating) làm tròn 1 chữ thập phân             │
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```
