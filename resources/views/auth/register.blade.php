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

            {{-- Location --}}
            <div class="col-md-4">
                <label class="form-label">Tỉnh / Thành phố</label>
                <select id="province" name="province_name" class="form-select @error('province_name') is-invalid @enderror" required>
                    <option value="">Chọn Tỉnh/TP</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Quận / Huyện</label>
                <select id="district" name="district_name" class="form-select @error('district_name') is-invalid @enderror" required disabled>
                    <option value="">Chọn Quận/Huyện</option>
                </select>
            </div>
            <div class="col-md-4" id="wardWrapper" style="display:none;">
                <label class="form-label">Phường / Xã <small class="text-muted">(Tùy chọn)</small></label>
                <select id="ward" name="ward_name" class="form-select @error('ward_name') is-invalid @enderror" disabled>
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

        <button type="submit" class="btn btn-primary-custom w-100 mt-4 mb-3">Hoàn tất đăng ký</button>

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

    // ─── Parse toạ độ từ nhiều định dạng link ─────
    function extractLatLng(input) {
        input = (input || '').trim();
        let m;
        m = input.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/);
        if (m) return [+m[1], +m[2]];
        m = input.match(/[?&]q=(-?\d+\.?\d*)[,+%20]+(-?\d+\.?\d*)/);
        if (m) return [+m[1], +m[2]];
        m = input.match(/ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
        if (m) return [+m[1], +m[2]];
        m = input.match(/!3d(-?\d+\.?\d*)!4d(-?\d+\.?\d*)/);
        if (m) return [+m[1], +m[2]];
        m = input.match(/^@?(-?\d+\.?\d*)[,\s]+(-?\d+\.?\d*)$/);
        if (m) return [+m[1], +m[2]];
        return null;
    }

    // ─── Bỏ prefix hành chính VN khi so sánh ─────
    function stripVN(s) {
        return (s || '').replace(/^(tỉnh|thành phố|tp\.?|thị xã|thị trấn|quận|huyện|phường|xã)\s+/i, '').trim();
    }

    $(document).ready(function() {
        const $prov = $('#province'), $dist = $('#district'), $ward = $('#ward');

        // Load tỉnh từ local DB API
        fetch('/api/regions/provinces')
            .then(r => r.json())
            .then(data => data.forEach(p =>
                $prov.append(`<option value="${p.name}" data-code="${p.code}">${p.name}</option>`)
            ));

        // Tỉnh → Huyện
        $prov.on('change', function() {
            const code = $(this).find(':selected').attr('data-code');
            $dist.empty().append('<option value="">Chọn Quận/Huyện</option>').prop('disabled', true);
            $ward.empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
            if (!code) return;
            fetch(`/api/regions/districts/${code}`)
                .then(r => r.json())
                .then(d => {
                    d.forEach(x => $dist.append(`<option value="${x.name}" data-code="${x.code}">${x.name}</option>`));
                    $dist.prop('disabled', false);
                });
        });

        // Huyện → Xã
        $dist.on('change', function() {
            const code = $(this).find(':selected').attr('data-code');
            $ward.empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
            $('#wardWrapper').hide(); // ẩn cho đến khi có xã
            if (!code) return;
            fetch(`/api/regions/wards/${code}`)
                .then(r => r.json())
                .then(d => {
                    if (d.length === 0) return; // không có xã → ẩn
                    d.forEach(x => $ward.append(`<option value="${x.name}">${x.name}</option>`));
                    $ward.prop('disabled', false);
                    $('#wardWrapper').show(); // có xã → hiện
                });
        });

        // ─── Fuzzy match option trong select ─────────
        function selectClosest($sel, text) {
            if (!text) return false;
            const lo = text.toLowerCase();
            const loS = stripVN(lo);
            let best = -1, bestVal = '';
            $sel.find('option').each(function() {
                const t  = $(this).text().toLowerCase();
                const tS = stripVN(t);
                let score = 0;
                if (t === lo || tS === loS) score = 100;
                else if (t.includes(lo) || lo.includes(t) || tS.includes(loS) || loS.includes(tS))
                    score = 50 + t.length;
                if (score > best) { best = score; bestVal = $(this).val(); }
            });
            if (best >= 50) { $sel.val(bestVal); return true; }
            return false;
        }

        // ─── Reverse geocode → điền dropdown ─────────
        function fillAddress(lat, lng) {
            const $msg = $('#gmapParseMsg');
            $msg.html('<span style="color:#6366f1;"><i class="fa fa-spinner fa-spin me-1"></i>Đang xác định địa chỉ...</span>');

            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=vi`)
            .then(r => r.json())
            .then(data => {
                const a = data.address || {};
                console.log('[GeoLookup]', JSON.stringify(a));
                const provName = a.state || a.city || '';
                const distName = a.city_district || a.district || (a.state ? a.city : '') || a.county || a.town || '';
                const wardName = a.suburb || a.quarter || a.neighbourhood || a.borough || a.village || a.hamlet || '';
                const road = a.road || a.pedestrian || a.footway || a.street || '';

                // Hàm lấy phần địa chỉ chi tiết từ display_name trước khi gặp ward/district/province
                function extractStreetDetail(displayName, wN, dN, pN) {
                    if (!displayName) return '';
                    const skip = [wN, dN, pN, 'việt nam', 'vietnam']
                        .filter(Boolean).map(s => stripVN(s.toLowerCase()));
                    const parts = displayName.split(', ');
                    const result = [];
                    for (const part of parts) {
                        const pl = stripVN(part.toLowerCase());
                        if (skip.some(sw => sw && (pl.includes(sw) || sw.includes(pl) || pl.length > 2 && sw.length > 2 && (pl.startsWith(sw.slice(0,4)) || sw.startsWith(pl.slice(0,4)))))) break;
                        if (/^\d{5,}$/.test(part.trim())) continue; // bỏ mã bưu chính
                        result.push(part.trim());
                    }
                    return result.join(', ');
                }

                const detail = [a.house_number, road, a.amenity].filter(Boolean).join(' ')
                    || extractStreetDetail(data.display_name, wardName, distName, provName);
                if (detail) $('#address_detail').val(detail);

                // Dùng /api/regions/search để tìm tỉnh chính xác theo tên
                fetch(`/api/regions/search?q=${encodeURIComponent(provName)}&level=province`)
                .then(r => r.json())
                .then(results => {
                    if (!results.length) {
                        $msg.html(`<span style="color:#f59e0b;"><i class="fa fa-warning me-1"></i>Không tìm được tỉnh "${provName}" — chọn thủ công.</span>`);
                        return;
                    }
                    const prov = results[0];
                    $prov.val(prov.name);

                    if (!distName) {
                        $msg.html('<span style="color:#22c55e;"><i class="fa fa-check me-1"></i>Đã điền tỉnh. <span style="color:#f59e0b;">✏️ Nhập số nhà/đường vào ô địa chỉ chi tiết.</span></span>');
                        $('#address_detail').focus();
                        return;
                    }

                    // Bước 2: load districts từ local API rồi chọn
                    fetch(`/api/regions/districts/${prov.code}`)
                    .then(r => r.json())
                    .then(districts => {
                        $dist.empty().append('<option value="">Chọn Quận/Huyện</option>');
                        districts.forEach(d => $dist.append(`<option value="${d.name}" data-code="${d.code}">${d.name}</option>`));
                        $dist.prop('disabled', false);

                        const okD = selectClosest($dist, distName);
                        if (!okD) {
                            $msg.html('<span style="color:#22c55e;"><i class="fa fa-check me-1"></i>Đã điền tỉnh — không tìm được quận/huyện, chọn thủ công.</span>');
                            return;
                        }

                        const distCode = $dist.find('option:selected').attr('data-code');
                        if (!distCode || !wardName) {
                            $msg.html('<span style="color:#22c55e;"><i class="fa fa-check me-1"></i>Đã điền tỉnh & quận/huyện. <span style="color:#f59e0b;">✏️ Nhập số nhà/đường vào ô bên dưới.</span></span>');
                            $('#address_detail').focus();
                            return;
                        }

                        // Bước 3: load wards từ local API rồi chọn
                        fetch(`/api/regions/wards/${distCode}`)
                        .then(r => r.json())
                        .then(wards => {
                            if (!wards.length) {
                                $('#wardWrapper').hide();
                                $msg.html('<span style="color:#22c55e;"><i class="fa fa-check me-1"></i>Đã điền tỉnh & quận/huyện. <span style="color:#f59e0b;">✏️ Nhập số nhà/đường vào ô bên dưới.</span></span>');
                                $('#address_detail').focus();
                                return;
                            }
                            $ward.empty().append('<option value="">Chọn Phường/Xã</option>');
                            wards.forEach(w => $ward.append(`<option value="${w.name}">${w.name}</option>`));
                            $ward.prop('disabled', false);
                            $('#wardWrapper').show();
                            selectClosest($ward, wardName);
                            const hasDetail = !!$('#address_detail').val();
                            $msg.html('<span style="color:#22c55e;"><i class="fa fa-check me-1"></i>Đã điền tỉnh, quận & phường.' + (hasDetail ? '' : ' <span style="color:#f59e0b;">✏️ Nhập số nhà/đường vào ô bên dưới.</span>') + '</span>');
                            if (!hasDetail) $('#address_detail').focus();
                        });
                    });
                });
            })
            .catch(() => {
                $msg.html('<span style="color:#ef4444;"><i class="fa fa-times me-1"></i>Không kết nối được. Hãy chọn địa chỉ thủ công.</span>');
            });
        }

        // ─── Nút "Điền địa chỉ" ──────────────────────
        $('#parseGmapBtn').on('click', function() {
            const val = $('#gmapLinkInput').val().trim();
            const $m  = $('#gmapParseMsg');
            if (!val) { $m.html('<span style="color:#ef4444;">Hãy nhập link hoặc toạ độ.</span>'); return; }
            if (/goo\.gl|maps\.app/i.test(val)) {
                $m.html('<span style="color:#f59e0b;"><i class="fa fa-warning me-1"></i>Link rút gọn không hỗ trợ — mở link, copy URL đầy đủ rồi paste lại.</span>');
                return;
            }
            const c = extractLatLng(val);
            if (!c || Math.abs(c[0]) > 90 || Math.abs(c[1]) > 180) {
                $m.html('<span style="color:#ef4444;">Không đọc được toạ độ. Thử: <code>18.6796, 105.6813</code></span>');
                return;
            }
            fillAddress(c[0], c[1]);
        });

        $('#gmapLinkInput').on('keypress', function(e) {
            if (e.which === 13) { e.preventDefault(); $('#parseGmapBtn').click(); }
        });
    });
</script>
@endsection
