@extends('layouts.admin')

@section('title', 'Quản lý bảo trì')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0">Danh sách yêu cầu bảo trì</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Bảo trì</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <form action="{{ route('admin.maintenance.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã giải quyết</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Lọc</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Phòng</th>
                        <th>Người gửi</th>
                        <th>Tiêu đề</th>
                        <th class="text-center">Mức độ</th>
                        <th class="text-center">Trạng thái</th>
                        <th>Ngày gửi</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td class="ps-4">#{{ $req->id }}</td>
                            <td>
                                <div class="fw-semibold text-primary">{{ $req->room->name }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $req->user->name }}</div>
                                <div class="text-muted small">{{ $req->user->phone }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $req->title }}</div>
                                <div class="text-muted small">{{ Str::limit($req->description, 40) }}</div>
                            </td>
                            <td class="text-center">
                                @php
                                    $prioClass = match($req->priority) {
                                        'high' => 'danger',
                                        'medium' => 'warning',
                                        'low' => 'info',
                                        default => 'secondary'
                                    };
                                    $prioLabel = match($req->priority) {
                                        'high' => 'Cao',
                                        'medium' => 'T.Bình',
                                        'low' => 'Thấp',
                                        default => $req->priority
                                    };
                                @endphp
                                <span class="badge bg-{{ $prioClass }}">{{ $prioLabel }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $req->statusBadge() }}">{{ $req->statusLabel() }}</span>
                            </td>
                            <td>{{ $req->created_at->format('d/m/Y') }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.maintenance.show', $req) }}" class="btn btn-sm btn-icon btn-light">
                                    <i class="ti ti-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">Không có yêu cầu bảo trì nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($requests->hasPages())
        <div class="card-footer bg-white border-0 pt-0">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
