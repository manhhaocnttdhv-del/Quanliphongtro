@extends('layouts.admin')
@section('title', 'Hóa Đơn #' . $invoice->id)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}" class="text-decoration-none text-muted">Hóa đơn</a></li>
                <li class="breadcrumb-item active">Hóa đơn #{{ $invoice->id }}</li>
            </ol>
        </nav>
        <h1 class="fs-3 fw-bold mb-0">Hóa Đơn Tháng {{ $invoice->month }}/{{ $invoice->year }}</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Quay lại
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Invoice Card --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            {{-- Header --}}
            <div class="card-body p-4" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); border-radius: 12px 12px 0 0;">
                <div class="d-flex justify-content-between align-items-start text-white">
                    <div>
                        <div class="small text-white-50 text-uppercase fw-semibold mb-1">Hóa đơn tiền phòng</div>
                        <h4 class="fw-bold mb-0">Tháng {{ $invoice->month }}/{{ $invoice->year }}</h4>
                        <div class="mt-2 opacity-75 small">Phòng: {{ $invoice->room->name }}</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-white text-{{ $invoice->statusBadge() }} fs-6 px-3 py-2">
                            {{ $invoice->statusLabel() }}
                        </span>
                        <div class="text-white-50 small mt-2">{{ $invoice->transaction_id ?? '#'.$invoice->id }}</div>
                        @if($invoice->due_date)
                            <div class="text-white-50 small">Hạn: {{ $invoice->due_date->format('d/m/Y') }}</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Breakdown --}}
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-muted text-uppercase" style="font-size:11px;letter-spacing:1px;">Chi tiết khoản phí</h6>

                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tbody>
                            <tr class="border-bottom">
                                <td class="ps-0 d-flex align-items-center gap-2">
                                    <span class="rounded-2 bg-primary-subtle p-2 d-flex"><i class="ti ti-home text-primary"></i></span>
                                    <div>
                                        <div class="fw-semibold">Tiền thuê phòng</div>
                                        <div class="text-muted small">{{ $invoice->room->name }}</div>
                                    </div>
                                </td>
                                <td class="text-end fw-semibold align-middle">{{ number_format($invoice->room_fee) }}đ</td>
                            </tr>

                            @php
                                $utility = \App\Models\Utility::where('room_id', $invoice->room_id)
                                    ->where('month', $invoice->month)->where('year', $invoice->year)->first();
                            @endphp

                            <tr class="border-bottom">
                                <td class="ps-0 d-flex align-items-center gap-2">
                                    <span class="rounded-2 bg-warning-subtle p-2 d-flex"><i class="ti ti-bolt text-warning"></i></span>
                                    <div>
                                        <div class="fw-semibold">Tiền điện</div>
                                        @if($utility)
                                            <div class="text-muted small">
                                                {{ $utility->electricity_old }} → {{ $utility->electricity_new }} kWh
                                                × {{ number_format($utility->electricity_price) }}đ
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end fw-semibold align-middle">{{ number_format($invoice->electricity_fee) }}đ</td>
                            </tr>

                            <tr class="border-bottom">
                                <td class="ps-0 d-flex align-items-center gap-2">
                                    <span class="rounded-2 bg-info-subtle p-2 d-flex"><i class="ti ti-droplet text-info"></i></span>
                                    <div>
                                        <div class="fw-semibold">Tiền nước</div>
                                        @if($utility)
                                            <div class="text-muted small">
                                                {{ $utility->water_old }} → {{ $utility->water_new }} m³
                                                × {{ number_format($utility->water_price) }}đ
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end fw-semibold align-middle">{{ number_format($invoice->water_fee) }}đ</td>
                            </tr>

                            @if($invoice->service_fee > 0)
                                <tr class="border-bottom">
                                    <td class="ps-0 d-flex align-items-center gap-2">
                                        <span class="rounded-2 bg-success-subtle p-2 d-flex"><i class="ti ti-tools text-success"></i></span>
                                        <div><div class="fw-semibold">Dịch vụ khác</div></div>
                                    </td>
                                    <td class="text-end fw-semibold align-middle">{{ number_format($invoice->service_fee) }}đ</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="ps-0 fw-bold fs-5">Tổng cộng</td>
                                <td class="text-end fw-bold fs-4 text-primary">{{ number_format($invoice->total_amount) }}đ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($invoice->notes)
                    <div class="mt-3 p-3 bg-light rounded-3">
                        <div class="text-muted small fw-semibold mb-1">Ghi chú:</div>
                        <div class="small">{{ $invoice->notes }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Right Side --}}
    <div class="col-lg-4">
        {{-- Tenant Info --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="ti ti-user me-2 text-primary"></i>Thông tin khách thuê</h6>
            </div>
            <div class="card-body px-4 pb-4">
                @if($invoice->contract && $invoice->contract->user)
                    <div class="mb-2"><span class="text-muted small">Tên:</span> <strong>{{ $invoice->contract->user->name }}</strong></div>
                    <div class="mb-2"><span class="text-muted small">SĐT:</span> {{ $invoice->contract->user->phone ?? '—' }}</div>
                    <div><span class="text-muted small">Email:</span> {{ $invoice->contract->user->email }}</div>
                @else
                    <div class="text-muted small">Không có thông tin khách thuê</div>
                @endif
            </div>
        </div>

        {{-- Payment Info --}}
        @if($invoice->status === 'paid')
            <div class="card border-0 shadow-sm mb-4 border-success border-2">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                            <i class="ti ti-check text-success fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-success">Đã thanh toán</div>
                            <div class="text-muted small">{{ $invoice->paid_at?->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    @if($invoice->payment_method)
                        <div class="text-muted small"><strong>Phương thức:</strong> {{ $invoice->payment_method }}</div>
                    @endif
                    @if($invoice->payment_ref)
                        <div class="text-muted small mt-1"><strong>Mã tham chiếu:</strong> {{ $invoice->payment_ref }}</div>
                    @endif
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0"><i class="ti ti-credit-card me-2 text-warning"></i>Xác nhận thanh toán</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <form method="POST" action="{{ route('admin.invoices.confirm-payment', $invoice) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Phương thức thanh toán</label>
                            <select name="payment_method" class="form-select form-select-sm" required>
                                <option value="">— Chọn —</option>
                                <option value="Tiền mặt">💵 Tiền mặt</option>
                                <option value="Chuyển khoản">🏦 Chuyển khoản</option>
                                <option value="MoMo">📱 MoMo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Mã tham chiếu (nếu có)</label>
                            <input type="text" name="payment_ref" class="form-control form-control-sm" placeholder="Mã giao dịch...">
                        </div>
                        <button type="submit" class="btn btn-success w-100 fw-semibold">
                            <i class="ti ti-check me-1"></i>Xác nhận đã nhận tiền
                        </button>
                    </form>
                </div>
            </div>

            {{-- QR Code --}}
            @if(\App\Models\Setting::get('vietqr_account_no'))
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="fw-semibold mb-2 small">QR Thanh Toán VietQR</div>
                        <img src="{{ $invoice->vietQrUrl() }}" class="img-fluid rounded-2" style="max-width:200px;" alt="QR thanh toán">
                    </div>
                </div>
            @endif

            {{-- Cancel Button --}}
            @if(!$invoice->isPaid() && $invoice->status !== 'cancelled')
                <div class="mt-3">
                    <form method="POST" action="{{ route('admin.invoices.cancel', $invoice) }}" onsubmit="return confirm('Bạn có chắc muốn hủy hóa đơn này?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="ti ti-x me-1"></i>Hủy hóa đơn
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
