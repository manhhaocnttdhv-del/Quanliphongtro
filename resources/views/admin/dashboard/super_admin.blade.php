@extends('layouts.admin')
@section('title', 'Super Admin Dashboard')

@section('content')
<div class="mb-5">
    <h1 class="fs-3 mb-1">Hệ Thống Quản Trị Tổng</h1>
    <p class="text-muted">Tổng quan toàn bộ hệ thống nhà trọ</p>
</div>

<div class="row g-4 mb-4">
    {{-- Card 1 --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-25">
                    <i class="ti ti-users fs-1 text-white"></i>
                </div>
                <div class="text-white">
                    <p class="text-white-50 mb-1 small text-uppercase fw-semibold">Tổng Chủ Trọ</p>
                    <h2 class="mb-0 fw-bold">{{ $totalLandlords }}</h2>
                </div>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-20 text-white small">Toàn hệ thống</span>
                </div>
            </div>
        </div>
    </div>
    {{-- Card 2 --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-25">
                    <i class="ti ti-building fs-1 text-white"></i>
                </div>
                <div class="text-white">
                    <p class="text-white-50 mb-1 small text-uppercase fw-semibold">Tổng Số Phòng</p>
                    <h2 class="mb-0 fw-bold">{{ $totalRooms }}</h2>
                </div>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-20 text-white small">Quản lý trực tiếp</span>
                </div>
            </div>
        </div>
    </div>
    {{-- Card 3 --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-25">
                    <i class="ti ti-coin fs-1 text-white"></i>
                </div>
                <div class="text-white">
                    <p class="text-white-50 mb-1 small text-uppercase fw-semibold">Hoa Hồng Đã Thu</p>
                    <h2 class="mb-0 fw-bold">{{ number_format($totalCommissions) }}đ</h2>
                </div>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-20 text-white small">5% doanh thu</span>
                </div>
            </div>
        </div>
    </div>
    {{-- Card 4 --}}
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 overflow-hidden" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute end-0 top-0 mt-3 me-3 opacity-25">
                    <i class="ti ti-clock-hour-4 fs-1 text-white"></i>
                </div>
                <div class="text-white">
                    <p class="text-white-50 mb-1 small text-uppercase fw-semibold">Chờ Thanh Toán</p>
                    <h2 class="mb-0 fw-bold">{{ number_format($pendingCommissions) }}đ</h2>
                </div>
                <div class="mt-3">
                    <span class="badge bg-white bg-opacity-20 text-white small">Cần phê duyệt</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Charts Section --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Tổng doanh thu hệ thống</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown">6 tháng gần đây <i class="ti ti-chevron-down ms-1"></i></button>
                </div>
            </div>
            <div class="card-body p-4">
                <div id="revenueChart" style="min-height: 350px;"></div>
            </div>
        </div>
    </div>

    {{-- Recent Landlords Section --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Chủ trọ mới</h5>
                <a href="{{ route('admin.landlords.index') }}" class="btn btn-sm btn-outline-primary border-0 bg-primary bg-opacity-10 py-1">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            @foreach($recentLandlords as $landlord)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-sm rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-bold">
                                                {{ substr($landlord->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold small">{{ $landlord->name }}</div>
                                                <div class="text-muted" style="font-size: 11px;">{{ $landlord->created_at->format('d/m/Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.landlords.show', $landlord) }}" class="btn btn-sm btn-light p-1 rounded-2"><i class="ti ti-chevron-right"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                type: 'area',
                height: 350,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3, colors: ['#6366f1'] },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100]
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: { top: 0, right: 0, bottom: 0, left: 10 }
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
