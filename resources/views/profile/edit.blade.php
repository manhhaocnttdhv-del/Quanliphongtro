@extends('layouts.user')
@section('title', 'Hồ sơ cá nhân - ' . \App\Models\Setting::get('site_name','Nhà Trọ'))

@section('styles')
<style>
    .profile-page { background: linear-gradient(180deg,#f8fafc,#eef2f7); min-height: 80vh; padding: 40px 0 60px; }

    /* Sidebar avatar card */
    .profile-sidebar {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,.06);
        padding: 32px 24px;
        text-align: center;
        position: sticky;
        top: 90px;
    }
    .avatar-circle {
        width: 90px; height: 90px;
        background: linear-gradient(135deg,#f59e0b,#d97706);
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        font-size: 36px; color: #fff; font-weight: 700;
        box-shadow: 0 4px 16px rgba(245,158,11,.35);
    }
    .sidebar-name { font-weight: 700; font-size: 18px; color: #1e293b; margin-bottom: 4px; }
    .sidebar-role {
        display: inline-block; padding: 3px 12px;
        border-radius: 20px; font-size: 11px; font-weight: 700;
        background: #fef3c7; color: #92400e; margin-bottom: 16px;
    }
    .sidebar-nav { list-style: none; padding: 0; margin: 0; text-align: left; }
    .sidebar-nav li a {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px; border-radius: 10px;
        color: #475569; text-decoration: none;
        font-size: 13px; font-weight: 600;
        transition: all .2s;
    }
    .sidebar-nav li a:hover,
    .sidebar-nav li a.active { background: #fef3c7; color: #d97706; }
    .sidebar-nav li a i { width: 16px; text-align: center; }

    /* Cards */
    .profile-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,.06);
        padding: 32px 32px 28px;
        margin-bottom: 24px;
    }
    .card-title {
        font-size: 16px; font-weight: 700; color: #1e293b;
        margin-bottom: 4px;
        display: flex; align-items: center; gap: 8px;
    }
    .card-title i { color: #f59e0b; }
    .card-subtitle { font-size: 13px; color: #94a3b8; margin-bottom: 24px; }
    hr.card-divider { border: none; border-top: 1.5px solid #f1f5f9; margin: 0 0 24px; }

    /* Form controls */
    .profile-card .form-label {
        font-size: 11px; font-weight: 700;
        color: #64748b; text-transform: uppercase; letter-spacing: .4px;
        margin-bottom: 6px;
    }
    .profile-card .form-control,
    .profile-card .form-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        background: #f8fafc;
        transition: all .2s;
    }
    .profile-card .form-control:focus,
    .profile-card .form-select:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245,158,11,.12);
        background: #fff;
    }
    .profile-card .form-select:disabled { background: #f1f5f9; color: #94a3b8; }

    /* Buttons */
    .btn-save {
        background: linear-gradient(135deg,#f59e0b,#d97706);
        border: none; color: #fff;
        padding: 10px 28px; border-radius: 10px;
        font-weight: 700; font-size: 14px;
        transition: all .2s;
    }
    .btn-save:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(245,158,11,.4); color: #fff; }
    .btn-danger-outline {
        border: 1.5px solid #ef4444; color: #ef4444;
        padding: 10px 28px; border-radius: 10px;
        font-weight: 700; font-size: 14px; background: transparent;
        transition: all .2s;
    }
    .btn-danger-outline:hover { background: #ef4444; color: #fff; }

    /* Alert */
    .alert-success-inline {
        background: #f0fdf4; border: 1.5px solid #86efac;
        border-radius: 10px; padding: 10px 16px;
        font-size: 13px; color: #166534; font-weight: 600;
    }
</style>
@endsection

@section('content')
<div class="profile-page">
    <div class="container">
        <div class="row g-4">

            {{-- ===== SIDEBAR ===== --}}
            <div class="col-lg-3">
                <div class="profile-sidebar">
                    <div class="avatar-circle">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="sidebar-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-role">
                        @switch(auth()->user()->role)
                            @case('admin') 👑 Quản trị @break
                            @case('staff') 🛡️ Nhân viên @break
                            @case('landlord') 🏠 Chủ trọ @break
                            @default 👤 Người dùng
                        @endswitch
                    </div>
                    <ul class="sidebar-nav">
                        <li>
                            <a href="{{ route('profile.edit') }}" class="active">
                                <i class="fa fa-user"></i> Hồ sơ cá nhân
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('profile.edit') }}#password">
                                <i class="fa fa-lock"></i> Đổi mật khẩu
                            </a>
                        </li>
                        @if(auth()->user()->role === 'landlord')
                        <li>
                            <a href="{{ route('landlord.rooms.index') }}">
                                <i class="fa fa-home"></i> Phòng của tôi
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ route('home') }}">
                                <i class="fa fa-arrow-left"></i> Về trang chủ
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- ===== MAIN CONTENT ===== --}}
            <div class="col-lg-9">

                {{-- Flash messages --}}
                @if(session('status') === 'profile-updated')
                    <div class="alert-success-inline mb-3">
                        <i class="fa fa-check-circle me-2"></i>Đã lưu thông tin cá nhân thành công!
                    </div>
                @endif
                @if(session('status') === 'password-updated')
                    <div class="alert-success-inline mb-3">
                        <i class="fa fa-check-circle me-2"></i>Đã đổi mật khẩu thành công!
                    </div>
                @endif

                {{-- === Thông tin cá nhân === --}}
                <div class="profile-card">
                    <div class="card-title"><i class="fa fa-user"></i> Thông tin cá nhân</div>
                    <div class="card-subtitle">Cập nhật tên, email và các thông tin cơ bản</div>
                    <hr class="card-divider">

                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên *</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone) }}" placeholder="0912 345 678">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số CCCD / CMND</label>
                                <input type="text" name="id_card" class="form-control @error('id_card') is-invalid @enderror"
                                    value="{{ old('id_card', $user->id_card) }}" placeholder="0123456789">
                                @error('id_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Ngày sinh</label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror"
                                    value="{{ old('dob', $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '') }}">
                                @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Giới tính</label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">-- Chọn --</option>
                                    <option value="male"   {{ old('gender',$user->gender)==='male'   ? 'selected':'' }}>Nam</option>
                                    <option value="female" {{ old('gender',$user->gender)==='female' ? 'selected':'' }}>Nữ</option>
                                    <option value="other"  {{ old('gender',$user->gender)==='other'  ? 'selected':'' }}>Khác</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Tỉnh / TP</label>
                                <select id="p_province" name="province_name" class="form-select">
                                    <option value="">-- Chọn tỉnh --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quận / Huyện</label>
                                <select id="p_district" name="district_name" class="form-select" disabled>
                                    <option value="">-- Chọn quận --</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phường / Xã</label>
                                <select id="p_ward" name="ward_name" class="form-select" disabled>
                                    <option value="">-- Chọn phường --</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Địa chỉ chi tiết</label>
                            <input type="text" name="address_detail" class="form-control @error('address_detail') is-invalid @enderror"
                                value="{{ old('address_detail', $user->address_detail) }}" placeholder="Số nhà, tên đường...">
                            @error('address_detail')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-save">
                            <i class="fa fa-save me-2"></i>Lưu thông tin
                        </button>
                    </form>
                </div>

                {{-- === Đổi mật khẩu === --}}
                <div class="profile-card" id="password">
                    <div class="card-title"><i class="fa fa-lock"></i> Đổi mật khẩu</div>
                    <div class="card-subtitle">Đặt mật khẩu mới, tối thiểu 8 ký tự</div>
                    <hr class="card-divider">

                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="current_password"
                                    class="form-control @error('current_password','updatePassword') is-invalid @enderror"
                                    autocomplete="current-password">
                                @error('current_password','updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="password"
                                    class="form-control @error('password','updatePassword') is-invalid @enderror"
                                    autocomplete="new-password">
                                @error('password','updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" name="password_confirmation"
                                    class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-save">
                            <i class="fa fa-key me-2"></i>Đổi mật khẩu
                        </button>
                    </form>
                </div>

                {{-- === Xóa tài khoản === --}}
                <div class="profile-card border border-danger border-opacity-25">
                    <div class="card-title" style="color:#ef4444;"><i class="fa fa-exclamation-triangle" style="color:#ef4444;"></i> Xóa tài khoản</div>
                    <div class="card-subtitle">Hành động này không thể hoàn tác. Tất cả dữ liệu sẽ bị xóa vĩnh viễn.</div>
                    <hr class="card-divider">

                    <button type="button" class="btn btn-danger-outline" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fa fa-trash me-2"></i>Xóa tài khoản của tôi
                    </button>
                </div>

            </div>{{-- /col-lg-9 --}}
        </div>{{-- /row --}}
    </div>{{-- /container --}}
</div>

{{-- Delete Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="fa fa-exclamation-triangle me-2"></i>Xác nhận xóa tài khoản
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-muted mb-3" style="font-size:13px;">
                    Vui lòng nhập mật khẩu để xác nhận bạn muốn xóa tài khoản vĩnh viễn.
                </p>
                <form method="post" action="{{ route('profile.destroy') }}" id="deleteForm">
                    @csrf
                    @method('delete')
                    <label class="form-label" style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Mật khẩu</label>
                    <input type="password" name="password" required
                        class="form-control @error('password','userDeletion') is-invalid @enderror"
                        style="border:1.5px solid #e2e8f0;border-radius:10px;">
                    @error('password','userDeletion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="deleteForm" class="btn btn-danger rounded-3 fw-bold">
                    <i class="fa fa-trash me-1"></i>Xóa vĩnh viễn
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    const savedProvince = @json(old('province_name', $user->province_name ?? ''));
    const savedDistrict = @json(old('district_name', $user->district_name ?? ''));
    const savedWard     = @json(old('ward_name',     $user->ward_name     ?? ''));

    const pSel = document.getElementById('p_province');
    const dSel = document.getElementById('p_district');
    const wSel = document.getElementById('p_ward');

    function loadDistricts(provinceName, selectVal) {
        dSel.innerHTML = '<option value="">-- Chọn quận --</option>';
        wSel.innerHTML = '<option value="">-- Chọn phường --</option>';
        dSel.disabled = true; wSel.disabled = true;
        if (!provinceName) return;
        const opt = Array.from(pSel.options).find(o => o.value === provinceName);
        if (!opt) return;
        fetch('/api/regions/districts/' + opt.dataset.code)
            .then(r => r.json()).then(data => {
                data.forEach(d => {
                    const o = new Option(d.name, d.name);
                    o.dataset.code = d.code;
                    if (d.name === selectVal) o.selected = true;
                    dSel.appendChild(o);
                });
                dSel.disabled = false;
                if (selectVal) loadWards(selectVal, savedWard);
            });
    }

    function loadWards(districtName, selectVal) {
        wSel.innerHTML = '<option value="">-- Chọn phường --</option>';
        wSel.disabled = true;
        if (!districtName) return;
        const opt = Array.from(dSel.options).find(o => o.value === districtName);
        if (!opt) return;
        fetch('/api/regions/wards/' + opt.dataset.code)
            .then(r => r.json()).then(data => {
                data.forEach(w => {
                    const o = new Option(w.name, w.name);
                    if (w.name === selectVal) o.selected = true;
                    wSel.appendChild(o);
                });
                wSel.disabled = false;
            });
    }

    fetch('/api/regions/provinces')
        .then(r => r.json()).then(data => {
            data.forEach(p => {
                const o = new Option(p.name, p.name);
                o.dataset.code = p.code;
                if (p.name === savedProvince) o.selected = true;
                pSel.appendChild(o);
            });
            if (savedProvince) loadDistricts(savedProvince, savedDistrict);
        });

    pSel.addEventListener('change', () => loadDistricts(pSel.value, ''));
    dSel.addEventListener('change', () => loadWards(dSel.value, ''));

    // Auto-open delete modal if there's a deletion error
    @if($errors->userDeletion->isNotEmpty())
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
    @endif
})();
</script>
@endsection
