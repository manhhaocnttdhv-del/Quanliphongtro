@extends('layouts.admin')
@section('title', 'Cài Đặt Hệ Thống')

@section('content')
<div class="mb-4">
    <h1 class="fs-3 mb-1">Cài đặt hệ thống</h1>
    <p class="text-muted">Cấu hình thông tin nhà trọ và thanh toán</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf

                <h6 class="text-danger mb-3 border-bottom pb-2"><i class="ti ti-home me-1"></i>Trang chủ / Slider Hero</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tiêu đề hộp tìm kiếm</label>
                        <input type="text" class="form-control" name="hero_title"
                               value="{{ $settings['hero_title'] }}" placeholder="Tìm Phòng Trọ">
                        <div class="form-text">Hiển thị trong box tìm kiếm trên slider trang chủ.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mô tả dưới tiêu đề</label>
                        <input type="text" class="form-control" name="hero_subtitle"
                               value="{{ $settings['hero_subtitle'] }}" placeholder="VD: Tìm phòng nhanh, ở sướng hơn">
                    </div>
                </div>

                <h6 class="text-primary mb-3 border-bottom pb-2"><i class="ti ti-building me-1"></i>Thông tin nhà trọ</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tên nhà trọ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="site_name" value="{{ $settings['site_name'] }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Số điện thoại</label>
                        <input type="text" class="form-control" name="site_phone" value="{{ $settings['site_phone'] }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email liên hệ</label>
                        <input type="email" class="form-control" name="site_email" value="{{ $settings['site_email'] ?? '' }}" placeholder="contact@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Địa chỉ</label>
                        <input type="text" class="form-control" name="site_address" value="{{ $settings['site_address'] }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Mô tả website <small class="text-muted">(hiện trên trang chủ)</small></label>
                        <textarea class="form-control" name="site_description" rows="2" placeholder="Hệ thống quản lý phòng trọ hiện đại...">{{ $settings['site_description'] ?? '' }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tỉnh/TP mặc định <small class="text-muted">(cho khách chưa đăng nhập)</small></label>
                        <select class="form-select" name="default_province" id="settingProvince">
                            <option value="">-- Tất cả tỉnh --</option>
                        </select>
                        <div class="form-text">User đã đăng nhập sẽ thấy tỉnh họ đăng ký</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Logo nhà trọ</label>
                        <input type="file" class="form-control" name="logo" accept="image/*">
                        @if(\App\Models\Setting::get('site_logo'))
                            <div class="mt-2"><img src="{{ asset('storage/'.\App\Models\Setting::get('site_logo')) }}" height="50" class="rounded"></div>
                        @endif
                    </div>
                </div>

                <h6 class="text-warning mb-3 border-bottom pb-2"><i class="ti ti-bolt me-1"></i>Giá điện nước mặc định</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Giá điện mặc định (đ/kWh)</label>
                        <input type="number" class="form-control" name="default_electricity_price" value="{{ $settings['default_electricity_price'] }}" required min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Giá nước mặc định (đ/m³)</label>
                        <input type="number" class="form-control" name="default_water_price" value="{{ $settings['default_water_price'] }}" required min="0">
                    </div>
                </div>

                <h6 class="text-success mb-3 border-bottom pb-2"><i class="ti ti-qrcode me-1"></i>Thanh toán VietQR</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Mã ngân hàng</label>
                        <input type="text" class="form-control" name="vietqr_bank_id" value="{{ $settings['vietqr_bank_id'] }}" placeholder="VD: MB, VCB, TCB">
                        <div class="form-text"><a href="https://api.vietqr.io/v2/banks" target="_blank">Xem danh sách mã ngân hàng</a></div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Số tài khoản</label>
                        <input type="text" class="form-control" name="vietqr_account_no" value="{{ $settings['vietqr_account_no'] }}" placeholder="Nhập số tài khoản ngân hàng">
                    </div>
                </div>

                <h6 class="mb-3 border-bottom pb-2" style="color:#ae2d68;"><i class="ti ti-brand-mastercard me-1"></i>Thanh toán MoMo</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Số điện thoại MoMo</label>
                        <input type="text" class="form-control" name="momo_number" value="{{ $settings['momo_number'] }}" placeholder="0901234567">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        @if($settings['momo_number'])
                            <div class="p-2 rounded" style="background:#f9f0ff;border:1px solid #ae2d68;">
                                <img src="https://img.vietqr.io/image/{{ $settings['vietqr_bank_id'] }}-{{ $settings['vietqr_account_no'] }}-compact2.png?amount=0&addInfo=Test"
                                     alt="VietQR Preview" style="max-height:80px;" onerror="this.style.display='none'">
                            </div>
                        @endif
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4"><i class="ti ti-device-floppy me-1"></i>Lưu cài đặt</button>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4">
            <h6 class="mb-3">📱 Preview QR VietQR</h6>
            @if($settings['vietqr_account_no'])
                <img src="https://img.vietqr.io/image/{{ $settings['vietqr_bank_id'] }}-{{ $settings['vietqr_account_no'] }}-compact2.png?amount=1000000&addInfo=Thanh+toan+phong+tro"
                     alt="VietQR" class="img-fluid rounded" style="border:2px solid #e9ecef;padding:8px;">
                <div class="mt-2 small text-muted text-center">
                    {{ $settings['vietqr_bank_id'] }} · {{ $settings['vietqr_account_no'] }}
                </div>
            @else
                <div class="text-center text-muted py-4">Nhập thông tin tài khoản để xem QR</div>
            @endif
        </div>
        <div class="card p-4 mt-3">
            <h6 class="mb-3" style="color:#ae2d68;">📲 Preview MoMo</h6>
            @if($settings['momo_number'])
                <div class="text-center">
                    <a href="https://nhantien.momo.vn/{{ $settings['momo_number'] }}" target="_blank"
                       class="btn btn-lg px-4 py-3 fw-bold" style="background:linear-gradient(135deg,#ae2d68,#d82d8b);color:#fff;border-radius:20px;">
                        <i class="ti ti-brand-mastercard me-1"></i> Mở MoMo
                    </a>
                    <div class="mt-2 small text-muted">{{ $settings['momo_number'] }}</div>
                </div>
            @else
                <div class="text-center text-muted py-4">Nhập số MoMo để xem preview</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Load provinces for default_province dropdown
const savedProv = @json(\App\Models\Setting::get('default_province', ''));
fetch('/api/regions/provinces')
    .then(r => r.json())
    .then(data => {
        const sel = document.getElementById('settingProvince');
        data.forEach(p => {
            const o = new Option(p.name, p.name);
            if (p.name === savedProv) o.selected = true;
            sel.appendChild(o);
        });
    });
</script>
@endpush
@endsection
