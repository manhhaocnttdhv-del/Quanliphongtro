@extends('layouts.admin')
@section('title', 'Quản Lý Đơn Đặt Phòng')

@section('content')
<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">📋 Quản Lý Đơn Đặt Phòng</h1>
            <p class="text-muted small mb-0">Quản lý toàn bộ đơn đặt phòng và xác nhận thanh toán cọc</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ⚠️ {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left:4px solid #f59e0b !important;">
                <div class="card-body text-center">
                    <div class="fs-1 fw-bold text-warning">{{ $stats['pending'] }}</div>
                    <div class="small text-muted">⏳ Chờ thanh toán</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left:4px solid #10b981 !important;">
                <div class="card-body text-center">
                    <div class="fs-1 fw-bold text-success">{{ $stats['paid'] }}</div>
                    <div class="small text-muted">✅ Đã cọc</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left:4px solid #3b82f6 !important;">
                <div class="card-body text-center">
                    <div class="fs-1 fw-bold text-primary">{{ $stats['converted'] }}</div>
                    <div class="small text-muted">📋 Đã tạo HĐ</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left:4px solid #ef4444 !important;">
                <div class="card-body text-center">
                    <div class="fs-1 fw-bold text-danger">{{ $stats['cancelled'] }}</div>
                    <div class="small text-muted">❌ Đã hủy</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-1">Lọc theo trạng thái</label>
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>⏳ Chờ thanh toán</option>
                        <option value="paid"      {{ request('status') === 'paid'      ? 'selected' : '' }}>✅ Đã cọc</option>
                        <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>📋 Đã tạo hợp đồng</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>❌ Đã hủy</option>
                    </select>
                </div>
                @if(request('status'))
                <div class="col-auto d-flex align-items-end">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-light">Xóa lọc</a>
                </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 fw-semibold small text-muted">#</th>
                            <th class="py-3 fw-semibold small text-muted">Phòng</th>
                            <th class="py-3 fw-semibold small text-muted">Người đặt</th>
                            <th class="py-3 fw-semibold small text-muted">Tiền cọc</th>
                            <th class="py-3 fw-semibold small text-muted">Thanh toán</th>
                            <th class="py-3 fw-semibold small text-muted">Trạng thái</th>
                            <th class="py-3 fw-semibold small text-muted">Hết hạn</th>
                            <th class="py-3 fw-semibold small text-muted">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td class="px-4">
                                <span class="fw-bold text-muted">#{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ Str::limit($booking->room->name, 30) }}</div>
                                <div class="text-muted small">{{ $booking->room->district_name ?? $booking->room->province_name ?? '' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $booking->tenant_name }}</div>
                                <div class="text-muted small">{{ $booking->tenant_phone }}</div>
                            </td>
                            <td>
                                <span class="fw-bold text-warning">{{ number_format($booking->deposit_amount) }}đ</span>
                            </td>
                            <td>
                                <span class="badge {{ $booking->payment_method === 'online' ? 'bg-info' : 'bg-secondary' }}">
                                    {{ $booking->payment_method === 'online' ? '💳 Online' : '🏢 Offline' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $badge = match($booking->status) {
                                        'pending'   => 'warning',
                                        'paid'      => 'success',
                                        'cancelled' => 'danger',
                                        'converted' => 'info',
                                        default     => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ $booking->statusLabel() }}</span>
                            </td>
                            <td>
                                @if($booking->expired_at && $booking->status === 'pending')
                                    @if($booking->expired_at->isPast())
                                        <span class="text-danger small">Đã hết hạn</span>
                                    @else
                                        <span class="text-warning small">{{ $booking->expired_at->diffForHumans() }}</span>
                                    @endif
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                        Chi tiết
                                    </a>
                                    @if($booking->status === 'pending' && $booking->payment_method === 'offline')
                                        <form action="{{ route('admin.bookings.confirm-payment', $booking) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Xác nhận đã nhận tiền cọc?')">
                                                ✓ Xác nhận
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div style="font-size:48px;margin-bottom:12px;">📭</div>
                                Không có đơn đặt phòng nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($bookings->hasPages())
        <div class="card-footer bg-white border-top-0">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
