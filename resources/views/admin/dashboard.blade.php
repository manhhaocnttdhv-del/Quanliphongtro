@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="mb-5">
    <h1 class="fs-3 mb-1">Dashboard</h1>
    <p class="text-muted">Tổng quan hệ thống quản lý phòng trọ</p>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-sm-6">
        <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-primary text-white rounded-2"><i class="ti ti-building fs-4"></i></div>
                <div>
                    <h2 class="mb-0 fs-6 text-muted">Tổng số phòng</h2>
                    <h3 class="fw-bold mb-0">{{ $totalRooms }}</h3>
                    <p class="text-primary mb-0 small">{{ $rentedRooms }} đang thuê</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-3">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-success text-white rounded-2"><i class="ti ti-home-check fs-4"></i></div>
                <div>
                    <h2 class="mb-0 fs-6 text-muted">Phòng trống</h2>
                    <h3 class="fw-bold mb-0">{{ $availableRooms }}</h3>
                    <p class="text-success mb-0 small">Sẵn sàng cho thuê</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-warning text-white rounded-2"><i class="ti ti-file-description fs-4"></i></div>
                <div>
                    <h2 class="mb-0 fs-6 text-muted">Yêu cầu chờ</h2>
                    <h3 class="fw-bold mb-0">{{ $pendingRequests }}</h3>
                    <p class="text-warning mb-0 small">Cần xử lý</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card p-4 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-3">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-info text-white rounded-2"><i class="ti ti-currency-dong fs-4"></i></div>
                <div>
                    <h2 class="mb-0 fs-6 text-muted">Doanh thu tháng</h2>
                    <h3 class="fw-bold mb-0">{{ number_format($monthlyRevenue/1000000, 1) }}M</h3>
                    <p class="text-info mb-0 small">VNĐ tháng này</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart + Pending --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Doanh thu 12 tháng gần nhất</h5>
                <span class="badge bg-primary-subtle text-primary">{{ number_format($yearlyRevenue) }} đ / năm nay</span>
            </div>
            <div class="card-body p-4">
                <div id="revenueChart"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-transparent border-bottom px-4 py-3">
                <h5 class="mb-0">Yêu cầu mới cần duyệt</h5>
            </div>
            <ul class="list-group list-group-flush">
                @forelse($pendingRequests2 as $req)
                    <li class="list-group-item px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold small">{{ $req->user->name }}</div>
                                <div class="text-muted" style="font-size:12px;">{{ $req->room->name }} · {{ $req->created_at->diffForHumans() }}</div>
                            </div>
                            <a href="{{ route('admin.rent-requests.index') }}" class="btn btn-sm btn-warning">Xem</a>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted py-4">Không có yêu cầu mới</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

{{-- Recent invoices --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex justify-content-between">
                <h5 class="mb-0">Hóa đơn gần đây</h5>
                <a href="{{ route('admin.invoices.index') }}" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Phòng</th><th>Tháng/Năm</th><th>Tổng tiền</th><th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentInvoices as $invoice)
                            <tr>
                                <td>{{ $invoice->room->name ?? 'N/A' }}</td>
                                <td>{{ $invoice->month }}/{{ $invoice->year }}</td>
                                <td>{{ number_format($invoice->total_amount) }}đ</td>
                                <td><span class="badge bg-{{ $invoice->statusBadge() }}-subtle text-{{ $invoice->statusBadge() }}">{{ $invoice->statusLabel() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
var chartData = @json($chartData);
var options = {
    series: [{ name: 'Doanh thu', data: chartData.map(d => d.revenue) }],
    chart: { type: 'bar', height: 280, toolbar: { show: false } },
    plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
    colors: ['#0d6efd'],
    xaxis: { categories: chartData.map(d => d.label), labels: { style: { fontSize: '11px' } } },
    yaxis: { labels: { formatter: v => (v/1000000).toFixed(1)+'M' } },
    tooltip: { y: { formatter: v => new Intl.NumberFormat('vi-VN').format(v) + ' đ' } },
    dataLabels: { enabled: false },
    grid: { borderColor: '#f0f0f0' },
};
new ApexCharts(document.getElementById('revenueChart'), options).render();
</script>
@endsection
