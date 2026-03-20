@extends('layouts.admin')
@section('title', 'Báo Cáo & Thống Kê')

@section('content')
<div class="mb-4">
    <h1 class="fs-3 fw-bold mb-1">📊 Báo Cáo & Thống Kê</h1>
    <p class="text-muted mb-0">Tổng quan hoạt động kinh doanh</p>
</div>

{{-- KPIs --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg,#4f46e5,#3730a3);">
            <div class="card-body p-4 text-white position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-15"><i class="ti ti-cash fs-1"></i></div>
                <div class="small text-white-50 text-uppercase fw-semibold mb-1">Doanh thu tháng này</div>
                <div class="fs-3 fw-bold">{{ number_format($monthRevenue) }}đ</div>
                <div class="small opacity-75 mt-1">Tháng {{ now()->month }}/{{ now()->year }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg,#10b981,#059669);">
            <div class="card-body p-4 text-white position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-15"><i class="ti ti-chart-bar fs-1"></i></div>
                <div class="small text-white-50 text-uppercase fw-semibold mb-1">Doanh thu năm {{ now()->year }}</div>
                <div class="fs-3 fw-bold">{{ number_format($yearRevenue) }}đ</div>
                <div class="small opacity-75 mt-1">Cộng dồn từ đầu năm</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg,#0ea5e9,#0284c7);">
            <div class="card-body p-4 text-white position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-15"><i class="ti ti-home fs-1"></i></div>
                <div class="small text-white-50 text-uppercase fw-semibold mb-1">Tỉ lệ lấp đầy</div>
                <div class="fs-3 fw-bold">{{ $occupancyRate }}%</div>
                <div class="small opacity-75 mt-1">{{ $rentedRooms }}/{{ $totalRooms }} phòng đang thuê</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg,#ef4444,#dc2626);">
            <div class="card-body p-4 text-white position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-15"><i class="ti ti-alert-triangle fs-1"></i></div>
                <div class="small text-white-50 text-uppercase fw-semibold mb-1">Tổng nợ tồn đọng</div>
                <div class="fs-3 fw-bold">{{ number_format($totalDebtAmount) }}đ</div>
                <div class="small opacity-75 mt-1">{{ $pendingInvoices->count() }} hóa đơn chưa thanh toán</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Revenue Chart --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">Doanh thu 12 tháng gần đây</h5>
                    <div class="text-muted small">Thu nhập từ hóa đơn đã thanh toán</div>
                </div>
            </div>
            <div class="card-body p-4">
                <div id="revenueChart" style="min-height:300px;"></div>
            </div>
        </div>
    </div>

    {{-- Occupancy Donut --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Tỉ lệ lấp đầy</h5>
                <div class="text-muted small">Phân bố trạng thái phòng</div>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
                <div id="donutChart" style="min-height:220px;width:100%;"></div>
                <div class="d-flex gap-4 mt-2">
                    <div class="text-center">
                        <div class="fw-bold text-primary fs-5">{{ $rentedRooms }}</div>
                        <div class="text-muted small">Đang thuê</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold text-success fs-5">{{ $availableRooms }}</div>
                        <div class="text-muted small">Còn trống</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Debt List --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">Nợ tồn đọng</h5>
                    <div class="text-muted small">Hóa đơn chưa được thanh toán</div>
                </div>
                <a href="{{ route('admin.invoices.index', ['status' => 'unpaid']) }}" class="btn btn-sm btn-outline-danger">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                @php $debtList = $pendingInvoices->take(6); @endphp
                @if($debtList->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-circle-check fs-1 text-success d-block mb-2"></i>
                        Không có nợ tồn đọng!
                    </div>
                @else
                    @foreach($debtList as $inv)
                        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                            <div>
                                <div class="fw-semibold">{{ $inv->room->name }}</div>
                                <div class="text-muted small">
                                    {{ $inv->contract?->user?->name ?? '—' }} •
                                    Tháng {{ $inv->month }}/{{ $inv->year }}
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-danger">{{ number_format($inv->total_amount) }}đ</div>
                                <a href="{{ route('admin.invoices.show', $inv) }}" class="text-decoration-none small text-primary">Thu tiền →</a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- Expiring Contracts --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">⏰ Hợp đồng sắp hết hạn</h5>
                <div class="text-muted small">Trong vòng 30 ngày tới</div>
            </div>
            <div class="card-body p-0">
                @forelse($expiringContracts as $contract)
                    @php $daysLeft = now()->diffInDays($contract->end_date, false); @endphp
                    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $contract->room->name }}</div>
                            <div class="text-muted small">{{ $contract->user->name }}</div>
                        </div>
                        <div class="text-end">
                            <span class="badge rounded-pill {{ $daysLeft <= 7 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ $daysLeft }} ngày
                            </span>
                            <div class="text-muted small mt-1">{{ $contract->end_date->format('d/m/Y') }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-calendar-check fs-1 d-block mb-2"></i>
                        Không có hợp đồng sắp hết hạn
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var chartData = @json($revenueChart);

    // Revenue Bar Chart
    new ApexCharts(document.querySelector('#revenueChart'), {
        series: [{ name: 'Doanh thu', data: chartData.map(d => d.revenue) }],
        chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'Inter, sans-serif', sparkline: { enabled: false } },
        stroke: { curve: 'smooth', width: 2 },
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] } },
        dataLabels: { enabled: false },
        colors: ['#4f46e5'],
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        xaxis: {
            categories: chartData.map(d => d.label),
            axisBorder: { show: false }, axisTicks: { show: false },
            labels: { style: { colors: '#64748b', fontSize: '12px' } }
        },
        yaxis: { labels: { style: { colors: '#64748b', fontSize: '12px' }, formatter: v => (v/1000000).toFixed(1) + 'M' } },
        tooltip: { theme: 'light', y: { formatter: v => new Intl.NumberFormat('vi-VN').format(v) + ' đ' } }
    }).render();

    // Donut Chart
    new ApexCharts(document.querySelector('#donutChart'), {
        series: [{{ $rentedRooms }}, {{ $availableRooms }}],
        chart: { type: 'donut', height: 220, fontFamily: 'Inter, sans-serif' },
        labels: ['Đang thuê', 'Còn trống'],
        colors: ['#4f46e5', '#10b981'],
        legend: { show: false },
        plotOptions: { pie: { donut: { size: '70%', labels: {
            show: true,
            total: { show: true, label: 'Tỉ lệ', formatter: () => '{{ $occupancyRate }}%', color: '#4f46e5', fontWeight: 700, fontSize: '18px' }
        } } } },
        dataLabels: { enabled: false },
        stroke: { width: 0 }
    }).render();
});
</script>
@endsection
