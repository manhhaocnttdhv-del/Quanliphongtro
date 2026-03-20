<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Tên phòng <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name')is-invalid@enderror" name="name" value="{{ old('name', $room->name ?? '') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Giá thuê/tháng (VNĐ) <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('price')is-invalid@enderror" name="price" value="{{ old('price', $room->price ?? '') }}" required min="0">
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Diện tích (m²)</label>
        <input type="number" class="form-control" name="area" value="{{ old('area', $room->area ?? '') }}" step="0.1" min="0">
    </div>
    <div class="col-md-2">
        <label class="form-label fw-semibold">Tầng</label>
        <input type="number" class="form-control" name="floor" value="{{ old('floor', $room->floor ?? '') }}" min="1">
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Giá điện (đ/kWh) <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('electricity_price')is-invalid@enderror" name="electricity_price" value="{{ old('electricity_price', $room->electricity_price ?? \App\Models\Setting::get('default_electricity_price', 3500)) }}" required min="0">
        @error('electricity_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label fw-semibold">Giá nước (đ/m³) <span class="text-danger">*</span></label>
        <input type="number" class="form-control @error('water_price')is-invalid@enderror" name="water_price" value="{{ old('water_price', $room->water_price ?? \App\Models\Setting::get('default_water_price', 15000)) }}" required min="0">
        @error('water_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Phí dịch vụ (đ/tháng)</label>
        <input type="number" class="form-control" name="service_fee" value="{{ old('service_fee', $room->service_fee ?? 0) }}" min="0">
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold">Tiện ích (phân cách bằng dấu phẩy)</label>
        <textarea class="form-control" name="amenities_text" rows="2" placeholder="Wifi, Máy lạnh, WC riêng, ...">{{ old('amenities_text', isset($room->amenities) ? implode(', ', $room->amenities) : '') }}</textarea>
        <div class="form-text">Nhập danh sách tiện ích, mỗi mục cách nhau bằng dấu phẩy.</div>
    </div>
    <div class="col-md-12">
        <label class="form-label fw-semibold">Mô tả</label>
        <textarea class="form-control" name="description" rows="4">{{ old('description', $room->description ?? '') }}</textarea>
    </div>

    {{-- ĐỊA CHỈ --}}
    <div class="col-md-12">
        <label class="form-label fw-semibold">Địa chỉ phòng trọ</label>
        <div class="row g-2">
            <div class="col-md-4">
                <select id="province" name="province_name" class="form-select" required>
                    <option value="">Chọn Tỉnh/TP</option>
                    @if(isset($room) && $room->province_name)
                        <option value="{{ $room->province_name }}" selected>{{ $room->province_name }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-4">
                <select id="district" name="district_name" class="form-select" required {{ isset($room) && $room->province_name ? '' : 'disabled' }}>
                    <option value="">Chọn Quận/Huyện</option>
                    @if(isset($room) && $room->district_name)
                        <option value="{{ $room->district_name }}" selected>{{ $room->district_name }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-4">
                <select id="ward" name="ward_name" class="form-select" required {{ isset($room) && $room->district_name ? '' : 'disabled' }}>
                    <option value="">Chọn Phường/Xã</option>
                    @if(isset($room) && $room->ward_name)
                        <option value="{{ $room->ward_name }}" selected>{{ $room->ward_name }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-12 mt-2">
                <input type="text" name="address_detail" class="form-control" placeholder="Số nhà, tên đường..." value="{{ old('address_detail', $room->address_detail ?? '') }}">
            </div>
        </div>
    </div>

    {{-- ===== TOẠ ĐỘ BẢN ĐỒ ===== --}}
    <div class="col-md-12">
        <div style="background:#f8fafc; border-radius:16px; padding:24px; border:1px solid #e2e8f0;">
            <div class="d-flex align-items-center gap-2 mb-4">
                <div style="width:36px;height:36px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;color:#2563eb;font-size:18px;flex-shrink:0;">
                    <i class="ti ti-map-pin"></i>
                </div>
                <div>
                    <div class="fw-bold">Vị trí trên bản đồ</div>
                    <div class="text-muted" style="font-size:12px;">Giúp người thuê dễ tìm phòng hơn — tùy chọn</div>
                </div>
            </div>

            {{-- CÁCH 1: Paste link / toạ độ --}}
            <div class="mb-3">
                <label class="form-label fw-semibold small">
                    <i class="ti ti-link me-1"></i>Paste link Google Maps hoặc toạ độ
                </label>
                <div class="input-group">
                    <input type="text" id="locationInput" class="form-control"
                        placeholder="VD: https://maps.google.com/... hoặc 18.6796, 105.6813">
                    <button class="btn btn-primary" type="button" id="parseLocationBtn">
                        <i class="ti ti-current-location me-1"></i>Xác định
                    </button>
                </div>
                <div class="form-text">Hỗ trợ: link Google Maps, toạ độ <code>18.6796, 105.6813</code>, URL dạng <code>@18.67,105.68,15z</code></div>
                <div id="locationParseMsg" class="mt-1 small"></div>
            </div>

            {{-- CÁCH 2: Nhập thủ công --}}
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small"><i class="ti ti-adjustments-horizontal me-1"></i>Vĩ độ (Latitude)</label>
                    <input type="number" id="latInput" name="latitude"
                        class="form-control @error('latitude') is-invalid @enderror"
                        placeholder="VD: 18.6796" step="0.0000001"
                        value="{{ old('latitude', $room->latitude ?? '') }}">
                    @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small"><i class="ti ti-adjustments-horizontal me-1"></i>Kinh độ (Longitude)</label>
                    <input type="number" id="lngInput" name="longitude"
                        class="form-control @error('longitude') is-invalid @enderror"
                        placeholder="VD: 105.6813" step="0.0000001"
                        value="{{ old('longitude', $room->longitude ?? '') }}">
                    @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Preview --}}
            <div id="coordPreview" class="mb-3 px-3 py-2 rounded" style="background:#f0fdf4; border:1px solid #bbf7d0; display:none;">
                <small class="text-success fw-semibold">
                    <i class="ti ti-check me-1"></i>
                    Đã chọn: <span id="coordDisplay"></span>
                    <a id="gmapLink" href="#" target="_blank" class="ms-2 small">Xem Google Maps <i class="ti ti-external-link"></i></a>
                </small>
            </div>

            {{-- CÁCH 3: Click bản đồ --}}
            <div>
                <label class="form-label fw-semibold small mb-2">
                    <i class="ti ti-pointer me-1"></i>Hoặc click trực tiếp trên bản đồ
                </label>
                <div id="adminMap" style="height:380px; border-radius:12px; border:2px solid #e2e8f0; overflow:hidden;"></div>
                <div class="form-text mt-1">
                    <i class="ti ti-info-circle text-primary me-1"></i>Click lên bản đồ hoặc kéo marker để chọn vị trí.
                </div>
            </div>
        </div>
    </div>
    {{-- ===== KẾT THÚC TOẠ ĐỘ ===== --}}

    <div class="col-md-12">
        <label class="form-label fw-semibold">Ảnh phòng</label>
        <input type="file" class="form-control" name="images[]" multiple accept="image/*">
        <div class="form-text">Có thể chọn nhiều ảnh. Định dạng: JPG, PNG. Tối đa 5MB/ảnh.</div>
        @error('images.*')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>

{{-- ===== SCRIPTS ===== --}}
<script>
// Hàm parse địa điểm — global để cả 2 phần dùng được
function _parseLocationStr(input) {
    input = (input || '').trim();
    let m;
    m = input.match(/@(-?\d+\.?\d*),(-?\d+\.?\d*)/);
    if (m) return [parseFloat(m[1]), parseFloat(m[2])];
    m = input.match(/[?&]q=(-?\d+\.?\d*)[,+%20]+(-?\d+\.?\d*)/);
    if (m) return [parseFloat(m[1]), parseFloat(m[2])];
    m = input.match(/ll=(-?\d+\.?\d*),(-?\d+\.?\d*)/);
    if (m) return [parseFloat(m[1]), parseFloat(m[2])];
    m = input.match(/!3d(-?\d+\.?\d*)!4d(-?\d+\.?\d*)/);
    if (m) return [parseFloat(m[1]), parseFloat(m[2])];
    m = input.match(/^@?(-?\d+\.?\d*)[,\s]+(-?\d+\.?\d*)$/);
    if (m) return [parseFloat(m[1]), parseFloat(m[2])];
    return null;
}

$(document).ready(function() {
    // ─── Province / District / Ward ──────────────────────
    const $province = $('#province');
    const $district = $('#district');
    const $ward     = $('#ward');

    fetch('/api/regions/provinces')
        .then(r => r.json())
        .then(data => {
            const cur = "{{ old('province_name', $room->province_name ?? '') }}";
            data.forEach(p => {
                if (p.name !== cur) {
                    $province.append(`<option value="${p.name}" data-code="${p.code}">${p.name}</option>`);
                } else {
                    $province.find(`option[value="${p.name}"]`).attr('data-code', p.code);
                }
            });
        });

    $province.on('change', function() {
        const code = $(this).find(':selected').attr('data-code');
        $district.empty().append('<option value="">Chọn Quận/Huyện</option>').prop('disabled', true);
        $ward.empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
        if (!code) return;
        fetch(`/api/regions/districts/${code}`)
            .then(r => r.json())
            .then(d => {
                d.forEach(x => $district.append(`<option value="${x.name}" data-code="${x.code}">${x.name}</option>`));
                $district.prop('disabled', false);
            });
    });

    $district.on('change', function() {
        const code = $(this).find(':selected').attr('data-code');
        $ward.empty().append('<option value="">Chọn Phường/Xã</option>').prop('disabled', true);
        if (!code) return;
        fetch(`/api/regions/wards/${code}`)
            .then(r => r.json())
            .then(d => {
                d.forEach(x => $ward.append(`<option value="${x.name}">${x.name}</option>`));
                $ward.prop('disabled', false);
            });
    });

    // ─── Nhập lat/lng thủ công ─────────────────────────
    $('#latInput, #lngInput').on('change', function() {
        const lat = parseFloat($('#latInput').val());
        const lng = parseFloat($('#lngInput').val());
        if (!isNaN(lat) && !isNaN(lng) && window._mapSetMarker) {
            window._mapSetMarker(lat, lng);
        }
    });

    // ─── Paste link ─────────────────────────────────────
    $('#locationInput').on('keypress', function(e) {
        if (e.which === 13) { e.preventDefault(); $('#parseLocationBtn').click(); }
    });

    $('#parseLocationBtn').on('click', function() {
        const val  = $('#locationInput').val().trim();
        const $msg = $('#locationParseMsg');

        if (!val) {
            $msg.html('<span class="text-danger"><i class="ti ti-circle-x me-1"></i>Vui lòng nhập link hoặc toạ độ.</span>');
            return;
        }
        if (/goo\.gl|maps\.app/i.test(val)) {
            $msg.html('<span class="text-warning"><i class="ti ti-alert-triangle me-1"></i>Link rút gọn không hỗ trợ — hãy mở link trong trình duyệt, copy URL đầy đủ rồi paste lại.</span>');
            return;
        }

        const result = _parseLocationStr(val);
        if (result && Math.abs(result[0]) <= 90 && Math.abs(result[1]) <= 180) {
            if (window._mapSetMarker) window._mapSetMarker(result[0], result[1]);
            $msg.html('<span class="text-success"><i class="ti ti-circle-check me-1"></i>Đã xác định vị trí thành công!</span>');
            $('#locationInput').val('');
        } else {
            $msg.html('<span class="text-danger"><i class="ti ti-circle-x me-1"></i>Không đọc được toạ độ. Thử: <code>18.6796, 105.6813</code></span>');
        }
    });
});

// ─── Leaflet Map Init — window.onload đảm bảo Leaflet JS đã sẵn ─
window.addEventListener('load', function () {
    if (typeof L === 'undefined') {
        document.getElementById('adminMap').innerHTML =
            '<div style="padding:20px;color:#94a3b8;text-align:center;"><i class="ti ti-alert-triangle"></i> Không tải được bản đồ. Kiểm tra kết nối internet.</div>';
        return;
    }

    const defaultLat = {{ old('latitude', $room->latitude ?? 18.6796) }};
    const defaultLng = {{ old('longitude', $room->longitude ?? 105.6813) }};
    const hasCoord   = {{ (old('latitude', $room->latitude ?? null)) ? 'true' : 'false' }};

    const map = L.map('adminMap').setView([defaultLat, defaultLng], hasCoord ? 16 : 13);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap © CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    const pinIcon = L.divIcon({
        html: '<div style="background:#e74c3c;width:18px;height:18px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.4);"></div>',
        iconSize: [18, 18], iconAnchor: [9, 18], className: ''
    });

    let marker = null;

    function setMarker(lat, lng) {
        lat = Math.round(lat * 1e7) / 1e7;
        lng = Math.round(lng * 1e7) / 1e7;
        $('#latInput').val(lat);
        $('#lngInput').val(lng);
        $('#coordDisplay').text(lat + ', ' + lng);
        $('#gmapLink').attr('href', 'https://www.google.com/maps?q=' + lat + ',' + lng);
        $('#coordPreview').show();
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], { icon: pinIcon, draggable: true }).addTo(map);
            marker.on('dragend', function(e) {
                const p = e.target.getLatLng();
                setMarker(p.lat, p.lng);
            });
        }
        map.setView([lat, lng], 16);
    }

    // Expose ra global để jQuery handlers gọi được
    window._mapSetMarker = setMarker;

    if (hasCoord) setMarker(defaultLat, defaultLng);

    map.on('click', function(e) { setMarker(e.latlng.lat, e.latlng.lng); });
});
</script>
