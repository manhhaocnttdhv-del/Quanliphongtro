@extends('layouts.user')
@section('title', 'Hợp Đồng Của Tôi')

@section('styles')
<style>
    .mc-page {
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        min-height: 80vh;
        padding: 50px 0 80px;
    }
    .mc-page-title { font-size: 26px; font-weight: 800; color: #1e293b; }
    .mc-page-sub   { color: #94a3b8; font-size: 14px; margin-top: 4px; }

    /* Contract card */
    .mc-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        overflow: hidden;
        margin-bottom: 24px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .mc-card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,0.11); }

    .mc-card-header {
        padding: 20px 24px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
    }
    .mc-card-header.active  { border-left: 5px solid #10b981; }
    .mc-card-header.ended   { border-left: 5px solid #94a3b8; }

    .mc-room-name  { font-size: 17px; font-weight: 700; color: #1e293b; }
    .mc-room-addr  { font-size: 12px; color: #94a3b8; margin-top: 3px; }

    .mc-status-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 700;
    }
    .mc-status-badge.active { background:#ecfdf5; color:#10b981; border:1px solid #a7f3d0; }
    .mc-status-badge.ended  { background:#f8fafc; color:#94a3b8; border:1px solid #e2e8f0; }

    .mc-card-body { padding: 20px 24px; }

    .mc-info-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }
    @media (max-width: 768px) {
        .mc-info-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .mc-info-item { }
    .mc-info-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #94a3b8;
        margin-bottom: 4px;
    }
    .mc-info-value { font-size: 15px; font-weight: 600; color: #1e293b; }
    .mc-info-value.price { color: #f97316; font-size: 18px; }
    .mc-info-value.deposit { color: #3b82f6; }

    .mc-card-footer {
        padding: 14px 24px;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .mc-invoice-summary { font-size: 13px; color: #64748b; }
    .mc-invoice-summary strong { color: #1e293b; }

    .mc-view-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #1e293b, #334155);
        color: #fff;
        border-radius: 10px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .mc-view-btn:hover {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        text-decoration: none;
        transform: translateX(2px);
    }

    /* Empty state */
    .mc-empty {
        text-align: center;
        padding: 80px 20px;
        color: #94a3b8;
    }
    .mc-empty svg { opacity: 0.3; margin-bottom: 20px; }
    .mc-empty h4 { font-size: 18px; color: #64748b; margin-bottom: 8px; }
    .mc-empty p  { font-size: 14px; }
</style>
@endsection

@section('content')
<div class="mc-page">
    <div class="container">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-5">
            <div>
                <h1 class="mc-page-title">Hợp Đồng Của Tôi</h1>
                <p class="mc-page-sub">Danh sách hợp đồng thuê phòng đang có hiệu lực và đã kết thúc</p>
            </div>
            <a href="{{ route('rooms.index') }}" class="mc-view-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>
                Tìm phòng mới
            </a>
        </div>

        @forelse($contracts as $contract)
            <div class="mc-card">
                {{-- Card header --}}
                <div class="mc-card-header {{ $contract->status }}">
                    <div>
                        <div class="mc-room-name">{{ $contract->room?->name ?? 'Phòng không xác định' }}</div>
                        @if($contract->room?->fullAddress())
                            <div class="mc-room-addr">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:2px;"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                                {{ $contract->room->fullAddress() }}
                            </div>
                        @endif
                    </div>
                    <div class="text-end">
                        <span class="mc-status-badge {{ $contract->status }}">
                            @if($contract->status === 'active')
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg>
                                Đang hiệu lực
                            @else
                                Đã kết thúc
                            @endif
                        </span>
                        <div style="font-size:11px;color:#94a3b8;margin-top:4px;">HĐ #{{ $contract->id }}</div>
                    </div>
                </div>

                {{-- Card body --}}
                <div class="mc-card-body">
                    <div class="mc-info-grid">
                        <div class="mc-info-item">
                            <div class="mc-info-label">Giá thuê / tháng</div>
                            <div class="mc-info-value price">{{ number_format($contract->monthly_rent) }}đ</div>
                        </div>
                        <div class="mc-info-item">
                            <div class="mc-info-label">Tiền cọc</div>
                            <div class="mc-info-value deposit">{{ number_format($contract->deposit) }}đ</div>
                        </div>
                        <div class="mc-info-item">
                            <div class="mc-info-label">Ngày bắt đầu</div>
                            <div class="mc-info-value">{{ $contract->start_date->format('d/m/Y') }}</div>
                        </div>
                        <div class="mc-info-item">
                            <div class="mc-info-label">Ngày kết thúc</div>
                            <div class="mc-info-value">
                                @if($contract->end_date)
                                    {{ $contract->end_date->format('d/m/Y') }}
                                    @if($contract->status === 'active' && $contract->end_date->diffInDays(now()) <= 30 && $contract->end_date->isFuture())
                                        <span style="font-size:11px;background:#fff3cd;color:#856404;padding:2px 8px;border-radius:10px;margin-left:6px;">
                                            Còn {{ $contract->end_date->diffInDays(now()) }} ngày
                                        </span>
                                    @endif
                                @else
                                    <span style="color:#94a3b8;">Không xác định</span>
                                @endif
                            </div>
                        </div>
                        @if($contract->room?->electricity_price)
                        <div class="mc-info-item">
                            <div class="mc-info-label">Giá điện</div>
                            <div class="mc-info-value" style="font-size:14px;">{{ number_format($contract->room->electricity_price) }}đ/kWh</div>
                        </div>
                        @endif
                        @if($contract->room?->water_price)
                        <div class="mc-info-item">
                            <div class="mc-info-label">Giá nước</div>
                            <div class="mc-info-value" style="font-size:14px;">{{ number_format($contract->room->water_price) }}đ/khối</div>
                        </div>
                        @endif
                    </div>

                    @if($contract->notes)
                        <div style="margin-top:16px;padding:12px 16px;background:#f8fafc;border-radius:10px;font-size:13px;color:#64748b;border-left:3px solid #e2e8f0;">
                            <strong style="color:#1e293b;">Ghi chú hợp đồng:</strong> {{ $contract->notes }}
                        </div>
                    @endif
                </div>

                {{-- Card footer --}}
                <div class="mc-card-footer">
                    <div class="mc-invoice-summary">
                        @php
                            $totalInvoices  = $contract->invoices->count();
                            $unpaidInvoices = $contract->invoices->where('status', 'unpaid')->count();
                            $overdueInvoices = $contract->invoices->where('status', 'overdue')->count();
                        @endphp
                        @if($totalInvoices > 0)
                            <strong>{{ $totalInvoices }}</strong> hóa đơn
                            @if($overdueInvoices > 0)
                                · <span style="color:#ef4444;font-weight:600;">{{ $overdueInvoices }} quá hạn</span>
                            @elseif($unpaidInvoices > 0)
                                · <span style="color:#f59e0b;font-weight:600;">{{ $unpaidInvoices }} chưa thanh toán</span>
                            @else
                                · <span style="color:#10b981;">Đã thanh toán hết</span>
                            @endif
                        @else
                            Chưa có hóa đơn nào
                        @endif
                    </div>
                    <a href="{{ route('user.contracts.show', $contract) }}" class="mc-view-btn">
                        Xem chi tiết
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="mc-empty">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="#94a3b8"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                <h4>Chưa có hợp đồng nào</h4>
                <p>Bạn chưa có hợp đồng thuê phòng. Hãy tìm phòng và gửi yêu cầu thuê!</p>
                <a href="{{ route('rooms.index') }}" style="display:inline-block;margin-top:16px;background:#f97316;color:#fff;padding:10px 28px;border-radius:12px;text-decoration:none;font-weight:600;">
                    Tìm phòng ngay
                </a>
            </div>
        @endforelse

        @if($contracts->hasPages())
            <div class="mt-4">{{ $contracts->links() }}</div>
        @endif
    </div>
</div>
@endsection
