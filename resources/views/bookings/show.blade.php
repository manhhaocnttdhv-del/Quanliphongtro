@extends('layouts.user')
@section('title', 'Chi Tiết Đơn Đặt Phòng #' . $booking->id)

@section('styles')
<style>
    .bs-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        padding: 48px 0 80px;
    }

    .bs-breadcrumb { font-size: 13px; color: #94a3b8; margin-bottom: 24px; }
    .bs-breadcrumb a { color: #64748b; text-decoration: none; }
    .bs-breadcrumb a:hover { color: #f97316; }
    .bs-breadcrumb span { color: #cbd5e1; margin: 0 6px; }

    .bs-header {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 28px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .bs-header-left h1 { font-size: 22px; font-weight: 800; color: #1e293b; margin: 0 0 6px; }
    .bs-header-left p { font-size: 13px; color: #64748b; margin: 0; }
    .bs-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: 24px;
        font-size: 14px;
        font-weight: 700;
    }
    .bs-status-badge.pending  { background: #fef3c7; color: #92400e; border: 1.5px solid #fde68a; }
    .bs-status-badge.paid     { background: #d1fae5; color: #064e3b; border: 1.5px solid #6ee7b7; }
    .bs-status-badge.cancelled{ background: #fee2e2; color: #7f1d1d; border: 1.5px solid #fca5a5; }
    .bs-status-badge.converted{ background: #dbeafe; color: #1e3a8a; border: 1.5px solid #93c5fd; }

    .bs-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .bs-grid { grid-template-columns: 1fr; }
    }

    .bs-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        padding: 24px;
        margin-bottom: 16px;
    }
    .bs-card-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94a3b8;
        margin-bottom: 16px;
        padding-bottom: 10px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .bs-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f8fafc;
        font-size: 14px;
    }
    .bs-info-row:last-child { border: none; padding-bottom: 0; }
    .bs-info-key { color: #64748b; }
    .bs-info-val { font-weight: 600; color: #1e293b; text-align: right; }

    /* Timeline */
    .bs-timeline { padding: 8px 0; }
    .bs-tl-item {
        display: flex;
        gap: 14px;
        margin-bottom: 16px;
        position: relative;
    }
    .bs-tl-item:not(:last-child)::after {
        content: '';
        position: absolute;
        left: 15px; top: 32px;
        width: 2px;
        height: calc(100% - 8px);
        background: #e2e8f0;
    }
    .bs-tl-dot {
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        z-index: 1;
    }
    .bs-tl-dot.done    { background: #10b981; color: #fff; }
    .bs-tl-dot.active  { background: #f97316; color: #fff; box-shadow: 0 0 0 4px rgba(249,115,22,0.2); }
    .bs-tl-dot.pending { background: #f1f5f9; color: #94a3b8; }
    .bs-tl-content { padding-top: 4px; }
    .bs-tl-title { font-size: 14px; font-weight: 600; color: #1e293b; }
    .bs-tl-sub { font-size: 12px; color: #94a3b8; margin-top: 2px; }

    /* Actions */
    .bs-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        margin-bottom: 10px;
    }
    .btn-pay { background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; box-shadow: 0 4px 12px rgba(249,115,22,0.3); }
    .btn-pay:hover { transform: translateY(-1px); color: #fff; }
    .btn-cancel { background: #fee2e2; color: #b91c1c; border: 1.5px solid #fca5a5; }
    .btn-cancel:hover { background: #fecaca; }
    .btn-rooms { background: #f1f5f9; color: #475569; }
    .btn-rooms:hover { background: #e2e8f0; color: #475569; }

    /* Room mini */
    .bs-room-mini {
        display: flex; gap: 12px; align-items: center;
        background: #f8fafc; border-radius: 12px; padding: 14px; margin-bottom: 16px;
    }
    .bs-room-mini img { width: 64px; height: 50px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
    .bs-room-mini .rn { font-size: 14px; font-weight: 700; color: #1e293b; line-height: 1.3; margin-bottom: 4px; }
    .bs-room-mini .rp { font-size: 16px; font-weight: 800; color: #f97316; }
</style>
@endsection

@section('content')
<div class="bs-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <div class="bs-breadcrumb">
            <a href="{{ route('rooms.index') }}">Phòng trợ</a>
            <span>›</span>
            <a href="{{ route('bookings.my') }}">Đơn đặt của tôi</a>
            <span>›</span>
            Đơn #{{ $booking->id }}
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div style="background:#d1fae5;border:1.5px solid #6ee7b7;border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#064e3b;font-size:14px;display:flex;align-items:center;gap:8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:#fee2e2;border:1.5px solid #fca5a5;border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#7f1d1d;font-size:14px;">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="bs-header">
            <div class="bs-header-left">
                <h1>Đơn Đặt Phòng #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</h1>
                <p>Tạo lúc {{ $booking->created_at->format('H:i, d/m/Y') }}</p>
            </div>
            <span class="bs-status-badge {{ $booking->status }}">
                @if($booking->status === 'pending') ⏳
                @elseif($booking->status === 'paid') ✅
                @elseif($booking->status === 'cancelled') ❌
                @else 📋
                @endif
                {{ $booking->statusLabel() }}
            </span>
        </div>

        <div class="bs-grid">
            {{-- LEFT --}}
            <div>
                {{-- Thông tin người thuê --}}
                <div class="bs-card">
                    <div class="bs-card-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        Thông Tin Người Thuê
                    </div>
                    <div class="bs-info-row">
                        <span class="bs-info-key">Họ tên</span>
                        <span class="bs-info-val">{{ $booking->tenant_name }}</span>
                    </div>
                    <div class="bs-info-row">
                        <span class="bs-info-key">Số điện thoại</span>
                        <span class="bs-info-val">{{ $booking->tenant_phone }}</span>
                    </div>
                    @if($booking->tenant_cccd)
                    <div class="bs-info-row">
                        <span class="bs-info-key">CCCD / CMND</span>
                        <span class="bs-info-val">{{ $booking->tenant_cccd }}</span>
                    </div>
                    @endif
                    <div class="bs-info-row">
                        <span class="bs-info-key">Số người ở</span>
                        <span class="bs-info-val">{{ $booking->num_people }} người</span>
                    </div>
                    @if($booking->move_in_date)
                    <div class="bs-info-row">
                        <span class="bs-info-key">Ngày dự kiến vào ở</span>
                        <span class="bs-info-val">{{ $booking->move_in_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($booking->note)
                    <div class="bs-info-row">
                        <span class="bs-info-key">Ghi chú</span>
                        <span class="bs-info-val">{{ $booking->note }}</span>
                    </div>
                    @endif
                </div>

                {{-- Thông tin thanh toán --}}
                <div class="bs-card">
                    <div class="bs-card-title">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        Thanh Toán
                    </div>
                    <div class="bs-info-row">
                        <span class="bs-info-key">Tiền đặt cọc</span>
                        <span class="bs-info-val" style="color:#f97316;font-size:18px;">{{ number_format($booking->deposit_amount) }}đ</span>
                    </div>
                    <div class="bs-info-row">
                        <span class="bs-info-key">Phương thức</span>
                        <span class="bs-info-val">{{ $booking->paymentMethodLabel() }}</span>
                    </div>
                    @if($booking->paid_at)
                    <div class="bs-info-row">
                        <span class="bs-info-key">Thanh toán lúc</span>
                        <span class="bs-info-val">{{ $booking->paid_at->format('H:i, d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($booking->payment_ref)
                    <div class="bs-info-row">
                        <span class="bs-info-key">Mã giao dịch</span>
                        <span class="bs-info-val" style="font-family:monospace;color:#10b981;">{{ $booking->payment_ref }}</span>
                    </div>
                    @endif
                    @if($booking->status === 'pending' && $booking->expired_at)
                    <div class="bs-info-row">
                        <span class="bs-info-key">Hết hạn lúc</span>
                        <span class="bs-info-val" style="color:#f59e0b;">{{ $booking->expired_at->format('H:i, d/m/Y') }}</span>
                    </div>
                    @endif
                </div>

                {{-- Hợp đồng (nếu đã convert) --}}
                @if($booking->status === 'converted' && $booking->contract)
                <div class="bs-card" style="border:2px solid #bfdbfe;">
                    <div class="bs-card-title" style="color:#1e3a8a;">
                        📋 Hợp Đồng Đã Được Tạo
                    </div>
                    <div class="bs-info-row">
                        <span class="bs-info-key">Mã hợp đồng</span>
                        <span class="bs-info-val">#{{ $booking->contract->id }}</span>
                    </div>
                    <div class="bs-info-row">
                        <span class="bs-info-key">Ngày bắt đầu</span>
                        <span class="bs-info-val">{{ $booking->contract->start_date->format('d/m/Y') }}</span>
                    </div>
                    <a href="{{ route('user.contracts.show', $booking->contract) }}" style="display:flex;align-items:center;gap:6px;color:#3b82f6;font-size:14px;font-weight:600;text-decoration:none;margin-top:12px;">
                        Xem chi tiết hợp đồng →
                    </a>
                </div>
                @endif
            </div>

            {{-- RIGHT --}}
            <div>
                {{-- Room --}}
                <div class="bs-card">
                    <div class="bs-card-title">🏠 Phòng đã đặt</div>
                    <div class="bs-room-mini">
                        @if($booking->room->images->first())
                            <img src="{{ asset('storage/' . $booking->room->images->first()->image_path) }}" alt="">
                        @else
                            <div style="width:64px;height:50px;border-radius:10px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#94a3b8;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            </div>
                        @endif
                        <div>
                            <div class="rn">{{ $booking->room->name }}</div>
                            <div class="rp">{{ number_format($booking->room->price) }}đ/tháng</div>
                        </div>
                    </div>
                    @if($booking->room->fullAddress())
                    <div style="font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:4px;margin-bottom:12px;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        {{ $booking->room->fullAddress() }}
                    </div>
                    @endif
                    <a href="{{ route('rooms.show', $booking->room) }}" style="display:flex;align-items:center;gap:4px;color:#f97316;font-size:13px;font-weight:600;text-decoration:none;">
                        Xem phòng →
                    </a>
                </div>

                {{-- Timeline --}}
                <div class="bs-card">
                    <div class="bs-card-title">📈 Tiến trình đặt phòng</div>
                    <div class="bs-timeline">
                        <div class="bs-tl-item">
                            <div class="bs-tl-dot done">✓</div>
                            <div class="bs-tl-content">
                                <div class="bs-tl-title">Gửi đơn đặt phòng</div>
                                <div class="bs-tl-sub">{{ $booking->created_at->format('H:i d/m/Y') }}</div>
                            </div>
                        </div>
                        <div class="bs-tl-item">
                            <div class="bs-tl-dot {{ in_array($booking->status, ['paid','converted']) ? 'done' : ($booking->status === 'cancelled' ? 'pending' : 'active') }}">
                                {{ in_array($booking->status, ['paid','converted']) ? '✓' : '2' }}
                            </div>
                            <div class="bs-tl-content">
                                <div class="bs-tl-title">Thanh toán tiền cọc</div>
                                <div class="bs-tl-sub">
                                    @if($booking->paid_at)
                                        {{ $booking->paid_at->format('H:i d/m/Y') }}
                                    @elseif($booking->status === 'cancelled')
                                        Đã hủy
                                    @else
                                        Đang chờ...
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="bs-tl-item">
                            <div class="bs-tl-dot {{ $booking->status === 'converted' ? 'done' : 'pending' }}">
                                {{ $booking->status === 'converted' ? '✓' : '3' }}
                            </div>
                            <div class="bs-tl-content">
                                <div class="bs-tl-title">Ký hợp đồng</div>
                                <div class="bs-tl-sub">
                                    @if($booking->contract)
                                        {{ $booking->contract->created_at->format('H:i d/m/Y') }}
                                    @else
                                        Chờ admin tạo hợp đồng
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="bs-card">
                    <div class="bs-card-title">⚡ Thao tác</div>
                    @if($booking->status === 'pending')
                        <a href="{{ route('bookings.payment', $booking) }}" class="bs-action-btn btn-pay">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                            Thanh Toán Ngay
                        </a>
                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Hủy đơn đặt này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bs-action-btn btn-cancel">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                Hủy Đơn Đặt
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('bookings.my') }}" class="bs-action-btn btn-rooms">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Xem tất cả đơn của tôi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
