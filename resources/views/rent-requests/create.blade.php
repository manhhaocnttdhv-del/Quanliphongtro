@extends('layouts.user')
@section('title', 'Gửi Yêu Cầu Thuê - ' . $room->name)

@section('styles')
<style>
    /* ── Page wrapper ── */
    .rr-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        padding: 56px 0 80px;
    }

    /* ── Breadcrumb ── */
    .rr-breadcrumb {
        font-size: 13px;
        color: #94a3b8;
        margin-bottom: 28px;
    }
    .rr-breadcrumb a { color: #64748b; text-decoration: none; }
    .rr-breadcrumb a:hover { color: #f97316; }
    .rr-breadcrumb span { color: #cbd5e1; margin: 0 6px; }

    /* ── Main grid ── */
    .rr-grid {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 28px;
        align-items: start;
    }
    @media (max-width: 900px) {
        .rr-grid { grid-template-columns: 1fr; }
    }

    /* ── Form card ── */
    .rr-form-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.07);
        overflow: hidden;
    }
    .rr-form-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 28px 32px 24px;
        position: relative;
        overflow: hidden;
    }
    .rr-form-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 160px; height: 160px;
        background: rgba(249,115,22,0.15);
        border-radius: 50%;
    }
    .rr-form-header::after {
        content: '';
        position: absolute;
        bottom: -30px; left: 60px;
        width: 100px; height: 100px;
        background: rgba(249,115,22,0.08);
        border-radius: 50%;
    }
    .rr-form-header h1 {
        color: #fff;
        font-size: 22px;
        font-weight: 700;
        margin: 0 0 6px;
        position: relative; z-index: 1;
    }
    .rr-form-header p {
        color: #94a3b8;
        font-size: 14px;
        margin: 0;
        position: relative; z-index: 1;
    }
    .rr-form-header .step-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(249,115,22,0.2);
        border: 1px solid rgba(249,115,22,0.4);
        color: #fb923c;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 12px;
        position: relative; z-index: 1;
    }

    .rr-form-body { padding: 32px; }

    /* ── Field groups ── */
    .rr-field-group {
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 20px;
        transition: border-color 0.2s;
    }
    .rr-field-group:focus-within {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249,115,22,0.08);
    }
    .rr-field-group.readonly-group {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .rr-field-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        margin-bottom: 10px;
    }
    .rr-field-label .label-icon {
        width: 22px; height: 22px;
        background: #f97316;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 11px;
        flex-shrink: 0;
    }
    .rr-field-label .label-icon.icon-blue   { background: #3b82f6; }
    .rr-field-label .label-icon.icon-green  { background: #10b981; }
    .rr-field-label .label-icon.icon-purple { background: #8b5cf6; }
    .rr-field-label .label-icon.icon-gray   { background: #94a3b8; }
    .rr-field-label .optional-tag {
        margin-left: auto;
        font-size: 10px;
        font-weight: 400;
        color: #94a3b8;
        text-transform: none;
        letter-spacing: 0;
    }

    .rr-info-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .rr-avatar {
        width: 40px; height: 40px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #fff; font-size: 16px;
        flex-shrink: 0;
    }
    .rr-info-text .name { font-weight: 600; color: #1e293b; font-size: 15px; }
    .rr-info-text .sub  { font-size: 13px; color: #64748b; }

    /* ── Inputs ── */
    .rr-input {
        width: 100%;
        border: none;
        background: transparent;
        font-size: 15px;
        color: #1e293b;
        outline: none;
        padding: 0;
    }
    .rr-input::placeholder { color: #cbd5e1; }
    .rr-input[readonly] { color: #64748b; cursor: not-allowed; }
    .rr-date-input {
        width: 100%;
        border: none;
        background: transparent;
        font-size: 15px;
        color: #1e293b;
        outline: none;
        padding: 0;
        cursor: pointer;
    }
    .rr-textarea {
        width: 100%;
        border: none;
        background: transparent;
        font-size: 14px;
        color: #1e293b;
        outline: none;
        padding: 0;
        resize: none;
        line-height: 1.6;
        min-height: 100px;
    }
    .rr-textarea::placeholder { color: #cbd5e1; }
    .rr-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ── Date quick picks ── */
    .date-shortcuts {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 12px;
    }
    .date-shortcut {
        padding: 5px 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s;
        background: #f8fafc;
        user-select: none;
    }
    .date-shortcut:hover, .date-shortcut.active {
        border-color: #f97316;
        background: #fff7ed;
        color: #f97316;
    }

    /* ── Submit button ── */
    .rr-submit-btn {
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
        letter-spacing: 0.3px;
    }
    .rr-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(249,115,22,0.45);
    }
    .rr-submit-btn:active { transform: translateY(0); }
    .rr-submit-btn .btn-arrow {
        transition: transform 0.3s;
    }
    .rr-submit-btn:hover .btn-arrow { transform: translateX(4px); }

    .rr-back-link {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 14px;
        color: #64748b;
        font-size: 14px;
        text-decoration: none;
        transition: color 0.2s;
    }
    .rr-back-link:hover { color: #f97316; }

    /* ── Sidebar: Room card ── */
    .rr-room-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.07);
        overflow: hidden;
        position: sticky;
        top: 20px;
    }
    .rr-room-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }
    .rr-room-img-placeholder {
        width: 100%; height: 200px;
        background: linear-gradient(135deg, #1e293b, #334155);
        display: flex; align-items: center; justify-content: center;
        color: #475569; font-size: 48px;
    }
    .rr-room-info { padding: 20px; }
    .rr-room-name {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 6px;
        line-height: 1.4;
    }
    .rr-room-location {
        font-size: 12px;
        color: #94a3b8;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .rr-room-price {
        font-size: 22px;
        font-weight: 800;
        color: #f97316;
        margin-bottom: 4px;
    }
    .rr-room-price span { font-size: 13px; font-weight: 500; color: #94a3b8; }
    .rr-room-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }
    .rr-stat {
        background: #f8fafc;
        border-radius: 10px;
        padding: 10px 12px;
        text-align: center;
    }
    .rr-stat .val { font-size: 15px; font-weight: 700; color: #1e293b; }
    .rr-stat .key { font-size: 11px; color: #94a3b8; margin-top: 2px; }

    /* Amenities */
    .rr-amenities {
        padding: 0 20px 20px;
    }
    .rr-amenity-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        padding: 4px 10px;
        margin: 3px 3px 0 0;
    }

    /* ── Process steps ── */
    .rr-steps {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 32px rgba(0,0,0,0.07);
        padding: 20px;
        margin-top: 20px;
    }
    .rr-steps-title {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #94a3b8;
        margin-bottom: 16px;
    }
    .rr-step {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 14px;
    }
    .rr-step:last-child { margin-bottom: 0; }
    .step-dot {
        width: 28px; height: 28px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700;
        flex-shrink: 0;
        margin-top: 1px;
    }
    .step-dot.active  { background: #f97316; color: #fff; }
    .step-dot.done    { background: #10b981; color: #fff; }
    .step-dot.pending { background: #f1f5f9; color: #94a3b8; }
    .step-line {
        position: absolute;
        left: 13px; top: 29px;
        width: 2px; height: calc(100% - 29px);
        background: #e2e8f0;
    }
    .rr-step-wrap { position: relative; }
    .step-content .step-name { font-size: 13px; font-weight: 600; color: #1e293b; }
    .step-content .step-desc { font-size: 12px; color: #94a3b8; margin-top: 2px; }

    /* ── Error state ── */
    .rr-field-error { border-color: #ef4444 !important; box-shadow: 0 0 0 3px rgba(239,68,68,0.08) !important; }
    .rr-error-msg { color: #ef4444; font-size: 12px; margin-top: 6px; display: flex; align-items: center; gap: 4px; }
</style>
@endsection

@section('content')
<div class="rr-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <div class="rr-breadcrumb">
            <a href="{{ route('rooms.index') }}">Danh sách phòng</a>
            <span>›</span>
            <a href="{{ route('rooms.show', $room) }}">{{ Str::limit($room->name, 40) }}</a>
            <span>›</span>
            Gửi yêu cầu thuê
        </div>

        <div class="rr-grid">

            {{-- ══ LEFT: Form ══ --}}
            <div>
                <div class="rr-form-card">
                    <div class="rr-form-header">
                        <div class="step-badge">
                            <i class="fa fa-file-text-o"></i>
                            Bước 1 / 3 — Điền thông tin
                        </div>
                        <h1>Gửi Yêu Cầu Thuê Phòng</h1>
                        <p>Điền đầy đủ thông tin bên dưới. Chủ nhà sẽ liên hệ xác nhận trong vòng 24 giờ.</p>
                    </div>

                    <div class="rr-form-body">
                        <form id="rent-request-form" method="POST" action="{{ route('rent-requests.store', $room) }}">
                            @csrf

                            {{-- Thông tin cá nhân --}}
                            <div class="rr-field-group readonly-group">
                                <div class="rr-field-label">
                                    <span class="label-icon icon-gray"><i class="fa fa-user"></i></span>
                                    Thông tin của bạn
                                </div>
                                <div class="rr-info-row">
                                    <div class="rr-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                                    <div class="rr-info-text">
                                        <div class="name">{{ auth()->user()->name }}</div>
                                        <div class="sub">{{ auth()->user()->email }}
                                            @if(auth()->user()->phone)
                                                · {{ auth()->user()->phone }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Ngày dự kiến vào ở --}}
                            <div class="rr-field-group {{ $errors->has('move_in_date') ? 'rr-field-error' : '' }}" id="date-group">
                                <div class="rr-field-label">
                                    <span class="label-icon icon-blue"><i class="fa fa-calendar"></i></span>
                                    Ngày dự kiến vào ở
                                    <span class="optional-tag">Không bắt buộc</span>
                                </div>
                                <input
                                    id="move_in_date"
                                    type="date"
                                    name="move_in_date"
                                    class="rr-date-input"
                                    value="{{ old('move_in_date') }}"
                                    min="{{ now()->format('Y-m-d') }}"
                                >
                                <div class="date-shortcuts">
                                    <div class="date-shortcut" data-days="7">7 ngày nữa</div>
                                    <div class="date-shortcut" data-days="14">2 tuần nữa</div>
                                    <div class="date-shortcut" data-days="30">1 tháng nữa</div>
                                    <div class="date-shortcut" data-days="0" data-clear="1">Xóa</div>
                                </div>
                                <div class="rr-hint">
                                    <i class="fa fa-info-circle"></i>
                                    Giúp chủ nhà chuẩn bị phòng đúng thời điểm cho bạn
                                </div>
                                @error('move_in_date')
                                    <div class="rr-error-msg"><i class="fa fa-times-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Ghi chú --}}
                            <div class="rr-field-group {{ $errors->has('note') ? 'rr-field-error' : '' }}">
                                <div class="rr-field-label">
                                    <span class="label-icon icon-purple"><i class="fa fa-comment"></i></span>
                                    Ghi chú / Yêu cầu thêm
                                    <span class="optional-tag">Không bắt buộc</span>
                                </div>
                                <textarea
                                    name="note"
                                    class="rr-textarea"
                                    placeholder="VD: Tôi muốn xem phòng trực tiếp vào buổi chiều, hoặc có thể thương lượng giá..."
                                    maxlength="1000"
                                    id="note-input"
                                >{{ old('note') }}</textarea>
                                <div class="rr-hint">
                                    <i class="fa fa-pencil"></i>
                                    <span id="char-count">0</span>/1000 ký tự
                                </div>
                                @error('note')
                                    <div class="rr-error-msg"><i class="fa fa-times-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Submit --}}
                            <button type="submit" class="rr-submit-btn" id="submit-btn">
                                <i class="fa fa-paper-plane"></i>
                                Gửi Yêu Cầu Thuê Phòng
                                <i class="fa fa-arrow-right btn-arrow"></i>
                            </button>

                            <a href="{{ route('rooms.show', $room) }}" class="rr-back-link">
                                <i class="fa fa-arrow-left"></i>
                                Quay lại trang phòng
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ══ RIGHT: Sidebar ══ --}}
            <div>
                {{-- Room preview card --}}
                <div class="rr-room-card">
                    @if($room->images->first())
                        <img
                            src="{{ asset('storage/' . $room->images->first()->image_path) }}"
                            alt="{{ $room->name }}"
                            class="rr-room-img"
                        >
                    @else
                        <div class="rr-room-img-placeholder">
                            <i class="fa fa-home"></i>
                        </div>
                    @endif

                    <div class="rr-room-info">
                        <h2 class="rr-room-name">{{ $room->name }}</h2>
                        @if($room->fullAddress())
                            <div class="rr-room-location">
                                <i class="fa fa-map-marker"></i>
                                {{ $room->fullAddress() }}
                            </div>
                        @endif

                        <div class="rr-room-price">
                            {{ number_format($room->price) }}đ
                            <span>/ tháng</span>
                        </div>

                        <div class="rr-room-stats">
                            @if($room->area)
                            <div class="rr-stat">
                                <div class="val">{{ $room->area }} m²</div>
                                <div class="key">Diện tích</div>
                            </div>
                            @endif
                            @if($room->floor)
                            <div class="rr-stat">
                                <div class="val">{{ $room->floor }}</div>
                                <div class="key">Tầng</div>
                            </div>
                            @endif
                            @if($room->electricity_price)
                            <div class="rr-stat">
                                <div class="val">{{ number_format($room->electricity_price) }}đ</div>
                                <div class="key">Điện / kWh</div>
                            </div>
                            @endif
                            @if($room->water_price)
                            <div class="rr-stat">
                                <div class="val">{{ number_format($room->water_price) }}đ</div>
                                <div class="key">Nước / khối</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if(!empty($room->amenities))
                    <div class="rr-amenities">
                        @foreach($room->amenities as $amenity)
                            <span class="rr-amenity-tag">
                                <i class="fa fa-check"></i>
                                {{ $amenity }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Process steps --}}
                <div class="rr-steps">
                    <div class="rr-steps-title">Quy trình thuê phòng</div>

                    <div class="rr-step">
                        <div class="step-dot active">1</div>
                        <div class="step-content">
                            <div class="step-name">Gửi yêu cầu</div>
                            <div class="step-desc">Điền thông tin và gửi yêu cầu này</div>
                        </div>
                    </div>
                    <div class="rr-step">
                        <div class="step-dot pending">2</div>
                        <div class="step-content">
                            <div class="step-name">Chủ nhà liên hệ</div>
                            <div class="step-desc">Xác nhận thông tin, hẹn xem phòng (nếu cần)</div>
                        </div>
                    </div>
                    <div class="rr-step">
                        <div class="step-dot pending">3</div>
                        <div class="step-content">
                            <div class="step-name">Ký hợp đồng</div>
                            <div class="step-desc">Đặt cọc và chuyển vào ở</div>
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
    // ── Date shortcuts ──
    var dateInput = document.getElementById('move_in_date');

    document.querySelectorAll('.date-shortcut').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.date-shortcut').forEach(function(b){ b.classList.remove('active'); });

            if (this.dataset.clear) {
                dateInput.value = '';
                return;
            }

            var days = parseInt(this.dataset.days);
            var d = new Date();
            d.setDate(d.getDate() + days);
            var yyyy = d.getFullYear();
            var mm   = String(d.getMonth() + 1).padStart(2, '0');
            var dd   = String(d.getDate()).padStart(2, '0');
            dateInput.value = yyyy + '-' + mm + '-' + dd;
            this.classList.add('active');
        });
    });

    // ── Char counter ──
    var noteInput  = document.getElementById('note-input');
    var charCount  = document.getElementById('char-count');
    function updateCount() {
        var len = noteInput.value.length;
        charCount.textContent = len;
        charCount.style.color = len > 900 ? '#ef4444' : '#94a3b8';
    }
    noteInput.addEventListener('input', updateCount);
    updateCount();

    // ── Submit loading state ──
    document.getElementById('rent-request-form').addEventListener('submit', function () {
        var btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang gửi...';
    });
})();
</script>
@endsection
