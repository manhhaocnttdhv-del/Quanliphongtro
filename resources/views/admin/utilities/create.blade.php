@extends('layouts.admin')
@section('title', 'Nhập Chỉ Số Điện Nước')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fs-3 mb-0">Nhập chỉ số điện nước</h1>
    <a href="{{ route('admin.utilities.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i>Quay lại</a>
</div>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card p-4">
            <form method="POST" action="{{ route('admin.utilities.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phòng <span class="text-danger">*</span></label>
                        <select class="form-select @error('room_id')is-invalid@enderror" name="room_id" required>
                            <option value="">-- Chọn phòng --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" {{ old('room_id')==$room->id?'selected':'' }}>{{ $room->name }}</option>
                            @endforeach
                        </select>
                        @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tháng <span class="text-danger">*</span></label>
                        <select class="form-select" name="month" required>
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ $m }}" {{ old('month', now()->month)==$m?'selected':'' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Năm <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="year" value="{{ old('year', now()->year) }}" required min="2020">
                    </div>

                    <div class="col-12"><hr class="my-1"><h6 class="text-primary"><i class="ti ti-bolt me-1"></i>Điện</h6></div>
                    <div class="col-md-3">
                        <label class="form-label">Chỉ số cũ</label>
                        <input type="number" class="form-control @error('electricity_old')is-invalid@enderror" name="electricity_old" value="{{ old('electricity_old', 0) }}" required min="0" step="0.01">
                        @error('electricity_old')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Chỉ số mới</label>
                        <input type="number" class="form-control @error('electricity_new')is-invalid@enderror" name="electricity_new" value="{{ old('electricity_new', 0) }}" required min="0" step="0.01">
                        @error('electricity_new')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Giá (đ/kWh)</label>
                        <input type="number" class="form-control" name="electricity_price" value="{{ old('electricity_price', $defaultElecPrice) }}" required min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Thành tiền</label>
                        <div class="form-control bg-light fw-bold text-primary" id="elecAmount">—</div>
                    </div>

                    <div class="col-12"><hr class="my-1"><h6 class="text-info"><i class="ti ti-droplet me-1"></i>Nước</h6></div>
                    <div class="col-md-3">
                        <label class="form-label">Chỉ số cũ</label>
                        <input type="number" class="form-control @error('water_old')is-invalid@enderror" name="water_old" value="{{ old('water_old', 0) }}" required min="0" step="0.01">
                        @error('water_old')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Chỉ số mới</label>
                        <input type="number" class="form-control @error('water_new')is-invalid@enderror" name="water_new" value="{{ old('water_new', 0) }}" required min="0" step="0.01">
                        @error('water_new')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Giá (đ/m³)</label>
                        <input type="number" class="form-control" name="water_price" value="{{ old('water_price', $defaultWaterPrice) }}" required min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Thành tiền</label>
                        <div class="form-control bg-light fw-bold text-info" id="waterAmount">—</div>
                    </div>

                    <div class="col-12 mt-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="ti ti-device-floppy me-1"></i>Lưu chỉ số</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function calcUtil() {
    const elecOld = parseFloat(document.querySelector('[name=electricity_old]').value)||0;
    const elecNew = parseFloat(document.querySelector('[name=electricity_new]').value)||0;
    const elecPrice = parseFloat(document.querySelector('[name=electricity_price]').value)||0;
    const waterOld = parseFloat(document.querySelector('[name=water_old]').value)||0;
    const waterNew = parseFloat(document.querySelector('[name=water_new]').value)||0;
    const waterPrice = parseFloat(document.querySelector('[name=water_price]').value)||0;
    document.getElementById('elecAmount').textContent = new Intl.NumberFormat('vi-VN').format(Math.round((elecNew-elecOld)*elecPrice)) + 'đ';
    document.getElementById('waterAmount').textContent = new Intl.NumberFormat('vi-VN').format(Math.round((waterNew-waterOld)*waterPrice)) + 'đ';
}
document.querySelectorAll('input[type=number]').forEach(el => el.addEventListener('input', calcUtil));
</script>
@endsection
