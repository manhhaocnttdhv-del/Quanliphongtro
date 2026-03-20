@extends('layouts.admin')
@section('title', 'Yêu Cầu Bảo Trì')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fs-3 fw-bold mb-1">🔧 Yêu Cầu Bảo Trì</h1>
        <p class="text-muted mb-0">Quản lý và xử lý các yêu cầu sửa chữa từ khách thuê</p>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg,#f59e0b,#d97706);">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="rounded-3 bg-white bg-opacity-25 p-3"><i class="ti ti-clock fs-3 text-white"></i></div>
                <div class="text-white">
                    <div class="small text-white-50 text-uppercase fw-semibold">Chờ xử lý</div>
                    <div class="fs-2 fw-bold">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg,#06b6d4,#0891b2);">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="rounded-3 bg-white bg-opacity-25 p-3"><i class="ti ti-loader fs-3 text-white"></i></div>
                <div class="text-white">
                    <div class="small text-white-50 text-uppercase fw-semibold">Đang xử lý</div>
                    <div class="fs-2 fw-bold">{{ $stats['in_progress'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg,#10b981,#059669);">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="rounded-3 bg-white bg-opacity-25 p-3"><i class="ti ti-check fs-3 text-white"></i></div>
                <div class="text-white">
                    <div class="small text-white-50 text-uppercase fw-semibold">Hoàn thành</div>
                    <div class="fs-2 fw-bold">{{ $stats['done'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-sm-4 col-lg-3">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">— Tất cả trạng thái —</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="done" {{ request('status') === 'done' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                </select>
            </div>
            <div class="col-sm-4 col-lg-3">
                <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">— Tất cả mức độ —</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>🔴 Khẩn cấp</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>🟠 Cao</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>🔵 Trung bình</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>⚪ Thấp</option>
                </select>
            </div>
            @if(request()->hasAny(['status','priority']))
                <div class="col-auto">
                    <a href="{{ route('admin.maintenance.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-x me-1"></i>Xóa bộ lọc
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">#</th>
                    <th>Tiêu đề</th>
                    <th>Phòng</th>
                    <th>Khách thuê</th>
                    <th>Mức độ</th>
                    <th>Trạng thái</th>
                    <th>Ngày gửi</th>
                    <th class="text-end pe-4">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $req->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ Str::limit($req->title, 50) }}</div>
                            <div class="text-muted small">{{ Str::limit($req->description, 60) }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary rounded-pill">
                                <i class="ti ti-home me-1"></i>{{ $req->room->name }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold small">{{ $req->user->name }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ $req->user->phone }}</div>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $req->priorityBadge() }}-subtle text-{{ $req->priorityBadge() }} border border-{{ $req->priorityBadge() }}-subtle">
                                {{ $req->priorityLabel() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill bg-{{ $req->statusBadge() }}-subtle text-{{ $req->statusBadge() }}">
                                {{ $req->statusLabel() }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.maintenance.show', $req) }}" class="btn btn-sm btn-outline-primary">
                                <i class="ti ti-eye me-1"></i>Xử lý
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="ti ti-tool-off fs-1 d-block mb-2"></i>
                                Chưa có yêu cầu bảo trì nào
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
            <div class="text-muted small">Hiển thị {{ $requests->firstItem() }}–{{ $requests->lastItem() }} / {{ $requests->total() }}</div>
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
