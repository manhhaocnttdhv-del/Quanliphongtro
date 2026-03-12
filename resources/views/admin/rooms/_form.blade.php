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
    <div class="col-md-12">
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
    <div class="col-md-12">
        <label class="form-label fw-semibold">Địa chỉ phòng trọ</label>
        <div class="row g-2">
            <div class="col-md-4">
                <select id="province" name="province_name" class="form-select @error('province_name') is-invalid @enderror" required>
                    <option value="">Chọn Tỉnh/TP</option>
                    @if(isset($room) && $room->province_name)
                        <option value="{{ $room->province_name }}" selected>{{ $room->province_name }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-4">
                <select id="district" name="district_name" class="form-select @error('district_name') is-invalid @enderror" required {{ isset($room) ? '' : 'disabled' }}>
                    <option value="">Chọn Quận/Huyện</option>
                    @if(isset($room) && $room->district_name)
                        <option value="{{ $room->district_name }}" selected>{{ $room->district_name }}</option>
                    @endif
                </select>
            </div>
            <div class="col-md-4">
                <select id="ward" name="ward_name" class="form-select @error('ward_name') is-invalid @enderror" required {{ isset($room) ? '' : 'disabled' }}>
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
    <div class="col-md-12">
        <label class="form-label fw-semibold">Ảnh phòng</label>
        <input type="file" class="form-control" name="images[]" multiple accept="image/*">
        <div class="form-text">Có thể chọn nhiều ảnh. Định dạng: JPG, PNG. Tối đa 5MB/ảnh.</div>
        @error('images.*')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
</div>

<script>
    $(document).ready(function() {
        const provinceSelect = $('#province');
        const districtSelect = $('#district');
        const wardSelect = $('#ward');

        // Load provinces
        fetch('https://provinces.open-api.vn/api/p/')
            .then(response => response.json())
            .then(data => {
                const currentProvince = "{{ old('province_name', $room->province_name ?? '') }}";
                data.forEach(p => {
                    const selected = (p.name === currentProvince) ? 'selected' : '';
                    if (p.name !== currentProvince) {
                        provinceSelect.append(`<option value="${p.name}" data-code="${p.code}" ${selected}>${p.name}</option>`);
                    } else {
                        // Find the option and set data-code if it was already there (from server-side)
                        provinceSelect.find(`option[value="${p.name}"]`).attr('data-code', p.code);
                    }
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
