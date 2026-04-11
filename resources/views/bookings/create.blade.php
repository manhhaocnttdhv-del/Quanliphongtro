@extends('layouts.user')
@section('title', 'Đặt Phòng - ' . $room->name)

@section('styles')
<style>
    .bk-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        padding: 48px 0 80px;
    }

    /* Breadcrumb */
    .bk-breadcrumb {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 28px;
    }
    .bk-breadcrumb a { color: #64748b; text-decoration: none; }
    .bk-breadcrumb a:hover { color: #f97316; }
    .bk-breadcrumb span { color: #cbd5e1; margin: 0 6px; }

    /* Grid layout */
    .bk-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 28px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .bk-grid { grid-template-columns: 1fr; }
    }

    /* Progress steps */
    .bk-progress {
        display: flex;
        align-items: center;
        gap: 0;
        margin-bottom: 28px;
        background: #fff;
        border-radius: 16px;
        padding: 20px 28px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    }
    .bk-step {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }
    .bk-step:last-child { flex: none; }
    .bk-step-num {
        width: 34px; height: 34px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700;
        flex-shrink: 0;
    }
    .bk-step-num.active { background: #f97316; color: #fff; box-shadow: 0 4px 12px rgba(249,115,22,0.4); }
    .bk-step-num.pending { background: #f1f5f9; color: #94a3b8; }
    .bk-step-num.done { background: #10b981; color: #fff; }
    .bk-step-info .title { font-size: 13px; font-weight: 600; color: #1e293b; }
    .bk-step-info .sub { font-size: 11px; color: #94a3b8; margin-top: 1px; }
    .bk-step-line { flex: 1; height: 2px; background: #e2e8f0; margin: 0 12px; }
    .bk-step-line.done { background: #10b981; }

    /* Form card */
    .bk-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.07);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .bk-card-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
    }
    .bk-card-header::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 130px; height: 130px;
        background: rgba(249,115,22,0.15);
        border-radius: 50%;
    }
    .bk-card-header h2 {
        color: #fff; font-size: 18px; font-weight: 700;
        margin: 0 0 4px; position: relative; z-index: 1;
    }
    .bk-card-header p {
        color: #94a3b8; font-size: 13px;
        margin: 0; position: relative; z-index: 1;
    }
    .bk-card-body { padding: 28px; }

    /* Section titles */
    .bk-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #94a3b8;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Field groups */
    .bk-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .bk-row.single { grid-template-columns: 1fr; }
    .bk-row.triple { grid-template-columns: 1fr 1fr 1fr; }
    @media (max-width: 640px) {
        .bk-row, .bk-row.triple { grid-template-columns: 1fr; }
    }

    .bk-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .bk-label {
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .bk-label .req { color: #ef4444; }
    .bk-input {
        width: 100%;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 14px;
        color: #1e293b;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }
    .bk-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    }
    .bk-input.is-invalid { border-color: #ef4444 !important; }
    .bk-error { font-size: 12px; color: #ef4444; display: flex; align-items: center; gap: 4px; }
    .bk-hint { font-size: 11px; color: #94a3b8; }

    /* Payment method */
    .bk-payment-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }
    @media (max-width: 640px) {
        .bk-payment-options { grid-template-columns: 1fr; }
    }
    .bk-payment-option {
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    .bk-payment-option:hover { border-color: #f97316; background: #fff7ed; }
    .bk-payment-option.selected {
        border-color: #f97316;
        background: linear-gradient(135deg, #fff7ed, #fff);
        box-shadow: 0 4px 16px rgba(249,115,22,0.15);
    }
    .bk-payment-option input[type="radio"] {
        position: absolute;
        opacity: 0; width: 0; height: 0;
    }
    .bk-payment-icon {
        font-size: 28px;
        margin-bottom: 8px;
        display: block;
    }
    .bk-payment-name {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
    }
    .bk-payment-desc {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 2px;
    }
    .bk-payment-check {
        position: absolute;
        top: 10px; right: 10px;
        width: 20px; height: 20px;
        border-radius: 50%;
        background: #f97316;
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 11px;
    }
    .bk-payment-option.selected .bk-payment-check { display: flex; }

    /* Deposit input */
    .bk-deposit-wrap {
        position: relative;
    }
    .bk-deposit-wrap input {
        padding-right: 40px;
    }
    .bk-deposit-unit {
        position: absolute;
        right: 14px; top: 50%;
        transform: translateY(-50%);
        font-size: 13px;
        color: #94a3b8;
        font-weight: 500;
    }

    /* Submit */
    .bk-submit-btn {
        width: 100%;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 16px 24px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-shadow: 0 4px 16px rgba(249,115,22,0.35);
        margin-top: 20px;
    }
    .bk-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(249,115,22,0.45);
    }
    .bk-submit-btn:active { transform: translateY(0); }

    /* Sidebar: room preview */
    .bk-room-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.07);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }
    .bk-room-img { width: 100%; height: 200px; object-fit: cover; display: block; }
    .bk-room-placeholder {
        width: 100%; height: 200px;
        background: linear-gradient(135deg, #1e293b, #334155);
        display: flex; align-items: center; justify-content: center;
        color: #475569;
    }
    .bk-room-body { padding: 20px; }
    .bk-room-name { font-weight: 700; font-size: 16px; color: #1e293b; margin-bottom: 6px; }
    .bk-room-location { font-size: 12px; color: #94a3b8; margin-bottom: 14px; display: flex; align-items: center; gap: 4px; }
    .bk-room-price { font-size: 24px; font-weight: 800; color: #f97316; }
    .bk-room-price small { font-size: 13px; font-weight: 400; color: #94a3b8; }
    .bk-room-divider { border: none; border-top: 1px solid #f1f5f9; margin: 14px 0; }
    .bk-room-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .bk-meta-item {
        background: #f8fafc;
        border-radius: 10px;
        padding: 10px 12px;
        text-align: center;
    }
    .bk-meta-item .val { font-size: 14px; font-weight: 700; color: #1e293b; }
    .bk-meta-item .key { font-size: 11px; color: #94a3b8; }

    .bk-summary-box {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1.5px solid #86efac;
        border-radius: 14px;
        padding: 16px;
        margin-top: 16px;
    }
    .bk-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
        margin-bottom: 8px;
        color: #374151;
    }
    .bk-summary-row:last-child { margin-bottom: 0; }
    .bk-summary-row.total {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #86efac;
    }
    .bk-summary-row .sum-price { color: #f97316; font-weight: 700; }
</style>
@endsection

@section('content')
<div class="bk-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <div class="bk-breadcrumb">
            <a href="{{ route('rooms.index') }}">Danh sách phòng</a>
            <span>›</span>
            <a href="{{ route('rooms.show', $room) }}">{{ Str::limit($room->name, 40) }}</a>
            <span>›</span>
            Đặt phòng
        </div>

        {{-- Progress Steps --}}
        <div class="bk-progress">
            <div class="bk-step">
                <div class="bk-step-num active">1</div>
                <div class="bk-step-info">
                    <div class="title">Thông tin</div>
                    <div class="sub">Điền thông tin thuê</div>
                </div>
            </div>
            <div class="bk-step-line"></div>
            <div class="bk-step">
                <div class="bk-step-num pending">2</div>
                <div class="bk-step-info">
                    <div class="title">Thanh toán</div>
                    <div class="sub">Đặt cọc tiền thuê</div>
                </div>
            </div>
            <div class="bk-step-line"></div>
            <div class="bk-step">
                <div class="bk-step-num pending">3</div>
                <div class="bk-step-info">
                    <div class="title">Xác nhận</div>
                    <div class="sub">Ký hợp đồng</div>
                </div>
            </div>
        </div>

        <div class="bk-grid">

            {{-- ══ LEFT: FORM ══ --}}
            <div>
                <form id="booking-form" method="POST" action="{{ route('bookings.store', $room) }}">
                    @csrf

                    {{-- Thông tin người thuê --}}
                    <div class="bk-card">
                        <div class="bk-card-header">
                            <h2>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-3px;margin-right:6px;"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                Thông Tin Người Thuê
                            </h2>
                            <p>Điền chính xác để chủ nhà xác minh và liên hệ với bạn</p>
                        </div>
                        <div class="bk-card-body">

                            <div class="bk-row">
                                <div class="bk-field">
                                    <label class="bk-label">
                                        Họ và tên <span class="req">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        name="tenant_name"
                                        id="tenant_name"
                                        class="bk-input {{ $errors->has('tenant_name') ? 'is-invalid' : '' }}"
                                        value="{{ old('tenant_name', auth()->user()->name) }}"
                                        placeholder="Nguyễn Văn A"
                                        required
                                    >
                                    @error('tenant_name')
                                        <div class="bk-error">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12" stroke="white" stroke-width="2"/><circle cx="12" cy="16" r="1" fill="white"/></svg>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="bk-field">
                                    <label class="bk-label">
                                        Số điện thoại <span class="req">*</span>
                                    </label>
                                    <input
                                        type="tel"
                                        name="tenant_phone"
                                        id="tenant_phone"
                                        class="bk-input {{ $errors->has('tenant_phone') ? 'is-invalid' : '' }}"
                                        value="{{ old('tenant_phone', auth()->user()->phone) }}"
                                        placeholder="0901234567"
                                        required
                                    >
                                    @error('tenant_phone')
                                        <div class="bk-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="bk-row">
                                <div class="bk-field">
                                    <label class="bk-label">Số CCCD / CMND</label>
                                    <input
                                        type="text"
                                        name="tenant_cccd"
                                        id="tenant_cccd"
                                        class="bk-input {{ $errors->has('tenant_cccd') ? 'is-invalid' : '' }}"
                                        value="{{ old('tenant_cccd') }}"
                                        placeholder="001234567890"
                                    >
                                    <span class="bk-hint">Để xác minh danh tính (không bắt buộc)</span>
                                    @error('tenant_cccd')
                                        <div class="bk-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="bk-field">
                                    <label class="bk-label">
                                        Số người ở <span class="req">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        name="num_people"
                                        id="num_people"
                                        class="bk-input {{ $errors->has('num_people') ? 'is-invalid' : '' }}"
                                        value="{{ old('num_people', 1) }}"
                                        min="1" max="20"
                                        required
                                    >
                                    @error('num_people')
                                        <div class="bk-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="bk-row single">
                                <div class="bk-field">
                                    <label class="bk-label">Ngày dự kiến vào ở</label>
                                    <input
                                        type="date"
                                        name="move_in_date"
                                        id="move_in_date"
                                        class="bk-input {{ $errors->has('move_in_date') ? 'is-invalid' : '' }}"
                                        value="{{ old('move_in_date') }}"
                                        min="{{ now()->format('Y-m-d') }}"
                                    >
                                    @error('move_in_date')
                                        <div class="bk-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="bk-row single">
                                <div class="bk-field">
                                    <label class="bk-label">Ghi chú / Yêu cầu thêm</label>
                                    <textarea
                                        name="note"
                                        class="bk-input {{ $errors->has('note') ? 'is-invalid' : '' }}"
                                        rows="3"
                                        placeholder="VD: Muốn xem phòng trực tiếp, thương lượng giá..."
                                        maxlength="1000"
                                        id="note-input"
                                        style="resize:vertical;"
                                    >{{ old('note') }}</textarea>
                                    <span class="bk-hint"><span id="char-count">0</span>/1000 ký tự</span>
                                    @error('note')
                                        <div class="bk-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Thanh toán tiền cọc --}}
                    <div class="bk-card">
                        <div class="bk-card-header">
                            <h2>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-3px;margin-right:6px;"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                Đặt Cọc & Thanh Toán
                            </h2>
                            <p>Chọn phương thức thanh toán tiền đặt cọc</p>
                        </div>
                        <div class="bk-card-body">

                            <div class="bk-section-title">Số tiền đặt cọc</div>
                            <div class="bk-field" style="margin-bottom:20px;">
                                <div class="bk-deposit-wrap">
                                    <input
                                        type="number"
                                        name="deposit_amount"
                                        id="deposit_amount"
                                        class="bk-input {{ $errors->has('deposit_amount') ? 'is-invalid' : '' }}"
                                        value="{{ old('deposit_amount', $defaultDeposit) }}"
                                        min="0"
                                        step="100000"
                                        required
                                    >
                                    <span class="bk-deposit-unit">đ</span>
                                </div>
                                <span class="bk-hint">Mặc định = 1 tháng tiền thuê. Có thể thương lượng.</span>
                                @error('deposit_amount')
                                    <div class="bk-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="bk-section-title">Phương thức thanh toán</div>
                            <div class="bk-payment-options" id="payment-options">
                                <div class="bk-payment-option selected" data-method="offline" id="opt-offline">
                                    <input type="radio" name="payment_method" value="offline" checked>
                                    <span class="bk-payment-icon">🏢</span>
                                    <div class="bk-payment-name">Thanh toán tại chỗ</div>
                                    <div class="bk-payment-desc">Đến văn phòng/chủ nhà nộp tiền trực tiếp</div>
                                    <div class="bk-payment-check">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </div>
                                <div class="bk-payment-option" data-method="online" id="opt-online">
                                    <input type="radio" name="payment_method" value="online">
                                    <span class="bk-payment-icon">💳</span>
                                    <div class="bk-payment-name">Thanh toán trực tuyến</div>
                                    <div class="bk-payment-desc">Chuyển khoản / VNPay / MoMo</div>
                                    <div class="bk-payment-check">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                    </div>
                                </div>
                            </div>
                            @error('payment_method')
                                <div class="bk-error">{{ $message }}</div>
                            @enderror

                            {{-- Thông tin offline --}}
                            <div id="offline-info" style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:14px;font-size:13px;color:#78350f;margin-top:4px;">
                                <strong>📍 Hướng dẫn:</strong> Sau khi gửi đơn, bạn cần đến trực tiếp trong vòng <strong>24 giờ</strong> để nộp tiền cọc. Admin sẽ xác nhận và giữ phòng cho bạn.
                            </div>
                            <div id="online-info" style="display:none;background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:12px;padding:14px;font-size:13px;color:#1e3a8a;margin-top:4px;">
                                <strong>💳 Thanh toán online:</strong> Bạn sẽ được chuyển đến trang thanh toán sau khi gửi đơn. Phòng sẽ được giữ ngay khi thanh toán thành công.
                            </div>

                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="bk-submit-btn" id="submit-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                        Xác Nhận Đặt Phòng
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="btn-arrow"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>

                    <a href="{{ route('rooms.show', $room) }}" style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;color:#64748b;font-size:14px;text-decoration:none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Quay lại trang phòng
                    </a>

                </form>
            </div>

            {{-- ══ RIGHT: Sidebar ══ --}}
            <div>
                <div class="bk-room-card">
                    @if($room->images->first())
                        <img src="{{ asset('storage/' . $room->images->first()->image_path) }}" alt="{{ $room->name }}" class="bk-room-img">
                    @else
                        <div class="bk-room-placeholder">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                    @endif
                    <div class="bk-room-body">
                        <div class="bk-room-name">{{ $room->name }}</div>
                        @if($room->fullAddress())
                            <div class="bk-room-location">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                {{ $room->fullAddress() }}
                            </div>
                        @endif
                        <div class="bk-room-price">
                            {{ number_format($room->price) }}đ <small>/ tháng</small>
                        </div>
                        <hr class="bk-room-divider">
                        <div class="bk-room-meta">
                            @if($room->area)
                            <div class="bk-meta-item">
                                <div class="val">{{ $room->area }} m²</div>
                                <div class="key">Diện tích</div>
                            </div>
                            @endif
                            @if($room->floor)
                            <div class="bk-meta-item">
                                <div class="val">Tầng {{ $room->floor }}</div>
                                <div class="key">Tầng</div>
                            </div>
                            @endif
                        </div>

                        {{-- Summary --}}
                        <div class="bk-summary-box">
                            <div class="bk-summary-row">
                                <span>Tiền thuê / tháng</span>
                                <span class="sum-price">{{ number_format($room->price) }}đ</span>
                            </div>
                            <div class="bk-summary-row">
                                <span>Tiền đặt cọc</span>
                                <span class="sum-price" id="deposit-display">{{ number_format($defaultDeposit) }}đ</span>
                            </div>
                            <div class="bk-summary-row">
                                <span>Thời gian giữ chỗ</span>
                                <span style="color:#f59e0b;font-weight:600;">24 giờ</span>
                            </div>
                            <div class="bk-summary-row total">
                                <span>Cần thanh toán ngay</span>
                                <span class="sum-price" id="deposit-total">{{ number_format($defaultDeposit) }}đ</span>
                            </div>
                        </div>
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
    // Payment method toggle
    var options = document.querySelectorAll('.bk-payment-option');
    var offlineInfo = document.getElementById('offline-info');
    var onlineInfo  = document.getElementById('online-info');

    options.forEach(function (opt) {
        opt.addEventListener('click', function () {
            options.forEach(function(o){ o.classList.remove('selected'); });
            this.classList.add('selected');
            var method = this.dataset.method;
            this.querySelector('input[type="radio"]').checked = true;

            if (method === 'online') {
                offlineInfo.style.display = 'none';
                onlineInfo.style.display  = 'block';
            } else {
                offlineInfo.style.display = 'block';
                onlineInfo.style.display  = 'none';
            }
        });
    });

    // Deposit live update
    var depositInput = document.getElementById('deposit_amount');
    var depositDisplay = document.getElementById('deposit-display');
    var depositTotal   = document.getElementById('deposit-total');

    function formatVND(n) {
        return n.toLocaleString('vi-VN') + 'đ';
    }

    depositInput && depositInput.addEventListener('input', function () {
        var val = parseInt(this.value) || 0;
        depositDisplay.textContent = formatVND(val);
        depositTotal.textContent   = formatVND(val);
    });

    // Char counter
    var noteInput = document.getElementById('note-input');
    var charCount = document.getElementById('char-count');
    if (noteInput && charCount) {
        noteInput.addEventListener('input', function () {
            charCount.textContent = this.value.length;
        });
    }

    // Submit loading
    document.getElementById('booking-form').addEventListener('submit', function () {
        var btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-dasharray="8" stroke-dashoffset="0"/></svg> Đang xử lý...';
    });
})();
</script>
<style>
@keyframes spin { 100% { transform: rotate(360deg); } }
</style>
@endsection
