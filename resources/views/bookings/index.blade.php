@extends('layouts.user')
@section('title', 'Đơn Đặt Phòng Của Tôi')

@section('styles')
<style>
    .my-bk-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        padding: 48px 0 80px;
    }

    .my-bk-header {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
        padding: 28px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .my-bk-header h1 { font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; }
    .my-bk-header p { font-size: 13px; color: #64748b; margin: 4px 0 0; }

    .my-bk-new-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(249,115,22,0.3);
        transition: all 0.2s;
    }
    .my-bk-new-btn:hover { transform: translateY(-1px); color: #fff; }

    /* Filter tabs */
    .my-bk-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .my-bk-tab {
        padding: 7px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        color: #64748b;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        text-decoration: none;
        transition: all 0.2s;
    }
    .my-bk-tab:hover, .my-bk-tab.active {
        background: #f97316;
        border-color: #f97316;
        color: #fff;
    }

    /* Cards */
    .bk-list-item {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        padding: 20px;
        margin-bottom: 14px;
        display: flex;
        gap: 16px;
        align-items: center;
        transition: box-shadow 0.2s, transform 0.2s;
        text-decoration: none;
        color: inherit;
    }
    .bk-list-item:hover { box-shadow: 0 8px 32px rgba(0,0,0,0.1); transform: translateY(-2px); }

    .bk-list-img {
        width: 72px; height: 56px;
        border-radius: 12px;
        object-fit: cover;
        flex-shrink: 0;
    }
    .bk-list-img-ph {
        width: 72px; height: 56px;
        border-radius: 12px;
        background: linear-gradient(135deg, #e2e8f0, #f1f5f9);
        display: flex; align-items: center; justify-content: center;
        color: #94a3b8;
        flex-shrink: 0;
    }

    .bk-list-info { flex: 1; min-width: 0; }
    .bk-list-room { font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bk-list-meta { font-size: 12px; color: #94a3b8; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .bk-list-meta span { display: flex; align-items: center; gap: 4px; }

    .bk-list-right { text-align: right; flex-shrink: 0; }
    .bk-list-price { font-size: 18px; font-weight: 800; color: #f97316; margin-bottom: 6px; }
    .bk-list-price small { font-size: 11px; color: #94a3b8; font-weight: 400; }

    .bk-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .bk-badge.pending   { background: #fef3c7; color: #92400e; }
    .bk-badge.paid      { background: #d1fae5; color: #065f46; }
    .bk-badge.cancelled { background: #fee2e2; color: #7f1d1d; }
    .bk-badge.converted { background: #dbeafe; color: #1e3a8a; }

    .bk-empty {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.06);
    }
    .bk-empty svg { color: #cbd5e1; margin-bottom: 16px; }
    .bk-empty h3 { font-size: 18px; font-weight: 700; color: #475569; margin-bottom: 8px; }
    .bk-empty p { font-size: 14px; color: #94a3b8; margin-bottom: 20px; }
</style>
@endsection

@section('content')
<div class="my-bk-page">
    <div class="container">

        {{-- Header --}}
        <div class="my-bk-header">
            <div>
                <h1>📋 Đơn Đặt Phòng Của Tôi</h1>
                <p>Quản lý tất cả đơn đặt phòng của bạn</p>
            </div>
            <a href="{{ route('rooms.index') }}" class="my-bk-new-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                Đặt Phòng Mới
            </a>
        </div>

        @if(session('success'))
            <div style="background:#d1fae5;border:1.5px solid #6ee7b7;border-radius:12px;padding:14px 18px;margin-bottom:16px;color:#064e3b;font-size:14px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Filter tabs --}}
        <div class="my-bk-tabs">
            <a href="{{ route('bookings.my') }}" class="my-bk-tab {{ !request('status') ? 'active' : '' }}">Tất cả</a>
            <a href="{{ route('bookings.my', ['status' => 'pending']) }}" class="my-bk-tab {{ request('status') === 'pending' ? 'active' : '' }}">⏳ Chờ thanh toán</a>
            <a href="{{ route('bookings.my', ['status' => 'paid']) }}" class="my-bk-tab {{ request('status') === 'paid' ? 'active' : '' }}">✅ Đã cọc</a>
            <a href="{{ route('bookings.my', ['status' => 'converted']) }}" class="my-bk-tab {{ request('status') === 'converted' ? 'active' : '' }}">📋 Đã có HĐ</a>
            <a href="{{ route('bookings.my', ['status' => 'cancelled']) }}" class="my-bk-tab {{ request('status') === 'cancelled' ? 'active' : '' }}">❌ Đã hủy</a>
        </div>

        {{-- List --}}
        @forelse($bookings as $booking)
            <a href="{{ route('bookings.show', $booking) }}" class="bk-list-item">
                {{-- Image --}}
                @if($booking->room->images->first())
                    <img src="{{ asset('storage/' . $booking->room->images->first()->image_path) }}" alt="" class="bk-list-img">
                @else
                    <div class="bk-list-img-ph">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                    </div>
                @endif

                {{-- Info --}}
                <div class="bk-list-info">
                    <div class="bk-list-room">{{ $booking->room->name }}</div>
                    <div class="bk-list-meta">
                        <span>
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {{ $booking->room->district_name ?? $booking->room->province_name ?? 'N/A' }}
                        </span>
                        <span>
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            {{ $booking->created_at->diffForHumans() }}
                        </span>
                        <span>
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                            {{ $booking->num_people }} người
                        </span>
                    </div>
                </div>

                {{-- Right --}}
                <div class="bk-list-right">
                    <div class="bk-list-price">
                        {{ number_format($booking->deposit_amount) }}đ
                        <small>/ cọc</small>
                    </div>
                    <span class="bk-badge {{ $booking->status }}">
                        @if($booking->status === 'pending') ⏳
                        @elseif($booking->status === 'paid') ✅
                        @elseif($booking->status === 'cancelled') ❌
                        @else 📋
                        @endif
                        {{ $booking->statusLabel() }}
                    </span>
                </div>
            </a>
        @empty
            <div class="bk-empty">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                <h3>Chưa có đơn đặt phòng nào</h3>
                <p>Hãy khám phá danh sách phòng và đặt ngay!</p>
                <a href="{{ route('rooms.index') }}" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;text-decoration:none;padding:12px 24px;border-radius:12px;font-weight:600;">
                    Xem danh sách phòng →
                </a>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($bookings->hasPages())
            <div style="margin-top:20px;">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
