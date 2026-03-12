@extends('layouts.admin')
@section('title', 'Landlord Dashboard')

@section('content')
<div class="mb-5">
    <h1 class="fs-3 mb-1">Bảng Điều Khiển Chủ Trọ</h1>
    <p class="text-muted">Quản lý hiệu quả các phòng trọ của bạn</p>
</div>

<div class="row g-4 mb-4">
    {{-- Card 1 --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-25 text-white">
                    <i class="ti ti-building fs-1"></i>
                </div>
                <div class="text-white">
                    <p class="text-white-50 mb-1 small text-uppercase fw-semibold">Tổng Số Phòng</p>
                    <h2 class="mb-0 fw-bold">{{ $totalRooms }}</h2>
                </div>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-20 text-white small">{{ $rentedRooms }} phòng đang thuê</span>
                </div>
            </div>
        </div>
    </div>
    {{-- Card 2 --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-25 text-white">
                    <i class="ti ti-home-check fs-1"></i>
                </div>
                <div class="text-white">
                    <p class="text-white-50 mb-1 small text-uppercase fw-semibold">Phòng Còn Trống</p>
                    <h2 class="mb-0 fw-bold">{{ $availableRooms }}</h2>
                </div>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-20 text-white small">Sẵn sàng đăng bài</span>
                </div>
            </div>
        </div>
    </div>
    {{-- Card 3 --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-25 text-white">
                    <i class="ti ti-file-description fs-1"></i>
                </div>
                <div class="text-white">
                    <p class="text-white-50 mb-1 small text-uppercase fw-semibold">Yêu Cầu Thuê</p>
                    <h2 class="mb-0 fw-bold">{{ $pendingRequests }}</h2>
                </div>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-20 text-white small">Cần phản hồi gấp</span>
                </div>
            </div>
        </div>
    </div>
    {{-- Card 4 --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-25 text-white">
                    <i class="ti ti-coin fs-1"></i>
                </div>
                <div class="text-white">
                    <p class="text-white-50 mb-1 small text-uppercase fw-semibold">Thu Nhập Tháng</p>
                    <h2 class="mb-0 fw-bold">{{ number_format($monthlyIncome) }}đ</h2>
                </div>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-20 text-white small">Hoa hồng: {{ number_format($pendingCommission) }}đ</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Chart --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Doanh thu cá nhân</h5>
                <div class="text-muted small">Dữ liệu 6 tháng gần đây</div>
            </div>
            <div class="card-body p-4">
                <div id="revenueChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    {{-- Recent Invoices --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Hóa đơn mới</h5>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-outline-primary border-0 bg-primary bg-opacity-10 py-1">Tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentInvoices as $invoice)
                        <div class="list-group-item border-0 py-3 px-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold small">{{ $invoice->room->name }}</span>
                                <span class="fw-bold text-primary small">{{ number_format($invoice->total_amount) }}đ</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size: 11px;">Kỳ: {{ $invoice->month }}/{{ $invoice->year }}</span>
                                <span class="badge bg-{{ $invoice->statusBadge() }}-subtle text-{{ $invoice->statusBadge() }} rounded-pill" style="font-size: 10px;">{{ $invoice->statusLabel() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted small">Chưa có hóa đơn nào</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var chartData = @json($chartData);
        var options = {
            series: [{
                name: 'Doanh thu',
                data: chartData.map(d => d.revenue)
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '45%',
                    distributed: false,
                }
            },
            dataLabels: { enabled: false },
            colors: ['#4f46e5'],
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
            },
            xaxis: {
                categories: chartData.map(d => d.label),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#64748b', fontSize: '12px' } }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b', fontSize: '12px' },
                    formatter: v => (v/1000000).toFixed(1) + 'M'
                }
            },
            tooltip: {
                theme: 'light',
                y: { formatter: v => new Intl.NumberFormat('vi-VN').format(v) + ' đ' }
            }
        };

        var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
        chart.render();
    });
</script>
@endsection
