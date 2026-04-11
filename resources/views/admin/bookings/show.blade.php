@extends('layouts.admin')
@section('title', 'Chi Tiết Đơn Đặt #' . $booking->id)

@section('content')
<div class="container-fluid py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Đơn đặt phòng</a></li>
            <li class="breadcrumb-item active">Đơn #{{ $booking->id }}</li>
        </ol>
    </nav>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            ⚠️ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Đơn Đặt Phòng #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-muted small mb-0">Tạo lúc {{ $booking->created_at->format('H:i, d/m/Y') }}</p>
        </div>
        @php
            $badge = match($booking->status) {
                'pending'   => 'warning',
                'paid'      => 'success',
                'cancelled' => 'danger',
                'converted' => 'info',
                default     => 'secondary',
            };
        @endphp
        <span class="badge bg-{{ $badge }} fs-6 px-3 py-2">{{ $booking->statusLabel() }}</span>
    </div>

    <div class="row g-4">

        {{-- LEFT --}}
        <div class="col-lg-8">

            {{-- Thông tin người thuê --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h6 fw-bold mb-0">👤 Thông Tin Người Đặt</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Họ và tên</div>
                            <div class="fw-semibold">{{ $booking->tenant_name }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Số điện thoại</div>
                            <div class="fw-semibold">{{ $booking->tenant_phone }}</div>
                        </div>
                        @if($booking->tenant_cccd)
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">CCCD / CMND</div>
                            <div class="fw-semibold font-monospace">{{ $booking->tenant_cccd }}</div>
                        </div>
                        @endif
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Số người ở</div>
                            <div class="fw-semibold">{{ $booking->num_people }} người</div>
                        </div>
                        @if($booking->move_in_date)
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Ngày vào ở</div>
                            <div class="fw-semibold">{{ $booking->move_in_date->format('d/m/Y') }}</div>
                        </div>
                        @endif
                        @if($booking->user)
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Tài khoản</div>
                            <div class="fw-semibold">{{ $booking->user->name }} ({{ $booking->user->email }})</div>
                        </div>
                        @endif
                        @if($booking->note)
                        <div class="col-12">
                            <div class="small text-muted mb-1">Ghi chú</div>
                            <div class="p-3 bg-light rounded">{{ $booking->note }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Thanh toán --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h6 fw-bold mb-0">💳 Thông Tin Thanh Toán</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Tiền đặt cọc</div>
                            <div class="fs-4 fw-bold text-warning">{{ number_format($booking->deposit_amount) }}đ</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Phương thức</div>
                            <div class="fw-semibold">{{ $booking->paymentMethodLabel() }}</div>
                        </div>
                        @if($booking->paid_at)
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Thanh toán lúc</div>
                            <div class="fw-semibold text-success">{{ $booking->paid_at->format('H:i, d/m/Y') }}</div>
                        </div>
                        @endif
                        @if($booking->payment_ref)
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Mã giao dịch</div>
                            <div class="fw-semibold font-monospace text-success">{{ $booking->payment_ref }}</div>
                        </div>
                        @endif
                        @if($booking->confirmedBy)
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Xác nhận bởi</div>
                            <div class="fw-semibold">{{ $booking->confirmedBy->name }}</div>
                        </div>
                        @endif
                        @if($booking->expired_at && $booking->status === 'pending')
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Hết hạn lúc</div>
                            <div class="fw-semibold text-warning">{{ $booking->expired_at->format('H:i, d/m/Y') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Convert to Contract form (nếu status = paid) --}}
            @if($booking->status === 'paid' && !$booking->contract_id)
            <div class="card border-0 shadow-sm rounded-3 mb-4" style="border-top:4px solid #10b981 !important;">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h6 fw-bold mb-0 text-success">📋 Tạo Hợp Đồng Từ Đơn Đặt Này</h2>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Sau khi đã xác nhận thanh toán cọc, bạn có thể tạo hợp đồng chính thức từ đơn này.</p>
                    <form action="{{ route('admin.bookings.convert', $booking) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control form-control-sm"
                                    value="{{ $booking->move_in_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold">Ngày kết thúc</label>
                                <input type="date" name="end_date" class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small fw-semibold">Tiền thuê / tháng <span class="text-danger">*</span></label>
                                <input type="number" name="monthly_rent" class="form-control form-control-sm"
                                    value="{{ $booking->room->price }}" min="0" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Ghi chú hợp đồng</label>
                                <textarea name="notes" class="form-control form-control-sm" rows="2"
                                    placeholder="Điều khoản bổ sung..."></textarea>
                            </div>
                            <div class="col-12">
                                <div class="p-3 bg-light rounded small text-muted mb-3">
                                    📌 Tiền đặt cọc <strong>{{ number_format($booking->deposit_amount) }}đ</strong> sẽ được tự động ghi vào hợp đồng.
                                </div>
                                <button type="submit" class="btn btn-success"
                                    onclick="return confirm('Tạo hợp đồng và chuyển phòng sang trạng thái đang thuê?')">
                                    🏠 Tạo Hợp Đồng & Xác Nhận Vào Ở
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Contract info (nếu đã convert) --}}
            @if($booking->contract)
            <div class="card border-0 shadow-sm rounded-3 mb-4" style="border-top:4px solid #3b82f6 !important;">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h6 fw-bold mb-0 text-primary">📋 Hợp Đồng Đã Tạo</h2>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Mã hợp đồng</div>
                            <div class="fw-semibold">#{{ $booking->contract->id }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Ngày bắt đầu</div>
                            <div class="fw-semibold">{{ $booking->contract->start_date->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-sm-6">
                            <div class="small text-muted mb-1">Tiền thuê / tháng</div>
                            <div class="fw-semibold text-primary">{{ number_format($booking->contract->monthly_rent) }}đ</div>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('admin.contracts.show', $booking->contract) }}" class="btn btn-sm btn-outline-primary">
                                Xem chi tiết hợp đồng →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT: Actions & Room Info --}}
        <div class="col-lg-4">

            {{-- Actions --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h6 fw-bold mb-0">⚡ Thao Tác</h2>
                </div>
                <div class="card-body d-grid gap-2">
                    @if($booking->status === 'pending' && $booking->payment_method === 'offline')
                        <form action="{{ route('admin.bookings.confirm-payment', $booking) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100"
                                onclick="return confirm('Xác nhận đã nhận tiền cọc từ người thuê?')">
                                ✅ Xác Nhận Đã Nhận Cọc
                            </button>
                        </form>
                    @endif

                    @if(in_array($booking->status, ['pending', 'paid']))
                        <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100"
                                onclick="return confirm('Hủy đơn đặt này? Phòng sẽ được trả về trạng thái trống.')">
                                ❌ Hủy Đơn Đặt
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
                        ← Quay lại danh sách
                    </a>
                </div>
            </div>

            {{-- Room info --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <h2 class="h6 fw-bold mb-0">🏠 Phòng Đặt</h2>
                </div>
                @if($booking->room->images->first())
                    <img src="{{ asset('storage/' . $booking->room->images->first()->image_path) }}" alt="" style="width:100%;height:150px;object-fit:cover;">
                @endif
                <div class="card-body">
                    <h3 class="h6 fw-bold mb-1">{{ $booking->room->name }}</h3>
                    @if($booking->room->fullAddress())
                        <p class="small text-muted mb-2">📍 {{ $booking->room->fullAddress() }}</p>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted small">Giá thuê</span>
                        <span class="fw-bold text-warning fs-6">{{ number_format($booking->room->price) }}đ/tháng</span>
                    </div>
                    <div class="d-flex gap-2 mb-3">
                        @php
                            $roomBadge = $booking->room->status === 'available' ? 'success' : ($booking->room->status === 'reserved' ? 'warning' : 'danger');
                            $roomLabel = match($booking->room->status) {
                                'available' => 'Còn trống',
                                'reserved' => 'Đang giữ chỗ',
                                'rented' => 'Đang thuê',
                                default => $booking->room->status,
                            };
                        @endphp
                        <span class="badge bg-{{ $roomBadge }}">{{ $roomLabel }}</span>
                    </div>
                    <a href="{{ route('admin.rooms.show', $booking->room) }}" class="btn btn-sm btn-outline-primary w-100">
                        Xem chi tiết phòng
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
