@extends('layouts.admin')
@section('title', 'Tạo Hóa Đơn')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="fs-3 mb-0">Tạo hóa đơn mới</h1>
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i>Quay lại</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card p-4">
            <form method="POST" action="{{ route('admin.invoices.store') }}" id="invoiceForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Hợp đồng (Phòng - Người thuê) <span class="text-danger">*</span></label>
                        <select class="form-select" name="contract_id" id="contractSelect" required>
                            <option value="">-- Chọn hợp đồng --</option>
                            @foreach($activeContracts as $c)
                                <option value="{{ $c->id }}" data-room="{{ $c->room_id }}" data-fee="{{ $c->monthly_rent }}" data-service="{{ $c->room->service_fee }}">
                                    {{ $c->room->name }} - {{ $c->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Tháng</label>
                        <select class="form-select" name="month">
                            @for($m=1;$m<=12;$m++)
                                <option value="{{ $m }}" {{ $month==$m?'selected':'' }}>{{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Năm</label>
                        <input type="number" class="form-control" name="year" value="{{ $year }}" required min="2020">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tiền phòng</label>
                        <input type="number" class="form-control calc-field" name="room_fee" id="roomFee" value="{{ old('room_fee', 0) }}" required min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tiền điện</label>
                        <input type="number" class="form-control calc-field" name="electricity_fee" id="elecFee" value="{{ old('electricity_fee', 0) }}" required min="0">
                        <div class="form-text">Lấy từ dữ liệu điện nước đã nhập</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tiền nước</label>
                        <input type="number" class="form-control calc-field" name="water_fee" id="waterFee" value="{{ old('water_fee', 0) }}" required min="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phí dịch vụ</label>
                        <input type="number" class="form-control calc-field" name="service_fee" id="serviceFee" value="{{ old('service_fee', 0) }}" required min="0">
                    </div>

                    <div class="col-12">
                        <div class="p-3 rounded" style="background:#f0f7ff;border:1px solid #0d6efd33;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Tổng cộng:</span>
                                <span class="fw-bold fs-5 text-primary" id="totalDisplay">0đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Hạn thanh toán</label>
                        <input type="date" class="form-control" name="due_date" value="{{ old('due_date', now()->addDays(15)->format('Y-m-d')) }}">
                        <div class="form-text">Mặc định 15 ngày</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Ghi chú</label>
                        <textarea class="form-control" name="notes" rows="2">{{ old('notes') }}</textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-4"><i class="ti ti-receipt me-1"></i>Tạo hóa đơn</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function calcTotal() {
    var total = 0;
    document.querySelectorAll('.calc-field').forEach(el => total += parseFloat(el.value)||0);
    document.getElementById('totalDisplay').textContent = new Intl.NumberFormat('vi-VN').format(Math.round(total)) + 'đ';
}

document.querySelectorAll('.calc-field').forEach(el => el.addEventListener('input', calcTotal));

document.getElementById('contractSelect').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    if (opt.value) {
        document.getElementById('roomFee').value = opt.dataset.fee || 0;
        document.getElementById('serviceFee').value = opt.dataset.service || 0;

        // Try to load utility data
        var roomId = opt.dataset.room;
        var month = document.querySelector('[name=month]').value;
        var year = document.querySelector('[name=year]').value;
        fetch(`/admin/invoices/utility-data?room_id=${roomId}&month=${month}&year=${year}`)
            .then(r => r.json())
            .then(data => {
                if (data) {
                    document.getElementById('elecFee').value = data.electricity_amount || 0;
                    document.getElementById('waterFee').value = data.water_amount || 0;
                }
                calcTotal();
            }).catch(() => calcTotal());
    }
    calcTotal();
});
calcTotal();
</script>
@endsection
