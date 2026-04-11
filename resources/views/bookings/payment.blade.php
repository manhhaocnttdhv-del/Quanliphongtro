@extends('layouts.user')
@section('title', 'Thanh Toán Cọc - Đơn #' . $booking->id)

@section('styles')
<style>
    .pay-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        padding: 56px 0 80px;
    }

    .pay-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 28px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .pay-grid { grid-template-columns: 1fr; }
    }

    .pay-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.07);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .pay-card-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
    }
    .pay-card-header::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 130px; height: 130px;
        background: rgba(249,115,22,0.15);
        border-radius: 50%;
    }
    .pay-card-header h2 { color: #fff; font-size: 18px; font-weight: 700; margin: 0 0 4px; position: relative; z-index: 1; }
    .pay-card-header p { color: #94a3b8; font-size: 13px; margin: 0; position: relative; z-index: 1; }

    .pay-card-body { padding: 28px; }

    /* Countdown timer */
    .pay-countdown {
        background: linear-gradient(135deg, #fef3c7, #fff);
        border: 2px solid #fde68a;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        margin-bottom: 24px;
    }
    .pay-countdown-label { font-size: 12px; font-weight: 600; color: #78350f; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 10px; }
    .pay-countdown-time { font-size: 40px; font-weight: 800; color: #f97316; font-variant-numeric: tabular-nums; letter-spacing: 2px; }
    .pay-countdown-sub { font-size: 12px; color: #b45309; margin-top: 6px; }

    /* Amount display */
    .pay-amount-box {
        background: linear-gradient(135deg, #fff7ed, #fff);
        border: 2px solid #fed7aa;
        border-radius: 16px;
        padding: 24px;
        text-align: center;
        margin-bottom: 20px;
    }
    .pay-amount-label { font-size: 12px; font-weight: 600; color: #9a3412; text-transform: uppercase; letter-spacing: 0.6px; }
    .pay-amount-value { font-size: 42px; font-weight: 800; color: #f97316; margin: 8px 0; }
    .pay-amount-note { font-size: 13px; color: #94a3b8; }

    /* Bank transfer info */
    .pay-transfer-box {
        background: #f8fafc;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 16px;
    }
    .pay-transfer-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #94a3b8; margin-bottom: 14px; }
    .pay-transfer-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e2e8f0;
        font-size: 14px;
    }
    .pay-transfer-row:last-child { border: none; padding-bottom: 0; }
    .pay-transfer-key { color: #64748b; }
    .pay-transfer-val { font-weight: 700; color: #1e293b; font-family: monospace; }
    .pay-transfer-val.copy-btn {
        cursor: pointer;
        color: #f97316;
        border: 1px solid #fed7aa;
        border-radius: 6px;
        padding: 2px 10px;
        font-size: 13px;
        transition: all 0.2s;
    }
    .pay-transfer-val.copy-btn:hover { background: #fff7ed; }

    /* Confirm button */
    .pay-confirm-btn {
        width: 100%;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 16px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(16,185,129,0.35);
        text-decoration: none;
        margin-bottom: 10px;
    }
    .pay-confirm-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,185,129,0.45); color: #fff; }

    .pay-cancel-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #94a3b8;
        font-size: 13px;
        text-decoration: none;
        transition: color 0.2s;
    }
    .pay-cancel-link:hover { color: #ef4444; }

    /* Booking details sidebar */
    .pay-detail-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.07);
        padding: 24px;
        position: sticky;
        top: 20px;
    }
    .pay-detail-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #94a3b8; margin-bottom: 16px; }
    .pay-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
        font-size: 13px;
        gap: 10px;
    }
    .pay-detail-key { color: #64748b; flex-shrink: 0; }
    .pay-detail-val { font-weight: 600; color: #1e293b; text-align: right; }

    .pay-room-mini {
        display: flex;
        gap: 12px;
        align-items: center;
        background: #f8fafc;
        border-radius: 12px;
        padding: 12px;
        margin-bottom: 16px;
    }
    .pay-room-mini img { width: 56px; height: 44px; border-radius: 8px; object-fit: cover; flex-shrink: 0; }
    .pay-room-mini .room-placeholder { width: 56px; height: 44px; border-radius: 8px; background: #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: #94a3b8; }
    .pay-room-mini .room-name { font-size: 13px; font-weight: 700; color: #1e293b; line-height: 1.3; }
    .pay-room-mini .room-price { font-size: 14px; font-weight: 800; color: #f97316; }

    .pay-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
</style>
@endsection

@section('content')
<div class="pay-page">
    <div class="container">

        @if(session('info'))
            <div style="background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#1e3a8a;font-size:14px;">
                ℹ️ {{ session('info') }}
            </div>
        @endif

        <div class="pay-grid">

            {{-- LEFT: Payment --}}
            <div>
                <div class="pay-card">
                    <div class="pay-card-header">
                        <h2>💳 Thanh Toán Tiền Cọc</h2>
                        <p>Đơn đặt phòng #{{ $booking->id }} — Hoàn tất trong vòng 24 giờ</p>
                    </div>
                    <div class="pay-card-body">

                        {{-- Countdown --}}
                        <div class="pay-countdown">
                            <div class="pay-countdown-label">⏳ Thời gian còn lại để thanh toán</div>
                            <div class="pay-countdown-time" id="countdown">--:--:--</div>
                            <div class="pay-countdown-sub">Sau thời gian này, đơn đặt sẽ tự động bị hủy</div>
                        </div>

                        {{-- Amount --}}
                        <div class="pay-amount-box">
                            <div class="pay-amount-label">Số tiền đặt cọc</div>
                            <div class="pay-amount-value">{{ number_format($booking->deposit_amount) }}đ</div>
                            <div class="pay-amount-note">Bằng chữ: {{ 'Số tiền ' . number_format($booking->deposit_amount) . ' đồng' }}</div>
                        </div>

                        @if($booking->payment_method === 'offline')
                            {{-- Offline: Hướng dẫn --}}
                            <div class="pay-transfer-box">
                                <div class="pay-transfer-title">📍 Thông Tin Thanh Toán Tại Chỗ</div>
                                <div class="pay-transfer-row">
                                    <span class="pay-transfer-key">Liên hệ chủ nhà</span>
                                    <span class="pay-transfer-val">{{ $booking->room->landlord->name ?? 'Chủ nhà' }}</span>
                                </div>
                                <div class="pay-transfer-row">
                                    <span class="pay-transfer-key">SĐT chủ nhà</span>
                                    <span class="pay-transfer-val">{{ $booking->room->landlord->phone ?? 'Liên hệ qua hệ thống' }}</span>
                                </div>
                                <div class="pay-transfer-row">
                                    <span class="pay-transfer-key">Mã đơn đặt</span>
                                    <span class="pay-transfer-val" style="color:#f97316;">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <div style="margin-top:14px;padding:12px;background:#fffbeb;border-radius:10px;font-size:13px;color:#78350f;">
                                    ⚠️ Vui lòng mang theo <strong>CCCD</strong> và đến gặp trực tiếp chủ nhà để nộp tiền cọc và xác nhận đơn.
                                </div>
                            </div>

                            {{-- Simulate confirm (trong demo) --}}
                            <form action="{{ route('bookings.payment.success', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="pay-confirm-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    Xác Nhận Đã Nộp Tiền (Demo)
                                </button>
                            </form>

                        @else
                            {{-- Online: Chuyển khoản --}}
                            <div class="pay-transfer-box">
                                <div class="pay-transfer-title">🏦 Thông Tin Chuyển Khoản</div>
                                <div class="pay-transfer-row">
                                    <span class="pay-transfer-key">Ngân hàng</span>
                                    <span class="pay-transfer-val">Vietcombank</span>
                                </div>
                                <div class="pay-transfer-row">
                                    <span class="pay-transfer-key">Số tài khoản</span>
                                    <span class="pay-transfer-val copy-btn" onclick="copyText('1234567890', this)">1234 5678 90</span>
                                </div>
                                <div class="pay-transfer-row">
                                    <span class="pay-transfer-key">Tên tài khoản</span>
                                    <span class="pay-transfer-val">QUAN LY PHONG TRO</span>
                                </div>
                                <div class="pay-transfer-row">
                                    <span class="pay-transfer-key">Nội dung CK</span>
                                    <span class="pay-transfer-val copy-btn" onclick="copyText('DATCOC{{ $booking->id }}', this)">DATCOC{{ $booking->id }}</span>
                                </div>
                                <div class="pay-transfer-row">
                                    <span class="pay-transfer-key">Số tiền</span>
                                    <span class="pay-transfer-val" style="color:#f97316;">{{ number_format($booking->deposit_amount) }}đ</span>
                                </div>
                            </div>

                            <form action="{{ route('bookings.payment.success', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" class="pay-confirm-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                    Tôi Đã Chuyển Khoản Xong
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn đặt này không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="pay-cancel-link" style="background:none;border:none;width:100%;cursor:pointer;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                Hủy đơn đặt
                            </button>
                        </form>

                    </div>
                </div>
            </div>

            {{-- RIGHT: Booking details --}}
            <div>
                <div class="pay-detail-card">
                    <div class="pay-detail-title">Chi tiết đơn đặt phòng</div>

                    <div class="pay-room-mini">
                        @if($booking->room->images->first())
                            <img src="{{ asset('storage/' . $booking->room->images->first()->image_path) }}" alt="">
                        @else
                            <div class="room-placeholder">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            </div>
                        @endif
                        <div>
                            <div class="room-name">{{ $booking->room->name }}</div>
                            <div class="room-price">{{ number_format($booking->room->price) }}đ/tháng</div>
                        </div>
                    </div>

                    <div class="pay-detail-row">
                        <span class="pay-detail-key">Mã đơn</span>
                        <span class="pay-detail-val" style="color:#f97316;">#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="pay-detail-row">
                        <span class="pay-detail-key">Trạng thái</span>
                        <span class="pay-status-badge">⏳ {{ $booking->statusLabel() }}</span>
                    </div>
                    <div class="pay-detail-row">
                        <span class="pay-detail-key">Người thuê</span>
                        <span class="pay-detail-val">{{ $booking->tenant_name }}</span>
                    </div>
                    <div class="pay-detail-row">
                        <span class="pay-detail-key">Số điện thoại</span>
                        <span class="pay-detail-val">{{ $booking->tenant_phone }}</span>
                    </div>
                    @if($booking->tenant_cccd)
                    <div class="pay-detail-row">
                        <span class="pay-detail-key">CCCD</span>
                        <span class="pay-detail-val">{{ $booking->tenant_cccd }}</span>
                    </div>
                    @endif
                    <div class="pay-detail-row">
                        <span class="pay-detail-key">Số người</span>
                        <span class="pay-detail-val">{{ $booking->num_people }} người</span>
                    </div>
                    @if($booking->move_in_date)
                    <div class="pay-detail-row">
                        <span class="pay-detail-key">Ngày vào ở</span>
                        <span class="pay-detail-val">{{ $booking->move_in_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    <div class="pay-detail-row">
                        <span class="pay-detail-key">Thanh toán</span>
                        <span class="pay-detail-val">{{ $booking->paymentMethodLabel() }}</span>
                    </div>
                    <div style="border-top:1px solid #f1f5f9;margin:12px 0;"></div>
                    <div class="pay-detail-row">
                        <span class="pay-detail-key">Tiền cọc</span>
                        <span class="pay-detail-val" style="color:#f97316;font-size:18px;">{{ number_format($booking->deposit_amount) }}đ</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var expiredAt = new Date('{{ $booking->expired_at->toIso8601String() }}');

    function updateCountdown() {
        var now  = new Date();
        var diff = expiredAt - now;

        if (diff <= 0) {
            document.getElementById('countdown').textContent = '00:00:00';
            document.getElementById('countdown').style.color = '#ef4444';
            return;
        }

        var h = Math.floor(diff / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);

        document.getElementById('countdown').textContent =
            String(h).padStart(2, '0') + ':' +
            String(m).padStart(2, '0') + ':' +
            String(s).padStart(2, '0');

        if (diff < 3600000) { // <1 hour → red
            document.getElementById('countdown').style.color = '#ef4444';
        }
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    // Copy to clipboard
    window.copyText = function (text, el) {
        navigator.clipboard.writeText(text).then(function () {
            var orig = el.textContent;
            el.textContent = '✓ Đã copy!';
            el.style.background = '#f0fdf4';
            el.style.color = '#10b981';
            setTimeout(function () {
                el.textContent = orig;
                el.style.background = '';
                el.style.color = '';
            }, 2000);
        });
    };
})();
</script>
@endsection
