@extends('layouts.user')
@section('title', 'Chi Tiết Hợp Đồng #' . $contract->id)

@section('styles')
<style>
    .cd-page {
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%);
        min-height: 80vh;
        padding: 50px 0 80px;
    }

    /* Breadcrumb */
    .cd-breadcrumb { font-size: 13px; color: #94a3b8; margin-bottom: 28px; }
    .cd-breadcrumb a { color: #64748b; text-decoration: none; }
    .cd-breadcrumb a:hover { color: #f97316; }
    .cd-breadcrumb span { color: #cbd5e1; margin: 0 6px; }

    /* Hero contract header */
    .cd-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 20px;
        padding: 32px 36px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .cd-hero::before {
        content: '';
        position: absolute; top: -60px; right: -60px;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(249,115,22,0.15), transparent 70%);
        border-radius: 50%;
    }
    .cd-hero-title { font-size: 24px; font-weight: 800; margin: 0 0 4px; }
    .cd-hero-sub   { font-size: 14px; color: #94a3b8; }
    .cd-hero-badge {
        position: absolute; top: 28px; right: 36px;
        padding: 7px 18px; border-radius: 20px; font-size: 13px; font-weight: 700;
    }
    .cd-hero-badge.active { background: rgba(16,185,129,0.2); color: #34d399; border: 1px solid rgba(52,211,153,0.3); }
    .cd-hero-badge.ended  { background: rgba(148,163,184,0.2); color: #94a3b8; border: 1px solid rgba(148,163,184,0.3); }

    .cd-price-row { display: flex; gap: 40px; margin-top: 24px; }
    .cd-price-item label { display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; color: #64748b; margin-bottom: 4px; }
    .cd-price-item .val  { font-size: 22px; font-weight: 800; }
    .cd-price-item .val.main  { color: #fb923c; }
    .cd-price-item .val.deposit { color: #60a5fa; }

    /* Info cards */
    .cd-section {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .cd-section-header {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cd-section-icon {
        width: 28px; height: 28px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px;
    }
    .cd-section-body { padding: 20px 24px; }

    .cd-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 24px;
    }
    .cd-detail-item label { font-size: 12px; color: #94a3b8; font-weight: 500; display: block; margin-bottom: 2px; }
    .cd-detail-item .val  { font-size: 14px; font-weight: 600; color: #1e293b; }

    /* Invoice table */
    .cd-invoice-table { width: 100%; border-collapse: collapse; }
    .cd-invoice-table th { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; padding: 10px 14px; text-align: left; background: #f8fafc; }
    .cd-invoice-table td { padding: 12px 14px; border-top: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle; }
    .cd-invoice-table tr:hover td { background: #fafafa; }

    .inv-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; }
    .inv-badge.unpaid  { background: #fef9c3; color: #a16207; }
    .inv-badge.paid    { background: #dcfce7; color: #166534; }
    .inv-badge.overdue { background: #fee2e2; color: #991b1b; }
    .inv-badge.paying  { background: #dbeafe; color: #1e40af; }
    .inv-badge.cancelled { background: #f1f5f9; color: #64748b; }

    .cd-back-link {
        display: inline-flex; align-items: center; gap: 8px;
        color: #64748b; text-decoration: none; font-size: 14px;
        margin-bottom: 20px;
        transition: color 0.2s;
    }
    .cd-back-link:hover { color: #f97316; }
</style>
@endsection

@section('content')
<div class="cd-page">
    <div class="container">

        {{-- Breadcrumb --}}
        <div class="cd-breadcrumb">
            <a href="{{ route('user.contracts.index') }}">Hợp đồng của tôi</a>
            <span>›</span>
            Hợp đồng #{{ $contract->id }}
        </div>

        {{-- Hero --}}
        <div class="cd-hero">
            <span class="cd-hero-badge {{ $contract->status }}">
                @if($contract->status === 'active') ● Đang hiệu lực @else Đã kết thúc @endif
            </span>
            <div class="cd-hero-title">{{ $contract->room?->name ?? 'Phòng trọ' }}</div>
            <div class="cd-hero-sub">
                Hợp đồng #{{ $contract->id }} &nbsp;·&nbsp;
                Ngày ký: {{ $contract->created_at->format('d/m/Y') }}
                @if($contract->room?->fullAddress())
                    &nbsp;·&nbsp; {{ $contract->room->fullAddress() }}
                @endif
            </div>
            <div class="cd-price-row">
                <div class="cd-price-item">
                    <label>Tiền thuê / tháng</label>
                    <div class="val main">{{ number_format($contract->monthly_rent) }}đ</div>
                </div>
                <div class="cd-price-item">
                    <label>Tiền đặt cọc</label>
                    <div class="val deposit">{{ number_format($contract->deposit) }}đ</div>
                </div>
                <div class="cd-price-item">
                    <label>Thời hạn</label>
                    <div class="val" style="font-size:16px;color:#fff;">
                        {{ $contract->start_date->format('d/m/Y') }}
                        → {{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Không xác định' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- LEFT: Contract details + Amenities --}}
            <div class="col-lg-5">

                {{-- Contract info --}}
                <div class="cd-section">
                    <div class="cd-section-header">
                        <div class="cd-section-icon" style="background:#eff6ff;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#3b82f6"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg>
                        </div>
                        Thông tin hợp đồng
                    </div>
                    <div class="cd-section-body">
                        <div class="cd-detail-grid">
                            <div class="cd-detail-item">
                                <label>Ngày bắt đầu</label>
                                <div class="val">{{ $contract->start_date->format('d/m/Y') }}</div>
                            </div>
                            <div class="cd-detail-item">
                                <label>Ngày kết thúc</label>
                                <div class="val">
                                    @if($contract->end_date)
                                        {{ $contract->end_date->format('d/m/Y') }}
                                        @if($contract->status === 'active' && $contract->end_date->isFuture() && $contract->end_date->diffInDays(now()) <= 30)
                                            <div style="margin-top:4px;font-size:12px;background:#fef3c7;color:#92400e;padding:3px 8px;border-radius:8px;display:inline-block;">
                                                ⚠ Còn {{ (int)now()->diffInDays($contract->end_date) }} ngày
                                            </div>
                                        @endif
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </div>
                            </div>
                            <div class="cd-detail-item">
                                <label>Giá điện</label>
                                <div class="val">{{ $contract->room?->electricity_price ? number_format($contract->room->electricity_price).'đ/kWh' : '—' }}</div>
                            </div>
                            <div class="cd-detail-item">
                                <label>Giá nước</label>
                                <div class="val">{{ $contract->room?->water_price ? number_format($contract->room->water_price).'đ/khối' : '—' }}</div>
                            </div>
                            <div class="cd-detail-item">
                                <label>Diện tích</label>
                                <div class="val">{{ $contract->room?->area ? $contract->room->area.' m²' : '—' }}</div>
                            </div>
                            <div class="cd-detail-item">
                                <label>Tầng</label>
                                <div class="val">{{ $contract->room?->floor ?? '—' }}</div>
                            </div>
                        </div>

                        @if($contract->notes)
                            <div style="margin-top:16px;padding:12px 14px;background:#f8fafc;border-radius:10px;font-size:13px;border-left:3px solid #e2e8f0;">
                                <div style="font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px;">Điều khoản / Ghi chú</div>
                                {{ $contract->notes }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Amenities --}}
                @if(!empty($contract->room?->amenities))
                <div class="cd-section">
                    <div class="cd-section-header">
                        <div class="cd-section-icon" style="background:#f0fdf4;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#10b981"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                        </div>
                        Tiện nghi phòng
                    </div>
                    <div class="cd-section-body">
                        <div style="display:flex;flex-wrap:wrap;gap:8px;">
                            @foreach($contract->room->amenities as $amenity)
                                <span style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:500;">
                                    ✓ {{ $amenity }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

            </div>

            {{-- RIGHT: Invoice list --}}
            <div class="col-lg-7">
                <div class="cd-section">
                    <div class="cd-section-header">
                        <div class="cd-section-icon" style="background:#fff7ed;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#f97316"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                        </div>
                        Hóa đơn hàng tháng
                        <span style="margin-left:auto;font-size:12px;font-weight:400;color:#94a3b8;">{{ $contract->invoices->count() }} hóa đơn</span>
                    </div>

                    @if($contract->invoices->isEmpty())
                        <div style="text-align:center;padding:48px 20px;color:#94a3b8;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="#cbd5e1" style="display:block;margin:0 auto 12px;"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                            <div style="font-size:15px;color:#64748b;font-weight:500;">Chưa có hóa đơn nào</div>
                            <div style="font-size:13px;margin-top:4px;">Hoá đơn sẽ được tạo hàng tháng bởi chủ nhà.</div>
                        </div>
                    @else
                        <table class="cd-invoice-table">
                            <thead>
                                <tr>
                                    <th>Kỳ thanh toán</th>
                                    <th>Tổng tiền</th>
                                    <th>Hạn TT</th>
                                    <th>Trạng thái</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contract->invoices->sortByDesc('id') as $invoice)
                                <tr>
                                    <td><strong>Tháng {{ $invoice->month }}/{{ $invoice->year }}</strong></td>
                                    <td style="font-weight:700;color:#f97316;">{{ number_format($invoice->total_amount) }}đ</td>
                                    <td>
                                        @if($invoice->due_date)
                                            <span style="{{ $invoice->isOverdue() ? 'color:#ef4444;font-weight:600;' : 'color:#64748b;' }}">
                                                {{ $invoice->due_date->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span style="color:#94a3b8;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="inv-badge {{ $invoice->status }}">{{ $invoice->statusLabel() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('user.invoices.show', $invoice) }}"
                                           style="font-size:12px;color:#f97316;font-weight:600;text-decoration:none;white-space:nowrap;">
                                            Xem &rsaquo;
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Quick actions --}}
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="{{ route('user.invoices') }}"
                       style="flex:1;min-width:140px;display:flex;align-items:center;justify-content:center;gap:8px;background:#fff;border:1.5px solid #e2e8f0;color:#1e293b;border-radius:12px;padding:12px 20px;text-decoration:none;font-size:13px;font-weight:600;transition:all .2s;"
                       onmouseover="this.style.borderColor='#f97316';this.style.color='#f97316';"
                       onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#1e293b';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/></svg>
                        Tất cả hóa đơn
                    </a>
                    <a href="{{ route('maintenance.create') }}"
                       style="flex:1;min-width:140px;display:flex;align-items:center;justify-content:center;gap:8px;background:#fff;border:1.5px solid #e2e8f0;color:#1e293b;border-radius:12px;padding:12px 20px;text-decoration:none;font-size:13px;font-weight:600;transition:all .2s;"
                       onmouseover="this.style.borderColor='#f97316';this.style.color='#f97316';"
                       onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#1e293b';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M13.78 15.3L19.78 21.3L21.89 19.14L15.89 13.14L13.78 15.3M17.5 11.5C19.59 11.5 21.25 9.84 21.25 7.75C21.25 7.1 21.08 6.5 20.79 5.97L18 8.75L15.25 6L18.03 3.21C17.5 2.92 16.9 2.75 16.25 2.75C14.16 2.75 12.5 4.41 12.5 6.5C12.5 7 12.61 7.5 12.83 7.94L3.75 17C3.75 19.07 5.43 20.75 7.5 20.75C9.57 20.75 11.25 19.07 11.25 17L17.5 11.5Z"/></svg>
                        Yêu cầu bảo trì
                    </a>
                </div>
            </div>
        </div>

        <div style="margin-top:28px;">
            <a href="{{ route('user.contracts.index') }}" class="cd-back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/></svg>
                Quay lại danh sách hợp đồng
            </a>
        </div>

    </div>
</div>
@endsection
