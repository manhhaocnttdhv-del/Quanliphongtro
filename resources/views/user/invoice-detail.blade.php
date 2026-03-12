@extends('layouts.user')
@section('title', 'Chi Tiết Hóa Đơn - Tháng ' . $invoice->month . '/' . $invoice->year)

@section('content')
<div style="padding:50px 0;background:#f9f9f9;min-height:70vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Invoice card --}}
                <div class="card mb-4" style="border-radius:14px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.10);">
                    <div style="background:linear-gradient(135deg,#f9a825,#ff8f00);padding:24px 28px;color:#fff;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="mb-1">HÓA ĐƠN PHÒNG TRỌ</h4>
                                <p class="mb-0 opacity-75">{{ \App\Models\Setting::get('site_name','Nhà Trọ') }}</p>
                            </div>
                            <span class="badge bg-{{ $invoice->statusBadge() }} fs-6 px-3 py-2">{{ $invoice->statusLabel() }}</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="text-muted small">Phòng</div>
                                <strong>{{ $invoice->room->name ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-6">
                                <div class="text-muted small">Kỳ thanh toán</div>
                                <strong>Tháng {{ $invoice->month }}/{{ $invoice->year }}</strong>
                            </div>
                            @if($invoice->contract)
                                <div class="col-6 mt-3">
                                    <div class="text-muted small">Người thuê</div>
                                    <strong>{{ $invoice->contract->user->name ?? '' }}</strong>
                                </div>
                            @endif
                        </div>

                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted">Tiền phòng</td><td class="text-end fw-semibold">{{ number_format($invoice->room_fee) }} đ</td></tr>
                            <tr><td class="text-muted"><i class="fa fa-bolt mr-1 text-warning"></i>Tiền điện</td><td class="text-end fw-semibold">{{ number_format($invoice->electricity_fee) }} đ</td></tr>
                            <tr><td class="text-muted"><i class="fa fa-tint mr-1 text-info"></i>Tiền nước</td><td class="text-end fw-semibold">{{ number_format($invoice->water_fee) }} đ</td></tr>
                            @if($invoice->service_fee > 0)
                                <tr><td class="text-muted">Phí dịch vụ</td><td class="text-end fw-semibold">{{ number_format($invoice->service_fee) }} đ</td></tr>
                            @endif
                            <tr style="border-top:2px solid #f9a825;">
                                <td class="fw-bold fs-5">Tổng cộng</td>
                                <td class="text-end fw-bold fs-5" style="color:#f9a825;">{{ number_format($invoice->total_amount) }} đ</td>
                            </tr>
                        </table>

                        @if($invoice->notes)
                            <div class="mt-3 p-3 rounded" style="background:#f9f9f9;">
                                <div class="text-muted small">Ghi chú</div>
                                <div>{{ $invoice->notes }}</div>
                            </div>
                        @endif

                        @if($invoice->status === 'paid')
                            <div class="alert alert-success mt-4">
                                <i class="fa fa-check-circle mr-2"></i>
                                <strong>Đã thanh toán</strong> lúc {{ $invoice->paid_at?->format('d/m/Y H:i') }}
                                @if($invoice->payment_method) qua {{ $invoice->payment_method }} @endif
                            </div>
                        @endif
                    </div>
                </div>

                @if($invoice->status === 'unpaid')
                    {{-- Payment section --}}
                    <div class="card p-4 mb-4" style="border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,.08);">
                        <h5 class="mb-4">💳 Thanh toán hóa đơn</h5>
                        <div class="row g-4">
                            {{-- VietQR --}}
                            <div class="col-md-6 text-center">
                                <h6 class="mb-3"><i class="fa fa-qrcode mr-1"></i>Quét QR Chuyển Khoản</h6>
                                <img src="{{ $invoice->vietQrUrl() }}" alt="VietQR" class="img-fluid rounded-3" style="max-width:220px;border:2px solid #e9ecef;padding:8px;">
                                <div class="mt-2 small text-muted">
                                    Ngân hàng: <strong>{{ \App\Models\Setting::get('vietqr_bank_id','MB') }}</strong><br>
                                    STK: <strong>{{ \App\Models\Setting::get('vietqr_account_no','') }}</strong>
                                </div>
                            </div>
                            {{-- MoMo --}}
                            <div class="col-md-6 text-center d-flex flex-column align-items-center justify-content-center">
                                <h6 class="mb-3"><img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" height="28" alt="MoMo"> Thanh toán MoMo</h6>
                                <a href="{{ $invoice->momoUrl() }}" target="_blank"
                                   class="btn btn-lg fw-bold px-4 py-3"
                                   style="background:linear-gradient(135deg,#ae2d68,#d82d8b);color:#fff;border-radius:30px;text-decoration:none;">
                                    <i class="fa fa-mobile mr-2"></i>Mở MoMo & Thanh toán
                                </a>
                                <div class="mt-3 small text-muted">
                                    Số MoMo: <strong>{{ \App\Models\Setting::get('momo_number','') }}</strong><br>
                                    Số tiền: <strong>{{ number_format($invoice->total_amount) }} đ</strong>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info mt-4 mb-0">
                            <i class="fa fa-info-circle mr-1"></i>
                            Sau khi thanh toán, vui lòng chờ admin xác nhận (thường trong vòng 24h). Nếu cần hỗ trợ, liên hệ <strong>{{ \App\Models\Setting::get('site_phone','') }}</strong>.
                        </div>
                    </div>
                @endif

                <a href="{{ route('user.invoices') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left mr-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
