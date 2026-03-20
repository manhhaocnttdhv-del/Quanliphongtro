@extends('layouts.admin')
@section('title', 'Dashboard Nhân viên')

@section('content')
<div class="mb-5">
    <h1 class="fs-3 mb-1">Dashboard Nhân viên</h1>
    <p class="text-muted">Tổng quan công việc cần xử lý</p>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-sm-6">
        <div class="card p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-warning text-white rounded-2"><i class="ti ti-building fs-4"></i></div>
                <div>
                    <h2 class="mb-0 fs-6 text-muted">Phòng chờ duyệt</h2>
                    <h3 class="fw-bold mb-0">{{ $pendingRooms }}</h3>
                    <p class="text-warning mb-0 small">Cần kiểm duyệt</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card p-4 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-3">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-info text-white rounded-2"><i class="ti ti-file-description fs-4"></i></div>
                <div>
                    <h2 class="mb-0 fs-6 text-muted">Yêu cầu thuê</h2>
                    <h3 class="fw-bold mb-0">{{ $pendingRequests }}</h3>
                    <p class="text-info mb-0 small">Đang chờ xử lý</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card p-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-danger text-white rounded-2"><i class="ti ti-tool fs-4"></i></div>
                <div>
                    <h2 class="mb-0 fs-6 text-muted">Bảo trì chờ</h2>
                    <h3 class="fw-bold mb-0">{{ $pendingMaintenance }}</h3>
                    <p class="text-danger mb-0 small">Cần xử lý</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6">
        <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-3">
            <div class="d-flex gap-3">
                <div class="icon-shape icon-md bg-primary text-white rounded-2"><i class="ti ti-users fs-4"></i></div>
                <div>
                    <h2 class="mb-0 fs-6 text-muted">Tổng người dùng</h2>
                    <h3 class="fw-bold mb-0">{{ $totalUsers }}</h3>
                    <p class="text-primary mb-0 small">{{ $totalRooms }} phòng trọ</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Pending Rooms --}}
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent border-bottom px-4 py-3 d-flex justify-content-between">
                <h5 class="mb-0">Phòng chờ duyệt gần đây</h5>
                <a href="{{ route('admin.rooms.index') }}?approval=pending" class="btn btn-sm btn-outline-warning">Xem tất cả</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tên phòng</th><th>Chủ trọ</th><th>Giá</th><th>Khu vực</th><th>Ngày tạo</th><th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRooms as $room)
                            <tr>
                                <td class="fw-semibold">{{ $room->name }}</td>
                                <td>{{ $room->landlord->name ?? 'N/A' }}</td>
                                <td>{{ number_format($room->price) }}đ</td>
                                <td>{{ $room->district_name }}, {{ $room->province_name }}</td>
                                <td>{{ $room->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="{{ route('admin.rooms.approve', $room) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-success"><i class="ti ti-check"></i></button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.rooms.reject', $room) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-danger"><i class="ti ti-x"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Không có phòng chờ duyệt</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
