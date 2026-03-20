@extends('layouts.admin')
@section('title', 'Quản Lý Phòng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fs-3 mb-1">Quản lý phòng trọ</h1>
        <p class="text-muted mb-0">{{ $rooms->total() }} phòng</p>
    </div>
    @can('manage-rooms')
        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary rounded-pill px-4"><i class="ti ti-plus me-1"></i>Thêm phòng</a>
    @endcan
</div>

{{-- Search --}}
<div class="card mb-4">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4"><input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên phòng..."></div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Trạng thái</option>
                    <option value="available" {{ request('status')=='available'?'selected':'' }}>Còn trống</option>
                    <option value="rented" {{ request('status')=='rented'?'selected':'' }}>Đã thuê</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="approval">
                    <option value="">Duyệt</option>
                    <option value="pending" {{ request('approval')=='pending'?'selected':'' }}>Chờ duyệt</option>
                    <option value="approved" {{ request('approval')=='approved'?'selected':'' }}>Đã duyệt</option>
                    <option value="rejected" {{ request('approval')=='rejected'?'selected':'' }}>Bị từ chối</option>
                </select>
            </div>
            <div class="col-md-2"><button class="btn btn-primary w-100"><i class="ti ti-search me-1"></i>Tìm</button></div>
            <div class="col-md-2"><a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary w-100">Reset</a></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Phòng</th><th>Giá/tháng</th><th>Diện tích</th><th>Điện/kWh</th><th>Nước/m³</th><th>Trạng thái</th><th>Duyệt</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                    <tr>
                        <td>{{ $room->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($room->images->first())
                                    <img src="{{ asset('storage/'.$room->images->first()->image_path) }}" width="40" height="40" class="rounded" style="object-fit:cover;">
                                @else
                                    <div class="rounded bg-light d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="ti ti-building text-muted"></i></div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $room->name }}</div>
                                    <div class="text-muted small">{{ $room->district_name }}, {{ $room->province_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ number_format($room->price) }}đ</td>
                        <td>{{ $room->area ?? '-' }} m²</td>
                        <td>{{ number_format($room->electricity_price) }}đ</td>
                        <td>{{ number_format($room->water_price) }}đ</td>
                        <td><span class="badge bg-{{ $room->statusBadge() }}-subtle text-{{ $room->statusBadge() }}">{{ $room->statusLabel() }}</span></td>
                        <td>
                            <span class="badge bg-{{ $room->approvalBadge() }}-subtle text-{{ $room->approvalBadge() }}">
                                {{ $room->approvalLabel() }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                {{-- Approve/Reject buttons for pending rooms --}}
                                @if($room->approval_status === 'pending')
                                    @can('approve-rooms')
                                        <form method="POST" action="{{ route('admin.rooms.approve', $room) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-success" title="Duyệt"><i class="ti ti-check"></i></button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.rooms.reject', $room) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-danger" title="Từ chối"><i class="ti ti-x"></i></button>
                                        </form>
                                    @endcan
                                @endif
                                @can('manage-rooms')
                                    <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="ti ti-edit"></i></a>
                                    <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" onsubmit="return confirm('Xoá phòng này?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Xoá"><i class="ti ti-trash"></i></button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted py-5">Chưa có phòng nào</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-transparent">{{ $rooms->links() }}</div>
</div>
@endsection
