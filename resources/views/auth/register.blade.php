@extends('layouts.auth')
@section('title', 'Đăng ký tài khoản')
@section('card-class', 'register-card')

@section('content')
    <h3 class="text-center mb-4 fw-bold" style="color: #444;">Tham gia cùng chúng tôi</h3>

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        <div class="row g-3">
            {{-- Role Selection --}}
            <div class="col-12 mb-3">
                <label class="form-label d-block text-center fw-bold mb-3">Bạn tham gia với vai trò nào?</label>
                <div class="nav nav-pills nav-pills-custom justify-content-center" id="role-tab" role="tablist">
                    @php $currentRole = old('role', 'tenant'); @endphp
                    <button class="nav-link {{ $currentRole == 'tenant' ? 'active' : '' }} me-2" id="role-tenant-tab" data-bs-toggle="pill" type="button" onclick="setRole('tenant')">
                        <i class="fa fa-user me-2"></i>Tôi là Người thuê
                    </button>
                    <button class="nav-link {{ $currentRole == 'landlord' ? 'active' : '' }}" id="role-landlord-tab" data-bs-toggle="pill" type="button" onclick="setRole('landlord')">
                        <i class="fa fa-home me-2"></i>Tôi là Chủ trọ
                    </button>
                </div>
                <input type="hidden" name="role" id="role_input" value="{{ $currentRole }}">
            </div>

            {{-- Basic Info --}}
            <div class="col-md-6 mt-0">
                <label for="name" class="form-label">Họ và tên</label>
                <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus placeholder="VD: Nguyễn Văn A">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mt-0">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input id="phone" type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required placeholder="VD: 0987654321">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required placeholder="VD: email@example.com">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Tenant/Landlord Specific Info --}}
            <div class="col-md-4">
                <label for="id_card" class="form-label">Số CCCD</label>
                <input id="id_card" type="text" name="id_card" class="form-control @error('id_card') is-invalid @enderror" value="{{ old('id_card') }}" placeholder="12 số">
                @error('id_card') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label for="dob" class="form-label">Ngày sinh</label>
                <input id="dob" type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}">
                @error('dob') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label for="gender" class="form-label">Giới tính</label>
                <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
                    <option value="">Chọn...</option>
                    <option value="Nam" {{ old('gender') == 'Nam' ? 'selected' : '' }}>Nam</option>
                    <option value="Nữ" {{ old('gender') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
                    <option value="Khác" {{ old('gender') == 'Khác' ? 'selected' : '' }}>Khác</option>
                </select>
                @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Location Selection (API) --}}
            <div class="col-md-4 location-selects">
                <label class="form-label">Tỉnh / Thành phố</label>
                <select id="province" name="province_name" class="form-select @error('province_name') is-invalid @enderror" required>
                    <option value="">Chọn Tỉnh/TP</option>
                </select>
            </div>
            <div class="col-md-4 location-selects">
                <label class="form-label">Quận / Huyện</label>
                <select id="district" name="district_name" class="form-select @error('district_name') is-invalid @enderror" required disabled>
                    <option value="">Chọn Quận/Huyện</option>
                </select>
            </div>
            <div class="col-md-4 location-selects">
                <label class="form-label">Phường / Xã</label>
                <select id="ward" name="ward_name" class="form-select @error('ward_name') is-invalid @enderror" required disabled>
                    <option value="">Chọn Phường/Xã</option>
                </select>
            </div>

            <div class="col-12">
                <label for="address_detail" class="form-label">Địa chỉ chi tiết (Thôn/Xóm/Số nhà)</label>
                <input id="address_detail" type="text" name="address_detail" class="form-control @error('address_detail') is-invalid @enderror" value="{{ old('address_detail') }}" placeholder="VD: Số 123, đường ABC">
                @error('address_detail') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Password --}}
            <div class="col-md-6">
                <label for="password" class="form-label">Mật khẩu</label>
                <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
            </div>
        </div>

        <button type="submit" class="btn btn-primary-custom w-100 mt-4 mb-3">
            Hoàn tất đăng ký
        </button>

        <div class="text-center mt-2">
            <span class="text-muted">Đã có tài khoản?</span>
            <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">Đăng nhập</a>
        </div>
    </form>
@endsection

@section('scripts')
<script>
    function setRole(role) {
        document.getElementById('role_input').value = role;
    }

    $(document).ready(function() {
        const provinceSelect = $('#province');
        const districtSelect = $('#district');
        const wardSelect = $('#ward');

        // Load provinces
        fetch('https://provinces.open-api.vn/api/p/')
            .then(response => response.json())
            .then(data => {
                data.forEach(p => {
                    provinceSelect.append(`<option value="${p.name}" data-code="${p.code}">${p.name}</option>`);
                });
            });

        // Load districts when province changes
        provinceSelect.on('change', function() {
            const code = $(this).find(':selected').data('code');
            districtSelect.empty().append('<option value="">Chọn Quận/Huyện</option>').prop('disabled', true);
            wardSelect.empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
            
            if (code) {
                fetch(`https://provinces.open-api.vn/api/p/${code}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.districts.forEach(d => {
                            districtSelect.append(`<option value="${d.name}" data-code="${d.code}">${d.name}</option>`);
                        });
                        districtSelect.prop('disabled', false);
                    });
            }
        });

        // Load wards when district changes
        districtSelect.on('change', function() {
            const code = $(this).find(':selected').data('code');
            wardSelect.empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
            
            if (code) {
                fetch(`https://provinces.open-api.vn/api/d/${code}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        data.wards.forEach(w => {
                            wardSelect.append(`<option value="${w.name}">${w.name}</option>`);
                        });
                        wardSelect.prop('disabled', false);
                    });
            }
        });
    });
</script>
@endsection
