@extends('layouts.auth')
@section('title', 'Đăng ký tài khoản')
@section('card-class', 'register-card')

@section('content')
    <div class="auth-header">
        <h2>Tạo tài khoản</h2>
        <p>Tham gia cùng hàng nghìn người dùng</p>
    </div>

    <form method="POST" action="{{ route('register') }}" id="registerForm" class="auth-form">
        @csrf

        {{-- Role Selection --}}
        <div class="role-selector">
            @php $currentRole = old('role', 'tenant'); @endphp
            <button type="button"
                class="role-btn {{ $currentRole == 'tenant' ? 'active' : '' }}"
                onclick="setRole('tenant', this)">
                <i class="fa fa-user"></i>
                <span>Người thuê</span>
            </button>
            <button type="button"
                class="role-btn {{ $currentRole == 'landlord' ? 'active' : '' }}"
                onclick="setRole('landlord', this)">
                <i class="fa fa-home"></i>
                <span>Chủ trọ</span>
            </button>
            <input type="hidden" name="role" id="role_input" value="{{ $currentRole }}">
        </div>

        {{-- Họ tên & SĐT --}}
        <div class="form-row-2">
            <div class="form-group">
                <label for="name"><i class="fa fa-user"></i> Họ và tên</label>
                <input id="name" type="text" name="name"
                    class="form-input @error('name') is-invalid @enderror"
                    value="{{ old('name') }}" required autofocus placeholder="Nguyễn Văn A">
                @error('name') <span class="error-msg">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="phone"><i class="fa fa-phone"></i> Số điện thoại</label>
                <input id="phone" type="text" name="phone"
                    class="form-input @error('phone') is-invalid @enderror"
                    value="{{ old('phone') }}" required placeholder="0987654321">
                @error('phone') <span class="error-msg">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email"><i class="fa fa-envelope"></i> Email</label>
            <input id="email" type="email" name="email"
                class="form-input @error('email') is-invalid @enderror"
                value="{{ old('email') }}" required placeholder="email@example.com">
            @error('email') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        {{-- Khu vực --}}
        <div class="section-divider">
            <span><i class="fa fa-map-marker-alt"></i> Khu vực của bạn</span>
            <small>Dùng để hiển thị phòng gần bạn</small>
        </div>

        <div class="form-row-3">
            <div class="form-group">
                <label for="province"><i class="fa fa-city"></i> Tỉnh / Thành phố</label>
                <select id="province" name="province_name"
                    class="form-input form-select-custom @error('province_name') is-invalid @enderror"
                    required>
                    <option value="">Chọn Tỉnh/TP</option>
                </select>
                @error('province_name') <span class="error-msg">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="district"><i class="fa fa-map"></i> Quận / Huyện</label>
                <select id="district" name="district_name"
                    class="form-input form-select-custom @error('district_name') is-invalid @enderror"
                    required disabled>
                    <option value="">Chọn Quận/Huyện</option>
                </select>
                @error('district_name') <span class="error-msg">{{ $message }}</span> @enderror
            </div>
            <div class="form-group" id="wardWrapper" style="display:none;">
                <label for="ward"><i class="fa fa-map-pin"></i> Phường / Xã <small class="text-muted">(Tùy chọn)</small></label>
                <select id="ward" name="ward_name"
                    class="form-input form-select-custom"
                    disabled>
                    <option value="">Chọn Phường/Xã</option>
                </select>
            </div>
        </div>

        {{-- Địa chỉ chi tiết --}}
        <div class="form-group">
            <label for="address_detail"><i class="fa fa-map-marker-alt"></i> Địa chỉ chi tiết <small class="text-muted">(Tùy chọn)</small></label>
            <input id="address_detail" type="text" name="address_detail"
                class="form-input @error('address_detail') is-invalid @enderror"
                value="{{ old('address_detail') }}"
                placeholder="Số nhà, tên đường, khu phố...">
            @error('address_detail') <span class="error-msg">{{ $message }}</span> @enderror
        </div>

        {{-- Mật khẩu --}}
        <div class="form-row-2">
            <div class="form-group">
                <label for="password"><i class="fa fa-lock"></i> Mật khẩu</label>
                <div class="input-eye-wrap">
                    <input id="password" type="password" name="password"
                        class="form-input @error('password') is-invalid @enderror"
                        required autocomplete="new-password" placeholder="Tối thiểu 8 ký tự">
                    <button type="button" class="eye-btn" onclick="togglePwd('password', this)">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                @error('password') <span class="error-msg">{{ $message }}</span> @enderror
            </div>
            <div class="form-group">
                <label for="password_confirmation"><i class="fa fa-lock"></i> Xác nhận mật khẩu</label>
                <div class="input-eye-wrap">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="form-input"
                        required autocomplete="new-password" placeholder="Nhập lại mật khẩu">
                    <button type="button" class="eye-btn" onclick="togglePwd('password_confirmation', this)">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fa fa-user-plus me-2"></i> Tạo tài khoản
        </button>

        <p class="auth-switch">
            Đã có tài khoản?
            <a href="{{ route('login') }}">Đăng nhập</a>
        </p>
    </form>
@endsection

@section('styles')
<style>
    .register-card { max-width: 680px; }

    .section-divider {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        background: #f0f4ff;
        border-radius: 8px;
        border-left: 3px solid #6366f1;
        margin: 4px 0;
    }
    .section-divider span {
        font-size: .82rem;
        font-weight: 600;
        color: #4f46e5;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .section-divider small {
        font-size: .75rem;
        color: #6b7280;
    }

    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
    }
    @media (max-width: 600px) {
        .form-row-3 { grid-template-columns: 1fr; }
    }

    .form-select-custom {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 32px !important;
        cursor: pointer;
    }
    .form-select-custom:disabled {
        background-color: #f9fafb;
        color: #9ca3af;
        cursor: not-allowed;
    }
</style>
@endsection

@section('scripts')
<script>
    function setRole(role, btn) {
        document.getElementById('role_input').value = role;
        document.querySelectorAll('.role-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    (function () {
        const oldProv = @json(old('province_name'));
        const oldDist = @json(old('district_name'));
        const oldWard = @json(old('ward_name'));

        const selProv = document.getElementById('province');
        const selDist = document.getElementById('district');
        const selWard = document.getElementById('ward');
        const wardWrapper = document.getElementById('wardWrapper');

        // ── helpers ──────────────────────────────────
        function addOptions(sel, items, valueKey, labelKey, codeKey) {
            items.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item[valueKey];
                opt.textContent = item[labelKey];
                if (codeKey) opt.dataset.code = item[codeKey];
                sel.appendChild(opt);
            });
        }

        function resetSelect(sel, placeholder) {
            sel.innerHTML = `<option value="">${placeholder}</option>`;
            sel.disabled = true;
        }

        // ── Load tỉnh ────────────────────────────────
        const DEFAULT_PROVINCE = 'Tỉnh Nghệ An';
        const DEFAULT_DISTRICT = 'Thành phố Vinh';

        fetch('/api/regions/provinces')
            .then(r => r.json())
            .then(data => {
                addOptions(selProv, data, 'name', 'name', 'code');

                // Restore giá trị cũ (khi submit lỗi), hoặc chọn mặc định Nghệ An
                const target = oldProv || DEFAULT_PROVINCE;
                selProv.value = target;
                selProv.dispatchEvent(new Event('change'));
            })
            .catch(err => console.error('Load tỉnh lỗi:', err));

        // ── Tỉnh → Huyện ────────────────────────────
        selProv.addEventListener('change', function () {
            const selected = selProv.options[selProv.selectedIndex];
            const code = selected ? selected.dataset.code : null;

            resetSelect(selDist, 'Chọn Quận/Huyện');
            resetSelect(selWard, 'Chọn Phường/Xã');
            if (wardWrapper) wardWrapper.style.display = 'none';
            if (!code) return;

            fetch(`/api/regions/districts/${code}`)
                .then(r => r.json())
                .then(data => {
                    addOptions(selDist, data, 'name', 'name', 'code');
                    selDist.disabled = false;
                    // Restore giá trị cũ hoặc chọn mặc định Thành phố Vinh
                    const targetDist = oldDist || DEFAULT_DISTRICT;
                    selDist.value = targetDist;
                    if (selDist.value) {
                        selDist.dispatchEvent(new Event('change'));
                    }
                })
                .catch(err => console.error('Load huyện lỗi:', err));
        });

        // ── Huyện → Xã ──────────────────────────────
        selDist.addEventListener('change', function () {
            const selected = selDist.options[selDist.selectedIndex];
            const code = selected ? selected.dataset.code : null;

            resetSelect(selWard, 'Chọn Phường/Xã');
            if (wardWrapper) wardWrapper.style.display = 'none';
            if (!code) return;

            fetch(`/api/regions/wards/${code}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) return;
                    addOptions(selWard, data, 'name', 'name', null);
                    selWard.disabled = false;
                    if (wardWrapper) wardWrapper.style.display = '';
                    if (oldWard) selWard.value = oldWard;
                })
                .catch(err => console.error('Load xã lỗi:', err));
        });
    })();
</script>
@endsection
