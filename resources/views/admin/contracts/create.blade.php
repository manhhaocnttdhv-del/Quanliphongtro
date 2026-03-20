@extends('layouts.admin')
@section('title', 'Tạo Hợp Đồng Mới')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.contracts.index') }}" class="text-decoration-none text-muted">Hợp đồng</a></li>
                <li class="breadcrumb-item active">Tạo mới</li>
            </ol>
        </nav>
        <h1 class="fs-3 fw-bold mb-0">📄 Tạo Hợp Đồng Thuê Phòng</h1>
    </div>
    <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i>Quay lại
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('admin.contracts.store') }}">
                    @csrf

                    <div class="row g-4">
                        {{-- Room --}}
                        <div class="col-12">
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:11px;letter-spacing:1px;">
                                <i class="ti ti-home me-1"></i>Thông tin phòng
                            </h6>
                            <label class="form-label fw-semibold">Phòng trọ <span class="text-danger">*</span></label>
                            <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                                <option value="">— Chọn phòng còn trống —</option>
                                @foreach($availableRooms as $room)
                                    <option value="{{ $room->id }}"
                                            data-price="{{ $room->price }}"
                                            {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} — {{ number_format($room->price) }}đ/tháng
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @if($availableRooms->isEmpty())
                                <div class="alert alert-warning mt-2 py-2 small">
                                    <i class="ti ti-alert-triangle me-1"></i>
                                    Không có phòng trống. <a href="{{ route('admin.rooms.create') }}">Thêm phòng mới</a>
                                </div>
                            @endif
                        </div>

                        {{-- Tenant --}}
                        <div class="col-12">
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:11px;letter-spacing:1px;">
                                <i class="ti ti-user me-1"></i>Thông tin khách thuê
                            </h6>
                            <label class="form-label fw-semibold">Khách thuê <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">— Chọn khách thuê —</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('user_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }} {{ $tenant->phone ? '— ' . $tenant->phone : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Dates --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                            <input type="date" name="start_date"
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date', date('Y-m-d')) }}" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày kết thúc <span class="text-muted fw-normal">(để trống nếu không xác định)</span></label>
                            <input type="date" name="end_date"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date') }}">
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Financial --}}
                        <div class="col-12">
                            <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size:11px;letter-spacing:1px;">
                                <i class="ti ti-coin me-1"></i>Điều khoản tài chính
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tiền đặt cọc (đ) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="deposit"
                                       class="form-control @error('deposit') is-invalid @enderror"
                                       value="{{ old('deposit', 0) }}" min="0" step="50000" required>
                                <span class="input-group-text">đ</span>
                            </div>
                            @error('deposit')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Giá thuê / tháng (đ) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" id="monthly_rent" name="monthly_rent"
                                       class="form-control @error('monthly_rent') is-invalid @enderror"
                                       value="{{ old('monthly_rent') }}" min="0" step="50000" required>
                                <span class="input-group-text">đ</span>
                            </div>
                            @error('monthly_rent')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        {{-- Notes --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú hợp đồng</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Điều khoản bổ sung, thỏa thuận đặc biệt...">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Preview  --}}
                        <div class="col-12">
                            <div class="p-3 bg-primary-subtle rounded-3 border border-primary-subtle" id="contract-preview">
                                <div class="fw-semibold text-primary mb-2"><i class="ti ti-eye me-1"></i>Tóm tắt hợp đồng</div>
                                <div class="row g-2 small">
                                    <div class="col-6">💰 Tiền thuê: <strong id="prev-rent">—</strong>/tháng</div>
                                    <div class="col-6">📅 Bắt đầu: <strong id="prev-start">—</strong></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-3 pt-2">
                            <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary flex-fill">Hủy</a>
                            <button type="submit" class="btn btn-primary flex-fill fw-semibold" {{ $availableRooms->isEmpty() ? 'disabled' : '' }}>
                                <i class="ti ti-file-plus me-2"></i>Tạo Hợp Đồng
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('room_id').addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        const price = opt.dataset.price ? Number(opt.dataset.price).toLocaleString('vi-VN') : '—';
        document.getElementById('monthly_rent').value = opt.dataset.price || '';
        document.getElementById('prev-rent').textContent = price ? price + 'đ' : '—';
    });
    document.querySelector('[name=start_date]').addEventListener('change', function () {
        const d = new Date(this.value);
        document.getElementById('prev-start').textContent = d.toLocaleDateString('vi-VN');
    });
    // Init
    document.getElementById('prev-start').textContent = new Date('{{ date('Y-m-d') }}').toLocaleDateString('vi-VN');
</script>
@endsection
